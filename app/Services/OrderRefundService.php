<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderRefund;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderRefundService
{
    private function cents(mixed $amount): int
    {
        return (int) round((float) $amount * 100);
    }

    private function money(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }

    public function approve(int $orderId, int $returnId, User $admin, bool $includeDelivery,
        string $returnShippingAmount, ?string $shippingReceiptReference, string $note): OrderRefund
    {
        return DB::transaction(function () use ($orderId, $returnId, $admin, $includeDelivery, $returnShippingAmount, $shippingReceiptReference, $note) {
            $order = Order::query()->lockForUpdate()->findOrFail($orderId);
            $return = $order->returnRequests()->whereKey($returnId)->lockForUpdate()->firstOrFail();
            if ($order->status !== 'delivered' || $order->payment_method !== 'cod'
                || $order->payment_status !== 'paid' || $order->paid_at === null
                || $return->status !== 'received'
                || ! in_array($return->inspection_outcome, ['resellable', 'not_resellable'], true)
                || $return->refund()->exists()) {
                throw ValidationException::withMessages([
                    'refund' => 'A refund can only be approved once for an inspected, accepted return on a paid COD order.',
                ]);
            }

            $item = $order->items()->whereKey($return->order_item_id)->firstOrFail();
            $subtotal = $this->cents($order->subtotal);
            $discount = $this->cents($order->discount_amount);
            $gross = $this->cents($item->unit_price) * $return->quantity;
            $priorGross = $this->cents($order->refunds()->sum('item_amount'));
            $priorDiscount = $this->cents($order->refunds()->sum('discount_share'));
            $priorDelivery = $this->cents($order->refunds()->sum('delivery_amount'));
            $priorTotal = $this->cents($order->refunds()->sum('total_amount'));
            $priorReturnShipping = $this->cents($order->refunds()->sum('return_shipping_amount'));

            if ($subtotal < 1 || $discount < 0 || $discount > $subtotal || $gross < 1
                || $priorGross + $gross > $subtotal || $priorDiscount > $discount) {
                throw ValidationException::withMessages(['refund' => 'Order amounts cannot be reconciled automatically.']);
            }

            // Cumulative rounding ensures the final returned item receives the
            // last discount cent rather than over/under-allocating it.
            $cumulativeDiscount = (int) round($discount * ($priorGross + $gross) / $subtotal);
            $discountShare = $cumulativeDiscount - $priorDiscount;
            if ($discountShare < 0 || $discountShare > $gross) {
                throw ValidationException::withMessages(['refund' => 'The discount share is inconsistent.']);
            }

            $delivery = 0;
            if ($includeDelivery) {
                if ($priorGross + $gross !== $subtotal || $priorDelivery !== 0) {
                    throw ValidationException::withMessages([
                        'include_delivery' => 'Delivery charge can be included only when the full order is returned, once.',
                    ]);
                }
                $delivery = $this->cents($order->delivery_fee);
            }
            $returnShipping = $this->cents($returnShippingAmount);
            if ($returnShipping > 0 && ($return->return_shipping_payer !== 'earthquick'
                || ! $shippingReceiptReference)) {
                throw ValidationException::withMessages([
                    'return_shipping_amount' => 'Return postage reimbursement requires a verified Rthquick-paid reason and a courier receipt reference.',
                ]);
            }
            if ($returnShipping === 0 && $shippingReceiptReference) {
                throw ValidationException::withMessages([
                    'return_shipping_receipt_reference' => 'A courier receipt reference is only needed when reimbursing return postage.',
                ]);
            }
            $saleRefund = $gross - $discountShare + $delivery;
            $total = $saleRefund + $returnShipping;
            if ($saleRefund < 0 || $priorTotal - $priorReturnShipping + $saleRefund > $this->cents($order->total)) {
                throw ValidationException::withMessages(['refund' => 'The refund exceeds the original paid order total.']);
            }

            return $order->refunds()->create([
                'order_return_request_id' => $return->id,
                'item_amount' => $this->money($gross),
                'discount_share' => $this->money($discountShare),
                'delivery_amount' => $this->money($delivery),
                'return_shipping_amount' => $this->money($returnShipping),
                'return_shipping_receipt_reference' => $returnShipping > 0 ? $shippingReceiptReference : null,
                'total_amount' => $this->money($total),
                'status' => $total > 0 ? 'approved' : 'no_refund_due',
                'approved_by_user_id' => $admin->id,
                'approved_at' => now(),
                'approval_note' => $note,
            ]);
        });
    }
}

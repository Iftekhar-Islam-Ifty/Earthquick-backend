<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderCancellationRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderCancellationService
{
    /**
     * Cancel an unpaid, unfulfilled COD order exactly once. All inventory,
     * coupon, request and history changes share the same database transaction.
     */
    public function cancel(int $orderId, User $admin, string $note, ?int $requestId = null): Order
    {
        $order = DB::transaction(function () use ($orderId, $admin, $note, $requestId) {
            $order = Order::query()->lockForUpdate()->findOrFail($orderId);
            $oldStatus = $order->status;
            if (! in_array($order->status, ['pending', 'confirmed'], true)
                || $order->payment_method !== 'cod'
                || $order->payment_status !== 'due_on_delivery'
                || $order->paid_at !== null) {
                throw ValidationException::withMessages([
                    'status' => 'Only an unpaid COD order before processing can be cancelled here. Review delivery or refund separately.',
                ]);
            }

            $request = $requestId === null
                ? $order->cancellationRequests()->where('status', 'pending')->lockForUpdate()->first()
                : $order->cancellationRequests()->whereKey($requestId)->lockForUpdate()->firstOrFail();
            if ($request && $request->status !== 'pending') {
                throw ValidationException::withMessages(['cancellation' => 'This cancellation request was already decided.']);
            }
            if (! $request) {
                $request = $order->cancellationRequests()->create([
                    'requested_by_user_id' => $admin->id,
                    'source' => 'admin',
                    'reason' => $note,
                    'status' => 'pending',
                ]);
            }

            $items = $order->items()->orderBy('product_id')->orderBy('variant_id')->get();
            if ($items->isEmpty()) {
                throw ValidationException::withMessages(['status' => 'This order has no items to reconcile.']);
            }

            foreach ($items as $item) {
                $product = Product::query()->lockForUpdate()->find($item->product_id);
                if (! $product) {
                    throw ValidationException::withMessages(['status' => 'A product is missing; reconcile stock manually.']);
                }

                if ($item->variant_id !== null) {
                    $variant = ProductVariant::query()->where('product_id', $product->id)
                        ->lockForUpdate()->find($item->variant_id);
                    if (! $variant) {
                        throw ValidationException::withMessages(['status' => 'A variant is missing; reconcile stock manually.']);
                    }
                    $variant->increment('stock_quantity', $item->quantity);
                    $remaining = (int) $product->variants()->where('is_active', true)->sum('stock_quantity');
                } else {
                    if ($item->variant_sku || $product->variants()->exists()) {
                        throw ValidationException::withMessages(['status' => 'The original inventory option is unclear; reconcile stock manually.']);
                    }
                    $remaining = $product->stock_quantity + $item->quantity;
                }

                $product->update(['stock_quantity' => $remaining, 'in_stock' => $remaining > 0]);
            }

            if ($order->coupon_code) {
                $coupon = $order->coupon_id
                    ? Coupon::query()->lockForUpdate()->find($order->coupon_id)
                    : null;
                if (! $coupon || $coupon->code !== $order->coupon_code || $coupon->used_count < 1) {
                    throw ValidationException::withMessages([
                        'status' => 'The original coupon redemption cannot be verified; reconcile it manually.',
                    ]);
                }
                $coupon->decrement('used_count');
            }

            $order->update(['status' => 'cancelled', 'payment_status' => 'not_due', 'admin_notes' => $note]);
            $request->update([
                'status' => 'approved',
                'decided_by_user_id' => $admin->id,
                'decision_note' => $note,
                'decided_at' => now(),
            ]);
            $order->statusEvents()->create([
                'actor_user_id' => $admin->id,
                'source' => 'cancellation',
                'from_status' => $oldStatus,
                'to_status' => 'cancelled',
                'note' => $note,
            ]);

            return $order;
        });

        app(CustomerCommunications::class)->order($order, 'cancelled');

        return $order;
    }
}

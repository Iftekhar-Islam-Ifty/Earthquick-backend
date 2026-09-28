<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Tests\TestCase;

class RefundWorkflowTest extends TestCase
{
    private function fixture(int $discountCents = 20000): array
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = Product::firstOrFail();
        $product->update(['stock_quantity' => 3, 'in_stock' => true]);
        $order = Order::create([
            'order_number' => 'EQ-REFUND-'.uniqid(),
            'customer_name' => 'Synthetic customer', 'customer_phone' => '01712345678',
            'delivery_zone' => 'inside_ctg', 'district' => 'Chattogram',
            'area' => 'Nasirabad', 'address' => 'Synthetic address',
            'payment_method' => 'cod', 'payment_status' => 'paid', 'paid_at' => now(),
            'subtotal' => 2000, 'discount_amount' => $discountCents / 100,
            'delivery_fee' => 80, 'total' => 2080 - $discountCents / 100,
            'status' => 'delivered',
        ]);
        $items = collect();
        $returns = collect();
        foreach ([1, 2] as $index) {
            $item = OrderItem::create([
                'order_id' => $order->id, 'product_id' => $product->id,
                'product_name' => 'Synthetic item '.$index,
                'unit_price' => 1000, 'quantity' => 1, 'total_price' => 1000,
                'is_returnable' => true, 'return_window_days' => 7,
            ]);
            $items->push($item);
            $returns->push($order->returnRequests()->create([
                'order_item_id' => $item->id, 'source' => 'customer', 'quantity' => 1,
                'reason' => 'Item did not fit properly', 'status' => 'received',
            ]));
        }

        return [$admin, $order, $product, $items, $returns];
    }

    private function inspectUrl(Order $order, int $returnId): string
    {
        return route('admin.orders.return-inspect', [$order->id, $returnId]);
    }

    private function approveUrl(Order $order, int $returnId): string
    {
        return route('admin.orders.refund-approve', [$order->id, $returnId]);
    }

    private function verificationData(string $method): array
    {
        return [
            'method' => $method,
            'recipient_name' => 'Verified Recipient',
            'recipient_account_last4' => $method === 'cash' ? null : '1234',
            'recipient_verified_via' => 'order_contact',
            'recipient_verification_note' => 'Called the original order contact and confirmed recipient.',
            'confirm_recipient_verified' => '1',
        ];
    }

    private function verifyRecipient(Order $order, int $refundId, string $method): void
    {
        $this->post(route('admin.orders.refund-verify-recipient', [$order->id, $refundId]),
            $this->verificationData($method))->assertRedirect();
    }

    public function test_inspection_restock_and_refund_are_distinct_single_use_actions(): void
    {
        [$admin, $order, $product, , $returns] = $this->fixture();
        $return = $returns[0];
        $this->post($this->inspectUrl($order, $return->id), [
            'inspection_outcome' => 'resellable', 'inspection_note' => 'Product is in good condition',
        ])->assertRedirect('/login');
        $this->actingAs($admin)->post(route('admin.orders.return-restock', [$order->id, $return->id]), [
            'confirm_restock' => '1',
        ])->assertSessionHasErrors('return');
        $this->post($this->inspectUrl($order, $return->id), [
            'inspection_outcome' => 'resellable', 'inspection_note' => 'Product is in good condition',
        ])->assertRedirect();
        $this->post($this->inspectUrl($order, $return->id), [
            'inspection_outcome' => 'rejected', 'inspection_note' => 'Duplicate inspection',
        ])->assertSessionHasErrors('return');
        $this->assertSame(3, $product->fresh()->stock_quantity);
        $this->assertSame(0, $order->refunds()->count());
        $restock = route('admin.orders.return-restock', [$order->id, $return->id]);
        $this->post($restock, ['confirm_restock' => '1'])->assertRedirect();
        $this->assertSame(4, $product->fresh()->stock_quantity);
        $this->post($restock, ['confirm_restock' => '1'])->assertSessionHasErrors('return');
        $this->assertSame(4, $product->fresh()->stock_quantity);
        $this->assertNotNull($return->fresh()->restocked_at);
        $this->get(route('admin.orders.show', $order->id))
            ->assertOk()->assertSee('Approve Refund Amount')->assertSee('Restocked');
    }

    public function test_partial_discount_and_full_order_delivery_refund_then_external_completion(): void
    {
        [$admin, $order, , , $returns] = $this->fixture();
        foreach ($returns as $return) {
            $return->update(['inspection_outcome' => 'not_resellable', 'inspection_note' => 'Damage verified', 'inspected_at' => now()]);
        }
        $this->actingAs($admin)->post($this->approveUrl($order, $returns[0]->id), [
            'approval_note' => 'Eligible partial refund', 'include_delivery' => '1',
        ])->assertSessionHasErrors('include_delivery');
        $this->assertSame(0, $order->refunds()->count());

        $this->post($this->approveUrl($order, $returns[0]->id), [
            'approval_note' => 'Eligible partial refund',
        ])->assertRedirect();
        $first = $order->refunds()->sole();
        $this->assertSame('100.00', $first->discount_share);
        $this->assertSame('0.00', $first->delivery_amount);
        $this->assertSame('900.00', $first->total_amount);
        $this->post($this->approveUrl($order, $returns[0]->id), [
            'approval_note' => 'Duplicate approval attempt',
        ])->assertSessionHasErrors('refund');

        $this->post($this->approveUrl($order, $returns[1]->id), [
            'approval_note' => 'Full return delivery reviewed', 'include_delivery' => '1',
        ])->assertRedirect();
        $second = $order->refunds()->where('order_return_request_id', $returns[1]->id)->firstOrFail();
        $this->assertSame('100.00', $second->discount_share);
        $this->assertSame('80.00', $second->delivery_amount);
        $this->assertSame('980.00', $second->total_amount);
        $this->assertSame('1880.00', number_format($order->refunds()->sum('total_amount'), 2, '.', ''));

        $complete = route('admin.orders.refund-complete', [$order->id, $first->id]);
        $this->post($complete, ['method' => 'bank_transfer', 'reference' => 'BANK-1234'])
            ->assertSessionHasErrors('confirm_sent');
        $this->assertSame('approved', $first->fresh()->status);
        $this->post($complete, ['reference' => 'BANK-1234', 'confirm_sent' => '1'])
            ->assertSessionHasErrors('refund');
        $this->verifyRecipient($order, $first->id, 'bank_transfer');
        $this->post($complete, ['reference' => 'BANK-1234', 'confirm_sent' => '1'])
            ->assertRedirect();
        $this->assertSame('completed', $first->fresh()->status);
        $this->post($complete, ['reference' => 'BANK-5678', 'confirm_sent' => '1'])
            ->assertSessionHasErrors('refund');
        $this->verifyRecipient($order, $second->id, 'cash');
        $this->post(route('admin.orders.refund-complete', [$order->id, $second->id]),
            ['reference' => 'BANK-1234', 'confirm_sent' => '1'])->assertSessionHasErrors('reference');
    }

    public function test_rejected_inspection_and_unpaid_order_cannot_be_refunded_or_restocked(): void
    {
        [$admin, $order, $product, , $returns] = $this->fixture();
        $this->actingAs($admin)->post($this->inspectUrl($order, $returns[0]->id), [
            'inspection_outcome' => 'rejected', 'inspection_note' => 'Wrong item returned',
        ])->assertRedirect();
        $this->post($this->approveUrl($order, $returns[0]->id), [
            'approval_note' => 'Should be blocked',
        ])->assertSessionHasErrors('refund');
        $this->post(route('admin.orders.return-restock', [$order->id, $returns[0]->id]), [
            'confirm_restock' => '1',
        ])->assertSessionHasErrors('return');
        $returns[1]->update(['inspection_outcome' => 'resellable', 'inspection_note' => 'Looks fine']);
        $order->update(['payment_status' => 'pending', 'paid_at' => null]);
        $this->post($this->approveUrl($order, $returns[1]->id), [
            'approval_note' => 'Should be blocked',
        ])->assertSessionHasErrors('refund');
        $this->assertSame(0, $order->refunds()->count());
        $this->assertSame(3, $product->fresh()->stock_quantity);
    }

    public function test_discount_rounding_is_cumulative_and_customer_sees_paid_only_after_completion(): void
    {
        [$admin, $order, , , $returns] = $this->fixture(1);
        foreach ($returns as $return) {
            $return->update(['inspection_outcome' => 'not_resellable', 'inspection_note' => 'Damage verified']);
        }
        $this->actingAs($admin)->post($this->approveUrl($order, $returns[0]->id), [
            'approval_note' => 'Partial refund approved',
        ])->assertRedirect();
        $this->post($this->approveUrl($order, $returns[1]->id), [
            'approval_note' => 'Second refund approved',
        ])->assertRedirect();
        $this->assertSame('0.01', number_format($order->refunds()->sum('discount_share'), 2, '.', ''));
        $first = $order->refunds()->where('order_return_request_id', $returns[0]->id)->firstOrFail();
        session(['checkout_order_numbers' => [$order->order_number]]);
        $this->get(route('checkout.success', $order->order_number))
            ->assertOk()->assertSee('payment pending')->assertDontSee('Refund paid:');
        $this->verifyRecipient($order, $first->id, 'cash');
        $this->post(route('admin.orders.refund-complete', [$order->id, $first->id]),
            ['reference' => 'CASH-ROUND-1', 'confirm_sent' => '1'])->assertRedirect();
        $this->get(route('checkout.success', $order->order_number))
            ->assertOk()->assertSee('Refund paid:');
    }

    public function test_variant_restock_fails_safely_if_original_option_is_missing(): void
    {
        [$admin, $order, $product, $items, $returns] = $this->fixture();
        $variant = ProductVariant::create([
            'product_id' => $product->id, 'sku' => 'QA-RETURN-V1',
            'label' => 'QA size', 'attributes' => ['size' => 'QA'],
            'stock_quantity' => 2, 'is_active' => true,
        ]);
        $items[0]->update(['variant_id' => $variant->id, 'variant_sku' => $variant->sku]);
        $returns[0]->update(['inspection_outcome' => 'resellable', 'inspection_note' => 'Variant checked']);
        $url = route('admin.orders.return-restock', [$order->id, $returns[0]->id]);
        $variant->update(['sku' => 'QA-CHANGED']);
        $this->actingAs($admin)->post($url, ['confirm_restock' => '1'])->assertSessionHasErrors('return');
        $this->assertNull($returns[0]->fresh()->restocked_at);
        $this->assertSame(2, $variant->fresh()->stock_quantity);
        $variant->update(['sku' => 'QA-RETURN-V1']);
        $this->post($url, ['confirm_restock' => '1'])->assertRedirect();
        $this->assertSame(3, $variant->fresh()->stock_quantity);
        $this->assertSame(3, $product->fresh()->stock_quantity);
    }

    public function test_return_courier_reimbursement_requires_verified_earthquick_liability_and_unique_receipt(): void
    {
        [$admin, $order, , , $returns] = $this->fixture();
        foreach ($returns as $return) {
            $return->update(['inspection_outcome' => 'not_resellable', 'inspection_note' => 'Damage verified']);
        }
        $this->actingAs($admin)->post($this->approveUrl($order, $returns[0]->id), [
            'approval_note' => 'Review return postage',
            'return_shipping_amount' => '75.50',
            'return_shipping_receipt_reference' => 'COURIER-QA-1',
        ])->assertSessionHasErrors('return_shipping_amount');
        $returns[0]->update(['verified_issue_type' => 'other', 'return_shipping_payer' => 'customer']);
        $this->post($this->approveUrl($order, $returns[0]->id), [
            'approval_note' => 'Customer-funded postage', 'return_shipping_amount' => '75.50',
            'return_shipping_receipt_reference' => 'COURIER-QA-1',
        ])->assertSessionHasErrors('return_shipping_amount');
        $returns[0]->update(['verified_issue_type' => 'wrong_item', 'return_shipping_payer' => 'earthquick']);
        $this->post($this->approveUrl($order, $returns[0]->id), [
            'approval_note' => 'Missing courier receipt', 'return_shipping_amount' => '75.50',
        ])->assertSessionHasErrors('return_shipping_amount');
        $this->post($this->approveUrl($order, $returns[0]->id), [
            'approval_note' => 'Verified return courier receipt', 'return_shipping_amount' => '75.50',
            'return_shipping_receipt_reference' => 'COURIER-QA-1',
        ])->assertRedirect();
        $first = $order->refunds()->sole();
        $this->assertSame('75.50', $first->return_shipping_amount);
        $this->assertSame('975.50', $first->total_amount);
        $returns[1]->update(['verified_issue_type' => 'damaged_defective', 'return_shipping_payer' => 'earthquick']);
        $this->post($this->approveUrl($order, $returns[1]->id), [
            'approval_note' => 'Duplicate courier receipt', 'return_shipping_amount' => '75.50',
            'return_shipping_receipt_reference' => 'COURIER-QA-1',
        ])->assertSessionHasErrors('return_shipping_receipt_reference');
        $this->post($this->approveUrl($order, $returns[1]->id), [
            'approval_note' => 'Different courier receipt', 'return_shipping_amount' => '60',
            'return_shipping_receipt_reference' => 'COURIER-QA-2', 'include_delivery' => '1',
        ])->assertRedirect();
        $this->assertSame('1040.00', $order->refunds()->where('order_return_request_id', $returns[1]->id)->sole()->total_amount);
    }

    public function test_paid_refund_requires_recipient_verification_without_storing_full_account(): void
    {
        [$admin, $order, , , $returns] = $this->fixture();
        $returns[0]->update(['inspection_outcome' => 'not_resellable', 'inspection_note' => 'Damage verified']);
        $this->actingAs($admin)->post($this->approveUrl($order, $returns[0]->id), [
            'approval_note' => 'Eligible item refund',
        ])->assertRedirect();
        $refund = $order->refunds()->sole();
        $url = route('admin.orders.refund-verify-recipient', [$order->id, $refund->id]);
        $data = $this->verificationData('mobile_transfer');
        unset($data['confirm_recipient_verified']);
        $this->post($url, $data)->assertSessionHasErrors('confirm_recipient_verified');
        $this->assertSame('approved', $refund->fresh()->status);
        $data['confirm_recipient_verified'] = '1';
        $data['recipient_account_last4'] = '01712345678';
        $this->post($url, $data)->assertSessionHasErrors('recipient_account_last4');
        $this->assertSame('approved', $refund->fresh()->status);
        $data['recipient_account_last4'] = '5678';
        $this->post($url, $data)->assertRedirect();
        $this->assertSame('approved', $refund->fresh()->status);
        $this->post($url, $data)->assertSessionHasErrors('refund');
        $this->post(route('admin.orders.refund-complete', [$order->id, $refund->id]), [
            'reference' => 'MOBILE-QA-1', 'confirm_sent' => '1',
        ])->assertRedirect();
        $paid = $refund->fresh();
        $this->assertSame('completed', $paid->status);
        $this->assertSame('5678', $paid->recipient_account_last4);
        $this->assertSame($admin->id, $paid->recipient_verified_by_user_id);
        $this->assertNotNull($paid->recipient_verified_at);
        $this->assertSame('order_contact', $paid->recipient_verified_via);
    }
}

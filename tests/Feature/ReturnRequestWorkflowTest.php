<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class ReturnRequestWorkflowTest extends TestCase
{
    private function deliveredOrder(?User $customer = null, int $quantity = 2): array
    {
        $product = Product::firstOrFail();
        $product->update(['stock_quantity' => 4, 'in_stock' => true]);
        $order = Order::create([
            'order_number' => 'EQ-RETURN-'.uniqid(),
            'user_id' => $customer?->id,
            'customer_name' => 'Return QA Customer',
            'customer_phone' => '01712345678',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'Nasirabad',
            'address' => 'Synthetic address',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'paid_at' => now(),
            'subtotal' => 1000 * $quantity,
            'delivery_fee' => 80,
            'total' => 1000 * $quantity + 80,
            'status' => 'delivered',
        ]);
        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 1000,
            'quantity' => $quantity,
            'total_price' => 1000 * $quantity,
            'is_returnable' => true,
            'return_window_days' => 7,
        ]);
        $order->statusEvents()->create([
            'source' => 'admin', 'from_status' => 'in_transit', 'to_status' => 'delivered',
        ]);

        return [$order, $item, $product];
    }

    private function requestUrl(Order $order, OrderItem $item): string
    {
        return route('orders.return-request', [$order->order_number, $item->id]);
    }

    private function requestData(int $quantity = 1): array
    {
        return ['quantity' => $quantity, 'reported_issue_type' => 'other',
            'reason' => 'The item does not fit as expected'];
    }

    public function test_guest_and_account_order_ownership_are_enforced(): void
    {
        [$guestOrder, $guestItem] = $this->deliveredOrder();
        $this->post($this->requestUrl($guestOrder, $guestItem), $this->requestData())->assertNotFound();
        session(['checkout_order_numbers' => [$guestOrder->order_number]]);
        $this->post($this->requestUrl($guestOrder, $guestItem), $this->requestData())->assertRedirect();
        $this->assertSame('pending', $guestOrder->returnRequests()->sole()->status);

        $owner = User::factory()->create();
        [$accountOrder, $accountItem] = $this->deliveredOrder($owner);
        $this->actingAs(User::factory()->create())
            ->post($this->requestUrl($accountOrder, $accountItem), $this->requestData())->assertNotFound();
        $this->actingAs($owner)->post($this->requestUrl($accountOrder, $accountItem), $this->requestData())->assertRedirect();
        $this->assertSame($owner->id, $accountOrder->returnRequests()->sole()->requested_by_user_id);
        $this->actingAs($owner)->get(route('account.order', $accountOrder->order_number))
            ->assertOk()->assertSee('Item returns')->assertSee('1 unit(s)')->assertSee('Reason category');
    }

    public function test_item_must_belong_to_order_and_use_immutable_return_policy_and_delivery_window(): void
    {
        [$order, $item] = $this->deliveredOrder();
        [$other, $otherItem] = $this->deliveredOrder();
        session(['checkout_order_numbers' => [$order->order_number]]);
        $this->post($this->requestUrl($order, $otherItem), $this->requestData())->assertNotFound();

        $item->update(['is_returnable' => false]);
        $this->post($this->requestUrl($order, $item), $this->requestData())->assertSessionHasErrors('return');
        $item->update(['is_returnable' => true]);

        $event = $order->statusEvents()->sole();
        $event->created_at = now()->subDays(8);
        $event->save();
        $this->post($this->requestUrl($order, $item), $this->requestData())->assertSessionHasErrors('return');
        $this->assertSame(0, $order->returnRequests()->count());

        $event->delete();
        $this->post($this->requestUrl($order, $item), $this->requestData())->assertSessionHasErrors('return');
        $this->assertSame('delivered', $other->status);
    }

    public function test_partial_requests_cannot_exceed_purchased_quantity_and_rejection_releases_capacity(): void
    {
        [$order, $item] = $this->deliveredOrder(null, 2);
        session(['checkout_order_numbers' => [$order->order_number]]);
        $url = $this->requestUrl($order, $item);
        $this->post($url, $this->requestData(1))->assertRedirect();
        $this->post($url, $this->requestData(2))->assertSessionHasErrors('quantity');
        $this->post($url, $this->requestData(1))->assertRedirect();
        $this->post($url, $this->requestData(1))->assertSessionHasErrors('quantity');
        $this->assertSame(2, $order->returnRequests()->count());

        $first = $order->returnRequests()->orderBy('id')->firstOrFail();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.orders.return-decision', [$order->id, $first->id]), [
            'decision' => 'reject', 'decision_note' => 'Does not meet return conditions',
        ])->assertRedirect();
        $this->post($url, $this->requestData(1))->assertRedirect();
        $this->assertSame(3, $order->returnRequests()->count());
    }

    public function test_admin_authorization_and_receipt_do_not_mark_refunded_or_restock(): void
    {
        [$order, $item, $product] = $this->deliveredOrder();
        session(['checkout_order_numbers' => [$order->order_number]]);
        $this->post($this->requestUrl($order, $item), $this->requestData())->assertRedirect();
        $return = $order->returnRequests()->sole();
        $admin = User::factory()->create(['is_admin' => true]);
        $decisionUrl = route('admin.orders.return-decision', [$order->id, $return->id]);
        $receiptUrl = route('admin.orders.return-receive', [$order->id, $return->id]);

        $this->post($decisionUrl, ['decision' => 'authorize', 'decision_note' => 'Return approved for inspection',
            'verified_issue_type' => 'other'])
            ->assertRedirect('/login');
        $this->actingAs($admin)->post($receiptUrl, ['confirm_received' => '1', 'receipt_note' => 'Parcel arrived'])
            ->assertSessionHasErrors('return');
        $this->actingAs($admin)->post($decisionUrl, [
            'decision' => 'authorize', 'decision_note' => 'Return approved for inspection',
            'verified_issue_type' => 'other',
        ])->assertRedirect();
        $this->assertSame('authorized', $return->fresh()->status);
        $this->assertSame('customer', $return->fresh()->return_shipping_payer);
        $this->assertSame($admin->id, $return->fresh()->decided_by_user_id);
        $this->actingAs($admin)->post($decisionUrl, [
            'decision' => 'authorize', 'decision_note' => 'Duplicate action', 'verified_issue_type' => 'other',
        ])->assertSessionHasErrors('return');

        $this->actingAs($admin)->post($receiptUrl, ['receipt_note' => 'Parcel arrived'])
            ->assertSessionHasErrors('confirm_received');
        $this->actingAs($admin)->post($receiptUrl, [
            'confirm_received' => '1', 'receipt_note' => 'Parcel arrived and awaits inspection',
        ])->assertRedirect();
        $this->assertSame('received', $return->fresh()->status);
        $this->assertNotNull($return->fresh()->received_at);
        $this->actingAs($admin)->post($receiptUrl, [
            'confirm_received' => '1', 'receipt_note' => 'Duplicate receipt',
        ])->assertSessionHasErrors('return');

        $this->assertSame(4, $product->fresh()->stock_quantity);
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('delivered', $order->fresh()->status);
        $this->actingAs($admin)->get(route('admin.orders.show', $order))
            ->assertOk()->assertSee('Item Return Requests')->assertSee('Refund and stock review remain separate');
    }

    public function test_admin_verified_reason_controls_return_courier_payer_not_customer_self_report(): void
    {
        [$order, $item] = $this->deliveredOrder(null, 2);
        session(['checkout_order_numbers' => [$order->order_number]]);
        $this->post($this->requestUrl($order, $item), [
            'quantity' => 1, 'reported_issue_type' => 'wrong_item',
            'reason' => 'I received the wrong item',
        ])->assertRedirect();
        $this->post($this->requestUrl($order, $item), [
            'quantity' => 1, 'reported_issue_type' => 'other',
            'reason' => 'The size is not suitable',
        ])->assertRedirect();
        $returns = $order->returnRequests()->orderBy('id')->get();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.orders.return-decision', [$order->id, $returns[0]->id]), [
            'decision' => 'authorize', 'decision_note' => 'Verified this is a fit issue',
            'verified_issue_type' => 'other',
        ])->assertRedirect();
        $this->assertSame('customer', $returns[0]->fresh()->return_shipping_payer);
        $this->post(route('admin.orders.return-decision', [$order->id, $returns[1]->id]), [
            'decision' => 'authorize', 'decision_note' => 'Verified the wrong item was shipped',
            'verified_issue_type' => 'wrong_item',
        ])->assertRedirect();
        $this->assertSame('earthquick', $returns[1]->fresh()->return_shipping_payer);
        $this->get(route('admin.orders.show', $order->id))->assertOk()->assertSee('Return courier payer: Earthquick');
    }
}

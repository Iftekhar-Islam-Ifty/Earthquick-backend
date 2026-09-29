<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Tests\TestCase;

class CancellationWorkflowTest extends TestCase
{
    private function order(?User $customer = null): array
    {
        $product = Product::whereDoesntHave('variants')->firstOrFail();
        $product->update(['stock_quantity' => 4, 'in_stock' => true]);
        $order = Order::create([
            'order_number' => 'EQ-CANCEL-'.uniqid(),
            'user_id' => $customer?->id,
            'customer_name' => 'Cancellation QA',
            'customer_phone' => '01712345678',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'Nasirabad',
            'address' => 'Synthetic address',
            'payment_method' => 'cod',
            'payment_status' => 'due_on_delivery',
            'subtotal' => 1000,
            'delivery_fee' => 80,
            'total' => 1080,
            'status' => 'pending',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 1000,
            'quantity' => 1,
            'total_price' => 1000,
        ]);

        return [$order, $product];
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public function test_real_checkout_confirm_and_cancel_restore_the_original_available_units(): void
    {
        $product = Product::whereDoesntHave('variants')->where('is_active', true)->firstOrFail();
        $product->update(['stock_quantity' => 5, 'in_stock' => true]);
        $key = $product->id.'_standard';
        session(['cart' => [$key => [
            'key' => $key,
            'id' => $product->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (float) $product->price,
            'image' => $product->image,
            'size' => 'Standard',
            'quantity' => 1,
        ]]]);

        $this->post(route('checkout.store'), [
            'customer_name' => 'Stock Lifecycle QA',
            'customer_phone' => '01719998866',
            'customer_email' => 'stock-qa@example.test',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'QA Area',
            'address' => 'Synthetic QA address',
            'payment_method' => 'cod',
        ])->assertRedirectContains('/checkout/success/EQ-');

        $order = Order::where('customer_phone', '01719998866')->sole();
        $this->assertSame(4, $product->fresh()->stock_quantity);

        $stockUrl = route('admin.products.stock-levels', ['ids' => [$product->id]]);
        $this->getJson($stockUrl)->assertUnauthorized();
        $this->actingAs(User::factory()->create())->getJson($stockUrl)->assertForbidden();
        $admin = $this->admin();
        $this->actingAs($admin)->getJson($stockUrl)
            ->assertOk()->assertJsonPath('products.'.$product->id.'.units', 4);
        $this->getJson(route('admin.products.stock-levels', ['ids' => [0]]))->assertUnprocessable();
        $this->get(route('admin.orders.show', $order))->assertOk()->assertSee('Available now: 4 units');

        $this->post(route('admin.orders.update-status', $order), [
            'status' => 'confirmed',
        ])->assertRedirect();
        $this->assertSame(4, $product->fresh()->stock_quantity);
        $this->getJson($stockUrl)->assertOk()->assertJsonPath('products.'.$product->id.'.units', 4);

        $this->post(route('admin.orders.update-status', $order), [
            'status' => 'cancelled',
            'admin_notes' => 'Controlled stock lifecycle test',
        ])->assertRedirect();
        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(5, $product->fresh()->stock_quantity);
        $this->getJson($stockUrl)->assertOk()->assertJsonPath('products.'.$product->id.'.units', 5);
        $this->get(route('admin.orders.show', $order))->assertOk()->assertSee('Available now: 5 units');

        $this->get(route('admin.products'))->assertOk()
            ->assertSee('Available units')
            ->assertSee('Confirm does not change it');
    }

    public function test_guest_request_requires_checkout_session_and_does_not_cancel_immediately(): void
    {
        [$order, $product] = $this->order();
        $url = route('orders.cancellation-request', $order->order_number);
        $this->post($url, ['reason' => 'Please cancel this order'])->assertNotFound();

        session(['checkout_order_numbers' => [$order->order_number]]);
        $this->post($url, ['reason' => 'Please cancel this order'])->assertRedirect();
        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame(4, $product->fresh()->stock_quantity);
        $this->assertSame('pending', $order->cancellationRequests()->sole()->status);
        $this->get(route('checkout.success', $order->order_number))
            ->assertOk()->assertSee('Your request is under review');

        $this->post($url, ['reason' => 'Please cancel this order'])->assertSessionHasErrors('cancellation');
        $this->assertSame(1, $order->cancellationRequests()->count());
    }

    public function test_account_request_requires_exact_order_ownership(): void
    {
        $owner = User::factory()->create();
        [$order] = $this->order($owner);
        $url = route('orders.cancellation-request', $order->order_number);

        $this->actingAs(User::factory()->create())->post($url, ['reason' => 'Please cancel this order'])->assertNotFound();
        $this->actingAs($owner)->post($url, ['reason' => 'Please cancel this order'])->assertRedirect();
        $this->assertSame($owner->id, $order->cancellationRequests()->sole()->requested_by_user_id);
        $this->actingAs($owner)->get(route('account.order', $order->order_number))
            ->assertOk()->assertSee('Your request is under review');
    }

    public function test_admin_approval_restores_stock_coupon_and_history_only_once(): void
    {
        [$order, $product] = $this->order();
        $coupon = Coupon::create([
            'code' => 'CANCELQA', 'type' => 'fixed', 'value' => 100,
            'used_count' => 1, 'is_active' => true,
        ]);
        $order->update(['coupon_code' => $coupon->code, 'coupon_id' => $coupon->id, 'discount_amount' => 100, 'total' => 980]);
        session(['checkout_order_numbers' => [$order->order_number]]);
        $this->post(route('orders.cancellation-request', $order->order_number), ['reason' => 'Please cancel this order']);
        $requestId = $order->cancellationRequests()->sole()->id;
        $url = route('admin.orders.cancellation-decision', [$order->id, $requestId]);

        $this->post($url, ['decision' => 'approve', 'decision_note' => 'Customer requested cancellation'])
            ->assertRedirect('/login');
        $admin = $this->admin();
        $this->actingAs($admin)->post($url, [
            'decision' => 'approve', 'decision_note' => 'Customer requested cancellation',
        ])->assertRedirect(route('admin.orders.show', $order));

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame('not_due', $order->fresh()->payment_status);
        $this->assertSame(5, $product->fresh()->stock_quantity);
        $this->assertSame(0, $coupon->fresh()->used_count);
        $this->assertSame('approved', $order->cancellationRequests()->sole()->status);
        $this->assertSame($admin->id, $order->cancellationRequests()->sole()->decided_by_user_id);
        $this->assertSame('pending', $order->statusEvents()->sole()->from_status);
        $this->assertSame('cancelled', $order->statusEvents()->sole()->to_status);

        $this->actingAs($admin)->post($url, [
            'decision' => 'approve', 'decision_note' => 'Repeat attempt',
        ])->assertSessionHasErrors();
        $this->assertSame(5, $product->fresh()->stock_quantity);
        $this->assertSame(0, $coupon->fresh()->used_count);
    }

    public function test_rejection_leaves_order_and_inventory_untouched(): void
    {
        [$order, $product] = $this->order();
        session(['checkout_order_numbers' => [$order->order_number]]);
        $this->post(route('orders.cancellation-request', $order->order_number), ['reason' => 'Please cancel this order']);
        $requestId = $order->cancellationRequests()->sole()->id;
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.orders.cancellation-decision', [$order->id, $requestId]), [
            'decision' => 'reject', 'decision_note' => 'Already prepared for dispatch',
        ])->assertRedirect();

        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame(4, $product->fresh()->stock_quantity);
        $this->assertSame('rejected', $order->cancellationRequests()->sole()->status);
        $this->assertSame(0, $order->statusEvents()->count());
    }

    public function test_direct_admin_cancellation_uses_same_restock_path(): void
    {
        [$order, $product] = $this->order();
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.orders.update-status', $order), [
            'status' => 'cancelled', 'admin_notes' => 'Customer called to cancel',
        ])->assertRedirect();

        $this->assertSame(5, $product->fresh()->stock_quantity);
        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame('admin', $order->cancellationRequests()->sole()->source);
        $this->actingAs($admin)->post(route('admin.orders.update-status', $order), [
            'status' => 'cancelled', 'admin_notes' => 'Again',
        ])->assertSessionHasErrors('status');
        $this->assertSame(5, $product->fresh()->stock_quantity);
    }

    public function test_variant_stock_is_restored_without_double_counting_parent(): void
    {
        [$order, $product] = $this->order();
        $variant = ProductVariant::create([
            'product_id' => $product->id, 'sku' => 'QA-CANCEL-'.uniqid(),
            'label' => 'Large', 'stock_quantity' => 2, 'is_active' => true,
        ]);
        $product->update(['stock_quantity' => 2]);
        $order->items()->firstOrFail()->update(['variant_id' => $variant->id, 'variant_sku' => $variant->sku]);

        $this->actingAs($this->admin())->post(route('admin.orders.update-status', $order), [
            'status' => 'cancelled', 'admin_notes' => 'Cancelled before processing',
        ])->assertRedirect();

        $this->assertSame(3, $variant->fresh()->stock_quantity);
        $this->assertSame(3, $product->fresh()->stock_quantity);
    }

    public function test_missing_original_variant_rolls_back_cancellation_without_guessing_stock(): void
    {
        [$order, $product] = $this->order();
        $variant = ProductVariant::create([
            'product_id' => $product->id, 'sku' => 'QA-DELETED-'.uniqid(),
            'label' => 'Medium', 'stock_quantity' => 2, 'is_active' => true,
        ]);
        $item = $order->items()->firstOrFail();
        $item->update(['variant_id' => $variant->id, 'variant_sku' => $variant->sku]);
        $variant->delete();

        $this->actingAs($this->admin())->post(route('admin.orders.update-status', $order), [
            'status' => 'cancelled', 'admin_notes' => 'Variant no longer exists',
        ])->assertSessionHasErrors('status');

        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame(4, $product->fresh()->stock_quantity);
        $this->assertSame(0, $order->cancellationRequests()->count());
        $this->assertSame(0, $order->statusEvents()->count());
    }

    public function test_historical_coupon_code_without_original_coupon_id_cannot_decrement_a_reused_code(): void
    {
        [$order, $product] = $this->order();
        $newCoupon = Coupon::create([
            'code' => 'REUSEDQA', 'type' => 'fixed', 'value' => 50,
            'used_count' => 2, 'is_active' => true,
        ]);
        $order->update(['coupon_code' => $newCoupon->code, 'discount_amount' => 50]);

        $this->actingAs($this->admin())->post(route('admin.orders.update-status', $order), [
            'status' => 'cancelled', 'admin_notes' => 'Customer requested cancellation',
        ])->assertSessionHasErrors('status');

        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame(4, $product->fresh()->stock_quantity);
        $this->assertSame(2, $newCoupon->fresh()->used_count);
        $this->assertSame(0, $order->cancellationRequests()->count());
    }

    public function test_paid_or_fulfilled_orders_cannot_use_unpaid_cancellation_path(): void
    {
        [$paid, $product] = $this->order();
        $paid->update(['payment_status' => 'paid', 'paid_at' => now()]);
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.orders.update-status', $paid), [
            'status' => 'cancelled', 'admin_notes' => 'Must not auto-refund',
        ])->assertSessionHasErrors('status');
        $this->assertSame(4, $product->fresh()->stock_quantity);

        [$processing] = $this->order();
        $processing->update(['status' => 'processing']);
        $this->actingAs($admin)->post(route('admin.orders.update-status', $processing), [
            'status' => 'pending',
        ])->assertSessionHasErrors('status');
        $this->actingAs($admin)->post(route('admin.orders.update-status', $processing), [
            'status' => 'cancelled', 'admin_notes' => 'Too late',
        ])->assertSessionHasErrors('status');
    }
}

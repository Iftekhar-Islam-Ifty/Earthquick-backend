<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class OrderStatusHistoryTest extends TestCase
{
    private function order(): Order
    {
        return Order::create([
            'order_number' => 'EQ-HISTORY-'.uniqid(),
            'customer_name' => 'History QA Customer',
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
    }

    public function test_new_checkout_records_initial_pending_status(): void
    {
        $product = Product::where('in_stock', true)->where('stock_quantity', '>', 0)->firstOrFail();
        session(['cart' => [
            $product->id.'_standard' => [
                'id' => $product->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'image' => $product->image,
                'size' => 'Standard',
                'quantity' => 1,
            ],
        ]]);

        $this->post('/checkout/order', [
            'customer_name' => 'History QA Customer',
            'customer_phone' => '01712345678',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'Nasirabad',
            'address' => 'Synthetic address',
            'payment_method' => 'cod',
        ])->assertRedirect();

        $event = Order::sole()->statusEvents()->sole();
        $this->assertSame('checkout', $event->source);
        $this->assertNull($event->from_status);
        $this->assertSame('pending', $event->to_status);
        $this->assertNull($event->actor_user_id);
    }

    public function test_admin_change_creates_audited_event_but_same_status_edit_does_not(): void
    {
        $order = $this->order();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.orders.update-status', $order), [
            'status' => 'confirmed',
            'admin_notes' => 'Verified with warehouse',
        ])->assertRedirect();

        $event = $order->statusEvents()->sole();
        $this->assertSame('pending', $event->from_status);
        $this->assertSame('confirmed', $event->to_status);
        $this->assertSame($admin->id, $event->actor_user_id);
        $this->assertSame('Verified with warehouse', $event->note);

        $this->actingAs($admin)->post(route('admin.orders.update-status', $order), [
            'status' => 'confirmed',
            'tracking_number' => 'QA-123',
        ])->assertRedirect();
        $this->assertSame(1, $order->statusEvents()->count());

        $this->actingAs($admin)->get(route('admin.orders.show', $order))
            ->assertOk()->assertSee('Order Status History')->assertSee('Verified with warehouse');
    }

    public function test_rejected_status_change_does_not_write_history(): void
    {
        $order = $this->order();
        $order->update(['status' => 'delivered', 'payment_status' => 'paid', 'paid_at' => now()]);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.orders.update-status', $order), [
            'status' => 'cancelled',
        ])->assertSessionHasErrors('status');

        $this->assertSame('delivered', $order->fresh()->status);
        $this->assertSame(0, $order->statusEvents()->count());
    }

    public function test_old_order_is_not_given_a_fabricated_history(): void
    {
        $order = $this->order();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.orders.show', $order))
            ->assertOk()->assertSee('Its earlier changes cannot be reconstructed');
    }
}

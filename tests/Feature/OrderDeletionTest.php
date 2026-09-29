<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderDeletionTest extends TestCase
{
    private function cancelledTestOrder(): array
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
            'customer_name' => 'Synthetic QA Customer',
            'customer_phone' => '01719998865',
            'customer_email' => 'deletion-qa@example.test',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'QA Area',
            'address' => 'Synthetic QA address',
            'order_notes' => 'EARTHQUICK QA TEST - do not dispatch',
            'payment_method' => 'cod',
        ])->assertRedirectContains('/checkout/success/EQ-');

        $order = Order::where('customer_phone', '01719998865')->sole();
        $this->assertSame(4, $product->fresh()->stock_quantity);
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.orders.update-status', $order), [
            'status' => 'cancelled',
            'admin_notes' => 'EARTHQUICK QA TEST - controlled cancellation',
        ])->assertRedirect();
        $this->assertSame(5, $product->fresh()->stock_quantity);

        return [$order->fresh(), $product, $admin];
    }

    private function deletionData(Order $order): array
    {
        return [
            'confirm_order_number' => $order->order_number,
            'deletion_reason' => 'Controlled test order cleanup after verification',
            'confirm_permanent' => '1',
        ];
    }

    public function test_only_admin_can_permanently_delete_a_cancelled_unpaid_marked_test_order(): void
    {
        [$order, $product, $admin] = $this->cancelledTestOrder();
        $url = route('admin.orders.delete-test', $order);
        $this->get(route('admin.orders.show', $order))->assertOk()
            ->assertSee('Permanently delete test order');

        auth()->logout();
        $this->delete($url, $this->deletionData($order))->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->delete($url, $this->deletionData($order))->assertForbidden();
        $this->actingAs($admin)->delete($url, array_replace($this->deletionData($order), [
            'confirm_order_number' => 'WRONG-NUMBER',
        ]))->assertSessionHasErrors('confirm_order_number');
        $this->assertDatabaseHas('orders', ['id' => $order->id]);

        $this->delete($url, $this->deletionData($order))
            ->assertRedirect(route('admin.orders', ['status' => 'cancelled']));
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('order_items', ['order_id' => $order->id]);
        $this->assertDatabaseMissing('order_status_events', ['order_id' => $order->id]);
        $this->assertDatabaseMissing('order_cancellation_requests', ['order_id' => $order->id]);
        $this->assertDatabaseHas('order_deletion_audits', [
            'original_order_id' => $order->id,
            'order_number' => $order->order_number,
            'deleted_by_user_id' => $admin->id,
        ]);
        $this->assertSame(5, $product->fresh()->stock_quantity);
        $this->delete($url, $this->deletionData($order))->assertNotFound();
        $this->assertSame(1, DB::table('order_deletion_audits')->count());
    }

    public function test_real_or_paid_order_cannot_use_the_test_deletion_path(): void
    {
        [$order] = $this->cancelledTestOrder();
        $url = route('admin.orders.delete-test', $order);

        $order->update(['order_notes' => 'Real customer order']);
        $this->get(route('admin.orders.show', $order))->assertOk()
            ->assertDontSee('Permanently delete test order');
        $this->delete($url, $this->deletionData($order))->assertSessionHasErrors('delete');

        $order->update(['order_notes' => 'EARTHQUICK QA TEST', 'admin_notes' => 'Routine cancellation']);
        $this->delete($url, $this->deletionData($order))->assertSessionHasErrors('delete');

        $order->update(['admin_notes' => 'EARTHQUICK QA TEST - controlled cancellation']);
        $order->update(['order_notes' => 'EARTHQUICK QA TEST', 'payment_status' => 'paid', 'paid_at' => now()]);
        $this->delete($url, $this->deletionData($order))->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
        $this->assertSame(0, DB::table('order_deletion_audits')->count());
    }
}

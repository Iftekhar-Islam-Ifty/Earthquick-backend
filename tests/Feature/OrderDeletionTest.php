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

    public function test_cancelled_unpaid_order_can_be_trashed_and_restored_without_stock_changes(): void
    {
        [$order, $product, $admin] = $this->cancelledTestOrder();
        $order->update(['order_notes' => 'Ordinary cancelled customer order', 'admin_notes' => 'Routine cancellation']);
        $url = route('admin.orders.trash', $order);
        $this->get(route('admin.orders.show', $order))->assertOk()
            ->assertSee('Move to Trash');

        auth()->logout();
        $this->delete($url, $this->deletionData($order))->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->delete($url, $this->deletionData($order))->assertForbidden();
        $this->actingAs($admin)->delete($url, array_replace($this->deletionData($order), [
            'confirm_order_number' => 'WRONG-NUMBER',
        ]))->assertSessionHasErrors('confirm_order_number');
        $this->assertDatabaseHas('orders', ['id' => $order->id]);

        $this->delete($url, $this->deletionData($order))
            ->assertRedirect(route('admin.orders', ['folder' => 'trash']));
        $this->assertSoftDeleted('orders', ['id' => $order->id]);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id]);
        $this->assertDatabaseHas('order_status_events', ['order_id' => $order->id]);
        $this->get(route('admin.orders', ['folder' => 'trash']))->assertOk()->assertSee($order->order_number);
        $this->get(route('admin.orders'))->assertOk()->assertDontSee($order->order_number);
        $this->get(route('admin.orders.show', $order))->assertOk()->assertSee('Restore order')->assertDontSee('Permanently delete');
        $this->delete(route('admin.orders.purge', $order), $this->deletionData($order))->assertSessionHasErrors('delete');
        $this->assertSame(5, $product->fresh()->stock_quantity);
        $this->post(route('admin.orders.restore', $order))->assertRedirect(route('admin.orders.show', $order));
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'deleted_at' => null]);
        $this->assertSame(5, $product->fresh()->stock_quantity);
    }

    public function test_archive_and_restore_keep_order_in_database_and_separate_folder(): void
    {
        [$order] = $this->cancelledTestOrder();
        $this->post(route('admin.orders.archive', $order))->assertRedirect(route('admin.orders', ['folder' => 'archived']));
        $this->get(route('admin.orders'))->assertOk()->assertDontSee($order->order_number);
        $this->get(route('admin.orders', ['folder' => 'archived']))->assertOk()->assertSee($order->order_number);
        $this->get(route('admin.orders.show', $order))->assertOk()->assertSee('Restore to active orders');
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'deleted_at' => null]);
        $this->post(route('admin.orders.unarchive', $order))->assertRedirect(route('admin.orders.show', $order));
        $this->get(route('admin.orders'))->assertOk()->assertSee($order->order_number);
    }

    public function test_restoring_a_trashed_archived_order_returns_it_to_archive(): void
    {
        [$order] = $this->cancelledTestOrder();
        $this->post(route('admin.orders.archive', $order))->assertRedirect();
        $this->delete(route('admin.orders.trash', $order), $this->deletionData($order))->assertRedirect();
        $this->post(route('admin.orders.restore', $order))->assertRedirect();
        $this->assertNotNull($order->fresh()->archived_at);
        $this->get(route('admin.orders', ['folder' => 'archived']))->assertSee($order->order_number);
        $this->get(route('admin.orders'))->assertDontSee($order->order_number);
    }

    public function test_delivered_paid_order_may_be_archived_but_not_trashed(): void
    {
        [$order] = $this->cancelledTestOrder();
        $order->update(['status' => 'delivered', 'payment_status' => 'paid', 'paid_at' => now()]);
        $this->post(route('admin.orders.archive', $order))->assertRedirect(route('admin.orders', ['folder' => 'archived']));
        $this->delete(route('admin.orders.trash', $order), $this->deletionData($order))->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'deleted_at' => null]);
    }

    public function test_purge_requires_30_days_and_then_removes_children_but_leaves_audit(): void
    {
        [$order, $product, $admin] = $this->cancelledTestOrder();
        $this->delete(route('admin.orders.trash', $order), $this->deletionData($order))->assertRedirect();
        Order::withTrashed()->findOrFail($order->id)->forceFill(['deleted_at' => now()->subDays(31)])->save();
        $this->get(route('admin.orders.show', $order))->assertOk()->assertSee('Permanently delete');
        $this->delete(route('admin.orders.purge', $order), $this->deletionData($order))
            ->assertRedirect(route('admin.orders', ['folder' => 'trash']));
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('order_items', ['order_id' => $order->id]);
        $this->assertDatabaseMissing('order_status_events', ['order_id' => $order->id]);
        $this->assertDatabaseHas('order_deletion_audits', ['original_order_id' => $order->id, 'deleted_by_user_id' => $admin->id]);
        $this->assertSame(5, $product->fresh()->stock_quantity);
    }

    public function test_paid_order_cannot_be_trashed_or_purged(): void
    {
        [$order] = $this->cancelledTestOrder();
        $order->update(['payment_status' => 'paid', 'paid_at' => now()]);
        $this->get(route('admin.orders.show', $order))->assertOk()->assertDontSee('Move to Trash');
        $this->delete(route('admin.orders.trash', $order), $this->deletionData($order))->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
        $this->assertSame(0, DB::table('order_deletion_audits')->count());
    }
}

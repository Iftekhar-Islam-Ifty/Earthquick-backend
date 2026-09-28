<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Tests\TestCase;

class CodCollectionTest extends TestCase
{
    private function order(string $status = 'delivered', string $method = 'cod'): Order
    {
        return Order::create([
            'order_number' => 'EQ-COD-'.uniqid(),
            'customer_name' => 'COD QA Customer',
            'customer_phone' => '01712345678',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'Nasirabad',
            'address' => 'Synthetic address',
            'payment_method' => $method,
            'payment_status' => 'due_on_delivery',
            'subtotal' => 1000,
            'delivery_fee' => 80,
            'total' => 1080,
            'status' => $status,
        ]);
    }

    private function admin(bool $isAdmin = true): User
    {
        return User::factory()->create(['is_admin' => $isAdmin]);
    }

    private function paidData(string $channel = 'in_house'): array
    {
        return [
            'cod_collection_channel' => $channel,
            'cod_collection_note' => 'Receipt QA-123',
            'confirm_collected' => '1',
        ];
    }

    public function test_only_admin_can_record_cod_collection(): void
    {
        $order = $this->order();
        $url = route('admin.orders.cod-paid', $order);

        $this->post($url, $this->paidData())->assertRedirect('/login');
        $this->actingAs($this->admin(false))->post($url, $this->paidData())->assertForbidden();
        $this->assertSame('due_on_delivery', $order->fresh()->payment_status);
    }

    public function test_delivered_cod_requires_explicit_confirmation_then_records_admin_and_source(): void
    {
        $order = $this->order();
        $admin = $this->admin();
        $url = route('admin.orders.cod-paid', $order);

        $this->actingAs($admin)->from(route('admin.orders.show', $order))
            ->post($url, ['cod_collection_channel' => 'in_house'])
            ->assertSessionHasErrors('confirm_collected');
        $this->assertSame('due_on_delivery', $order->fresh()->payment_status);

        $this->actingAs($admin)->post($url, $this->paidData('courier_remittance'))
            ->assertRedirect(route('admin.orders.show', $order));

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame($admin->id, $order->paid_recorded_by);
        $this->assertSame('courier_remittance', $order->cod_collection_channel);
        $this->assertSame('Receipt QA-123', $order->cod_collection_note);
        $this->actingAs($admin)->get(route('admin.orders.show', $order))
            ->assertOk()->assertSee('Courier remittance')->assertSee('Receipt QA-123');

        $paidAt = $order->paid_at;
        $this->actingAs($admin)->from(route('admin.orders.show', $order))
            ->post($url, $this->paidData())->assertSessionHasErrors('payment');
        $this->assertEquals($paidAt, $order->fresh()->paid_at);
    }

    public function test_delivery_alone_does_not_mark_paid_and_non_delivered_or_non_cod_cannot_be_marked_paid(): void
    {
        $admin = $this->admin();
        $pending = $this->order('pending');
        $this->actingAs($admin)->post(route('admin.orders.cod-paid', $pending), $this->paidData())
            ->assertSessionHasErrors('payment');

        $this->actingAs($admin)->post(route('admin.orders.update-status', $pending), ['status' => 'delivered'])
            ->assertRedirect();
        $this->assertSame('due_on_delivery', $pending->fresh()->payment_status);

        $other = $this->order('delivered', 'bkash');
        $this->actingAs($admin)->post(route('admin.orders.cod-paid', $other), $this->paidData())
            ->assertSessionHasErrors('payment');
    }

    public function test_paid_order_cannot_be_cancelled_without_refund_resolution(): void
    {
        $order = $this->order();
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.orders.cod-paid', $order), $this->paidData());

        $this->actingAs($admin)->post(route('admin.orders.update-status', $order), ['status' => 'cancelled'])
            ->assertSessionHasErrors('status');
        $this->assertSame('delivered', $order->fresh()->status);
    }

    public function test_about_page_describes_only_currently_available_payment(): void
    {
        $this->get('/about')->assertOk()
            ->assertSee('bKash online payment is coming soon')
            ->assertDontSee('Visa, Mastercard, Amex')
            ->assertDontSee('bKash, Nagad, Rocket');
    }
}

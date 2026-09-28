<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Tests\TestCase;

class PaymentGatewayFoundationTest extends TestCase
{
    private function checkoutData(string $method): array
    {
        return [
            'customer_name' => 'Gateway QA Customer',
            'customer_phone' => '01712345678',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'Nasirabad',
            'address' => 'Synthetic test address',
            'payment_method' => $method,
        ];
    }

    private function seedCart(): Product
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

        return $product;
    }

    public function test_checkout_shows_no_manual_payment_number_and_disables_bkash_until_gateway_is_ready(): void
    {
        $this->seedCart();

        $this->get('/checkout')
            ->assertOk()
            ->assertSee('bKash Online Payment')
            ->assertSee('Coming Soon')
            ->assertDontSee('01876-543210')
            ->assertDontSee('Nagad / Rocket');
    }

    public function test_unavailable_bkash_cannot_create_an_order_or_reserve_stock(): void
    {
        $product = $this->seedCart();
        $originalStock = $product->stock_quantity;

        $this->from('/checkout')->post('/checkout/order', $this->checkoutData('bkash'))
            ->assertRedirect('/checkout')
            ->assertSessionHasErrors('payment_method');

        $this->assertSame(0, Order::count());
        $this->assertSame($originalStock, $product->fresh()->stock_quantity);
        $this->assertNotEmpty(session('cart'));
    }

    public function test_unapproved_card_method_is_rejected(): void
    {
        $this->seedCart();

        $this->from('/checkout')->post('/checkout/order', $this->checkoutData('card'))
            ->assertRedirect('/checkout')
            ->assertSessionHasErrors('payment_method');

        $this->assertSame(0, Order::count());
    }

    public function test_cod_orders_record_that_payment_is_due_on_delivery(): void
    {
        $this->seedCart();

        $this->post('/checkout/order', $this->checkoutData('cod'))
            ->assertRedirect();

        $order = Order::sole();
        $this->assertSame('cod', $order->payment_method);
        $this->assertSame('due_on_delivery', $order->payment_status);
        $this->assertNull($order->paid_at);
        $this->assertNull($order->payment_reference);
    }
}

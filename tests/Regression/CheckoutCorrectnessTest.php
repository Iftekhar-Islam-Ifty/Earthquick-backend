<?php

namespace Tests\Regression;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CheckoutCorrectnessTest extends TestCase
{
    use DatabaseTransactions;

    private function payload(): array
    {
        return ['customer_name' => 'QA Synthetic Customer', 'customer_phone' => '01712345678', 'customer_email' => 'qa-synthetic@example.test', 'delivery_zone' => 'inside_ctg', 'district' => 'Chattogram', 'area' => 'QA Area', 'address' => 'Synthetic test address', 'payment_method' => 'cod'];
    }

    private function cart(Product $p): array
    {
        return [$p->id.'_standard' => ['id' => $p->id, 'product_id' => $p->id, 'name' => $p->name, 'price' => (float) $p->price, 'quantity' => 1, 'size' => 'Standard', 'image' => $p->image, 'vendor_id' => $p->vendor_id]];
    }

    private function place(Product $p): Order
    {
        $this->withSession(['cart' => $this->cart($p)])->post('/checkout/order', $this->payload())->assertRedirect();

        return Order::latest('id')->firstOrFail();
    }

    public function test_out_of_stock_product_cannot_be_added(): void
    {
        $p = Product::firstOrFail();
        $p->update(['in_stock' => false, 'stock_quantity' => 0]);
        $this->postJson('/cart/add', ['product_id' => $p->id, 'quantity' => 1])->assertUnprocessable();
    }

    public function test_cart_rejects_quantity_above_inventory(): void
    {
        $p = Product::firstOrFail();
        $p->update(['in_stock' => true, 'stock_quantity' => 1]);
        $this->postJson('/cart/add', ['product_id' => $p->id, 'quantity' => 20])->assertUnprocessable();
    }

    public function test_checkout_uses_current_product_price(): void
    {
        $p = Product::firstOrFail();
        $cart = $this->cart($p);
        $p->update(['price' => 12345]);
        $this->withSession(['cart' => $cart])->post('/checkout/order', $this->payload())->assertRedirect();
        $this->assertEquals(12345, Order::latest('id')->firstOrFail()->subtotal);
    }

    public function test_checkout_decrements_inventory(): void
    {
        $p = Product::firstOrFail();
        $p->update(['stock_quantity' => 7]);
        $this->place($p);
        $this->assertSame(6, $p->fresh()->stock_quantity);
    }

    public function test_checkout_rechecks_vendor_status(): void
    {
        $p = Product::whereNotNull('vendor_id')->firstOrFail();
        $cart = $this->cart($p);
        $p->vendor->update(['is_active' => false]);
        $this->withSession(['cart' => $cart])->post('/checkout/order', $this->payload());
        $this->assertSame(0, Order::count());
    }

    public function test_advertised_free_delivery_matches_checkout(): void
    {
        $p = Product::firstOrFail();
        $p->update(['price' => 5000]);
        $this->withSession(['cart' => $this->cart($p)])->getJson('/cart')->assertJsonPath('free_shipping_unlocked', true);
        $o = $this->place($p);
        $this->assertEquals(0, $o->delivery_fee);
    }

    public function test_confirmation_is_not_public_to_another_session(): void
    {
        $o = $this->place(Product::firstOrFail());
        $this->flushSession();
        $this->get('/checkout/success/'.$o->order_number)->assertNotFound();
    }

    public function test_unverified_matching_contact_does_not_grant_order_access(): void
    {
        $o = $this->place(Product::firstOrFail());
        $u = User::create(['name' => 'QA Other Customer', 'email' => $o->customer_email, 'phone' => '01812345678', 'password' => 'qa-test-password']);
        $this->actingAs($u)->get('/account/order/'.$o->order_number)->assertNotFound();
    }
}

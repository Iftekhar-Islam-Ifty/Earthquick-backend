<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use Tests\TestCase;

class MixedVendorCheckoutTest extends TestCase
{
    public function test_one_order_preserves_each_products_vendor_and_totals(): void
    {
        $first = Product::firstOrFail();
        $second = $first->replicate();
        $second->fill([
            'name' => 'Test Electronics', 'slug' => 'test-electronics', 'sku' => 'BRT-QA-001',
            'price' => 1500, 'vendor_id' => Vendor::where('slug', 'bright')->firstOrFail()->id,
        ])->save();

        $cart = [];
        foreach ([$first, $second] as $product) {
            $cart[$product->id.'_standard'] = [
                'id' => $product->id, 'product_id' => $product->id, 'vendor_id' => $product->vendor_id,
                'name' => $product->name, 'slug' => $product->slug, 'image' => $product->image,
                'price' => $product->price, 'quantity' => 1, 'size' => 'Standard',
            ];
        }

        $this->withSession(['cart' => $cart])->post('/checkout/order', [
            'customer_name' => 'Synthetic Customer', 'customer_phone' => '01712345678',
            'delivery_zone' => 'inside_ctg', 'district' => 'Chattogram', 'area' => 'Test area',
            'address' => 'Test address', 'payment_method' => 'cod',
        ])->assertRedirect();

        $this->assertSame(1, Order::count());
        $order = Order::with('items')->firstOrFail();
        $this->assertCount(2, $order->items);
        foreach ([$first, $second] as $product) {
            $item = $order->items->firstWhere('product_id', $product->id);
            $this->assertNotNull($item);
            $this->assertSame($product->vendor_id, $item->vendor_id);
            $this->assertEquals($product->price, $item->total_price);
        }
        $this->assertEquals($first->price + $second->price, $order->subtotal);
        $this->assertEquals($order->subtotal - $order->discount_amount + $order->delivery_fee, $order->total);
    }
}

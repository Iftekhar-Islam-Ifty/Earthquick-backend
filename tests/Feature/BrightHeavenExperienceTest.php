<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BrightHeavenExperienceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_heaven_product_identity_and_bright_warranty_provider_survive_checkout(): void
    {
        $bright = Vendor::where('slug', 'bright')->firstOrFail();
        $brand = ProductBrand::create([
            'vendor_id' => $bright->id,
            'warranty_provider_vendor_id' => $bright->id,
            'name' => 'Heaven',
            'slug' => 'heaven',
            'is_active' => true,
        ]);
        $product = Product::firstOrFail();
        $product->update([
            'vendor_id' => $bright->id,
            'brand_id' => $brand->id,
            'category_id' => Category::where('slug', 'electronics')->value('id'),
            'name' => 'Heaven Test Switch',
            'slug' => 'heaven-test-switch',
            'product_type' => 'electronics',
            'specifications' => ['Gang' => '2'],
            'warranty_info' => null,
        ]);

        $this->get(route('product.show', $product->slug))
            ->assertOk()
            ->assertSee('Store:</strong> Bright', false)
            ->assertSee('Brand:</strong> Heaven', false)
            ->assertSee('Warranty provider:</strong> Bright', false);

        $this->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk()->assertJson(['success' => true]);

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Brand: Heaven', false)
            ->assertSee('Sold by: Bright', false)
            ->assertSee('Warranty provider: Bright', false);

        $this->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('Brand: Heaven', false)
            ->assertSee('Sold by: Bright', false)
            ->assertSee('Warranty provider: Bright', false);

        $this->post(route('checkout.store'), [
            'customer_name' => 'Phase Four QA',
            'customer_phone' => '01712345678',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'Test area',
            'address' => 'Test address',
            'payment_method' => 'cod',
        ])->assertRedirect();

        $order = Order::with('items')->firstOrFail();
        $item = $order->items->firstOrFail();
        $this->assertSame('Bright', $item->vendor_name);
        $this->assertSame('Heaven', $item->product_brand_name);
        $this->assertSame('Bright', $item->warranty_provider_name);

        $this->get(route('checkout.success', $order->order_number))
            ->assertOk()
            ->assertSee('Brand: Heaven', false)
            ->assertSee('Sold by: Bright', false)
            ->assertSee('Warranty provider: Bright', false);
    }
}

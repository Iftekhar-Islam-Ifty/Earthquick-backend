<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductVariantTest extends TestCase
{
    use DatabaseTransactions;

    public function test_variant_product_requires_a_selected_active_option(): void
    {
        [$product, $variant] = $this->makeVariantProduct();

        $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
            'size' => 'Standard',
        ])->assertStatus(422)
            ->assertJsonPath('success', false);

        $response = $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('items.0.variant_id', $variant->id)
            ->assertJsonPath('items.0.variant_sku', $variant->sku)
            ->assertJsonPath('items.0.size', 'Crimson / M')
            ->assertJsonPath('items.0.price', 1250);
    }

    public function test_variant_stock_is_enforced_independently(): void
    {
        [$product, $variant] = $this->makeVariantProduct();

        $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 4,
        ])->assertStatus(422);

        $this->assertFalse(session()->has('cart'));
    }

    public function test_checkout_decrements_selected_variant_and_snapshots_it(): void
    {
        [$product, $variant, $otherVariant] = $this->makeVariantProduct();

        $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 2,
        ])->assertOk();

        $this->post('/checkout/order', [
            'customer_name' => 'Variant Checkout Customer',
            'customer_phone' => '01719998877',
            'customer_email' => 'variant@example.com',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'GEC',
            'address' => 'Variant test delivery address',
            'payment_method' => 'cod',
        ])->assertRedirectContains('/checkout/success/EQ-');

        $variant->refresh();
        $otherVariant->refresh();
        $product->refresh();

        $this->assertSame(1, $variant->stock_quantity);
        $this->assertSame(5, $otherVariant->stock_quantity);
        $this->assertSame(6, $product->stock_quantity);

        $order = Order::where('customer_phone', '01719998877')->latest()->firstOrFail();
        $item = OrderItem::where('order_id', $order->id)->firstOrFail();

        $this->assertSame($variant->id, $item->variant_id);
        $this->assertSame($variant->sku, $item->variant_sku);
        $this->assertSame('Crimson / M', $item->variant_label);
        $this->assertSame(['Color' => 'Crimson', 'Size' => 'M'], $item->variant_attributes);
        $this->assertSame(1250.0, $item->unit_price);
    }

    public function test_admin_can_replace_variants_and_product_summary_stock_is_synced(): void
    {
        [$product] = $this->makeVariantProduct();
        $admin = User::create([
            'name' => 'Variant Catalog Admin',
            'email' => 'variant-admin'.random_int(1000, 9999).'@earthquick.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $variants = [
            [
                'sku' => 'VAR-'.strtoupper(substr(md5((string) microtime(true)), 0, 8)).'-S',
                'label' => 'Blue / S',
                'attributes' => ['Color' => 'Blue', 'Size' => 'S'],
                'price' => null,
                'stock_quantity' => 4,
                'is_active' => true,
            ],
            [
                'sku' => 'VAR-'.strtoupper(substr(md5((string) microtime(true)), 0, 8)).'-L',
                'label' => 'Blue / L',
                'attributes' => ['Color' => 'Blue', 'Size' => 'L'],
                'price' => 1400,
                'stock_quantity' => 2,
                'is_active' => true,
            ],
        ];

        $this->actingAs($admin)->put("/admin/products/{$product->id}", [
            'name' => $product->name,
            'category_id' => $product->category_id,
            'product_type' => 'apparel',
            'price' => $product->price,
            'stock_quantity' => $product->stock_quantity,
            'in_stock' => 1,
            'is_active' => 1,
            'variants' => json_encode($variants),
        ])->assertRedirect(route('admin.products'));

        $product->refresh();
        $this->assertSame(2, $product->variants()->count());
        $this->assertSame(6, $product->stock_quantity);
        $this->assertTrue($product->in_stock);
        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->id,
            'label' => 'Blue / L',
            'stock_quantity' => 2,
        ]);
    }

    /**
     * @return array{Product, ProductVariant, ProductVariant}
     */
    private function makeVariantProduct(): array
    {
        $category = Category::firstOrFail();
        $vendor = Vendor::where('is_active', true)->first();
        $token = strtoupper(substr(md5((string) microtime(true).random_int(1, 99999)), 0, 10));

        $product = Product::create([
            'vendor_id' => $vendor?->id,
            'category_id' => $category->id,
            'product_type' => 'apparel',
            'sku' => "VAR-PRODUCT-{$token}",
            'name' => "Variant Test Product {$token}",
            'slug' => strtolower("variant-test-product-{$token}"),
            'price' => 1000,
            'image' => 'images/products/test.jpg',
            'stock_quantity' => 8,
            'in_stock' => true,
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => "VAR-{$token}-M",
            'label' => 'Crimson / M',
            'attributes' => ['Color' => 'Crimson', 'Size' => 'M'],
            'price' => 1250,
            'stock_quantity' => 3,
            'is_active' => true,
        ]);

        $otherVariant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => "VAR-{$token}-L",
            'label' => 'Crimson / L',
            'attributes' => ['Color' => 'Crimson', 'Size' => 'L'],
            'price' => null,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        return [$product, $variant, $otherVariant];
    }
}

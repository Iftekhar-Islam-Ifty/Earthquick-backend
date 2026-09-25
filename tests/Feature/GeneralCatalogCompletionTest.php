<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GeneralCatalogCompletionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_category_and_search_support_general_catalog_filters(): void
    {
        $category = $this->makeCategory();
        $token = strtoupper(substr(md5((string) microtime(true)), 0, 8));

        $matching = $this->makeProduct($category, [
            'name' => "CatalogFilter {$token} Camera",
            'slug' => strtolower("catalog-filter-{$token}-camera"),
            'sku' => "FILTER-{$token}-A",
            'product_type' => 'electronics',
            'delivery_class' => 'fragile',
            'is_returnable' => true,
            'price' => 5500,
        ]);
        $other = $this->makeProduct($category, [
            'name' => "CatalogFilter {$token} Shirt",
            'slug' => strtolower("catalog-filter-{$token}-shirt"),
            'sku' => "FILTER-{$token}-B",
            'product_type' => 'apparel',
            'delivery_class' => 'standard',
            'is_returnable' => false,
            'price' => 1500,
        ]);

        $query = http_build_query([
            'product_type' => 'electronics',
            'delivery_class' => 'fragile',
            'returnable' => '1',
            'min_price' => 5000,
        ]);

        $this->get(route('category.show', $category->slug).'?'.$query)
            ->assertOk()
            ->assertSee($matching->name)
            ->assertDontSee($other->name)
            ->assertSee('Fragile — Special Handling');

        $this->get(route('search').'?'.http_build_query([
            'q' => "CatalogFilter {$token}",
            'product_type' => 'electronics',
            'delivery_class' => 'fragile',
            'returnable' => '1',
        ]))->assertOk()
            ->assertSee($matching->name)
            ->assertDontSee($other->name);
    }

    public function test_product_page_renders_role_based_media_and_operational_policy(): void
    {
        $category = $this->makeCategory();
        $product = $this->makeProduct($category, [
            'product_type' => 'electronics',
            'delivery_class' => 'fragile',
            'is_returnable' => true,
            'return_window_days' => 14,
            'return_policy_note' => 'Original packaging required',
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'images/products/detail-test.jpg',
            'role' => 'detail',
            'alt_text' => 'Close-up product controls',
            'sort_order' => 1,
        ]);
        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'images/products/size-chart-test.jpg',
            'role' => 'size_chart',
            'alt_text' => 'Product measurement chart',
            'sort_order' => 2,
        ]);

        $this->get(route('product.show', $product->slug))
            ->assertOk()
            ->assertSee('data-role="detail"', false)
            ->assertSee('Close-up product controls')
            ->assertSee('Product measurement chart')
            ->assertSee('Fragile — Special Handling')
            ->assertSee('14 days')
            ->assertSee('Original packaging required');
    }

    public function test_admin_can_upload_role_based_product_media(): void
    {
        $admin = User::create([
            'name' => 'Media Catalog Admin',
            'email' => 'media-admin'.random_int(1000, 9999).'@earthquick.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);
        $category = $this->makeCategory();
        $token = strtoupper(substr(md5((string) microtime(true)), 0, 8));
        $jpegBytes = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');

        $response = $this->actingAs($admin)->post('/admin/products', [
            'name' => "Media Upload {$token}",
            'category_id' => $category->id,
            'product_type' => 'electronics',
            'price' => 3200,
            'stock_quantity' => 4,
            'in_stock' => 1,
            'is_active' => 1,
            'delivery_class' => 'fragile',
            'is_returnable' => 1,
            'return_window_days' => 10,
            'return_policy_note' => 'Sealed packaging required',
            'image' => UploadedFile::fake()->createWithContent('master.jpg', $jpegBytes),
            'gallery_images' => [
                UploadedFile::fake()->createWithContent('detail.jpg', $jpegBytes),
            ],
            'gallery_role' => 'detail',
            'gallery_alt_text' => 'Detailed connection ports',
        ]);

        $response->assertRedirect(route('admin.products'));
        $product = Product::where('name', "Media Upload {$token}")->firstOrFail();
        $media = $product->images()->firstOrFail();

        $this->assertSame('fragile', $product->delivery_class);
        $this->assertTrue($product->is_returnable);
        $this->assertSame(10, $product->return_window_days);
        $this->assertSame('detail', $media->role);
        $this->assertSame('Detailed connection ports', $media->alt_text);
        $this->assertFileExists(public_path($media->image_path));

        foreach ([$product->image, $media->image_path] as $path) {
            if ($path && file_exists(public_path($path))) {
                @unlink(public_path($path));
            }
        }
    }

    public function test_checkout_snapshots_delivery_and_return_policy(): void
    {
        $category = $this->makeCategory();
        $product = $this->makeProduct($category, [
            'delivery_class' => 'oversized',
            'is_returnable' => false,
            'return_window_days' => null,
            'return_policy_note' => 'Final sale due to item dimensions',
        ]);
        $key = $product->id.'_standard';

        $response = $this->withSession(['cart' => [
            $key => [
                'key' => $key,
                'id' => $product->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'image' => $product->image,
                'size' => 'Standard',
                'quantity' => 1,
            ],
        ]])->post('/checkout/order', [
            'customer_name' => 'Policy Snapshot Customer',
            'customer_phone' => '01718887766',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'GEC',
            'address' => 'Policy snapshot test address',
            'payment_method' => 'cod',
        ]);

        $response->assertRedirectContains('/checkout/success/EQ-');
        $order = Order::where('customer_phone', '01718887766')->latest()->firstOrFail();
        $item = $order->items()->firstOrFail();

        $this->assertSame('oversized', $item->delivery_class);
        $this->assertFalse($item->is_returnable);
        $this->assertNull($item->return_window_days);
        $this->assertSame('Final sale due to item dimensions', $item->return_policy_note);
    }

    private function makeCategory(): Category
    {
        $token = substr(md5((string) microtime(true).random_int(1, 99999)), 0, 8);

        return Category::create([
            'name' => "General Catalog {$token}",
            'slug' => "general-catalog-{$token}",
            'is_active' => true,
            'sort_order' => 99,
        ]);
    }

    /** @param array<string, mixed> $overrides */
    private function makeProduct(Category $category, array $overrides = []): Product
    {
        $token = strtoupper(substr(md5((string) microtime(true).random_int(1, 99999)), 0, 10));
        $vendor = Vendor::where('is_active', true)->first();

        return Product::create(array_merge([
            'vendor_id' => $vendor?->id,
            'category_id' => $category->id,
            'product_type' => 'general',
            'sku' => "GENERAL-{$token}",
            'name' => "General Product {$token}",
            'slug' => strtolower("general-product-{$token}"),
            'price' => 2500,
            'image' => 'images/products/test-product.jpg',
            'stock_quantity' => 5,
            'in_stock' => true,
            'is_active' => true,
            'delivery_class' => 'standard',
            'is_returnable' => true,
            'return_window_days' => 7,
        ], $overrides));
    }
}

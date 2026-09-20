<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductPageTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test product detail page renders successfully without syntax errors.
     */
    public function test_product_detail_page_renders_successfully(): void
    {
        $category = Category::firstOrCreate(
            ['slug' => 'test-category'],
            ['name' => 'Test Category', 'is_active' => true, 'sort_order' => 1]
        );

        $product = Product::create([
            'category_id'    => $category->id,
            'name'           => 'Crafted Test Jamdani Dress',
            'slug'           => 'crafted-test-jamdani-dress-' . rand(1000, 9999),
            'price'          => 12500,
            'old_price'      => 15000,
            'fabric'         => 'Pure Silk',
            'product_type'   => 'apparel',
            'specifications' => ['Color' => 'Indigo', 'Size' => 'Free Size'],
            'warranty_info'  => '7-day quality support',
            'short_desc'     => 'An artisanal handloom dress.',
            'description'    => 'Detailed description for test product.',
            'image'          => 'images/products/test-product.jpg',
            'badge'          => 'New Arrival',
            'in_stock'       => true,
            'stock_quantity' => 4,
        ]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Crafted Test Jamdani Dress');
        $response->assertSee('12,500');
        $response->assertSee('15,000');
        $response->assertSee('https://schema.org/');
        $response->assertSee('Product');
        $response->assertSee('Indigo');
        $response->assertSee('7-day quality support');
    }
}

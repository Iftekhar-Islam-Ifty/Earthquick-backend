<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Tests\TestCase;

class PlatformIdentityTest extends TestCase
{
    public function test_shared_pages_present_earthquick_as_the_platform(): void
    {
        $this->get('/checkout')
            ->assertOk()
            ->assertSee('Secure Checkout — Earthquick', false)
            ->assertDontSee('Earthquick by Nous Telos', false);

        $this->get('/about')
            ->assertOk()
            ->assertSee('EARTHQUICK MARKETPLACE', false)
            ->assertSee('Bright')
            ->assertDontSee('Nous Telos Living', false)
            ->assertDontSee('Earthquick Studio', false);
    }

    public function test_navbar_renders_shop_and_stores_navigation(): void
    {
        $response = $this->get('/')->assertOk();
        $response->assertSee('id="nav-link-shop"', false);
        $response->assertSee('aria-controls="megamenu-shop"', false);
        $response->assertSee('Fashion &amp; Accessories', false);
        $response->assertSee('Home &amp; Living', false);
        $response->assertSee('Electronics &amp; Smart Living', false);
        $response->assertSee('Coming Soon', false);
        $response->assertSee(route('category.show', 'women'), false);
        $response->assertSee(route('category.show', 'home-decor'), false);
        $response->assertSee(route('subcategory.show', ['categorySlug' => 'home-decor', 'subcategorySlug' => 'cushion-cover']), false);
        $response->assertSee(route('stores.show', 'nous-telos'), false);
        $response->assertSee(route('stores.show', 'bright'), false);
        $response->assertSee('id="nav-link-stores"', false);
        $response->assertSee('id="nav-link-about"', false);
        $response->assertSee("Bangladesh's curated multi-vendor marketplace", false);
    }

    public function test_home_editorial_collections_only_include_nous_telos_products(): void
    {
        $bright = Vendor::where('slug', 'bright')->firstOrFail();
        $electronics = Category::where('slug', 'electronics')->firstOrFail();

        $brightProduct = Product::create([
            'vendor_id' => $bright->id,
            'category_id' => $electronics->id,
            'sku' => 'BRT-PHASE5-001',
            'name' => 'Bright Test Speaker',
            'slug' => 'bright-test-speaker',
            'price' => 4500,
            'image' => 'images/products/fixture.jpg',
            'in_stock' => true,
            'stock_quantity' => 10,
            'is_featured' => true,
            'is_new_arrival' => true,
        ]);

        $response = $this->get('/')->assertOk();
        $featuredIds = $response->viewData('featuredProducts')->pluck('id');
        $newArrivalIds = $response->viewData('newArrivals')->pluck('id');
        $productIds = $response->viewData('products')->pluck('id');

        $this->assertFalse($featuredIds->contains($brightProduct->id));
        $this->assertFalse($newArrivalIds->contains($brightProduct->id));
        $this->assertFalse($productIds->contains($brightProduct->id));
    }
}

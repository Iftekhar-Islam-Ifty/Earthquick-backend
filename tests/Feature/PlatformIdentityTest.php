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
        $response->assertSee('NOUS TELOS &bull; READY TO WEAR', false);
        $response->assertSee('NOUS TELOS &bull; CRAFTED ACCESSORIES', false);
        $response->assertSee('Discover collections from our featured stores', false);
        $response->assertSee('Curated Brands', false);
        $response->assertSee('Reliable Delivery', false);
        $response->assertSee('Easy Exchange', false);
        $response->assertSee('FROM NOUS TELOS', false);
        $response->assertSee('THE NOUS TELOS EDIT', false);
        $response->assertSee('NOUS TELOS READY TO WEAR', false);
        $response->assertSee('CONTEMPORARY SETS BY NOUS TELOS', false);
        $response->assertSee('curated by Nous Telos for contemporary wardrobes.', false);
        $response->assertDontSee('Earthquick’s signature craft', false);
        $response->assertDontSee('worldwide', false);
        $response->assertDontSee('Easy 7-Day Exchange', false);
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

    public function test_homepage_explores_active_brands_without_fake_bright_inventory(): void
    {
        $response = $this->get('/')->assertOk();

        $response->assertSee('id="explore-stores"', false);
        $response->assertSee('OUR STORES', false);
        $response->assertSee('Explore Earthquick', false);
        $response->assertSee('Distinct brands, one curated marketplace', false);
        $response->assertSee('id="home-store-nous-telos"', false);
        $response->assertSee(route('stores.show', 'nous-telos'), false);
        $response->assertSee('id="home-store-bright"', false);
        $response->assertSee('COMING SOON', false);
        $response->assertSee('Electronics &amp; Smart Living', false);
        $response->assertSee(route('stores.show', 'bright'), false);
        $response->assertDontSee('Add to Cart', false);
        $response->assertDontSee('Shop Now', false);
    }

    public function test_homepage_bags_and_story_are_attributed_to_nous_telos(): void
    {
        $response = $this->get('/')->assertOk();

        $response->assertSee('NOUS TELOS STORY', false);
        $response->assertSee('Nous Telos works with Bangladesh’s handloom traditions', false);
        $response->assertSee('CRAFTED ACCESSORIES BY NOUS TELOS', false);
        $response->assertDontSee('OUR PHILOSOPHY', false);
        $response->assertDontSee('Wicker Weekend Bag', false);
        $response->assertDontSee('Mini Crossbody', false);

        foreach ($response->viewData('bags')->take(6) as $bag) {
            $response->assertSee($bag->name, false);
            $response->assertSee(route('product.show', $bag->slug), false);
            $response->assertSee(number_format($bag->price), false);
        }
    }

    public function test_styled_by_you_uses_earthquick_community_copy_without_unverified_social_links(): void
    {
        $response = $this->get('/')->assertOk();

        $response->assertSee('EARTHQUICK COMMUNITY', false);
        $response->assertSee('Real looks and everyday moments shared by the Nous Telos community on Earthquick.', false);
        $response->assertSee('id="styled-by-you"', false);
        $response->assertSee('id="coverflow-btn-prev"', false);
        $response->assertSee('id="coverflow-btn-next"', false);
        $response->assertSee('data-coverflow-filter="details"', false);
        $response->assertSee('The Full Look', false);
        $response->assertSee('Celebration Edit', false);
        $response->assertSee('Everyday Rituals', false);
        $response->assertSee('Heritage Stories', false);
        $response->assertSee('Details &amp; Accents', false);
        $response->assertDontSee('@RTHQUICK', false);
        $response->assertDontSee('https://facebook.com', false);
    }

    public function test_homepage_final_cta_contains_the_newsletter_signup(): void
    {
        $response = $this->get('/')->assertOk();

        $response->assertSee('JOIN THE HOUSE OF EARTHQUICK', false);
        $response->assertSee("Be first to see what's next.", false);
        $response->assertSee('Sign up for early access to new drops, restocks, and the stories behind the makers we work with.', false);
        $response->assertSee('id="eq-newsletter-form"', false);
        $response->assertSee('Enter your email address', false);
        $response->assertSee('No spam, ever. Unsubscribe with one click anytime.', false);
    }
}

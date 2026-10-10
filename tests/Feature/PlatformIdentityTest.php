<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Tests\TestCase;

class PlatformIdentityTest extends TestCase
{
    public function test_product_images_use_existing_webp_and_fall_back_to_originals(): void
    {
        $product = new Product([
            'image' => 'images/bags/bag-3.jpg',
            'alt_image' => 'images/products/not-converted.jpg',
        ]);

        // The suite deliberately points public_path() at an isolated empty directory.
        $this->assertSame('images/bags/bag-3.jpg', $product->optimized_image);
        $this->assertFileExists(base_path('public/images/bags/bag-3.webp'));
        $this->assertSame('images/products/not-converted.jpg', $product->optimized_alt_image);
        $this->get('/')->assertOk()->assertSee('images/hero/hero-main-saree-2.webp', false);
    }
    public function test_public_sitemap_lists_only_browseable_pages(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false);
        $response->assertSee(route('home'), false);
        $response->assertSee(route('stores.show', 'nous-telos'), false);
        $response->assertDontSee('/checkout', false);
        $response->assertDontSee('/admin', false);
    }

    public function test_private_search_and_faceted_pages_are_not_indexed(): void
    {
        $this->get('/about')->assertOk()->assertSee('name="robots" content="index,follow"', false);
        $this->get('/search?q=saree')->assertOk()->assertSee('name="robots" content="noindex,follow"', false);
        $this->get('/cart')->assertOk()->assertSee('name="robots" content="noindex,follow"', false);
        $this->get('/shop/women?sort=price-asc')->assertOk()->assertSee('name="robots" content="noindex,follow"', false);
    }

    public function test_search_title_escapes_user_input_once(): void
    {
        $this->get('/search?q=%22%3E%3Cscript%3Ealert(1)%3C%2Fscript%3E')
            ->assertOk()
            ->assertSee('<title>Search: &quot;&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;&quot; | Rthquick</title>', false)
            ->assertDontSee('&amp;quot;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }
    public function test_paginated_store_has_its_own_canonical_url(): void
    {
        $this->get('/stores/nous-telos?page=2')
            ->assertOk()
            ->assertSee('rel="canonical" href="'.route('stores.show', 'nous-telos').'?page=2"', false);
    }
    public function test_scroll_reveal_is_opted_into_browsing_pages_but_not_transaction_pages(): void
    {
        foreach (['/', '/about', '/delivery-returns', '/stores', '/stores/nous-telos', '/shop/women', '/search?q=saree'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee("classList.add('eq-scroll-motion')", false)
                ->assertSee('eq-reveal', false);
        }

        foreach (['/cart', '/checkout', '/login', '/register'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertDontSee("classList.add('eq-scroll-motion')", false);
        }
    }

    public function test_home_and_shared_layout_version_their_stylesheets(): void
    {
        foreach (['/', '/about'] as $path) {
            $response = $this->get($path)->assertOk();
            $response->assertSee('css/style.css?v='.filemtime(base_path('public/css/style.css')), false);
            $response->assertSee('css/responsive.css?v='.filemtime(base_path('public/css/responsive.css')), false);
        }
    }

    public function test_shared_pages_present_earthquick_as_the_platform(): void
    {
        $this->get('/checkout')
            ->assertOk()
            ->assertSee('Secure Checkout — Rthquick', false)
            ->assertDontSee('Rthquick by Nous Telos', false);

        $this->get('/about')
            ->assertOk()
            ->assertSee('RTHQUICK MARKETPLACE', false)
            ->assertSee('Bright')
            ->assertDontSee('Nous Telos Living', false)
            ->assertDontSee('Rthquick Studio', false);
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
        $response->assertDontSee('Rthquick’s signature craft', false);
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
        $response->assertSeeInOrder(['id="new-arrivals"', 'id="explore-stores"', 'id="saree-section"'], false);
        $response->assertSee('OUR STORES', false);
        $response->assertSee('Explore Rthquick', false);
        $response->assertSee('Distinct brands, one curated marketplace', false);
        $response->assertSee('id="home-store-nous-telos"', false);
        $response->assertSee(asset('images/saree/saree-3.webp'), false);
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

    public function test_saree_editorial_keeps_four_mobile_cards_and_desktop_gallery_row(): void
    {
        $response = $this->get('/')->assertOk();
        $html = $response->getContent();

        $galleryCount = $response->viewData('sarees')->take(4)->count();
        $this->assertSame($galleryCount, substr_count($html, 'class="eq-product-card eq-saree-card"'));
        $this->assertSame($galleryCount, substr_count($html, 'class="eq-product-card eq-saree-card eq-saree-card--desktop-repeat"'));
        $response->assertSee('id="saree-gallery-prev"', false);
        $response->assertSee('id="saree-gallery-next"', false);
        $response->assertSee('id="link-view-all-sarees"', false);
        foreach ($response->viewData('sarees')->take(4) as $saree) {
            $response->assertSee('id="saree-grid-item-'.$saree->id.'-b"', false);
        }
    }

    public function test_styled_by_you_uses_earthquick_community_copy_without_unverified_social_links(): void
    {
        $response = $this->get('/')->assertOk();

        $response->assertSee('RTHQUICK COMMUNITY', false);
        $response->assertSee('Real looks and everyday moments shared by the Nous Telos community on Rthquick.', false);
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

        $response->assertSee('JOIN THE HOUSE OF RTHQUICK', false);
        $response->assertSee("Be first to see what's next.", false);
        $response->assertSee('Sign up for early access to new drops, restocks, and the stories behind the makers we work with.', false);
        $response->assertSee('id="eq-newsletter-form"', false);
        $response->assertSee('Enter your email address', false);
        $response->assertSee('No spam, ever. Unsubscribe with one click anytime.', false);
    }
}

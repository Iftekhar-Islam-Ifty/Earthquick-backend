<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Tests\TestCase;

class HomepageCatalogTest extends TestCase
{
    public function test_new_arrival_card_matches_the_product_page_and_respects_admin_visibility(): void
    {
        $product = Product::create([
            'vendor_id' => Vendor::where('slug', 'nous-telos')->firstOrFail()->id,
            'category_id' => Category::where('slug', 'women')->firstOrFail()->id,
            'sku' => 'NT-HOME-QA-001',
            'name' => 'Fresh Handloom Test Piece',
            'slug' => 'fresh-handloom-test-piece',
            'price' => 6200,
            'image' => 'images/products/fresh-handloom.webp',
            'in_stock' => true,
            'stock_quantity' => 3,
            'is_active' => true,
            'is_new_arrival' => true,
        ]);

        $home = $this->get('/')->assertOk();
        $home->assertSee('id="new-arrival-'.$product->id.'"', false)
            ->assertSee(route('product.show', $product->slug), false)
            ->assertSee($product->name)
            ->assertSee(asset($product->image), false)
            ->assertSee('৳6,200', false)
            ->assertDontSee('Muslin Jamdani Saree');

        $this->get(route('product.show', $product->slug))->assertOk()
            ->assertSee($product->name)
            ->assertSee('6,200', false);
        $this->get(route('category.show', 'women'))->assertOk()->assertSee($product->name);

        $product->update(['is_new_arrival' => false]);
        $this->get('/')->assertOk()->assertDontSee('id="new-arrival-'.$product->id.'"', false);
    }

    public function test_fashion_cards_link_to_their_real_catalog_products(): void
    {
        $home = $this->get('/')->assertOk();

        foreach (['sarees', 'threePieces', 'twoPieces'] as $section) {
            foreach ($home->viewData($section) as $product) {
                $home->assertSee(route('product.show', $product->slug), false)
                    ->assertSee($product->name)
                    ->assertSee(asset($product->optimized_image), false);
            }
        }

        $home->assertDontSee('Aarna Embroidered')
            ->assertDontSee('Selene Tunic Two Piece');
    }
}

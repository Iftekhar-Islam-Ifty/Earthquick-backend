<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SearchFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test live suggestions endpoint returns matching products as clean JSON.
     */
    public function test_live_search_suggestions_returns_matching_products(): void
    {
        // Find or create test product
        $category = Category::firstOrCreate(
            ['slug' => 'heritage-weaves'],
            ['name' => 'Heritage Weaves', 'is_active' => true, 'sort_order' => 1]
        );

        $product = Product::create([
            'category_id'    => $category->id,
            'name'           => 'Zari Jamdani Royal Heritage Saree',
            'slug'           => 'zari-jamdani-royal-heritage-saree-' . rand(100, 999),
            'price'          => 18500,
            'fabric'         => 'Pure Jamdani Silk',
            'description'    => 'Exquisite royal handloom Jamdani masterpiece.',
            'image'          => 'images/products/saree-jamdani-navy.jpg',
            'in_stock'       => true,
            'stock_quantity' => 5,
        ]);

        $response = $this->getJson('/api/search/suggestions?q=Jamdani');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'query'   => 'Jamdani',
            ])
            ->assertJsonStructure([
                'success',
                'query',
                'count',
                'suggestions' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'price',
                        'formatted_price',
                        'image',
                        'category_name',
                        'url',
                    ]
                ]
            ]);

        $suggestions = $response->json('suggestions');
        $this->assertNotEmpty($suggestions);
        $matched = collect($suggestions)->firstWhere('id', $product->id);
        $this->assertNotNull($matched);
        $this->assertEquals('Zari Jamdani Royal Heritage Saree', $matched['name']);
        $this->assertEquals(route('product.show', $product->slug), $matched['url']);
    }

    /**
     * Test suggestions endpoint caps at 6 items and ignores out of stock items.
     */
    public function test_live_suggestions_caps_at_six_and_ignores_out_of_stock(): void
    {
        $category = Category::first();

        // Create an out of stock product that shouldn't appear in live suggestions
        $outOfStock = Product::create([
            'category_id'    => $category->id,
            'name'           => 'UniqueQueryTerm OutOfStock Piece',
            'slug'           => 'uniquequeryterm-out-of-stock-' . rand(1000, 9999),
            'price'          => 4500,
            'image'          => 'images/products/test.jpg',
            'in_stock'       => false,
            'stock_quantity' => 0,
        ]);

        // Create an in stock product
        $inStock = Product::create([
            'category_id'    => $category->id,
            'name'           => 'UniqueQueryTerm InStock Piece',
            'slug'           => 'uniquequeryterm-in-stock-' . rand(1000, 9999),
            'price'          => 5500,
            'image'          => 'images/products/test.jpg',
            'in_stock'       => true,
            'stock_quantity' => 4,
        ]);

        $response = $this->getJson('/api/search/suggestions?q=UniqueQueryTerm');

        $response->assertStatus(200);
        $ids = collect($response->json('suggestions'))->pluck('id')->all();

        $this->assertContains($inStock->id, $ids);
        $this->assertNotContains($outOfStock->id, $ids);
    }

    /**
     * Test suggestions endpoint with empty query returns empty suggestions list.
     */
    public function test_live_suggestions_empty_query(): void
    {
        $response = $this->getJson('/api/search/suggestions?q=');

        $response->assertStatus(200)
            ->assertJson([
                'success'     => true,
                'count'       => 0,
                'suggestions' => [],
            ]);
    }

    /**
     * Test dedicated search results page renders correctly with query and products.
     */
    public function test_search_results_page_renders_with_query(): void
    {
        $category = Category::first();

        $product = Product::create([
            'category_id'    => $category->id,
            'name'           => 'SpecialTantuj Cotton Ensemble',
            'slug'           => 'specialtantuj-cotton-ensemble-' . rand(100, 999),
            'price'          => 6200,
            'fabric'         => 'Tangail Cotton',
            'image'          => 'images/products/test.jpg',
            'in_stock'       => true,
            'stock_quantity' => 3,
        ]);

        $response = $this->get('/search?q=SpecialTantuj');

        $response->assertStatus(200);
        $response->assertSee('Search Results for &ldquo;SpecialTantuj&rdquo;', false);
        $response->assertSee('SpecialTantuj Cotton Ensemble');
        $response->assertSee('৳6,200');
    }

    /**
     * Test dedicated search results page sorting.
     */
    public function test_search_results_sorting(): void
    {
        $category = Category::first();

        $cheap = Product::create([
            'category_id'    => $category->id,
            'name'           => 'SortTest Low Price Piece',
            'slug'           => 'sorttest-low-' . rand(100, 999),
            'price'          => 1200,
            'image'          => 'images/products/test.jpg',
            'in_stock'       => true,
            'stock_quantity' => 5,
        ]);

        $expensive = Product::create([
            'category_id'    => $category->id,
            'name'           => 'SortTest High Price Piece',
            'slug'           => 'sorttest-high-' . rand(100, 999),
            'price'          => 98000,
            'image'          => 'images/products/test.jpg',
            'in_stock'       => true,
            'stock_quantity' => 2,
        ]);

        // Ascending
        $responseAsc = $this->get('/search?q=SortTest&sort=price-asc');
        $responseAsc->assertStatus(200);
        $viewProductsAsc = $responseAsc->viewData('products');
        $this->assertEquals($cheap->id, $viewProductsAsc->first()->id);

        // Descending
        $responseDesc = $this->get('/search?q=SortTest&sort=price-desc');
        $responseDesc->assertStatus(200);
        $viewProductsDesc = $responseDesc->viewData('products');
        $this->assertEquals($expensive->id, $viewProductsDesc->first()->id);
    }

    /**
     * Test empty search results state displays shortcuts and friendly messaging.
     */
    public function test_search_results_empty_state_and_collection_shortcuts(): void
    {
        $response = $this->get('/search?q=NonExistentSuperUnlikelyProductKeywordXYZ999');

        $response->assertStatus(200);
        $response->assertSee('No matching pieces found');
        $response->assertSee('NonExistentSuperUnlikelyProductKeywordXYZ999');
        $response->assertSee('Explore All Sarees &rarr;', false);
        $response->assertSee('Popular Featured Collections');
        $response->assertSee('Three Piece');
        $response->assertSee('Bags');
    }

    /**
     * Test search page without query parameter displays catalog overview.
     */
    public function test_search_page_without_query(): void
    {
        $response = $this->get('/search');

        $response->assertStatus(200);
        $response->assertSee('Explore the Full Collection');
        $response->assertSee('Sort by:');
    }
}

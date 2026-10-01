<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class VendorStorefrontFiltersTest extends TestCase
{
    public function test_storefront_hides_irrelevant_filters_and_preserves_category_context(): void
    {
        $url = route('stores.show', 'nous-telos');
        $this->get($url)->assertOk()
            ->assertSee('All Pieces (4)')
            ->assertSee('4 pieces found')
            ->assertSee('Filters')
            ->assertDontSee('name="delivery_class"', false)
            ->assertDontSee('name="returnable"', false)
            ->assertDontSee('Highest Rated');

        $this->get($url.'?category=women&min_price=3000&sort=price-asc')->assertOk()
            ->assertSee('All Pieces (4)')
            ->assertSee('2 pieces found')
            ->assertSee('Test Cotton Set')
            ->assertDontSee('No pieces match this selection')
            ->assertSee('href="'.e(route('stores.show', [
                'slug' => 'nous-telos', 'min_price' => '3000', 'sort' => 'price-asc',
            ])).'"', false)
            ->assertSee('href="'.e(route('stores.show', [
                'slug' => 'nous-telos', 'category' => 'bags', 'min_price' => '3000', 'sort' => 'price-asc',
            ])).'"', false);
    }

    public function test_zero_results_keep_filters_and_show_an_accurate_empty_state(): void
    {
        $this->get(route('stores.show', 'nous-telos').'?category=women&min_price=999999')
            ->assertOk()
            ->assertSee('All Pieces (4)')
            ->assertSee('0 pieces found')
            ->assertSee('name="min_price"', false)
            ->assertSee('No pieces match this selection')
            ->assertSee('View all pieces')
            ->assertDontSee('Collection in Preparation');

        $this->get(route('stores.show', 'bright'))->assertOk()
            ->assertSee('All Pieces (0)')
            ->assertSee('Collection in Preparation')
            ->assertDontSee('No pieces match this selection');
    }

    public function test_mixed_policy_and_delivery_filters_are_available_and_work(): void
    {
        Product::where('slug', 'test-artisan-bag')->update([
            'delivery_class' => 'fragile', 'is_returnable' => false,
        ]);

        $url = route('stores.show', 'nous-telos');
        $this->get($url)->assertOk()
            ->assertSee('name="delivery_class"', false)
            ->assertSee('name="returnable"', false)
            ->assertSee('Final sale');
        $this->get($url.'?delivery_class=fragile&returnable=0')->assertOk()
            ->assertSee('1 piece found')
            ->assertSee('Test Artisan Bag')
            ->assertDontSee('Test Cotton Set');
    }

    public function test_unknown_filter_values_do_not_create_misleading_applied_filters(): void
    {
        $this->get(route('stores.show', 'nous-telos').'?returnable=maybe&min_price=-5&sort=rating')
            ->assertOk()
            ->assertSee('4 pieces found')
            ->assertDontSee('Final sale')
            ->assertDontSee('From ৳-5')
            ->assertDontSee('Highest Rated');
    }

    public function test_storefront_pagination_summary_counts_pages_not_product_positions(): void
    {
        $source = Product::where('slug', 'test-artisan-bag')->firstOrFail();
        for ($index = 1; $index <= 9; $index++) {
            $copy = $source->replicate();
            $copy->name = 'Pagination bag '.$index;
            $copy->slug = 'pagination-bag-'.$index;
            $copy->sku = 'NT-PAGE-'.$index;
            $copy->save();
        }

        $url = route('stores.show', 'nous-telos');
        $this->get($url)->assertOk()
            ->assertSee('All Pieces (13)')
            ->assertSee('Page <strong>1</strong> of <strong>2</strong>', false)
            ->assertDontSee('Showing <strong>1</strong>', false);
        $this->get($url.'?page=2')->assertOk()
            ->assertSee('Page <strong>2</strong> of <strong>2</strong>', false);
    }
}

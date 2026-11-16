<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Vendor;
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
            ->assertSee('category=bags', false)
            ->assertSee('min_price=3000', false)
            ->assertSee('sort=price-asc', false);
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
            ->assertSee('All Products (0)')
            ->assertSee('Electrical Catalog in Preparation')
            ->assertDontSee('No products match this selection');
    }

    public function test_bright_subcategories_filter_products_and_keep_sort_and_filters_working(): void
    {
        $bright = Vendor::where('slug', 'bright')->firstOrFail();
        $electronics = Category::where('slug', 'electronics')->firstOrFail();
        $switches = Subcategory::firstOrCreate(
            ['slug' => 'switches'],
            ['category_id' => $electronics->id, 'name' => 'Switches', 'is_active' => true, 'sort_order' => 1]
        );
        $sockets = Subcategory::firstOrCreate(
            ['slug' => 'sockets'],
            ['category_id' => $electronics->id, 'name' => 'Sockets', 'is_active' => true, 'sort_order' => 2]
        );

        foreach ([
            ['Bright Test Switch Low', 'bright-test-switch-low', $switches, 180, 'standard', true],
            ['Bright Test Switch High', 'bright-test-switch-high', $switches, 900, 'fragile', false],
            ['Bright Test Socket', 'bright-test-socket', $sockets, 240, 'standard', true],
        ] as [$name, $slug, $subcategory, $price, $deliveryClass, $returnable]) {
            Product::create([
                'vendor_id' => $bright->id,
                'category_id' => $electronics->id,
                'subcategory_id' => $subcategory->id,
                'product_type' => 'electronics',
                'sku' => 'BRT-'.strtoupper(str_replace('-', '', $slug)),
                'name' => $name,
                'slug' => $slug,
                'price' => $price,
                'image' => 'images/products/fixture.jpg',
                'delivery_class' => $deliveryClass,
                'is_returnable' => $returnable,
                'return_window_days' => $returnable ? 7 : null,
                'in_stock' => true,
                'stock_quantity' => 10,
                'is_active' => true,
            ]);
        }

        $switchUrl = route('stores.show', ['slug' => 'bright', 'subcategory' => 'switches']);
        $this->get($switchUrl)->assertOk()
            ->assertSee('Switches')
            ->assertSee('LED Lights')
            ->assertSee('Electrical Accessories')
            ->assertDontSee('Electronics</a>', false)
            ->assertSee('Bright Test Switch Low')
            ->assertSee('Bright Test Switch High')
            ->assertDontSee('Bright Test Socket')
            ->assertSee('href="'.e($switchUrl).'"', false);

        $this->get($switchUrl.'&sort=price-desc')->assertOk()
            ->assertSeeInOrder(['Bright Test Switch High', 'Bright Test Switch Low']);

        $this->get($switchUrl.'&min_price=500')->assertOk()
            ->assertSee('Bright Test Switch High')
            ->assertDontSee('Bright Test Switch Low')
            ->assertDontSee('Bright Test Socket');

        $this->get($switchUrl.'&delivery_class=fragile&returnable=0')->assertOk()
            ->assertSee('Bright Test Switch High')
            ->assertDontSee('Bright Test Switch Low')
            ->assertDontSee('Bright Test Socket');

        $this->get(route('stores.show', ['slug' => 'bright', 'subcategory' => 'sockets']))->assertOk()
            ->assertSee('Bright Test Socket')
            ->assertDontSee('Bright Test Switch Low')
            ->assertDontSee('Bright Test Switch High');

        $this->get(route('stores.show', ['slug' => 'bright', 'subcategory' => 'electrical-accessories']))->assertOk()
            ->assertSee('0 products found')
            ->assertSee('No products match this selection')
            ->assertDontSee('Bright Test Switch Low')
            ->assertDontSee('Bright Test Socket');
    }

    public function test_store_filter_and_sort_popovers_use_the_warm_store_palette(): void
    {
        $this->get(route('stores.show', 'nous-telos'))->assertOk()
            ->assertSee('background: var(--eq-cream)', false)
            ->assertSee('background: #f7f2e9; color: #2a2622', false)
            ->assertSee('color-scheme: light', false);
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

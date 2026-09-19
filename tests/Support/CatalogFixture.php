<?php

namespace Tests\Support;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Vendor;

final class CatalogFixture
{
    public static function seed(): void
    {
        $nousTelos = Vendor::create([
            'name' => 'Nous Telos', 'slug' => 'nous-telos', 'vendor_code' => 'NT',
            'is_active' => true, 'sort_order' => 1,
        ]);
        Vendor::create([
            'name' => 'Bright', 'slug' => 'bright', 'vendor_code' => 'BRT',
            'is_active' => true, 'sort_order' => 2,
        ]);

        $categories = [];
        foreach (['men' => 'Men', 'women' => 'Women', 'kids' => 'Kids', 'ornaments' => 'Ornaments',
            'bags' => 'Bags', 'home-decor' => 'Home Decor', 'electronics' => 'Electronics'] as $slug => $name) {
            $categories[$slug] = Category::create([
                'name' => $name, 'slug' => $slug, 'is_active' => true,
                'sort_order' => count($categories) + 1,
            ]);
        }

        $subcategories = [];
        foreach (['women' => ['saree' => 'Saree', 'three-piece' => 'Three Piece', 'two-piece' => 'Two Piece'],
            'home-decor' => ['kantha' => 'Kantha', 'bedsheet' => 'Bedsheet', 'cushion-cover' => 'Cushion Cover']] as $category => $items) {
            foreach ($items as $slug => $name) {
                $subcategories[$slug] = Subcategory::create([
                    'category_id' => $categories[$category]->id, 'name' => $name,
                    'slug' => $slug, 'is_active' => true, 'sort_order' => count($subcategories) + 1,
                ]);
            }
        }

        foreach ([['Test Jamdani Saree', 'test-jamdani-saree', 'women', 'saree', 18000],
            ['Test Cotton Set', 'test-cotton-set', 'women', 'three-piece', 4500],
            ['Test Artisan Bag', 'test-artisan-bag', 'bags', null, 2500],
            ['Test Kantha', 'test-kantha', 'home-decor', 'kantha', 3500]] as $index => [$name, $slug, $category, $subcategory, $price]) {
            Product::create([
                'vendor_id' => $nousTelos->id, 'category_id' => $categories[$category]->id,
                'subcategory_id' => $subcategory ? $subcategories[$subcategory]->id : null,
                'name' => $name, 'slug' => $slug, 'sku' => 'NT-QA-'.$index,
                'price' => $price, 'fabric' => 'Cotton', 'image' => 'images/products/fixture.jpg',
                'in_stock' => true, 'stock_quantity' => 20, 'is_featured' => true, 'is_new_arrival' => true,
            ]);
        }
    }
}

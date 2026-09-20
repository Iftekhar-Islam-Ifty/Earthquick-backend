<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class NousTelosBagSeeder extends Seeder
{
    /**
     * Add the existing bag photography to the flagship product catalog.
     */
    public function run(): void
    {
        $vendor = Vendor::where('slug', 'nous-telos')->firstOrFail();
        $category = Category::where('slug', 'bags')->firstOrFail();

        $products = [
            [
                'name' => 'Terra Structured Handbag',
                'slug' => 'terra-structured-handbag',
                'sku' => 'NT-BAG-003',
                'price' => 5200,
                'old_price' => 5800,
                'image' => 'images/bags/bag-3.jpg',
                'badge' => 'Signature',
                'badge_type' => 'signature',
            ],
            [
                'name' => 'Mini Canvas Crossbody',
                'slug' => 'mini-canvas-crossbody',
                'sku' => 'NT-BAG-004',
                'price' => 2900,
                'old_price' => null,
                'image' => 'images/bags/bag-5.jpg',
                'badge' => 'New',
                'badge_type' => 'ready',
            ],
            [
                'name' => 'Olive Structured Satchel',
                'slug' => 'olive-structured-satchel',
                'sku' => 'NT-BAG-005',
                'price' => 4800,
                'old_price' => null,
                'image' => 'images/bags/bag-7.jpg',
                'badge' => null,
                'badge_type' => null,
            ],
            [
                'name' => 'Handcrafted Stitch Tote',
                'slug' => 'handcrafted-stitch-tote',
                'sku' => 'NT-BAG-006',
                'price' => 4100,
                'old_price' => null,
                'image' => 'images/bags/bag-9.jpg',
                'badge' => null,
                'badge_type' => null,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['slug' => $product['slug']],
                array_merge($product, [
                    'vendor_id' => $vendor->id,
                    'category_id' => $category->id,
                    'subcategory_id' => null,
                    'alt_image' => null,
                    'fabric' => 'Canvas and leather',
                    'short_desc' => 'A considered everyday bag from the Nous Telos accessories edit.',
                    'description' => 'A practical silhouette from the Nous Telos accessories collection, made for thoughtful everyday carry.',
                    'rating' => 4.8,
                    'reviews_count' => 0,
                    'in_stock' => true,
                    'stock_quantity' => 10,
                    'is_featured' => false,
                    'is_new_arrival' => false,
                ])
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\Subcategory;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class BrightDemoProductsSeeder extends Seeder
{
    /**
     * Add replaceable sample listings for exercising Bright's public catalog.
     * No real product specifications, warranties or return promises are stated.
     */
    public function run(): void
    {
        $bright = Vendor::where('slug', 'bright')->firstOrFail();
        $electronics = Category::where('slug', 'electronics')->firstOrFail();
        $heaven = ProductBrand::where('vendor_id', $bright->id)
            ->where('slug', 'heaven')
            ->firstOrFail();

        $samples = [
            ['slug' => 'demo-heaven-modular-switch', 'name' => '[DEMO] Heaven Modular Switch', 'subcategory' => 'switches', 'price' => 180, 'sku' => 'DEMO-BRT-SW-001', 'image' => 'images/bright-demo/switch.svg'],
            ['slug' => 'demo-heaven-wall-socket', 'name' => '[DEMO] Heaven Wall Socket', 'subcategory' => 'sockets', 'price' => 240, 'sku' => 'DEMO-BRT-SO-001', 'image' => 'images/bright-demo/socket.svg'],
            ['slug' => 'demo-heaven-led-bulb', 'name' => '[DEMO] Heaven LED Bulb', 'subcategory' => 'led-lights', 'price' => 160, 'sku' => 'DEMO-BRT-LD-001', 'image' => 'images/bright-demo/led-bulb.svg'],
            ['slug' => 'demo-heaven-circuit-breaker', 'name' => '[DEMO] Heaven Circuit Breaker', 'subcategory' => 'circuit-breakers', 'price' => 420, 'sku' => 'DEMO-BRT-CB-001', 'image' => 'images/bright-demo/breaker.svg'],
            ['slug' => 'demo-heaven-ceiling-fan', 'name' => '[DEMO] Heaven Ceiling Fan', 'subcategory' => 'fans', 'price' => 1250, 'sku' => 'DEMO-BRT-FN-001', 'image' => 'images/bright-demo/fan.svg'],
            ['slug' => 'demo-heaven-electrical-accessory-set', 'name' => '[DEMO] Heaven Electrical Accessories', 'subcategory' => 'electrical-accessories', 'price' => 320, 'sku' => 'DEMO-BRT-EA-001', 'image' => 'images/bright-demo/accessories.svg'],
        ];

        foreach ($samples as $sample) {
            $subcategory = Subcategory::where('category_id', $electronics->id)
                ->where('slug', $sample['subcategory'])
                ->firstOrFail();

            Product::updateOrCreate(
                ['slug' => $sample['slug']],
                [
                    'vendor_id' => $bright->id,
                    'brand_id' => $heaven->id,
                    'category_id' => $electronics->id,
                    'subcategory_id' => $subcategory->id,
                    'product_type' => 'electronics',
                    'sku' => $sample['sku'],
                    'name' => $sample['name'],
                    'price' => $sample['price'],
                    'short_desc' => 'Demonstration listing only. Replace with Bright-verified product details before launch.',
                    'description' => 'This sample listing is for checking the Bright storefront, product details, cart and checkout. The displayed price and stock are test values, not a Bright quotation. Product specifications, warranty terms and return policy have not been provided.',
                    'image' => $sample['image'],
                    'badge' => 'DEMO',
                    'badge_type' => 'ready',
                    'rating' => 0,
                    'reviews_count' => 0,
                    'in_stock' => true,
                    'stock_quantity' => 20,
                    'is_active' => true,
                    'is_featured' => false,
                    'is_new_arrival' => true,
                    'is_returnable' => false,
                    'return_window_days' => null,
                    'return_policy_note' => 'Demo listing only; Bright has not provided a return policy.',
                    'warranty_info' => null,
                    'specifications' => null,
                    'delivery_class' => 'standard',
                ]
            );
        }
    }
}

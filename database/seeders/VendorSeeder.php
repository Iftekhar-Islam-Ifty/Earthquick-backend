<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Seed initial foundational vendors and top-level Electronics category.
     */
    public function run(): void
    {
        // 1. Primary Vendor: Nous Telos (Fashion & Handlooms)
        $nousTelos = Vendor::firstOrCreate(
            ['slug' => 'nous-telos'],
            [
                'name' => 'Nous Telos',
                'vendor_code' => 'NT',
                'tagline' => 'Handloom Heritage & Artisanal Fashion',
                'description' => 'Rooted in Bengal’s handloom heritage, Nous Telos unites generational artisan craftsmanship with refined modern silhouettes—partnering directly with master weavers to create conscious, timeless attire.',
                'email' => 'concierge@noustelos.com',
                'phone' => '01700000001',
                'address' => 'Handloom Guild Studio, Dhaka & Chattogram',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        // 2. Bright Electrical Company (verified electrical supplier)
        Vendor::firstOrCreate(
            ['slug' => 'bright'],
            [
                'name' => 'Bright Electrical Company',
                'vendor_code' => 'BRT',
                'tagline' => 'Wholesale & Retail Electrical Supplier',
                'description' => 'Electrical products supplier in Chattogram offering switches, sockets, LED lights, circuit breakers, fans and other electrical accessories.',
                'email' => null,
                'phone' => null,
                'address' => 'Police Plaza, Nandankanan, Chattogram, Bangladesh',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        // 3. Shared top-level category and verified electrical subcategories.
        Category::firstOrCreate(
            ['slug' => 'electronics'],
            [
                'name' => 'Electronics',
                'description' => 'Switches, sockets, LED lights, circuit breakers, fans and other electrical accessories.',
                'image' => null,
                'sort_order' => 7,
                'is_active' => true,
            ]
        );

        $electronics = Category::where('slug', 'electronics')->firstOrFail();
        foreach ([
            ['name' => 'Switches', 'slug' => 'switches'],
            ['name' => 'Sockets', 'slug' => 'sockets'],
            ['name' => 'LED Lights', 'slug' => 'led-lights'],
            ['name' => 'Circuit Breakers', 'slug' => 'circuit-breakers'],
            ['name' => 'Fans', 'slug' => 'fans'],
            ['name' => 'Electrical Accessories', 'slug' => 'electrical-accessories'],
        ] as $index => $subcategory) {
            $electronics->subcategories()->firstOrCreate(
                ['slug' => $subcategory['slug']],
                [
                    'name' => $subcategory['name'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        // Existing unassigned legacy products are intentionally not changed
        // automatically. A reviewed backfill is safer than silently assigning
        // real inventory during a routine seed run.
    }
}

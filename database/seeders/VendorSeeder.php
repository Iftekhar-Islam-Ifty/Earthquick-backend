<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
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
        $nousTelos = Vendor::updateOrCreate(
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

        // 2. Architectural Vendor: Bright (Electronics & Lighting - Prototype)
        Vendor::updateOrCreate(
            ['slug' => 'bright'],
            [
                'name' => 'Bright',
                'vendor_code' => 'BRT',
                'tagline' => 'Modern Living & Smart Illumination',
                'description' => 'Minimalist lighting and contemporary lifestyle electricals engineered for modern architectural spaces.',
                'email' => 'support@brightliving.com',
                'phone' => '01700000002',
                'address' => 'Innovation Hub, Chattogram',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        // 3. Top-Level Category: Electronics (Associated conceptually with Bright, currently 0 products, 0 subcategories)
        Category::updateOrCreate(
            ['slug' => 'electronics'],
            [
                'name' => 'Electronics',
                'description' => 'Modern lighting and smart electrical lifestyle products.',
                'image' => null,
                'sort_order' => 7,
                'is_active' => true,
            ]
        );

        // EarthquickSeeder establishes the original catalog first. Associate
        // unassigned legacy products with the flagship vendor without changing
        // any products that an administrator has already assigned elsewhere.
        Product::whereNull('vendor_id')->update(['vendor_id' => $nousTelos->id]);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const SUBCATEGORIES = [
        ['name' => 'Switches', 'slug' => 'switches', 'sort_order' => 1],
        ['name' => 'Sockets', 'slug' => 'sockets', 'sort_order' => 2],
        ['name' => 'LED Lights', 'slug' => 'led-lights', 'sort_order' => 3],
        ['name' => 'Circuit Breakers', 'slug' => 'circuit-breakers', 'sort_order' => 4],
        ['name' => 'Fans', 'slug' => 'fans', 'sort_order' => 5],
        ['name' => 'Electrical Accessories', 'slug' => 'electrical-accessories', 'sort_order' => 6],
    ];

    public function up(): void
    {
        $now = now();
        $electronics = DB::table('categories')->where('slug', 'electronics')->first();

        if (! $electronics) {
            $electronicsId = DB::table('categories')->insertGetId([
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Switches, sockets, LED lights, circuit breakers, fans and other electrical accessories.',
                'is_active' => true,
                'sort_order' => 7,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $electronicsId = $electronics->id;

            if ($electronics->description === 'Modern lighting and smart electrical lifestyle products.') {
                DB::table('categories')->where('id', $electronicsId)->update([
                    'description' => 'Switches, sockets, LED lights, circuit breakers, fans and other electrical accessories.',
                    'updated_at' => $now,
                ]);
            }
        }

        foreach (self::SUBCATEGORIES as $subcategory) {
            $existing = DB::table('subcategories')->where('slug', $subcategory['slug'])->first();
            if ($existing) {
                if ((int) $existing->category_id !== (int) $electronicsId) {
                    throw new RuntimeException("Subcategory slug '{$subcategory['slug']}' already belongs to another category.");
                }

                continue;
            }

            DB::table('subcategories')->insert([
                ...$subcategory,
                'category_id' => $electronicsId,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $bright = DB::table('vendors')->where('slug', 'bright')->first();
        if (! $bright) {
            return;
        }

        $updates = [];
        if ($bright->name === 'Bright') {
            $updates['name'] = 'Bright Electrical Company';
        }
        if ($bright->tagline === 'Modern Living & Smart Illumination') {
            $updates['tagline'] = 'Wholesale & Retail Electrical Supplier';
        }
        if ($bright->description === 'Minimalist lighting and contemporary lifestyle electricals engineered for modern architectural spaces.') {
            $updates['description'] = 'Electrical products supplier in Chattogram offering switches, sockets, LED lights, circuit breakers, fans and other electrical accessories.';
        }
        if ($bright->address === 'Innovation Hub, Chattogram') {
            $updates['address'] = 'Police Plaza, Nandankanan, Chattogram, Bangladesh';
        }
        if ($bright->email === 'support@brightliving.com') {
            $updates['email'] = null;
        }
        if ($bright->phone === '01700000002') {
            $updates['phone'] = null;
        }
        if ($updates !== []) {
            DB::table('vendors')->where('id', $bright->id)->update([...$updates, 'updated_at' => $now]);
        }
    }

    public function down(): void
    {
        // Keep these shared taxonomy records on rollback: products may have
        // been assigned after migration, and the category itself is retained.
        // Bright's public copy is verified source data, so never restore the
        // old prototype description or placeholder contact details.
    }
};

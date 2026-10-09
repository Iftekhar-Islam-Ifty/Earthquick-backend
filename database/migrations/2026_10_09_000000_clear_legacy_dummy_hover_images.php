<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $legacyHoverPaths = [];
        foreach ([
            'saree' => ['saree', 6],
            'three-piece' => ['three-piece', 6],
            'two-piece' => ['two-piece', 4],
            'bags' => ['bag', 4],
        ] as $folder => [$prefix, $count]) {
            for ($number = 1; $number <= $count; $number++) {
                $legacyHoverPaths[] = sprintf('images/%s/%s-%02d-alt.jpg', $folder, $prefix, $number);
            }
        }

        // Preserve admin-uploaded hover images and every main/gallery image.
        DB::table('products')->whereIn('alt_image', $legacyHoverPaths)->update(['alt_image' => null]);
    }

    public function down(): void
    {
        // The removed dummy assets cannot be restored by rolling back the database.
    }
};

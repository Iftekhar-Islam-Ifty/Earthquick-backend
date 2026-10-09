<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class LegacyHoverCleanupTest extends TestCase
{
    use DatabaseTransactions;

    public function test_only_known_text_dummy_hover_paths_are_cleared(): void
    {
        $category = Category::firstOrFail();
        $legacy = Product::create([
            'name' => 'Legacy hover QA',
            'slug' => 'legacy-hover-'.Str::random(8),
            'sku' => 'LH-'.Str::upper(Str::random(8)),
            'category_id' => $category->id,
            'price' => 1000,
            'image' => 'images/saree/saree-01.jpg',
            'alt_image' => 'images/saree/saree-01-alt.jpg',
        ]);
        $custom = Product::create([
            'name' => 'Custom hover QA',
            'slug' => 'custom-hover-'.Str::random(8),
            'sku' => 'CH-'.Str::upper(Str::random(8)),
            'category_id' => $category->id,
            'price' => 1000,
            'image' => 'images/saree/saree-01.jpg',
            'alt_image' => 'images/products/custom-hover.webp',
        ]);

        $migration = require base_path('database/migrations/2026_10_09_000000_clear_legacy_dummy_hover_images.php');
        $migration->up();

        $this->assertNull($legacy->fresh()->alt_image);
        $this->assertSame('images/saree/saree-01.jpg', $legacy->fresh()->image);
        $this->assertSame('images/products/custom-hover.webp', $custom->fresh()->alt_image);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('saree-01-alt.jpg')
            ->assertDontSee('two-piece-01-alt.jpg');
    }
}

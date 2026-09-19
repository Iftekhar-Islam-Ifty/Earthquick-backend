<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CatalogVendorIntegrityTest extends TestCase
{
    public function test_inactive_vendor_products_are_hidden_from_public_catalogs_and_cart(): void
    {
        $product = Product::whereNotNull('vendor_id')->firstOrFail();
        $product->vendor->update(['is_active' => false]);

        $this->get('/')->assertDontSee($product->name);
        $this->get('/product/'.$product->slug)->assertNotFound();
        $this->get('/shop/'.$product->category->slug)->assertDontSee($product->name);
        $this->get('/search?q='.urlencode($product->name))->assertDontSee('/product/'.$product->slug);
        $this->getJson('/api/search/suggestions?q='.urlencode($product->name))
            ->assertJsonPath('count', 0);
        $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'This product is no longer available.');
    }

    public function test_legacy_unassigned_product_remains_public(): void
    {
        $product = Product::firstOrFail();
        $product->update(['vendor_id' => null]);

        $this->get('/product/'.$product->slug)->assertOk()->assertSee($product->name);
    }

    public function test_admin_cannot_assign_a_subcategory_from_another_category(): void
    {
        $product = Product::firstOrFail();
        $subcategory = Subcategory::firstOrFail();
        $otherCategory = Category::whereKeyNot($subcategory->category_id)->firstOrFail();
        $admin = User::create([
            'name' => 'Catalog QA Admin', 'email' => 'catalog-qa-admin@example.test',
            'password' => 'qa-test-password', 'is_admin' => true,
        ]);

        $this->actingAs($admin)->putJson('/admin/products/'.$product->id, [
            'name' => $product->name,
            'price' => $product->price,
            'category_id' => $otherCategory->id,
            'subcategory_id' => $subcategory->id,
        ])->assertUnprocessable()->assertJsonValidationErrors('subcategory_id');
    }

    public function test_vendor_admin_ui_has_no_commission_or_featured_vendor_controls(): void
    {
        $admin = User::create([
            'name' => 'Vendor QA Admin', 'email' => 'vendor-qa-admin@example.test',
            'password' => 'qa-test-password', 'is_admin' => true,
        ]);
        $vendor = Vendor::firstOrFail();

        $this->actingAs($admin)->get('/admin/vendors/create')
            ->assertOk()->assertDontSee('Commission')->assertDontSee('Homepage Collective');
        $this->actingAs($admin)->get('/admin/vendors/'.$vendor->id.'/edit')
            ->assertOk()->assertDontSee('Commission')->assertDontSee('Homepage Collective');
    }

    public function test_development_seed_preserves_electronics_and_assigns_legacy_catalog_to_nous_telos(): void
    {
        Artisan::call('db:seed', ['--force' => true]);

        $nousTelos = Vendor::where('slug', 'nous-telos')->firstOrFail();

        $this->assertDatabaseHas('vendors', ['slug' => 'bright', 'is_active' => true]);
        $this->assertDatabaseHas('categories', ['slug' => 'electronics', 'is_active' => true]);
        $this->assertSame(0, Product::whereNull('vendor_id')->count());
        $this->assertSame(0, Product::where('vendor_id', '!=', $nousTelos->id)->count());
    }
}

<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VendorStorefrontTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Helper to create an authenticated admin user.
     */
    protected function createAdminUser(): User
    {
        return User::create([
            'name' => 'Vendor Test Admin',
            'email' => 'vendoradmin'.rand(1000, 9999).'@earthquick.com',
            'phone' => '01711'.rand(100000, 999999),
            'password' => Hash::make('secret123'),
            'is_admin' => true,
        ]);
    }

    /**
     * Test public /stores directory lists all active partner brands.
     */
    public function test_public_stores_directory_renders_successfully(): void
    {
        $response = $this->get('/stores');

        $response->assertStatus(200);
        $response->assertSee('Partner Ateliers &amp; Brands', false);
        $response->assertSee('Nous Telos');
        $response->assertSee('Bright');
    }

    /**
     * Test single vendor storefront renders for Nous Telos with active products.
     */
    public function test_nous_telos_storefront_renders_with_catalog(): void
    {
        $response = $this->get('/stores/nous-telos');

        $response->assertStatus(200);
        $response->assertSee('Nous Telos');
        $response->assertSee('NT');
        $response->assertSee('Creations');
    }

    /**
     * Test Bright storefront renders with 0 products and an elegant empty catalog state.
     */
    public function test_bright_storefront_renders_with_curation_empty_state(): void
    {
        $response = $this->get('/stores/bright');

        $response->assertStatus(200);
        $response->assertSee('Bright');
        $response->assertSee('BRT');
        $response->assertSee('Atelier Collection in Preparation');
    }

    /**
     * Test inactive vendor storefront yields a 404 response.
     */
    public function test_inactive_vendor_storefront_returns_404(): void
    {
        $inactiveVendor = Vendor::create([
            'name' => 'Draft Studio '.rand(100, 999),
            'slug' => 'draft-studio-'.rand(100, 999),
            'vendor_code' => 'DS'.rand(10, 99),
            'is_active' => false,
        ]);

        $response = $this->get("/stores/{$inactiveVendor->slug}");
        $response->assertStatus(404);
    }

    /**
     * Test admin can view the vendor portfolio management screen.
     */
    public function test_admin_can_view_vendor_portfolio(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin/vendors');

        $response->assertStatus(200);
        $response->assertSee('Brand Partners &amp; Stores', false);
        $response->assertSee('Nous Telos');
        $response->assertSee('Bright');
    }

    /**
     * Test admin can onboard a new vendor partner.
     */
    public function test_admin_can_onboard_new_vendor(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post('/admin/vendors', [
            'name' => 'Loom Craft Artisan',
            'vendor_code' => 'LCA',
            'slug' => 'loom-craft-artisan',
            'tagline' => 'Authentic Handloom Textiles',
            'description' => 'Heirloom weaving heritage preserved by master craftspeople.',
            'email' => 'concierge@loomcraft.test',
            'phone' => '01811223344',
            'address' => 'Sonargaon, Narayanganj',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.vendors.index'));
        $this->assertDatabaseHas('vendors', [
            'name' => 'Loom Craft Artisan',
            'vendor_code' => 'LCA',
            'slug' => 'loom-craft-artisan',
            'is_active' => true,
        ]);
    }

    /**
     * Test admin can toggle vendor active status.
     */
    public function test_admin_can_toggle_vendor_active_status(): void
    {
        $admin = $this->createAdminUser();
        $vendor = Vendor::where('slug', 'bright')->firstOrFail();
        $initialStatus = $vendor->is_active;

        $response = $this->actingAs($admin)->post("/admin/vendors/{$vendor->id}/toggle");

        $response->assertRedirect();
        $this->assertDatabaseHas('vendors', [
            'id' => $vendor->id,
            'is_active' => ! $initialStatus,
        ]);

        // Revert status to restore test DB consistency
        $vendor->update(['is_active' => $initialStatus]);
    }

    /**
     * Test multi-vendor checkout tags order_items with product vendor_id.
     */
    public function test_checkout_tags_order_items_with_vendor_id(): void
    {
        $product = Product::whereNotNull('vendor_id')->firstOrFail();

        $cart = [
            $product->id.'_standard' => [
                'key' => $product->id.'_standard',
                'id' => $product->id,
                'product_id' => $product->id,
                'vendor_id' => $product->vendor_id,
                'vendor_name' => $product->vendor->name,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'image' => $product->image,
                'size' => 'Standard',
                'quantity' => 1,
            ],
        ];

        $response = $this->withSession(['cart' => $cart])->post('/checkout/order', [
            'customer_name' => 'Imtiaz Shovon',
            'customer_phone' => '01712345678',
            'customer_email' => 'shovon@test.com',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'Nasirabad',
            'address' => 'House 12, Road 4, GEC',
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect();
        $order = Order::where('customer_phone', '01712345678')->latest()->first();
        $this->assertNotNull($order);

        $orderItem = OrderItem::where('order_id', $order->id)->first();
        $this->assertNotNull($orderItem);
        $this->assertEquals($product->vendor_id, $orderItem->vendor_id);
    }
}

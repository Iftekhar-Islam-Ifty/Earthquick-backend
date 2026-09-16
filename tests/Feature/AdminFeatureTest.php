<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test guests are redirected to login when accessing admin dashboard.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    /**
     * Test regular non-admin customers are blocked with 403 Forbidden.
     */
    public function test_non_admin_customer_is_blocked_with_forbidden(): void
    {
        $customer = User::create([
            'name'     => 'Regular Customer',
            'email'    => 'regular' . rand(1000, 9999) . '@example.com',
            'phone'    => '01711' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => false,
        ]);

        $response = $this->actingAs($customer)->get('/admin');
        $response->assertStatus(403);
    }

    /**
     * Test authenticated admin can access dashboard with key metrics.
     */
    public function test_admin_can_view_dashboard_and_metrics(): void
    {
        $admin = User::create([
            'name'     => 'Chief Administrator',
            'email'    => 'admin' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01811' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Executive Store Dashboard');
        $response->assertSee('Total Orders');
        $response->assertSee('Net Sales Revenue');
    }

    /**
     * Test admin can view orders list and filter by status.
     */
    public function test_admin_can_view_orders_and_filter(): void
    {
        $admin = User::create([
            'name'     => 'Order Manager',
            'email'    => 'orders' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01911' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $order = Order::create([
            'order_number'   => 'EQ-TEST-' . rand(1000, 9999),
            'customer_name'  => 'Shamsur Rahman',
            'customer_phone' => '01799000000',
            'delivery_zone'  => 'inside_ctg',
            'district'       => 'Chattogram',
            'area'           => 'Nasirabad',
            'address'        => 'Road 3, House 15',
            'payment_method' => 'cod',
            'subtotal'       => 4500,
            'delivery_fee'   => 80,
            'total'          => 4580,
            'status'         => 'pending',
        ]);

        $response = $this->actingAs($admin)->get('/admin/orders');
        $response->assertStatus(200);
        $response->assertSee($order->order_number);
        $response->assertSee('Shamsur Rahman');

        // Test filter by pending
        $responseFiltered = $this->actingAs($admin)->get('/admin/orders?status=pending');
        $responseFiltered->assertStatus(200);
        $responseFiltered->assertSee($order->order_number);
    }

    /**
     * Test admin can update order status and it reflects in database.
     */
    public function test_admin_can_update_order_status(): void
    {
        $admin = User::create([
            'name'     => 'Dispatch Lead',
            'email'    => 'dispatch' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01511' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $order = Order::create([
            'order_number'   => 'EQ-STATUS-' . rand(1000, 9999),
            'customer_name'  => 'Jasim Uddin',
            'customer_phone' => '01611000000',
            'delivery_zone'  => 'outside_ctg',
            'district'       => 'Dhaka',
            'area'           => 'Dhanmondi',
            'address'        => 'Road 27, House 4',
            'payment_method' => 'cod',
            'subtotal'       => 8500,
            'delivery_fee'   => 150,
            'total'          => 8650,
            'status'         => 'pending',
        ]);

        // Update status to processing
        $response = $this->actingAs($admin)->post("/admin/orders/{$order->id}/status", [
            'status' => 'processing',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'processing',
        ]);

        // Next update status to in_transit
        $this->actingAs($admin)->post("/admin/orders/{$order->id}/status", [
            'status' => 'in_transit',
        ]);

        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'in_transit',
        ]);
    }

    /**
     * Test admin can view products list and toggle in_stock status.
     */
    public function test_admin_can_toggle_product_stock(): void
    {
        $admin = User::create([
            'name'     => 'Inventory Head',
            'email'    => 'stock' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01311' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $category = Category::first();
        $product = Product::create([
            'category_id'    => $category->id,
            'name'           => 'Handloom Silk Kurti',
            'slug'           => 'handloom-silk-kurti-' . rand(100, 999),
            'price'          => 3200,
            'image'          => 'images/products/test.jpg',
            'in_stock'       => true,
            'stock_quantity' => 5,
        ]);

        // View products list
        $response = $this->actingAs($admin)->get('/admin/products');
        $response->assertStatus(200);
        $response->assertSee('Handloom Silk Kurti');

        // Toggle stock to false
        $toggleResponse = $this->actingAs($admin)->post("/admin/products/{$product->id}/toggle-stock");
        $toggleResponse->assertRedirect();

        $this->assertDatabaseHas('products', [
            'id'       => $product->id,
            'in_stock' => false,
        ]);

        // Toggle stock back to true
        $this->actingAs($admin)->post("/admin/products/{$product->id}/toggle-stock");

        $this->assertDatabaseHas('products', [
            'id'       => $product->id,
            'in_stock' => true,
        ]);
    }

    /**
     * Test admin login automatically redirects to /admin dashboard.
     */
    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::create([
            'name'     => 'Auto Redirect Admin',
            'email'    => 'autoadmin' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01722' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $response = $this->post('/login', [
            'identifier' => $admin->email,
            'password'   => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Test navbar displays Admin Panel button for admin, and hides for customer.
     */
    public function test_navbar_displays_admin_panel_button_for_admin(): void
    {
        $admin = User::create([
            'name'     => 'Navbar Admin',
            'email'    => 'navbaradmin' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01733' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $customer = User::create([
            'name'     => 'Navbar Customer',
            'email'    => 'navbarcust' . rand(1000, 9999) . '@example.com',
            'phone'    => '01744' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => false,
        ]);

        // Admin visiting homepage sees Admin Panel button
        $adminResponse = $this->actingAs($admin)->get('/');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Admin Panel');
        $adminResponse->assertSee(route('admin.dashboard'));

        // Customer visiting homepage does NOT see Admin Panel button
        $customerResponse = $this->actingAs($customer)->get('/');
        $customerResponse->assertStatus(200);
        $customerResponse->assertDontSee(route('admin.dashboard'));
    }

    /**
     * Test admin can view product creation page.
     */
    public function test_admin_can_view_product_create_page(): void
    {
        $admin = User::create([
            'name'     => 'Product Creator Admin',
            'email'    => 'prodcreate' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01755' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/products/create');
        $response->assertStatus(200);
        $response->assertSee('Create New Product');
        $response->assertSee('Selling Price (BDT ৳)');
    }

    /**
     * Test admin can create a new product with image upload.
     */
    public function test_admin_can_create_product_with_image_upload(): void
    {
        $admin = User::create([
            'name'     => 'Catalog Uploader Admin',
            'email'    => 'uploader' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01766' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $category = Category::first();
        $jpegBytes = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        $fakeImage = UploadedFile::fake()->createWithContent('jamdani_test.jpg', $jpegBytes);

        $response = $this->actingAs($admin)->post('/admin/products', [
            'name'           => 'Heritage Jamdani Gold Zari',
            'category_id'    => $category->id,
            'price'          => 14500,
            'old_price'      => 16000,
            'fabric'         => 'Pure Silk',
            'stock_quantity' => 8,
            'in_stock'       => 1,
            'is_featured'    => 1,
            'is_new_arrival' => 1,
            'badge'          => 'Atelier Pick',
            'short_desc'     => 'Exquisite heirloom zari weaving.',
            'description'    => 'Detailed handcrafted jamdani created by master artisans.',
            'image'          => $fakeImage,
        ]);

        $response->assertRedirect(route('admin.products'));
        $this->assertDatabaseHas('products', [
            'name'        => 'Heritage Jamdani Gold Zari',
            'category_id' => $category->id,
            'price'       => 14500,
            'fabric'      => 'Pure Silk',
            'in_stock'    => true,
            'is_featured' => true,
        ]);

        $product = Product::where('name', 'Heritage Jamdani Gold Zari')->first();
        $this->assertNotNull($product);
        $this->assertStringStartsWith('images/products/', $product->image);

        // Clean up uploaded test image
        if (file_exists(public_path($product->image))) {
            @unlink(public_path($product->image));
        }
    }

    /**
     * Test admin can view product edit page.
     */
    public function test_admin_can_view_product_edit_page(): void
    {
        $admin = User::create([
            'name'     => 'Editor Admin',
            'email'    => 'editor' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01777' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $product = Product::first();

        $response = $this->actingAs($admin)->get("/admin/products/{$product->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Product:');
        $response->assertSee(e($product->name));
    }

    /**
     * Test admin can update a product.
     */
    public function test_admin_can_update_product(): void
    {
        $admin = User::create([
            'name'     => 'Updater Admin',
            'email'    => 'updater' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01788' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $category = Category::first();
        $product = Product::create([
            'name'           => 'Temporary Update Test Item',
            'slug'           => 'temp-update-test-' . rand(100, 999),
            'sku'            => 'NT-TST-' . rand(100, 999),
            'category_id'    => $category->id,
            'price'          => 4000,
            'fabric'         => 'Cotton',
            'image'          => 'images/products/test.jpg',
            'in_stock'       => true,
            'stock_quantity' => 10,
        ]);

        $response = $this->actingAs($admin)->put("/admin/products/{$product->id}", [
            'name'           => 'Updated Atelier Masterpiece',
            'category_id'    => $category->id,
            'price'          => 5500,
            'old_price'      => 6000,
            'fabric'         => 'Pure Linen',
            'stock_quantity' => 15,
            'in_stock'       => 1,
            'is_featured'    => 1,
            'badge'          => 'Revised Edition',
        ]);

        $response->assertRedirect(route('admin.products'));
        $this->assertDatabaseHas('products', [
            'id'          => $product->id,
            'name'        => 'Updated Atelier Masterpiece',
            'price'       => 5500,
            'fabric'      => 'Pure Linen',
            'is_featured' => true,
        ]);
    }

    /**
     * Test admin can delete a product.
     */
    public function test_admin_can_delete_product(): void
    {
        $admin = User::create([
            'name'     => 'Deletion Admin',
            'email'    => 'deleter' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01799' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $category = Category::first();
        $product = Product::create([
            'name'           => 'To Be Deleted Saree',
            'slug'           => 'to-be-deleted-saree-' . rand(100, 999),
            'sku'            => 'NT-DEL-' . rand(100, 999),
            'category_id'    => $category->id,
            'price'          => 3500,
            'fabric'         => 'Mulmul',
            'image'          => 'images/products/test-delete.jpg',
            'in_stock'       => true,
            'stock_quantity' => 5,
        ]);

        $productId = $product->id;

        $response = $this->actingAs($admin)->delete("/admin/products/{$productId}");
        $response->assertRedirect(route('admin.products'));

        $this->assertDatabaseMissing('products', [
            'id' => $productId,
        ]);
    }

    /**
     * Test admin can search orders by phone, order number, or customer name.
     */
    public function test_admin_can_search_orders_by_phone_or_order_number(): void
    {
        $admin = User::create([
            'name'     => 'Order Searcher',
            'email'    => 'searcher' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01722' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $uniqueNum = 'EQ-SRCH-' . rand(10000, 99999);
        $uniquePhone = '01899' . rand(100000, 999999);

        $order = Order::create([
            'order_number'   => $uniqueNum,
            'customer_name'  => 'Syed Waliullah',
            'customer_phone' => $uniquePhone,
            'delivery_zone'  => 'inside_ctg',
            'district'       => 'Chattogram',
            'area'           => 'Nasirabad',
            'address'        => 'Lane 4, Plot 12',
            'payment_method' => 'cod',
            'subtotal'       => 4500,
            'delivery_fee'   => 80,
            'total'          => 4580,
            'status'         => 'pending',
        ]);

        // Search by unique order number
        $responseOrderNum = $this->actingAs($admin)->get("/admin/orders?search={$uniqueNum}");
        $responseOrderNum->assertStatus(200);
        $responseOrderNum->assertSee($uniqueNum);
        $responseOrderNum->assertSee('Syed Waliullah');

        // Search by unique customer phone
        $responsePhone = $this->actingAs($admin)->get("/admin/orders?search={$uniquePhone}");
        $responsePhone->assertStatus(200);
        $responsePhone->assertSee($uniqueNum);

        // Search by nonexistent term
        $responseNone = $this->actingAs($admin)->get('/admin/orders?search=NONEXISTENT_ORDER_99999');
        $responseNone->assertStatus(200);
        $responseNone->assertDontSee($uniqueNum);
    }

    /**
     * Test admin can update order with courier name, tracking number, and admin notes.
     */
    public function test_admin_can_update_order_with_courier_and_tracking(): void
    {
        $admin = User::create([
            'name'     => 'Courier Dispatcher',
            'email'    => 'dispatcher' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01633' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $order = Order::create([
            'order_number'   => 'EQ-COUR-' . rand(1000, 9999),
            'customer_name'  => 'Rokeya Sakhawat',
            'customer_phone' => '01711223344',
            'delivery_zone'  => 'outside_ctg',
            'district'       => 'Rajshahi',
            'area'           => 'Shaheb Bazar',
            'address'        => 'Ghoramara',
            'payment_method' => 'cod',
            'subtotal'       => 6200,
            'delivery_fee'   => 150,
            'total'          => 6350,
            'status'         => 'processing',
        ]);

        $response = $this->actingAs($admin)->post("/admin/orders/{$order->id}/status", [
            'status'          => 'in_transit',
            'courier_name'    => 'Steadfast Courier',
            'tracking_number' => 'SF-TEST-8899',
            'admin_notes'     => 'Packed in signature luxury silk gift box.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id'              => $order->id,
            'status'          => 'in_transit',
            'courier_name'    => 'Steadfast Courier',
            'tracking_number' => 'SF-TEST-8899',
            'admin_notes'     => 'Packed in signature luxury silk gift box.',
        ]);
    }

    /**
     * Test admin can view printable order packing slip and invoice.
     */
    public function test_admin_can_view_order_invoice(): void
    {
        $admin = User::create([
            'name'     => 'Billing Specialist',
            'email'    => 'billing' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01844' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $order = Order::create([
            'order_number'   => 'EQ-INV-' . rand(1000, 9999),
            'customer_name'  => 'Kazi Nazrul',
            'customer_phone' => '01955667788',
            'customer_email' => 'nazrul@example.com',
            'delivery_zone'  => 'inside_ctg',
            'district'       => 'Chattogram',
            'area'           => 'Khulshi',
            'address'        => 'Road 1, Hill View',
            'payment_method' => 'cod',
            'subtotal'       => 5000,
            'delivery_fee'   => 80,
            'total'          => 5080,
            'status'         => 'in_transit',
            'courier_name'   => 'Pathao Courier',
            'tracking_number'=> 'PT-998811',
        ]);

        $product = Product::first();

        OrderItem::create([
            'order_id'      => $order->id,
            'product_id'    => $product->id,
            'product_name'  => 'Atelier Silk Panjabi',
            'size'          => '42',
            'unit_price'    => 5000,
            'quantity'      => 1,
            'total_price'   => 5000,
            'product_image' => 'images/products/placeholder.jpg',
        ]);

        $response = $this->actingAs($admin)->get("/admin/orders/{$order->id}/invoice");
        $response->assertStatus(200);
        $response->assertSee('PACKING SLIP &amp; INVOICE', false);
        $response->assertSee($order->order_number);
        $response->assertSee('Kazi Nazrul');
        $response->assertSee('Pathao Courier');
        $response->assertSee('PT-998811');
        $response->assertSee('Atelier Silk Panjabi');
    }

    /**
     * Test admin dashboard calculates and renders executive analytics, charts, and top products.
     */
    public function test_admin_dashboard_renders_analytics_and_charts(): void
    {
        $admin = User::create([
            'name'     => 'Analytics Lead',
            'email'    => 'analytics' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01777' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $product = Product::first();
        $order = Order::create([
            'order_number'   => 'EQ-ANALYTICS-' . rand(1000, 9999),
            'customer_name'  => 'Tareq Masud',
            'customer_phone' => '01811223344',
            'delivery_zone'  => 'inside_ctg',
            'district'       => 'Chattogram',
            'area'           => 'Panchlaish',
            'address'        => 'House 12, Road 4',
            'payment_method' => 'cod',
            'subtotal'       => 7200,
            'delivery_fee'   => 80,
            'total'          => 7280,
            'status'         => 'delivered',
        ]);

        OrderItem::create([
            'order_id'      => $order->id,
            'product_id'    => $product->id,
            'product_name'  => $product->name,
            'unit_price'    => 3600,
            'quantity'      => 2,
            'total_price'   => 7200,
            'product_image' => $product->image,
        ]);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Executive Performance &amp; Analytics', false);
        $response->assertSee('revenueTrendChart');
        $response->assertSee('categorySalesChart');
        $response->assertSee('Top Performing Creations');
        $response->assertSee('Export Orders (CSV)');
        $response->assertSee($product->name);
    }

    /**
     * Test admin can stream export of orders ledger as CSV.
     */
    public function test_admin_can_export_orders_csv(): void
    {
        $admin = User::create([
            'name'     => 'Export Auditor',
            'email'    => 'export' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01633' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $order = Order::create([
            'order_number'   => 'EQ-CSV-' . rand(1000, 9999),
            'customer_name'  => 'Begum Rokeya',
            'customer_phone' => '01577889900',
            'customer_email' => 'rokeya@example.com',
            'delivery_zone'  => 'outside_ctg',
            'district'       => 'Rangpur',
            'area'           => 'Pairaband',
            'address'        => 'Atelier Manor',
            'payment_method' => 'cod',
            'subtotal'       => 12000,
            'delivery_fee'   => 150,
            'total'          => 12150,
            'status'         => 'confirmed',
            'courier_name'   => 'Steadfast Courier',
            'tracking_number'=> 'SF-CSV-12345',
        ]);

        $response = $this->actingAs($admin)->get('/admin/orders/export');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('attachment; filename="earthquick_sales_orders_', (string) $response->headers->get('content-disposition'));

        // Capture streamed content
        $streamedContent = $response->streamedContent();
        $this->assertStringContainsString('Order Number', $streamedContent);
        $this->assertStringContainsString($order->order_number, $streamedContent);
        $this->assertStringContainsString('Begum Rokeya', $streamedContent);
        $this->assertStringContainsString('SF-CSV-12345', $streamedContent);
    }

    /**
     * Test admin can export filtered orders by status.
     */
    public function test_admin_can_export_filtered_orders_csv(): void
    {
        $admin = User::create([
            'name'     => 'Filtered Auditor',
            'email'    => 'filter' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01988' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $deliveredOrder = Order::create([
            'order_number'   => 'EQ-DELIVERED-' . rand(1000, 9999),
            'customer_name'  => 'Delivered Client',
            'customer_phone' => '01711999888',
            'delivery_zone'  => 'inside_ctg',
            'district'       => 'Chattogram',
            'area'           => 'Agrabad',
            'address'        => 'Commercial Area',
            'payment_method' => 'cod',
            'subtotal'       => 4000,
            'delivery_fee'   => 80,
            'total'          => 4080,
            'status'         => 'delivered',
        ]);

        $pendingOrder = Order::create([
            'order_number'   => 'EQ-PENDING-' . rand(1000, 9999),
            'customer_name'  => 'Pending Client',
            'customer_phone' => '01722999888',
            'delivery_zone'  => 'inside_ctg',
            'district'       => 'Chattogram',
            'area'           => 'Nasirabad',
            'address'        => 'Housing Society',
            'payment_method' => 'cod',
            'subtotal'       => 3000,
            'delivery_fee'   => 80,
            'total'          => 3080,
            'status'         => 'pending',
        ]);

        $response = $this->actingAs($admin)->get('/admin/orders/export?status=delivered');
        $response->assertStatus(200);

        $csv = $response->streamedContent();
        $this->assertStringContainsString($deliveredOrder->order_number, $csv);
        $this->assertStringNotContainsString($pendingOrder->order_number, $csv);
    }

    /**
     * Test admin can view coupons list page and KPI metrics.
     */
    public function test_admin_can_view_coupons_list(): void
    {
        $admin = User::create([
            'name'     => 'Coupon Admin',
            'email'    => 'couponadmin' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01711' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $coupon = Coupon::create([
            'code'             => 'TESTEID' . rand(10, 99),
            'name'             => 'Eid Festive Special',
            'type'             => 'percent',
            'value'            => 15.00,
            'min_order_amount' => 2000,
            'is_active'        => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/coupons');
        $response->assertStatus(200);
        $response->assertSee($coupon->code);
        $response->assertSee('Eid Festive Special');
    }

    /**
     * Test admin can create a new promo campaign coupon.
     */
    public function test_admin_can_create_coupon(): void
    {
        $admin = User::create([
            'name'     => 'Campaign Creator',
            'email'    => 'creator' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01822' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $code = 'PUJA' . rand(100, 999);

        $response = $this->actingAs($admin)->post('/admin/coupons', [
            'code'             => $code,
            'name'             => 'Durga Puja Celebrations',
            'type'             => 'fixed',
            'value'            => 500,
            'min_order_amount' => 3000,
            'usage_limit'      => 50,
            'is_active'        => 1,
        ]);

        $response->assertRedirect(route('admin.coupons'));
        $this->assertDatabaseHas('coupons', [
            'code'  => $code,
            'name'  => 'Durga Puja Celebrations',
            'type'  => 'fixed',
            'value' => 500,
        ]);
    }

    /**
     * Test admin can toggle coupon active status.
     */
    public function test_admin_can_toggle_coupon_status(): void
    {
        $admin = User::create([
            'name'     => 'Toggle Admin',
            'email'    => 'toggle' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01833' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $coupon = Coupon::create([
            'code'      => 'TOGGLE' . rand(10, 99),
            'type'      => 'percent',
            'value'     => 10.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post("/admin/coupons/{$coupon->id}/toggle");
        $response->assertRedirect(route('admin.coupons'));

        $this->assertDatabaseHas('coupons', [
            'id'        => $coupon->id,
            'is_active' => false,
        ]);
    }

    /**
     * Test admin can delete a coupon.
     */
    public function test_admin_can_delete_coupon(): void
    {
        $admin = User::create([
            'name'     => 'Delete Coupon Admin',
            'email'    => 'delcoupon' . rand(1000, 9999) . '@earthquick.com',
            'phone'    => '01844' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $coupon = Coupon::create([
            'code'      => 'DEL' . rand(100, 999),
            'type'      => 'fixed',
            'value'     => 200,
            'is_active' => true,
        ]);

        $couponId = $coupon->id;

        $response = $this->actingAs($admin)->delete("/admin/coupons/{$couponId}");
        $response->assertRedirect(route('admin.coupons'));

        $this->assertDatabaseMissing('coupons', [
            'id' => $couponId,
        ]);
    }
}


<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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
        $response->assertSee('Atelier Executive Dashboard');
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
}


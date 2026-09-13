<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAuthAndAccountTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test registration form view renders.
     */
    public function test_register_page_renders_successfully(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Create Account');
    }

    /**
     * Test customer registration with valid credentials.
     */
    public function test_customer_can_register_and_auto_login(): void
    {
        $payload = [
            'name'                  => 'Kazi Nazrul',
            'email'                 => 'kazi.nazrul' . rand(1000, 9999) . '@example.com',
            'phone'                 => '01712' . rand(100000, 999999),
            'city'                  => 'Chattogram',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'terms'                 => '1',
        ];

        $response = $this->post('/register', $payload);

        $response->assertRedirect('/account');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name'  => 'Kazi Nazrul',
            'email' => $payload['email'],
            'phone' => $payload['phone'],
            'city'  => 'Chattogram',
        ]);
    }

    /**
     * Test login page view renders.
     */
    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Welcome Back');
    }

    /**
     * Test customer can login with email.
     */
    public function test_customer_can_login_with_email(): void
    {
        $user = User::create([
            'name'     => 'Taslima Nasrin',
            'email'    => 'taslima' . rand(1000, 9999) . '@example.com',
            'phone'    => '01819' . rand(100000, 999999),
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'identifier' => $user->email,
            'password'   => 'password123',
        ]);

        $response->assertRedirect('/account');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test customer can login with phone number.
     */
    public function test_customer_can_login_with_phone(): void
    {
        $phone = '01911' . rand(100000, 999999);
        $user = User::create([
            'name'     => 'Humayun Ahmed',
            'email'    => 'humayun' . rand(1000, 9999) . '@example.com',
            'phone'    => $phone,
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'identifier' => $phone,
            'password'   => 'password123',
        ]);

        $response->assertRedirect('/account');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test guest cannot access /account and is redirected to /login.
     */
    public function test_guest_is_redirected_to_login_when_accessing_account(): void
    {
        $response = $this->get('/account');
        $response->assertRedirect('/login');
    }

    /**
     * Test authenticated customer can view /account dashboard.
     */
    public function test_authenticated_customer_can_view_account(): void
    {
        $user = User::create([
            'name'     => 'Rokeya Sakhawat',
            'email'    => 'rokeya' . rand(1000, 9999) . '@example.com',
            'phone'    => '01611' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'city'     => 'Chattogram',
        ]);

        $response = $this->actingAs($user)->get('/account');
        $response->assertStatus(200);
        $response->assertSee('Rokeya Sakhawat');
        $response->assertSee('Order History');
    }

    /**
     * Test customer can update their profile information.
     */
    public function test_customer_can_update_profile(): void
    {
        $user = User::create([
            'name'     => 'Old Name',
            'email'    => 'old' . rand(1000, 9999) . '@example.com',
            'phone'    => '01511' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'city'     => 'Dhaka',
        ]);

        $response = $this->actingAs($user)->post('/account/profile/update', [
            'name'    => 'Updated Name',
            'email'   => $user->email,
            'phone'   => '01799' . rand(100000, 999999),
            'city'    => 'Chattogram',
            'area'    => 'GEC Circle',
            'address' => 'House 12, Road 4, O.R. Nizam Road',
        ]);

        $response->assertRedirect('/account');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id'      => $user->id,
            'name'    => 'Updated Name',
            'city'    => 'Chattogram',
            'area'    => 'GEC Circle',
            'address' => 'House 12, Road 4, O.R. Nizam Road',
        ]);
    }

    /**
     * Test checkout links user_id when customer is logged in.
     */
    public function test_checkout_links_user_id_when_customer_authenticated(): void
    {
        $product = Product::first();
        $this->assertNotNull($product);

        $user = User::create([
            'name'     => 'Zahir Raihan',
            'email'    => 'zahir' . rand(1000, 9999) . '@example.com',
            'phone'    => '01311' . rand(100000, 999999),
            'password' => Hash::make('password123'),
        ]);

        // Put item in session cart
        $cart = [
            $product->id . '_Standard' => [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => (float) $product->price,
                'quantity' => 1,
                'size'     => 'Standard',
                'color'    => null,
                'image'    => $product->image,
            ],
        ];

        $checkoutData = [
            'customer_name'  => 'Zahir Raihan',
            'customer_email' => $user->email,
            'customer_phone' => '01311000000',
            'delivery_zone'  => 'inside_ctg',
            'district'       => 'Chattogram',
            'area'           => 'Agrabad',
            'address'        => 'Commercial Area, House 4',
            'payment_method' => 'cod',
        ];

        $response = $this->actingAs($user)
            ->withSession(['cart' => $cart])
            ->post('/checkout/order', $checkoutData);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id'        => $user->id,
            'customer_phone' => '01311000000',
            'district'       => 'Chattogram',
        ]);

        $order = Order::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals($user->id, $order->user_id);
    }

    /**
     * Test logout terminates session.
     */
    public function test_customer_can_logout(): void
    {
        $user = User::create([
            'name'     => 'Logout User',
            'email'    => 'logout' . rand(1000, 9999) . '@example.com',
            'phone'    => '01700' . rand(100000, 999999),
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}

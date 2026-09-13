<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CartAndCheckoutTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test cart page renders successfully.
     */
    public function test_cart_page_renders_successfully(): void
    {
        $response = $this->get('/cart');
        $response->assertStatus(200);
    }

    /**
     * Test adding product to cart via AJAX returns JSON.
     */
    public function test_add_product_to_cart_ajax(): void
    {
        $product = Product::first();
        $this->assertNotNull($product, 'A product must exist in database.');

        $response = $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity'   => 2,
            'size'       => 'Standard',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count'   => 2,
            ]);

        $this->assertTrue(session()->has('cart'));
    }

    /**
     * Test updating cart item quantity via AJAX.
     */
    public function test_update_cart_quantity_ajax(): void
    {
        $product = Product::first();
        $key = $product->id . '_standard';

        $cart = [
            $key => [
                'key'        => $key,
                'id'         => $product->id,
                'product_id' => $product->id,
                'name'       => $product->name,
                'slug'       => $product->slug,
                'price'      => (float) $product->price,
                'image'      => $product->image,
                'size'       => 'Standard',
                'quantity'   => 2,
            ]
        ];

        session(['cart' => $cart]);

        $response = $this->postJson('/cart/update', [
            'key'   => $key,
            'delta' => 1,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count'   => 3,
            ]);
    }

    /**
     * Test removing item from cart.
     */
    public function test_remove_item_from_cart_ajax(): void
    {
        $product = Product::first();
        $key = $product->id . '_standard';

        $cart = [
            $key => [
                'key'        => $key,
                'id'         => $product->id,
                'product_id' => $product->id,
                'name'       => $product->name,
                'slug'       => $product->slug,
                'price'      => (float) $product->price,
                'image'      => $product->image,
                'size'       => 'Standard',
                'quantity'   => 1,
            ]
        ];

        session(['cart' => $cart]);

        $response = $this->postJson('/cart/remove', [
            'key' => $key,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count'   => 0,
            ]);
    }

    /**
     * Test clearing the cart.
     */
    public function test_clear_cart_ajax(): void
    {
        $product = Product::first();
        $key = $product->id . '_standard';

        session(['cart' => [$key => ['quantity' => 2, 'price' => 1000]]]);

        $response = $this->postJson('/cart/clear');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count'   => 0,
            ]);
    }

    /**
     * Test checkout page view with session cart items.
     */
    public function test_checkout_page_renders_with_session_cart(): void
    {
        $product = Product::first();
        $key = $product->id . '_standard';

        session(['cart' => [
            $key => [
                'key'        => $key,
                'id'         => $product->id,
                'product_id' => $product->id,
                'name'       => $product->name,
                'slug'       => $product->slug,
                'price'      => (float) $product->price,
                'image'      => $product->image,
                'size'       => 'Standard',
                'quantity'   => 1,
            ]
        ]]);

        $response = $this->get('/checkout');
        $response->assertStatus(200);
        $response->assertSee('Customer Contact Details');
        $response->assertSee('Delivery Address &amp; Shipping', false);
    }

    /**
     * Test order placement validation fails on invalid BD phone number.
     */
    public function test_order_placement_rejects_invalid_phone(): void
    {
        $product = Product::first();
        $key = $product->id . '_standard';
        session(['cart' => [
            $key => [
                'key'        => $key,
                'id'         => $product->id,
                'product_id' => $product->id,
                'name'       => $product->name,
                'slug'       => $product->slug,
                'price'      => (float) $product->price,
                'image'      => $product->image,
                'size'       => 'Standard',
                'quantity'   => 1,
            ]
        ]]);

        $response = $this->post('/checkout/order', [
            'customer_name'  => 'Test Invalid Phone',
            'customer_phone' => '12345', // Invalid BD phone
            'delivery_zone'  => 'inside_ctg',
            'district'       => 'Chattogram',
            'area'           => 'GEC',
            'address'        => 'Test Address',
            'payment_method' => 'cod',
        ]);

        $response->assertSessionHasErrors(['customer_phone']);
    }

    /**
     * Test successful order placement and creation in DB.
     */
    public function test_successful_order_placement_in_db(): void
    {
        $product = Product::first();
        $key = $product->id . '_standard';
        $itemPrice = (float) $product->price;
        $quantity = 2;
        $subtotal = $itemPrice * $quantity;
        $deliveryFee = 80.00; // inside_ctg
        $expectedTotal = $subtotal + $deliveryFee;

        session(['cart' => [
            $key => [
                'key'        => $key,
                'id'         => $product->id,
                'product_id' => $product->id,
                'name'       => $product->name,
                'slug'       => $product->slug,
                'price'      => $itemPrice,
                'image'      => $product->image,
                'size'       => 'Standard',
                'quantity'   => $quantity,
            ]
        ]]);

        $response = $this->post('/checkout/order', [
            'customer_name'  => 'Iftekhar Islam Ifty',
            'customer_phone' => '01793123456',
            'customer_email' => 'ifty@earthquick.com',
            'delivery_zone'  => 'inside_ctg',
            'district'       => 'Chattogram',
            'area'           => 'Nasirabad',
            'address'        => 'House 42, Road 3, Nasirabad Housing Society',
            'order_notes'    => 'Ring doorbell twice on arrival',
            'payment_method' => 'cod',
        ]);

        // Expect redirect to success page
        $response->assertStatus(302);
        $targetUrl = $response->headers->get('Location');
        $this->assertStringContainsString('/checkout/success/EQ-', $targetUrl);

        // Extract order number
        preg_match('#/checkout/success/(EQ-[^/]+)#', $targetUrl, $matches);
        $orderNumber = $matches[1];

        // Assert Order saved in database
        $this->assertDatabaseHas('orders', [
            'order_number'    => $orderNumber,
            'customer_name'   => 'Iftekhar Islam Ifty',
            'customer_phone'  => '01793123456',
            'delivery_zone'   => 'inside_ctg',
            'delivery_fee'    => $deliveryFee,
            'subtotal'        => $subtotal,
            'total'           => $expectedTotal,
            'status'          => 'pending',
        ]);

        // Assert Order Items saved in database
        $this->assertDatabaseHas('order_items', [
            'product_id'  => $product->id,
            'unit_price'  => $itemPrice,
            'quantity'    => $quantity,
            'total_price' => $subtotal,
        ]);

        // Assert cart was emptied from session
        $this->assertFalse(session()->has('cart'));

        // Assert confirmation receipt page displays order number and 200 OK
        $successResponse = $this->get('/checkout/success/' . $orderNumber);
        $successResponse->assertStatus(200);
        $successResponse->assertSee($orderNumber);
        $successResponse->assertSee('Iftekhar Islam Ifty');
        $successResponse->assertSee('01793123456');
    }
}


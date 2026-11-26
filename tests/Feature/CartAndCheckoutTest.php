<?php

namespace Tests\Feature;

use App\Http\Controllers\CartController;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
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
            'quantity' => 2,
            'size' => 'Standard',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count' => 2,
            ]);

        $this->assertTrue(session()->has('cart'));
    }

    /**
     * Test updating cart item quantity via AJAX.
     */
    public function test_update_cart_quantity_ajax(): void
    {
        $product = Product::first();
        $key = $product->id.'_standard';

        $cart = [
            $key => [
                'key' => $key,
                'id' => $product->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'image' => $product->image,
                'size' => 'Standard',
                'quantity' => 2,
            ],
        ];

        session(['cart' => $cart]);

        $response = $this->postJson('/cart/update', [
            'key' => $key,
            'delta' => 1,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count' => 3,
            ]);
    }

    /**
     * Test removing item from cart.
     */
    public function test_remove_item_from_cart_ajax(): void
    {
        $product = Product::first();
        $key = $product->id.'_standard';

        $cart = [
            $key => [
                'key' => $key,
                'id' => $product->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'image' => $product->image,
                'size' => 'Standard',
                'quantity' => 1,
            ],
        ];

        session(['cart' => $cart]);

        $response = $this->postJson('/cart/remove', [
            'key' => $key,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count' => 0,
            ]);
    }

    /**
     * Test clearing the cart.
     */
    public function test_clear_cart_ajax(): void
    {
        $product = Product::first();
        $key = $product->id.'_standard';

        session(['cart' => [$key => ['quantity' => 2, 'price' => 1000]]]);

        $response = $this->postJson('/cart/clear');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count' => 0,
            ]);
    }

    /**
     * Test checkout page view with session cart items.
     */
    public function test_checkout_page_renders_with_session_cart(): void
    {
        $product = Product::first();
        $key = $product->id.'_standard';

        session(['cart' => [
            $key => [
                'key' => $key,
                'id' => $product->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'image' => $product->image,
                'size' => 'Standard',
                'quantity' => 1,
            ],
        ]]);

        $response = $this->get('/checkout');
        $response->assertStatus(200);
        $response->assertSee('Customer Contact Details');
        $response->assertSee('Delivery Address &amp; Shipping', false);
        $response->assertSee(route('policies.delivery-returns').'#delivery', false);
        $response->assertDontSee('Within 48 hours');
        $response->assertDontSee('Courier in 2–4 days');
    }

    /**
     * Test order placement validation fails on invalid BD phone number.
     */
    public function test_order_placement_rejects_invalid_phone(): void
    {
        $product = Product::first();
        $key = $product->id.'_standard';
        session(['cart' => [
            $key => [
                'key' => $key,
                'id' => $product->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'image' => $product->image,
                'size' => 'Standard',
                'quantity' => 1,
            ],
        ]]);

        $response = $this->post('/checkout/order', [
            'customer_name' => 'Test Invalid Phone',
            'customer_phone' => '12345', // Invalid BD phone
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'GEC',
            'address' => 'Test Address',
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
        $key = $product->id.'_standard';
        $itemPrice = (float) $product->price;
        $quantity = 2;
        $subtotal = $itemPrice * $quantity;
        $deliveryFee = $subtotal >= CartController::FREE_SHIPPING_THRESHOLD ? 0.00 : 80.00;
        $expectedTotal = $subtotal + $deliveryFee;

        session(['cart' => [
            $key => [
                'key' => $key,
                'id' => $product->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $itemPrice,
                'image' => $product->image,
                'size' => 'Standard',
                'quantity' => $quantity,
            ],
        ]]);

        $response = $this->post('/checkout/order', [
            'customer_name' => 'Iftekhar Islam Ifty',
            'customer_phone' => '01793123456',
            'customer_email' => 'ifty@rthquick.com',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'Nasirabad',
            'address' => 'House 42, Road 3, Nasirabad Housing Society',
            'order_notes' => 'Ring doorbell twice on arrival',
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
            'order_number' => $orderNumber,
            'customer_name' => 'Iftekhar Islam Ifty',
            'customer_phone' => '01793123456',
            'delivery_zone' => 'inside_ctg',
            'delivery_fee' => $deliveryFee,
            'subtotal' => $subtotal,
            'total' => $expectedTotal,
            'status' => 'pending',
        ]);

        // Assert Order Items saved in database
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'unit_price' => $itemPrice,
            'quantity' => $quantity,
            'total_price' => $subtotal,
        ]);

        // Assert cart was emptied from session
        $this->assertFalse(session()->has('cart'));

        // Assert confirmation receipt page displays order number and 200 OK
        $successResponse = $this->get('/checkout/success/'.$orderNumber);
        $successResponse->assertStatus(200);
        $successResponse->assertSee($orderNumber);
        $successResponse->assertSee('Iftekhar Islam Ifty');
        $successResponse->assertSee('01793123456');
    }

    public function test_legacy_sizes_share_one_stock_limit_in_the_cart(): void
    {
        $product = Product::publiclyAvailable()->whereDoesntHave('variants')->firstOrFail();
        $product->update(['stock_quantity' => 3, 'in_stock' => true]);

        $this->postJson('/cart/add', [
            'product_id' => $product->id, 'size' => 'Small', 'quantity' => 2,
        ])->assertOk();
        $this->postJson('/cart/add', [
            'product_id' => $product->id, 'size' => 'Large', 'quantity' => 2,
        ])->assertUnprocessable();

        $this->assertCount(1, session('cart'));
        $this->assertSame(2, (int) array_values(session('cart'))[0]['quantity']);
    }

    public function test_checkout_rejects_combined_legacy_size_demand_above_stock(): void
    {
        $product = Product::publiclyAvailable()->whereDoesntHave('variants')->firstOrFail();
        $product->update(['stock_quantity' => 3, 'in_stock' => true]);
        $cart = [];
        foreach (['Small', 'Large'] as $size) {
            $cart[$product->id.'_'.strtolower($size)] = [
                'product_id' => $product->id, 'size' => $size,
                'quantity' => 2, 'price' => (float) $product->price,
            ];
        }
        $orderCount = Order::count();

        $this->withSession(['cart' => $cart])->post('/checkout/order', [
            'customer_name' => 'Shared Stock Customer',
            'customer_phone' => '01719998871',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'GEC',
            'address' => 'Stock pool test address',
            'payment_method' => 'cod',
        ])->assertSessionHasErrors('cart');

        $this->assertSame($orderCount, Order::count());
        $this->assertSame(3, $product->fresh()->stock_quantity);
    }

    public function test_checkout_decrements_shared_legacy_stock_once_and_keeps_size_snapshots(): void
    {
        $product = Product::publiclyAvailable()->whereDoesntHave('variants')->firstOrFail();
        $product->update(['stock_quantity' => 4, 'in_stock' => true]);
        $cart = [
            $product->id.'_small' => [
                'product_id' => $product->id, 'size' => 'Small',
                'quantity' => 2, 'price' => (float) $product->price,
            ],
            $product->id.'_large' => [
                'product_id' => $product->id, 'size' => 'Large',
                'quantity' => 1, 'price' => (float) $product->price,
            ],
        ];

        $this->withSession(['cart' => $cart])->post('/checkout/order', [
            'customer_name' => 'Shared Stock Customer',
            'customer_phone' => '01719998872',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'GEC',
            'address' => 'Stock pool test address',
            'payment_method' => 'cod',
        ])->assertRedirectContains('/checkout/success/EQ-');

        $order = Order::where('customer_phone', '01719998872')->latest()->firstOrFail();
        $this->assertSame(1, $product->fresh()->stock_quantity);
        $this->assertCount(2, $order->items);
        $this->assertEqualsCanonicalizing(
            [$product->name.' (Small)', $product->name.' (Large)'],
            $order->items->pluck('product_name')->all()
        );
    }

    public function test_repeated_checkout_submission_returns_the_first_order_without_reusing_stock_or_coupon(): void
    {
        $product = Product::publiclyAvailable()->whereDoesntHave('variants')->firstOrFail();
        $product->update(['stock_quantity' => 5, 'in_stock' => true]);
        $coupon = Coupon::create([
            'code' => 'RETRY'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)),
            'type' => 'fixed', 'value' => 100, 'is_active' => true,
        ]);
        $cart = [$product->id.'_standard' => [
            'product_id' => $product->id, 'size' => 'Standard',
            'quantity' => 1, 'price' => (float) $product->price,
        ]];
        $checkoutData = [
            'customer_name' => 'Retry Customer',
            'customer_phone' => '01719998874',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'GEC',
            'address' => 'Retry test address',
            'payment_method' => 'cod',
        ];
        $initialOrderCount = Order::count();
        $this->withSession(['cart' => $cart, 'coupon' => ['id' => $coupon->id]])
            ->post('/checkout/order', $checkoutData)
            ->assertRedirectContains('/checkout/success/EQ-');
        $firstOrder = Order::where('customer_phone', '01719998874')->latest()->firstOrFail();

        // Retry after the first response has cleared the cart.
        $this->post('/checkout/order', $checkoutData)
            ->assertRedirect(route('checkout.success', $firstOrder->order_number));

        // Retry using a stale session snapshot from before the first response.
        $this->withSession(['cart' => $cart, 'checkout_nonce' => 'initial'])
            ->post('/checkout/order', $checkoutData)
            ->assertRedirect(route('checkout.success', $firstOrder->order_number));

        $this->assertSame($initialOrderCount + 1, Order::count());
        $this->assertSame(4, $product->fresh()->stock_quantity);
        $this->assertSame(1, $coupon->fresh()->used_count);
    }

    public function test_new_cart_purchase_after_success_receives_a_new_order_number(): void
    {
        $product = Product::publiclyAvailable()->whereDoesntHave('variants')->firstOrFail();
        $product->update(['stock_quantity' => 5, 'in_stock' => true]);
        $cart = [$product->id.'_standard' => [
            'product_id' => $product->id, 'size' => 'Standard',
            'quantity' => 1, 'price' => (float) $product->price,
        ]];
        $checkoutData = [
            'customer_name' => 'Repeat Buyer',
            'customer_phone' => '01719998875',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'GEC',
            'address' => 'Repeat buyer test address',
            'payment_method' => 'cod',
        ];

        $this->withSession(['cart' => $cart])->post('/checkout/order', $checkoutData)
            ->assertRedirectContains('/checkout/success/EQ-');
        $firstNumber = Order::where('customer_phone', '01719998875')->latest()->firstOrFail()->order_number;
        $this->withSession(['cart' => $cart])->post('/checkout/order', $checkoutData)
            ->assertRedirectContains('/checkout/success/EQ-');
        $secondNumber = Order::where('customer_phone', '01719998875')->latest()->firstOrFail()->order_number;

        $this->assertNotSame($firstNumber, $secondNumber);
        $this->assertSame(2, Order::where('customer_phone', '01719998875')->count());
        $this->assertSame(3, $product->fresh()->stock_quantity);
    }

    /**
     * Test applying a valid percentage coupon via AJAX.
     */
    public function test_apply_valid_percentage_coupon_ajax(): void
    {
        $product = Product::first();
        $code = 'PERC'.rand(10, 99);
        $coupon = Coupon::create([
            'code' => $code,
            'name' => '10% Seasonal Voucher',
            'type' => 'percent',
            'value' => 10.00,
            'min_order_amount' => 1000,
            'is_active' => true,
        ]);

        $key = $product->id.'_standard';
        session(['cart' => [
            $key => [
                'key' => $key,
                'id' => $product->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => 4000.00,
                'image' => $product->image,
                'size' => 'Standard',
                'quantity' => 1,
            ],
        ]]);

        $response = $this->postJson('/cart/coupon/apply', [
            'code' => $code,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'subtotal' => 4000,
            'discount' => 400,
            'total' => 3600,
        ]);

        $this->assertTrue(session()->has('coupon'));
        $this->assertEquals(400, session('coupon.discount'));
    }

    /**
     * Test applying a valid fixed amount coupon via AJAX.
     */
    public function test_apply_valid_fixed_coupon_ajax(): void
    {
        $product = Product::first();
        $code = 'FIXED'.rand(10, 99);
        $coupon = Coupon::create([
            'code' => $code,
            'name' => '৳500 Eid Special',
            'type' => 'fixed',
            'value' => 500.00,
            'min_order_amount' => 2000,
            'is_active' => true,
        ]);

        $key = $product->id.'_standard';
        session(['cart' => [
            $key => [
                'key' => $key,
                'id' => $product->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => 3500.00,
                'image' => $product->image,
                'size' => 'Standard',
                'quantity' => 1,
            ],
        ]]);

        $response = $this->postJson('/cart/coupon/apply', [
            'code' => $code,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'subtotal' => 3500,
            'discount' => 500,
            'total' => 3000,
        ]);

        $this->assertEquals(500, session('coupon.discount'));
    }

    /**
     * Test applying an invalid or non-existent coupon code.
     */
    public function test_apply_invalid_coupon_rejected(): void
    {
        $product = Product::first();
        $key = $product->id.'_standard';
        session(['cart' => [
            $key => [
                'key' => $key,
                'id' => $product->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => 3000.00,
                'image' => $product->image,
                'size' => 'Standard',
                'quantity' => 1,
            ],
        ]]);

        $response = $this->postJson('/cart/coupon/apply', [
            'code' => 'NONEXISTENT999',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertFalse(session()->has('coupon'));
    }

    /**
     * Test coupon rejected when minimum spend is not met.
     */
    public function test_apply_coupon_minimum_spend_not_met(): void
    {
        $code = 'HIGHSPEND'.rand(10, 99);
        Coupon::create([
            'code' => $code,
            'type' => 'fixed',
            'value' => 1000.00,
            'min_order_amount' => 5000.00,
            'is_active' => true,
        ]);

        $product = Product::first();
        $key = $product->id.'_standard';
        session(['cart' => [
            $key => [
                'key' => $key,
                'id' => $product->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => 2000.00,
                'image' => $product->image,
                'size' => 'Standard',
                'quantity' => 1,
            ],
        ]]);

        $response = $this->postJson('/cart/coupon/apply', [
            'code' => $code,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertFalse(session()->has('coupon'));
    }

    /**
     * Test removing an applied coupon via AJAX.
     */
    public function test_remove_coupon_ajax(): void
    {
        session(['coupon' => [
            'id' => 1,
            'code' => 'REMOVE10',
            'name' => 'Remove Me',
            'type' => 'fixed',
            'value' => 200,
            'discount' => 200,
        ]]);

        $response = $this->postJson('/cart/coupon/remove');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'coupon' => null,
            'discount' => 0,
        ]);

        $this->assertFalse(session()->has('coupon'));
    }

    /**
     * Test order placement persists coupon code, discount amount, and correctly updates total.
     */
    public function test_order_placement_persists_coupon_and_discount(): void
    {
        $code = 'EIDORDER'.rand(10, 99);
        $coupon = Coupon::create([
            'code' => $code,
            'name' => 'Eid Checkout Promotion',
            'type' => 'fixed',
            'value' => 400.00,
            'min_order_amount' => 1500.00,
            'is_active' => true,
        ]);

        $product = Product::first();
        $product->update(['price' => 3000.00]);
        $key = $product->id.'_standard';
        $subtotal = 3000.00;
        $deliveryFee = 0.00;
        $discount = 400.00;
        $expectedTotal = ($subtotal - $discount) + $deliveryFee;

        session([
            'cart' => [
                $key => [
                    'key' => $key,
                    'id' => $product->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $subtotal,
                    'image' => $product->image,
                    'size' => 'Standard',
                    'quantity' => 1,
                ],
            ],
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'discount' => $discount,
            ],
        ]);

        $response = $this->post('/checkout/order', [
            'customer_name' => 'Promo Customer',
            'customer_phone' => '01711223344',
            'customer_email' => 'promo@rthquick.com',
            'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram',
            'area' => 'Panchlaish',
            'address' => 'House 10, Road 4',
            'payment_method' => 'cod',
        ]);

        $response->assertStatus(302);
        $targetUrl = $response->headers->get('Location');
        preg_match('#/checkout/success/(EQ-[^/]+)#', $targetUrl, $matches);
        $orderNumber = $matches[1];

        $this->assertDatabaseHas('orders', [
            'order_number' => $orderNumber,
            'coupon_code' => $code,
            'coupon_id' => $coupon->id,
            'discount_amount' => 400.00,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $expectedTotal,
        ]);

        // Verify coupon used_count was incremented
        $coupon->refresh();
        $this->assertEquals(1, $coupon->used_count);

        // Verify coupon and cart cleared from session
        $this->assertFalse(session()->has('cart'));
        $this->assertFalse(session()->has('coupon'));
    }
}

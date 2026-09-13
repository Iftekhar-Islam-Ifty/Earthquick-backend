<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

echo "=== STARTING EARTHQUICK CART & CHECKOUT TEST ===\n\n";

// 1. Find a sample product
$product = Product::first();
if (!$product) {
    echo "[FAIL] No products in database.\n";
    exit(1);
}
echo "[OK] Found test product: ID={$product->id}, Name='{$product->name}', Price=৳{$product->price}\n";

// 2. Test Cart Add (AJAX)
$requestAdd = Request::create('/cart/add', 'POST', [
    'product_id' => $product->id,
    'quantity'   => 2,
    'size'       => 'Standard / Free Size'
], [], [], [
    'HTTP_ACCEPT' => 'application/json',
    'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
]);

$responseAdd = $kernel->handle($requestAdd);
echo "[TEST] POST /cart/add -> HTTP " . $responseAdd->getStatusCode() . "\n";
$dataAdd = json_decode($responseAdd->getContent(), true);

if ($responseAdd->getStatusCode() === 200 && ($dataAdd['success'] ?? false)) {
    echo "[PASS] Cart Add succeeded. Items in cart: " . count($dataAdd['items']) . ", Total Count: " . $dataAdd['count'] . ", Subtotal: " . $dataAdd['formatted_subtotal'] . "\n";
} else {
    echo "[FAIL] Cart Add failed. Content: " . $responseAdd->getContent() . "\n";
    exit(1);
}

// 3. Test Cart Update (+1 qty)
$firstItemKey = $dataAdd['items'][0]['key'];
$requestUpdate = Request::create('/cart/update', 'POST', [
    'key'   => $firstItemKey,
    'delta' => 1
], [], [], [
    'HTTP_ACCEPT' => 'application/json',
    'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
]);
$responseUpdate = $kernel->handle($requestUpdate);
echo "[TEST] POST /cart/update (+1) -> HTTP " . $responseUpdate->getStatusCode() . "\n";
$dataUpdate = json_decode($responseUpdate->getContent(), true);

if ($responseUpdate->getStatusCode() === 200 && ($dataUpdate['count'] ?? 0) === 3) {
    echo "[PASS] Cart Update succeeded. New quantity count: " . $dataUpdate['count'] . "\n";
} else {
    echo "[FAIL] Cart Update failed. Content: " . $responseUpdate->getContent() . "\n";
}

// 4. Test Checkout Page GET
$requestCheckout = Request::create('/checkout', 'GET');
$responseCheckout = $kernel->handle($requestCheckout);
echo "[TEST] GET /checkout -> HTTP " . $responseCheckout->getStatusCode() . "\n";
if ($responseCheckout->getStatusCode() === 200) {
    echo "[PASS] Checkout page rendered with status 200 OK.\n";
} else {
    echo "[FAIL] Checkout page returned HTTP " . $responseCheckout->getStatusCode() . "\n";
}

// 5. Test Order Placement (POST /checkout/order)
$orderPayload = [
    'customer_name'  => 'Test Customer Iftekhar',
    'customer_phone' => '01793123456',
    'customer_email' => 'ifty.test@earthquick.com',
    'delivery_zone'  => 'inside_ctg',
    'district'       => 'Chattogram',
    'area'           => 'Nasirabad',
    'address'        => 'Road 3, House 12, Nasirabad Housing Society',
    'order_notes'    => 'Please call 10 mins before arrival.',
    'payment_method' => 'cod',
];

$requestOrder = Request::create('/checkout/order', 'POST', $orderPayload);
$responseOrder = $kernel->handle($requestOrder);
echo "[TEST] POST /checkout/order -> HTTP " . $responseOrder->getStatusCode() . "\n";

if ($responseOrder->isRedirect()) {
    $targetUrl = $responseOrder->headers->get('Location');
    echo "[PASS] Order submitted! Redirected to: {$targetUrl}\n";
    
    // Extract order number from URL
    preg_match('#/checkout/success/(EQ-[^/]+)#', $targetUrl, $matches);
    $orderNumber = $matches[1] ?? null;

    if ($orderNumber) {
        echo "[PASS] Generated Order Number: {$orderNumber}\n";
        
        // Verify in DB
        $dbOrder = Order::with('items')->where('order_number', $orderNumber)->first();
        if ($dbOrder) {
            echo "[PASS] Order verified in MySQL:\n";
            echo "       - Customer: {$dbOrder->customer_name}\n";
            echo "       - Phone: {$dbOrder->customer_phone}\n";
            echo "       - Zone: {$dbOrder->delivery_zone} (Fee: ৳{$dbOrder->delivery_fee})\n";
            echo "       - Subtotal: ৳{$dbOrder->subtotal}\n";
            echo "       - Total Payable: ৳{$dbOrder->total}\n";
            echo "       - Order Items Count: " . $dbOrder->items->count() . "\n";

            // Verify order success page
            $requestSuccess = Request::create('/checkout/success/' . $orderNumber, 'GET');
            $responseSuccess = $kernel->handle($requestSuccess);
            echo "[TEST] GET /checkout/success/{$orderNumber} -> HTTP " . $responseSuccess->getStatusCode() . "\n";
            if ($responseSuccess->getStatusCode() === 200) {
                echo "[PASS] Order Confirmation Receipt rendered successfully (200 OK)!\n";
            } else {
                echo "[FAIL] Order success page returned " . $responseSuccess->getStatusCode() . "\n";
            }
        } else {
            echo "[FAIL] Order not found in database.\n";
        }
    } else {
        echo "[FAIL] Could not extract order number from redirect URL: {$targetUrl}\n";
    }
} else {
    echo "[FAIL] Order submission did not redirect. Content: " . substr($responseOrder->getContent(), 0, 500) . "\n";
}

echo "\n=== ALL CART & CHECKOUT ENGINE TESTS COMPLETED ===\n";

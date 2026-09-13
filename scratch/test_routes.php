<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$urls = [
    '/' => 'Home Page',
    '/about' => 'About Us Page',
    '/shop/women' => 'Women Category',
    '/shop/women/saree' => 'Women Saree Subcategory',
    '/shop/women/three-piece' => 'Women Three Piece Subcategory',
    '/shop/bags' => 'Bags Category',
    '/shop/home-decor' => 'Home Decor Category',
    '/product/crimson-heirloom-jamdani' => 'Product Detail Page',
    '/cart' => 'Cart Page',
    '/checkout' => 'Checkout Page',
];

$errors = 0;
foreach ($urls as $url => $label) {
    $request = Illuminate\Http\Request::create($url, 'GET');
    try {
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        if ($status >= 200 && $status < 400) {
            echo " [PASS] $label ($url) -> HTTP $status\n";
        } else {
            echo " [FAIL] $label ($url) -> HTTP $status\n";
            $errors++;
        }
    } catch (\Throwable $e) {
        echo " [ERROR] $label ($url) -> Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
        $errors++;
    }
}

exit($errors === 0 ? 0 : 1);


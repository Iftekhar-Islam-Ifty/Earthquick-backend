<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$urls = [
    '/shop/women?fabric=Jamdani' => 'Fabric Filter',
    '/shop/women?max_price=15000' => 'Price Range Filter',
    '/shop/women?sort=price-low' => 'Price Sort Filter',
    '/shop/women?in_stock=1' => 'In-Stock Filter',
    '/shop/women/saree?fabric=Tangail' => 'Subcategory Fabric Filter',
];

foreach ($urls as $url => $label) {
    $request = Illuminate\Http\Request::create($url, 'GET');
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    echo " [$status] $label: $url\n";
}


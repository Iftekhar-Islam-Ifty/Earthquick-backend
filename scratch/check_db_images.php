<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = App\Models\Product::all();
$missing = [];

foreach ($products as $p) {
    if ($p->image && !file_exists('public/' . ltrim($p->image, '/'))) {
        $missing[] = "Product #{$p->id} ({$p->name}) image: {$p->image}";
    }
    if ($p->alt_image && !file_exists('public/' . ltrim($p->alt_image, '/'))) {
        $missing[] = "Product #{$p->id} ({$p->name}) alt_image: {$p->alt_image}";
    }
}

$categories = App\Models\Category::all();
foreach ($categories as $c) {
    if ($c->image && !file_exists('public/' . ltrim($c->image, '/'))) {
        $missing[] = "Category #{$c->id} ({$c->name}) image: {$c->image}";
    }
}

echo "Database Image Check:\n";
echo "Total missing: " . count($missing) . "\n";
foreach ($missing as $m) {
    echo " - $m\n";
}


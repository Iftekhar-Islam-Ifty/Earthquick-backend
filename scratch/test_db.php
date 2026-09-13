<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = App\Models\Product::with(['category', 'subcategory'])->get();
foreach ($products as $p) {
    echo "ID: {$p->id} | Name: {$p->name} | Cat: {$p->category->slug} | Sub: " . ($p->subcategory?->slug ?? 'none') . " | Image: {$p->image} | Price: {$p->price}\n";
}


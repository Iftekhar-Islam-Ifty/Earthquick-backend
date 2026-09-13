<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "USERS COLUMNS:\n";
print_r(Schema::getColumnListing('users'));

echo "\nORDERS COLUMNS:\n";
print_r(Schema::getColumnListing('orders'));


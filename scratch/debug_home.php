<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $controller = new App\Http\Controllers\HomeController();
    $view = $controller->index();
    echo "Controller returned view: " . $view->name() . "\n";
    echo "Rendering view...\n";
    $html = $view->render();
    echo "Rendered successfully! Length: " . strlen($html) . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "In: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}


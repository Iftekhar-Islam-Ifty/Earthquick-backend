<?php
$views = [
    'resources/views/home.blade.php',
    'resources/views/about.blade.php',
    'resources/views/category.blade.php',
    'resources/views/product.blade.php',
    'resources/views/partials/navbar.blade.php',
    'resources/views/partials/footer.blade.php',
];

$missing = [];
$found = [];

foreach ($views as $view) {
    if (!file_exists($view)) continue;
    $content = file_get_contents($view);
    preg_match_all("/asset\(['\"]([^'\"]+)['\"]\)/", $content, $matches);
    foreach ($matches[1] as $path) {
        $fullPath = 'public/' . ltrim($path, '/');
        if (!file_exists($fullPath)) {
            $missing[$path] = $view;
        } else {
            $found[$path] = true;
        }
    }
}

echo "Checked assets summary:\n";
echo "Total found: " . count($found) . "\n";
echo "Total missing: " . count($missing) . "\n";
if (!empty($missing)) {
    echo "\nMISSING ASSETS:\n";
    foreach ($missing as $path => $view) {
        echo " - {$path} (in {$view})\n";
    }
}


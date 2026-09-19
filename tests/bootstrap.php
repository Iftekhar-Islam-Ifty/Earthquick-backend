<?php

// PHPUnit only: never load application config/cache before establishing isolation.
require dirname(__DIR__).'/vendor/autoload.php';

define('EARTHQUICK_TEST_ROOT', sys_get_temp_dir().'/earthquick-tests-'.bin2hex(random_bytes(8)));
$cacheRelative = 'storage/qa/cache-'.basename(EARTHQUICK_TEST_ROOT);
mkdir(dirname(__DIR__).'/'.$cacheRelative, 0777, true);

foreach (['bootstrap', 'public', 'storage/framework/views', 'storage/framework/sessions',
    'storage/framework/cache/data', 'storage/logs', 'storage/app/private', 'storage/app/public'] as $directory) {
    if (! mkdir(EARTHQUICK_TEST_ROOT.'/'.$directory, 0777, true) && ! is_dir(EARTHQUICK_TEST_ROOT.'/'.$directory)) {
        throw new RuntimeException('Cannot create isolated test directory.');
    }
}

$environment = [
    'APP_ENV' => 'testing',
    'APP_KEY' => 'base64:'.base64_encode(str_repeat('t', 32)),
    'APP_URL' => 'http://localhost',
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => ':memory:',
    'DB_URL' => '',
    'DB_FOREIGN_KEYS' => 'true',
    'BCRYPT_ROUNDS' => '4',
    'CACHE_STORE' => 'array',
    'SESSION_DRIVER' => 'array',
    'MAIL_MAILER' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'LOG_CHANNEL' => 'single',
    'APP_MAINTENANCE_DRIVER' => 'file',
    'LARAVEL_STORAGE_PATH' => EARTHQUICK_TEST_ROOT.'/storage',
    'VIEW_COMPILED_PATH' => EARTHQUICK_TEST_ROOT.'/storage/framework/views',
    // Relative paths also work with Laravel's cache-path resolver on Windows.
    'APP_CONFIG_CACHE' => $cacheRelative.'/config.php',
    'APP_ROUTES_CACHE' => $cacheRelative.'/routes.php',
    'APP_EVENTS_CACHE' => $cacheRelative.'/events.php',
    'APP_PACKAGES_CACHE' => $cacheRelative.'/packages.php',
    'APP_SERVICES_CACHE' => $cacheRelative.'/services.php',
];

foreach ($environment as $name => $value) {
    putenv($name.'='.$value);
    $_ENV[$name] = $_SERVER[$name] = $value;
}

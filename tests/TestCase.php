<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Tests\Support\CatalogFixture;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        if (! defined('EARTHQUICK_TEST_ROOT')) {
            throw new \RuntimeException('Run tests using phpunit.xml or phpunit.regressions.xml.');
        }

        $app = parent::createApplication();
        $config = $app->make('config');
        if (! $app->environment('testing') || $config->get('database.default') !== 'sqlite'
            || $config->get('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Refusing to run tests outside an in-memory SQLite database.');
        }

        // Remove alternative named connections so tests cannot reach the real DB.
        $config->set('database.connections', ['sqlite' => $config->get('database.connections.sqlite')]);
        $app->usePublicPath(EARTHQUICK_TEST_ROOT.'/public');
        $config->set('filesystems.disks.local.root', EARTHQUICK_TEST_ROOT.'/storage/app/private');
        $config->set('filesystems.disks.public.root', EARTHQUICK_TEST_ROOT.'/storage/app/public');

        // A new application means a new in-memory DB, including tests without a
        // DatabaseTransactions trait. Never call the destructive production seeder.
        Artisan::call('migrate', ['--force' => true]);
        CatalogFixture::seed();

        return $app;
    }
}

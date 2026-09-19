<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EnvironmentIsolationTest extends TestCase
{
    public function test_database_and_writable_paths_are_isolated(): void
    {
        $this->assertTrue(app()->environment('testing'));
        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        $this->assertSame(['sqlite'], array_keys(config('database.connections')));
        $this->assertSame(4, Product::count());
        $this->assertSame(0, Order::count());
        $this->assertSame(EARTHQUICK_TEST_ROOT.'/public', public_path());
        $this->assertNotSame(base_path('public'), public_path());
        $this->assertStringStartsWith(EARTHQUICK_TEST_ROOT, storage_path());
        $this->assertStringStartsWith(EARTHQUICK_TEST_ROOT, config('view.compiled'));
        $this->assertStringStartsWith(EARTHQUICK_TEST_ROOT, config('filesystems.disks.public.root'));

        // A real write via the helper used by upload/delete controllers must stay isolated.
        file_put_contents(public_path('isolation-sentinel.txt'), 'test only');
        $this->assertFileExists(EARTHQUICK_TEST_ROOT.'/public/isolation-sentinel.txt');
    }
}

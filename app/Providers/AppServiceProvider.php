<?php

namespace App\Providers;

use App\Models\Vendor;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.navbar', function ($view): void {
            $storeLogos = Vendor::whereIn('slug', ['nous-telos', 'bright'])
                ->get(['id', 'name', 'slug', 'logo'])
                ->keyBy('slug');

            $view->with('storeLogos', $storeLogos);
        });
    }
}

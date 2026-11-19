<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\View\View;

/* =========================================================================
 * HOME CONTROLLER
 * Renders the Rthquick marketplace entry point and its available stores.
 * ========================================================================= */

class HomeController extends Controller
{
    /* =========================================================================
     * HOMEPAGE EDITORIAL AGGREGATION
     * Fetches categorized inventory and featured items for homepage layout.
     * ========================================================================= */

    /** Render the marketplace entry page with one clear choice per store. */
    public function index(): View
    {
        $stores = Vendor::where('is_active', true)
            ->whereIn('slug', ['nous-telos', 'bright'])
            ->orderBy('sort_order')->get()->keyBy('slug');

        return view('home', compact('stores'));
    }
}

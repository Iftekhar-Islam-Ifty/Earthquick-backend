<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\View\View;

/* =========================================================================
 * HOME CONTROLLER
 * Curates flagship homepage editorial sections, hero carousels,
 * artisanal handloom spotlights, and featured product collections.
 * ========================================================================= */

class HomeController extends Controller
{
    /* =========================================================================
     * HOMEPAGE EDITORIAL AGGREGATION
     * Fetches categorized inventory and featured items for homepage layout.
     * ========================================================================= */

    /**
     * Render the Earthquick / Nous Telos flagship homepage.
     */
    public function index(): View
    {
        $nousTelos = Vendor::where('slug', 'nous-telos')->where('is_active', true)->first();
        $bright = Vendor::where('slug', 'bright')->where('is_active', true)->first();

        // Active categories sorted by administrative sort order
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $flagshipProducts = Product::publiclyAvailable()
            ->where('in_stock', true);
        if ($nousTelos) {
            $flagshipProducts->where('vendor_id', $nousTelos->id);
        } else {
            $flagshipProducts->whereRaw('1 = 0');
        }
        $products = (clone $flagshipProducts)->latest()->take(8)->get();
        $featuredProducts = (clone $flagshipProducts)->where('is_featured', true)->take(8)->get();
        $newArrivals = (clone $flagshipProducts)->where('is_new_arrival', true)->latest()->take(8)->get();

        // Authentic handloom Sarees for flagship atelier showcase
        $sarees = (clone $flagshipProducts)->whereHas('subcategory', function ($q) {
            $q->where('slug', 'saree');
        })->where('in_stock', true)->get();

        // Left-side masterpiece spotlight saree
        $sareeSpotlight = $sarees->first() ?? $featuredProducts->first();

        // Handcrafted Bags collection
        $bags = (clone $flagshipProducts)->whereHas('category', function ($q) {
            $q->where('slug', 'bags');
        })->where('in_stock', true)->latest()->take(5)->get();

        return view('home', compact(
            'categories',
            'nousTelos',
            'bright',
            'products',
            'featuredProducts',
            'newArrivals',
            'sarees',
            'sareeSpotlight',
            'bags'
        ));
    }
}

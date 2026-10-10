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
     * Render the Rthquick / Nous Telos flagship homepage.
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
        $newArrivals = (clone $flagshipProducts)->with(['category', 'subcategory'])
            ->where('is_new_arrival', true)->latest()->orderByDesc('id')->take(8)->get();

        // Authentic handloom Sarees for flagship atelier showcase
        $sarees = (clone $flagshipProducts)->whereHas('subcategory', function ($q) {
            $q->where('slug', 'saree');
        })->latest()->take(5)->get();

        // Left-side masterpiece spotlight saree
        $sareeSpotlight = $sarees->first();

        $threePieces = (clone $flagshipProducts)->whereHas('subcategory', function ($q) {
            $q->where('slug', 'three-piece');
        })->latest()->take(8)->get();

        $twoPieces = (clone $flagshipProducts)->whereHas('subcategory', function ($q) {
            $q->where('slug', 'two-piece');
        })->latest()->take(5)->get();

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
            'threePieces',
            'twoPieces',
            'bags'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
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
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Active categories sorted by administrative sort order
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $products = Product::where('in_stock', true)->latest()->take(8)->get();
        $featuredProducts = Product::where('is_featured', true)->where('in_stock', true)->take(8)->get();
        $newArrivals = Product::where('is_new_arrival', true)->where('in_stock', true)->latest()->take(8)->get();

        // Authentic handloom Sarees for flagship atelier showcase
        $sarees = Product::whereHas('subcategory', function ($q) {
            $q->where('slug', 'saree');
        })->where('in_stock', true)->get();

        // Left-side masterpiece spotlight saree
        $sareeSpotlight = $sarees->first() ?? $featuredProducts->first();

        // Handcrafted Bags collection
        $bags = Product::whereHas('category', function ($q) {
            $q->where('slug', 'bags');
        })->where('in_stock', true)->get();

        return view('home', compact(
            'categories',
            'products',
            'featuredProducts',
            'newArrivals',
            'sarees',
            'sareeSpotlight',
            'bags'
        ));
    }
}
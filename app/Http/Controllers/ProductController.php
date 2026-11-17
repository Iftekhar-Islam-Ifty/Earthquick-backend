<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

/* =========================================================================
 * PRODUCT CONTROLLER
 * Handles detailed single product display, gallery rendering, SKU inventory,
 * and dynamic cross-sell recommendations ("You May Also Admire").
 * ========================================================================= */

class ProductController extends Controller
{
    /* =========================================================================
     * PRODUCT DETAIL DISPLAY
     * Loads product with relationships and related category recommendations.
     * ========================================================================= */

    /**
     * Display a single product detail page.
     */
    public function show(string $slug): View
    {
        $product = Product::publiclyAvailable()->with([
            'category',
            'subcategory',
            'images',
            'vendor',
            'brand.warrantyProvider',
            'variants' => fn ($query) => $query->where('is_active', true)->orderBy('id'),
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Keep recommendations inside the same selling store and category.
        $relatedProducts = Product::publiclyAvailable()->where('category_id', $product->category_id)
            ->where('vendor_id', $product->vendor_id)
            ->where('id', '!=', $product->id)
            ->where('in_stock', true)
            ->with(['category', 'subcategory', 'brand', 'vendor'])
            ->take(4)
            ->get();

        return view('product', compact('product', 'relatedProducts'));
    }
}

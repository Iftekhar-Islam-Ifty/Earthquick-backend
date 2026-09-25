<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

/* =========================================================================
 * VENDOR STOREFRONT CONTROLLER
 * Handles public brand directories (/stores) and dedicated multi-vendor
 * brand boutiques (/stores/{slug}) across the Earthquick collective.
 * ========================================================================= */
class VendorController extends Controller
{
    /**
     * Display directory of all active brand partners and atelier stores.
     */
    public function index(): View
    {
        $vendors = Vendor::where('is_active', true)
            ->withCount(['products' => function ($q) {
                $q->where('in_stock', true);
                $q->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('vendor.index', compact('vendors'));
    }

    /**
     * Display an individual partner boutique/storefront with curated products.
     */
    public function show(Request $request, string $slug): View
    {
        $vendor = Vendor::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = Product::where('vendor_id', $vendor->id)
            ->with(['category', 'subcategory'])
            ->where('in_stock', true)
            ->where('is_active', true)
            ->applyCatalogFilters($request->query());

        // Filter by category if specified
        if ($request->filled('category')) {
            $categorySlug = $request->query('category');
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Apply sorting criteria
        match ($request->query('sort')) {
            'price-asc', 'price-low' => $query->orderBy('price', 'asc'),
            'price-desc', 'price-high' => $query->orderBy('price', 'desc'),
            'rating' => $query->orderBy('rating', 'desc'),
            'newest' => $query->latest(),
            default => $query->latest()
        };

        $products = $query->paginate(12)->withQueryString();

        // Retrieve distinct categories currently represented by this vendor's in-stock inventory
        $categories = Category::whereHas('products', function ($q) use ($vendor) {
            $q->where('vendor_id', $vendor->id)
                ->where('in_stock', true)
                ->where('is_active', true);
        })->get();

        $availableProductTypes = Product::where('vendor_id', $vendor->id)
            ->where('is_active', true)
            ->where('in_stock', true)
            ->whereNotNull('product_type')
            ->distinct()
            ->pluck('product_type');
        $availableDeliveryClasses = Product::where('vendor_id', $vendor->id)
            ->where('is_active', true)
            ->where('in_stock', true)
            ->whereNotNull('delivery_class')
            ->distinct()
            ->pluck('delivery_class');

        return view('vendor.show', compact(
            'vendor',
            'products',
            'categories',
            'availableProductTypes',
            'availableDeliveryClasses'
        ));
    }
}

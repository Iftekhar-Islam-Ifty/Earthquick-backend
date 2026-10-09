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
 * brand boutiques (/stores/{slug}) across the Rthquick collective.
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

        $storeQuery = [];
        foreach (['category', 'product_type', 'delivery_class', 'returnable', 'min_price', 'max_price', 'sort'] as $key) {
            $value = $request->query($key);
            if (is_string($value) && $value !== '') {
                $storeQuery[$key] = $value;
            }
        }
        if (isset($storeQuery['product_type']) && ! array_key_exists($storeQuery['product_type'], config('catalog.product_types'))) {
            unset($storeQuery['product_type']);
        }
        if (isset($storeQuery['delivery_class']) && ! array_key_exists($storeQuery['delivery_class'], config('catalog.delivery_classes'))) {
            unset($storeQuery['delivery_class']);
        }
        if (isset($storeQuery['returnable']) && ! in_array($storeQuery['returnable'], ['0', '1'], true)) {
            unset($storeQuery['returnable']);
        }
        foreach (['min_price', 'max_price'] as $key) {
            if (isset($storeQuery[$key]) && (! is_numeric($storeQuery[$key]) || ! is_finite((float) $storeQuery[$key]) || (float) $storeQuery[$key] < 0)) {
                unset($storeQuery[$key]);
            }
        }
        if (isset($storeQuery['sort'])) {
            $storeQuery['sort'] = match ($storeQuery['sort']) {
                'price-low' => 'price-asc',
                'price-high' => 'price-desc',
                'price-asc', 'price-desc', 'newest' => $storeQuery['sort'],
                default => 'newest',
            };
        }

        $catalog = Product::where('vendor_id', $vendor->id)
            ->where('in_stock', true)
            ->where('is_active', true);
        $allProductsCount = (clone $catalog)->count();

        $query = (clone $catalog)
            ->with(['category', 'subcategory'])
            ->applyCatalogFilters($storeQuery);

        // Apply sorting criteria
        match ($storeQuery['sort'] ?? null) {
            'price-asc', 'price-low' => $query->orderBy('price')->orderByDesc('id'),
            'price-desc', 'price-high' => $query->orderByDesc('price')->orderByDesc('id'),
            default => $query->latest()->orderByDesc('id'),
        };

        $products = $query->paginate(12)->withQueryString();

        // Retrieve distinct categories currently represented by this vendor's in-stock inventory
        $categories = Category::whereHas('products', fn ($q) => $q->where('vendor_id', $vendor->id)
            ->where('in_stock', true)->where('is_active', true))->get();

        $availableProductTypes = (clone $catalog)->whereNotNull('product_type')
            ->distinct()
            ->pluck('product_type');
        $availableDeliveryClasses = (clone $catalog)->whereNotNull('delivery_class')
            ->distinct()
            ->pluck('delivery_class');
        $hasMixedReturnPolicies = (clone $catalog)->distinct()->count('is_returnable') > 1;

        return view('vendor.show', compact(
            'vendor',
            'products',
            'allProductsCount',
            'categories',
            'availableProductTypes',
            'availableDeliveryClasses',
            'hasMixedReturnPolicies',
            'storeQuery'
        ));
    }
}

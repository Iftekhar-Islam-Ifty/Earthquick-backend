<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

/* =========================================================================
 * CATEGORY CONTROLLER
 * Handles primary catalog browsing, multi-attribute filtering (fabric,
 * price ranges, availability), sorting, and subcategory routing.
 * ========================================================================= */

class CategoryController extends Controller
{
    /* =========================================================================
     * PRIMARY CATEGORY CATALOG
     * Renders main catalog page (e.g., /shop/women, /shop/bags, /shop/men).
     * ========================================================================= */

    /**
     * Display a main category catalog page.
     */
    public function showCategory(Request $request, string $slug): View
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $query = Product::publiclyAvailable()->where('category_id', $category->id);

        // Apply inventory availability filter
        if ($request->filled('in_stock')) {
            $query->where('in_stock', true);
        }

        // Apply fabric attribute filter (supports single or multi-select arrays)
        if ($request->filled('fabric')) {
            $fabrics = is_array($request->query('fabric'))
                ? $request->query('fabric')
                : explode(',', $request->query('fabric'));
            $query->whereIn('fabric', $fabrics);
        }

        // Apply dynamic price boundary filters
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->query('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->query('max_price'));
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
        $subcategories = $category->subcategories;

        // Retrieve distinct fabrics in category for dynamic filter sidebar
        $availableFabrics = Product::publiclyAvailable()->where('category_id', $category->id)
            ->whereNotNull('fabric')
            ->distinct()
            ->pluck('fabric');

        return view('category', compact('category', 'products', 'subcategories', 'availableFabrics'));
    }

    /* =========================================================================
     * SUBCATEGORY SPECIFIC CATALOG
     * Renders subcategory collections (e.g., /shop/women/saree, /shop/home-decor/kantha).
     * ========================================================================= */

    /**
     * Display a subcategory catalog page.
     */
    public function showSubcategory(Request $request, string $categorySlug, string $subcategorySlug): View
    {
        $category = Category::where('slug', $categorySlug)->where('is_active', true)->firstOrFail();
        $subcategory = Subcategory::where('slug', $subcategorySlug)->where('category_id', $category->id)->firstOrFail();

        $query = Product::publiclyAvailable()->where('subcategory_id', $subcategory->id);

        // Apply inventory availability filter
        if ($request->filled('in_stock')) {
            $query->where('in_stock', true);
        }

        // Apply fabric attribute filter
        if ($request->filled('fabric')) {
            $fabrics = is_array($request->query('fabric'))
                ? $request->query('fabric')
                : explode(',', $request->query('fabric'));
            $query->whereIn('fabric', $fabrics);
        }

        // Apply dynamic price boundary filters
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->query('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->query('max_price'));
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
        $subcategories = $category->subcategories;

        // Retrieve distinct fabrics in subcategory for dynamic filter sidebar
        $availableFabrics = Product::publiclyAvailable()->where('subcategory_id', $subcategory->id)
            ->whereNotNull('fabric')
            ->distinct()
            ->pluck('fabric');

        return view('category', compact('category', 'subcategory', 'products', 'subcategories', 'availableFabrics'));
    }
}

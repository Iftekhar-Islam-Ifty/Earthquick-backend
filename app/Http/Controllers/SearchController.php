<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/* =========================================================================
 * SEARCH CONTROLLER
 * Powers instant live search suggestions (AJAX modal) and comprehensive
 * full-text search results page with sorting and category filtering.
 * ========================================================================= */

class SearchController extends Controller
{
    /* =========================================================================
     * LIVE AJAX SEARCH SUGGESTIONS
     * Returns matching products in real-time as user types in search drawer.
     * ========================================================================= */

    /**
     * AJAX Live Search Suggestions Endpoint.
     * Returns up to 6 in-stock products matching name, fabric, or categories.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));

        // Return empty payload if search term is blank
        if (mb_strlen($query) < 1) {
            return response()->json([
                'success' => true,
                'query' => '',
                'count' => 0,
                'suggestions' => [],
            ]);
        }

        // Query active inventory across product metadata and category associations
        $products = Product::publiclyAvailable()->with(['category', 'subcategory'])
            ->where('in_stock', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('fabric', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('short_desc', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('specifications', 'like', "%{$query}%")
                    ->orWhereHas('category', function ($cq) use ($query) {
                        $cq->where('name', 'like', "%{$query}%");
                    })
                    ->orWhereHas('subcategory', function ($sq) use ($query) {
                        $sq->where('name', 'like', "%{$query}%");
                    });
            })
            ->take(6)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => (float) $product->price,
                    'formatted_price' => '৳'.number_format($product->price),
                    'image' => asset($product->image),
                    'category_name' => $product->subcategory ? $product->subcategory->name : ($product->category ? $product->category->name : 'Nous Telos Studio'),
                    'url' => route('product.show', $product->slug),
                ];
            });

        return response()->json([
            'success' => true,
            'query' => $query,
            'count' => $products->count(),
            'suggestions' => $products,
        ]);
    }

    /* =========================================================================
     * DEDICATED SEARCH RESULTS PAGE
     * Renders paginated search catalog with multiple sorting strategies.
     * ========================================================================= */

    /**
     * Dedicated Search Results Page.
     */
    public function index(Request $request): View
    {
        $query = trim($request->input('q', ''));
        $sort = $request->input('sort', 'featured');

        $productsQuery = Product::publiclyAvailable()->with(['category', 'subcategory']);
        $productsQuery->applyCatalogFilters($request->query());

        if (! empty($query)) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('fabric', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('short_desc', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('specifications', 'like', "%{$query}%")
                    ->orWhereHas('category', function ($cq) use ($query) {
                        $cq->where('name', 'like', "%{$query}%");
                    })
                    ->orWhereHas('subcategory', function ($sq) use ($query) {
                        $sq->where('name', 'like', "%{$query}%");
                    });
            });
        }

        // Apply selected sorting strategy
        switch ($sort) {
            case 'price-asc':
            case 'price-low':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price-desc':
            case 'price-high':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'latest':
            case 'newest':
                $productsQuery->orderBy('created_at', 'desc');
                break;
            default:
                $productsQuery->orderBy('is_featured', 'desc')->orderBy('id', 'desc');
                break;
        }

        $products = $productsQuery->paginate(12)->withQueryString();

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();

        return view('search-results', compact('products', 'query', 'sort', 'categories', 'vendors'));
    }
}

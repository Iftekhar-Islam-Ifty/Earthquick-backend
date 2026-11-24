<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\HomepageImage;
use Illuminate\Http\Request;
use Illuminate\View\View;

/* =========================================================================
 * VENDOR STOREFRONT CONTROLLER
 * Handles public store directories (/stores) and dedicated store pages
 * (/stores/{slug}) across the Rthquick marketplace.
 * ========================================================================= */
class VendorController extends Controller
{
    /**
     * Display directory of all active stores.
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
     * Display an individual store page with its available products.
     */
    public function show(Request $request, string $slug): View|\Illuminate\Http\RedirectResponse
    {
        $vendor = Vendor::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $catalogKeys = ['category', 'subcategory', 'product_type', 'delivery_class', 'returnable', 'min_price', 'max_price', 'sort', 'page', 'q'];
        if (collect($catalogKeys)->contains(fn ($key) => $request->query($key) !== null)) {
            return redirect()->route('stores.catalog', array_merge(['slug' => $vendor->slug], $request->query()));
        }

        if ($vendor->slug === 'nous-telos') {
            $products = Product::publiclyAvailable()->where('vendor_id', $vendor->id)->where('in_stock', true);
            $newArrivals = (clone $products)->with(['category', 'subcategory'])->where('is_new_arrival', true)->latest()->orderByDesc('id')->take(8)->get();
            $sarees = (clone $products)->whereHas('subcategory', fn ($q) => $q->where('slug', 'saree'))->latest()->orderByDesc('id')->take(9)->get();
            $sareeSpotlight = $sarees->first();
            $threePieces = (clone $products)->whereHas('subcategory', fn ($q) => $q->where('slug', 'three-piece'))->latest()->take(8)->get();
            $twoPieces = (clone $products)->whereHas('subcategory', fn ($q) => $q->where('slug', 'two-piece'))->latest()->take(5)->get();
            $bags = (clone $products)->whereHas('category', fn ($q) => $q->where('slug', 'bags'))->latest()->take(5)->get();
            $homepageMedia = HomepageImage::resolvedSlots();

            return view('vendor.nous-telos-home', compact('vendor', 'newArrivals', 'sarees', 'sareeSpotlight', 'threePieces', 'twoPieces', 'bags', 'homepageMedia'));
        }

        if ($vendor->slug === 'bright') {
            $electronics = Category::where('slug', 'electronics')->first();
            $subcategories = $electronics?->subcategories()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() ?? collect();
            $catalogCount = Product::where('vendor_id', $vendor->id)->where('is_active', true)->where('in_stock', true)->count();
            $featuredProducts = Product::publiclyAvailable()
                ->where('vendor_id', $vendor->id)
                ->where('in_stock', true)
                ->with(['category', 'subcategory'])
                ->latest('id')
                ->take(4)
                ->get();
            $homepageMedia = HomepageImage::resolvedSlots('bright');

            return view('vendor.bright-home', compact('vendor', 'subcategories', 'catalogCount', 'featuredProducts', 'homepageMedia'));
        }

        return view('vendor.landing', compact('vendor'));
    }

    /** Display the filtered, paginated product catalog for an individual store. */
    public function catalog(Request $request, string $slug): View
    {
        $vendor = Vendor::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        $isBrightStore = $vendor->slug === 'bright';

        $storeQuery = [];
        foreach (['category', 'subcategory', 'product_type', 'delivery_class', 'returnable', 'min_price', 'max_price', 'sort', 'q'] as $key) {
            $value = $request->query($key);
            if (is_string($value) && $value !== '') {
                $storeQuery[$key] = $value;
            }
        }
        $electronics = $isBrightStore ? Category::where('slug', 'electronics')->first() : null;
        if ($isBrightStore) {
            unset($storeQuery['category']);
            $categories = collect();
            $subcategories = $electronics?->subcategories()->where('is_active', true)->orderBy('name')->get() ?? collect();
        } else {
            unset($storeQuery['category']);
            $categories = Category::query()
                ->where('is_active', true)
                ->where('slug', '!=', 'electronics')
                ->with(['subcategories' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get();
            $subcategories = $categories->flatMap(fn ($category) => $category->subcategories);
            $requestQueryCategory = $request->query('category');
            if (is_string($requestQueryCategory) && $categories->contains('slug', $requestQueryCategory)) {
                $storeQuery['category'] = $requestQueryCategory;
            }
        }
        $selectedSubcategory = isset($storeQuery['subcategory'])
            ? $subcategories->firstWhere('slug', $storeQuery['subcategory'])
            : null;
        if (isset($storeQuery['subcategory']) && ! $selectedSubcategory) {
            unset($storeQuery['subcategory']);
        } elseif ($selectedSubcategory && ! $isBrightStore) {
            // Navbar subcategory links can stand alone; keep the parent category
            // in the URL so the catalog state and product filter stay in sync.
            $storeQuery['category'] = $selectedSubcategory->category_id
                ? $categories->firstWhere('id', $selectedSubcategory->category_id)?->slug
                : null;
            if (! $storeQuery['category']) {
                unset($storeQuery['category'], $storeQuery['subcategory']);
                $selectedSubcategory = null;
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
            ->with(['category', 'subcategory', 'brand'])
            ->applyCatalogFilters($storeQuery);
        if (! empty($storeQuery['q'])) {
            $term = trim($storeQuery['q']);
            if (mb_strlen($term) > 100) {
                $term = mb_substr($term, 0, 100);
            }
            $query->where(function ($builder) use ($term) {
                $builder->where('name', 'like', '%'.$term.'%')
                    ->orWhere('short_desc', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%');
            });
        }
        if ($selectedSubcategory) {
            $query->where('subcategory_id', $selectedSubcategory->id);
        }

        // Apply sorting criteria
        match ($storeQuery['sort'] ?? null) {
            'price-asc', 'price-low' => $query->orderBy('price')->orderByDesc('id'),
            'price-desc', 'price-high' => $query->orderByDesc('price')->orderByDesc('id'),
            default => $query->latest()->orderByDesc('id'),
        };

        $products = $query->paginate(12)->withQueryString();

        // Catalog taxonomy stays visible even when a store has no products in a category.
        $availableProductTypes = (clone $catalog)->whereNotNull('product_type')
            ->distinct()
            ->pluck('product_type');
        $availableDeliveryClasses = (clone $catalog)->whereNotNull('delivery_class')
            ->distinct()
            ->pluck('delivery_class');
        $hasMixedReturnPolicies = (clone $catalog)->distinct()->count('is_returnable') > 1;
        return view('vendor.show', compact(
            'vendor',
            'isBrightStore',
            'products',
            'allProductsCount',
            'categories',
            'subcategories',
            'availableProductTypes',
            'availableDeliveryClasses',
            'hasMixedReturnPolicies',
            'storeQuery'
        ));
    }
}

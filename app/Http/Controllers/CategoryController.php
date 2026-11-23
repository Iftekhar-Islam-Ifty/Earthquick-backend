<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/* =========================================================================
 * CATEGORY CONTROLLER
 * Shared marketplace category, subcategory and grouped collection pages.
 * ========================================================================= */
class CategoryController extends Controller
{
    /** Display one marketplace category. */
    public function showCategory(Request $request, string $slug): View
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $facetQuery = Product::publiclyAvailable()->where('category_id', $category->id);

        return $this->renderCatalog($request, $facetQuery, $category);
    }

    /** Display products in a configured marketplace category group. */
    public function showGroup(Request $request, string $group): View
    {
        $categoryGroup = config("catalog.category_groups.{$group}");
        abort_unless(is_array($categoryGroup), 404);

        $groupCategories = Category::whereIn('slug', $categoryGroup['categories'])
            ->where('is_active', true)
            ->with('subcategories')
            ->orderBy('sort_order')
            ->get();
        abort_if($groupCategories->isEmpty(), 404);

        $categoryIds = $groupCategories->pluck('id');
        $selectedGroupCategory = $groupCategories->firstWhere('slug', $request->query('category'));
        $selectedGroupSubcategory = null;

        if ($request->filled('subcategory')) {
            $subcategoryQuery = Subcategory::whereIn('category_id', $categoryIds)
                ->where('slug', $request->query('subcategory'));
            if ($selectedGroupCategory) {
                $subcategoryQuery->where('category_id', $selectedGroupCategory->id);
            }
            $selectedGroupSubcategory = $subcategoryQuery->first();
            if ($selectedGroupSubcategory) {
                $selectedGroupCategory = $groupCategories->firstWhere('id', $selectedGroupSubcategory->category_id);
            }
        }

        $filters = $request->query();
        if (! $selectedGroupCategory) {
            unset($filters['category']);
        }
        if (! $selectedGroupSubcategory) {
            unset($filters['subcategory']);
        }

        $facetQuery = Product::publiclyAvailable()->whereIn('category_id', $categoryIds);
        if ($selectedGroupCategory) {
            $facetQuery->where('category_id', $selectedGroupCategory->id);
        }
        if ($selectedGroupSubcategory) {
            $facetQuery->where('subcategory_id', $selectedGroupSubcategory->id);
        }

        $category = $selectedGroupCategory ?? $groupCategories->first();

        return $this->renderCatalog(
            $request,
            $facetQuery,
            $category,
            $selectedGroupSubcategory,
            $filters,
            [
                'categoryGroup' => $categoryGroup,
                'groupSlug' => $group,
                'groupCategories' => $groupCategories,
                'selectedGroupCategory' => $selectedGroupCategory,
                'selectedGroupSubcategory' => $selectedGroupSubcategory,
            ]
        );
    }

    /** Display one marketplace subcategory. */
    public function showSubcategory(Request $request, string $categorySlug, string $subcategorySlug): View
    {
        $category = Category::where('slug', $categorySlug)->where('is_active', true)->firstOrFail();
        $subcategory = Subcategory::where('slug', $subcategorySlug)
            ->where('category_id', $category->id)
            ->firstOrFail();
        $facetQuery = Product::publiclyAvailable()->where('subcategory_id', $subcategory->id);

        return $this->renderCatalog($request, $facetQuery, $category, $subcategory);
    }

    /** Reuse the shared marketplace listing and its facets for every category journey. */
    private function renderCatalog(
        Request $request,
        Builder $facetQuery,
        Category $category,
        ?Subcategory $subcategory = null,
        ?array $filters = null,
        array $extraViewData = []
    ): View {
        $filters ??= $request->query();
        $query = (clone $facetQuery)
            ->with(['category', 'subcategory', 'vendor', 'brand'])
            ->applyCatalogFilters($filters);

        if ($subcategory) {
            $query->where('subcategory_id', $subcategory->id);
        }

        match ($request->query('sort')) {
            'price-asc', 'price-low' => $query->orderBy('price', 'asc'),
            'price-desc', 'price-high' => $query->orderBy('price', 'desc'),
            'rating' => $query->orderBy('rating', 'desc'),
            'newest' => $query->latest(),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $subcategories = $category->subcategories;
        $availableFabrics = (clone $facetQuery)->whereNotNull('fabric')->distinct()->pluck('fabric');
        $availableProductTypes = (clone $facetQuery)->whereNotNull('product_type')->distinct()->pluck('product_type');
        $availableDeliveryClasses = (clone $facetQuery)->whereNotNull('delivery_class')->distinct()->pluck('delivery_class');
        $availableVendors = Vendor::where('is_active', true)
            ->whereIn('id', (clone $facetQuery)->whereNotNull('vendor_id')->distinct()->pluck('vendor_id'))
            ->orderBy('name')
            ->get();

        return view('category', array_merge(compact(
            'category',
            'subcategory',
            'products',
            'subcategories',
            'availableFabrics',
            'availableProductTypes',
            'availableDeliveryClasses',
            'availableVendors'
        ), $extraViewData));
    }
}

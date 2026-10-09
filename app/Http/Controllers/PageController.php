<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Vendor;
use Illuminate\Http\Response;
use Illuminate\View\View;

/* =========================================================================
 * PAGE CONTROLLER
 * Serves institutional and editorial static brand pages (e.g., About Us,
 * Brand Ecosystem, Artisan Philosophy, and Support).
 * ========================================================================= */

class PageController extends Controller
{
    /* =========================================================================
     * ABOUT US & BRAND STORY
     * Renders authentic Nous Telos brand philosophy and artisan story.
     * ========================================================================= */

    /**
     * Display the About Us and Nous Telos brand heritage page.
     *
     * @return \Illuminate\View\View
     */
    public function about(): View
    {
        return view('about');
    }

    public function sitemap(): Response
    {
        $urls = [
            route('home'),
            route('about'),
            route('policies.delivery-returns'),
            route('stores.index'),
        ];

        foreach (Vendor::where('is_active', true)->pluck('slug') as $slug) {
            $urls[] = route('stores.show', $slug);
        }

        foreach (Category::where('is_active', true)
            ->whereHas('products', fn ($query) => $query->publiclyAvailable())
            ->pluck('slug') as $slug) {
            $urls[] = route('category.show', $slug);
        }

        foreach (Subcategory::where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->whereHas('products', fn ($query) => $query->publiclyAvailable())
            ->with('category:id,slug')
            ->get(['id', 'category_id', 'slug']) as $subcategory) {
            $urls[] = route('subcategory.show', [
                'categorySlug' => $subcategory->category->slug,
                'subcategorySlug' => $subcategory->slug,
            ]);
        }

        foreach (Product::publiclyAvailable()->pluck('slug') as $slug) {
            $urls[] = route('product.show', $slug);
        }

        $entries = array_map(
            fn (string $url) => '  <url><loc>'.htmlspecialchars($url, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</loc></url>',
            array_unique($urls)
        );
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL
            .implode(PHP_EOL, $entries).PHP_EOL
            .'</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=900');
    }

    public function deliveryReturns(): View
    {
        return view('policies.delivery-returns');
    }
}

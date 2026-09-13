<?php

namespace App\Http\Controllers;

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
}

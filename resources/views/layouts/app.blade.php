<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
  <meta name="app-url" content="{{ url('/') }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Dynamic SEO Titles & Meta -->
    <title>@yield('title', 'Earthquick — Discover independent Bangladeshi brands')</title>
    <meta name="description" content="@yield('meta_description', 'Discover independent Bangladeshi brands, starting with Nous Telos heritage handloom and Bright electronics, at Earthquick.')" />
    <meta name="keywords" content="@yield('meta_keywords', 'earthquick, bangladesh marketplace, independent brands, nous telos, bright electronics')" />
    <link rel="canonical" href="@yield('canonical_url', url()->current())" />

    <!-- Open Graph Social Protocol -->
    <meta property="og:site_name" content="Earthquick" />
    <meta property="og:type" content="@yield('og_type', 'website')" />
    <meta property="og:title" content="@yield('og_title', 'Earthquick — Discover independent Bangladeshi brands')" />
    <meta property="og:description" content="@yield('og_description', 'Discover independent Bangladeshi brands at Earthquick.')" />
    <meta property="og:url" content="@yield('og_url', url()->current())" />
    <meta property="og:image" content="@yield('og_image', asset('images/hero/hero-main-saree-2.jpg'))" />

    <!-- Twitter Card Protocol -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="@yield('og_title', 'Earthquick — Discover independent Bangladeshi brands')" />
    <meta name="twitter:description" content="@yield('og_description', 'Discover independent Bangladeshi brands at Earthquick.')" />
    <meta name="twitter:image" content="@yield('og_image', asset('images/hero/hero-main-saree-2.jpg'))" />

    <!-- Google Fonts: Fraunces (display/serif) + Jost (body/sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <!-- Earthquick Custom Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <!-- Earthquick Dedicated Responsive Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}" />
    @stack('styles')
</head>
<body class="@yield('body_class')">
    <!-- Header & Navigation Bar -->
    @include('partials.navbar')

    <!-- Main Page Content -->
    @yield('content')

    <!-- Footer & Search Modal -->
    @include('partials.footer')

    <!-- Quick View Modal Component -->
    <div class="eq-quickview-modal" id="eq-quickview-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Product quick view">
        <div class="eq-quickview-backdrop" id="eq-quickview-backdrop"></div>
        <div class="eq-quickview-panel" id="eq-quickview-panel">
            <button type="button" class="eq-quickview-close" id="eq-quickview-close" aria-label="Close quick view">&times;</button>
            <div class="eq-quickview-body" id="eq-quickview-body"></div>
        </div>
    </div>

    <!-- Earthquick Core Scripts -->
    <script src="{{ asset('js/script.js') }}"></script>
    @stack('scripts')
</body>
</html>

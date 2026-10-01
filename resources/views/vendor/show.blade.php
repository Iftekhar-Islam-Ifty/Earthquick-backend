@extends('layouts.app')

@section('title', $vendor->name . ' — Storefront | Earthquick')
@section('meta_description', $vendor->description ?? ('Discover exclusive creations by ' . $vendor->name . ' on Earthquick.'))
@section('canonical_url', route('stores.show', $vendor->slug))
@section('body_class', 'eq-storefront-page')
@section('scroll_motion', '1')

@push('styles')
<style>
  .eq-store-toolbar { display: grid; gap: .85rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--eq-line); }
  .eq-store-categories { display: flex; flex-wrap: wrap; gap: .5rem; }
  .eq-store-category { display: inline-flex; align-items: center; min-height: 2.25rem; padding: .4rem .85rem; border: 1px solid var(--eq-line); border-radius: 999px; background: var(--eq-cream); color: var(--eq-charcoal); font-size: .8rem; font-weight: 500; text-decoration: none; }
  .eq-store-category.is-active { border-color: var(--eq-navy); background: var(--eq-navy); color: #fff; }
  .eq-store-category:focus-visible, .eq-store-filter-toggle:focus-visible, .eq-store-control:focus-visible { outline: 2px solid var(--eq-gold-dark); outline-offset: 2px; }
  .eq-store-tools { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .75rem; }
  .eq-store-results { margin: 0; color: var(--eq-charcoal-soft); font-size: .82rem; }
  .eq-store-sort { display: flex; align-items: center; gap: .45rem; margin: 0; font-size: .82rem; white-space: nowrap; }
  .eq-store-control { min-width: 0; min-height: 2.3rem; padding: .4rem .6rem; border: 1px solid var(--eq-line); border-radius: 6px; background: #fff; color: var(--eq-charcoal); font: inherit; }
  .eq-store-filters { border: 1px solid var(--eq-line); border-radius: 9px; background: #fff; }
  .eq-store-filter-toggle { display: flex; align-items: center; gap: .5rem; padding: .7rem .85rem; color: var(--eq-navy); font-size: .86rem; font-weight: 600; cursor: pointer; list-style: none; }
  .eq-store-filter-toggle::-webkit-details-marker { display: none; }
  .eq-store-filter-toggle::after { content: '+'; margin-left: auto; font-size: 1.1rem; line-height: 1; }
  .eq-store-filters[open] .eq-store-filter-toggle::after { content: '−'; }
  .eq-store-filter-count { display: inline-grid; place-items: center; min-width: 1.25rem; height: 1.25rem; padding: 0 .2rem; border-radius: 999px; background: var(--eq-navy); color: #fff; font-size: .7rem; }
  .eq-store-filter-panel { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .85rem; padding: 1rem; border-top: 1px solid var(--eq-line); }
  .eq-store-filter-panel.is-compact { display: flex; flex-wrap: wrap; align-items: end; }
  .eq-store-filter-panel.is-compact .eq-store-filter-field { width: min(100%, 270px); }
  .eq-store-filter-field { display: grid; align-content: start; gap: .35rem; min-width: 0; color: var(--eq-charcoal); font-size: .78rem; font-weight: 600; }
  .eq-store-price-fields { display: flex; gap: .4rem; }
  .eq-store-price-fields .eq-store-control { width: 50%; }
  .eq-store-filter-actions { display: flex; align-items: center; gap: .8rem; grid-column: 1 / -1; }
  .eq-store-clear { color: var(--eq-navy); font-size: .8rem; text-underline-offset: 2px; }
  .eq-store-active { display: flex; flex-wrap: wrap; align-items: center; gap: .4rem; }
  .eq-store-active span { padding: .25rem .55rem; border-radius: 999px; background: var(--eq-cream); color: var(--eq-charcoal-soft); font-size: .72rem; }
  @media (max-width: 760px) {
    .eq-store-toolbar { gap: .7rem; }
    .eq-store-categories { flex-wrap: nowrap; overflow-x: auto; padding-bottom: .3rem; scrollbar-width: thin; }
    .eq-store-category { flex: 0 0 auto; }
    .eq-store-filter-panel { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width: 480px) {
    .eq-store-tools { align-items: flex-start; }
    .eq-store-sort { width: 100%; justify-content: space-between; }
    .eq-store-sort .eq-store-control { max-width: 68%; }
    .eq-store-filter-panel { grid-template-columns: minmax(0, 1fr); padding: .8rem; }
    .eq-store-filter-actions { flex-wrap: wrap; }
  }
</style>
@endpush

@section('content')
<main id="main-content" tabindex="-1">

  <!-- Breadcrumb Navigation -->
  <nav class="eq-breadcrumb" aria-label="Breadcrumb" style="padding: 0.85rem 0; border-bottom: 1px solid var(--eq-line); background: var(--eq-cream-soft, #faf8f5);">
    <div class="eq-container">
      <ol class="eq-breadcrumb__list" style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.45rem; list-style: none; margin: 0; padding: 0; font-size: 0.82rem; color: var(--eq-charcoal-soft);">
        <li><a href="{{ route('home') }}" style="color: inherit; text-decoration: none;">Home</a></li>
        <li><span style="opacity: 0.5;">&rsaquo;</span></li>
        <li><a href="{{ route('stores.index') }}" style="color: inherit; text-decoration: none;">Brand Stores</a></li>
        <li><span style="opacity: 0.5;">&rsaquo;</span></li>
        <li style="color: var(--eq-gold-dark); font-weight: 500;">{{ $vendor->name }}</li>
      </ol>
    </div>
  </nav>

  <!-- Store Header Banner -->
  <section style="background: linear-gradient(135deg, var(--eq-navy) 0%, #1a364a 100%); color: #ffffff; position: relative; overflow: hidden; border-bottom: 1px solid var(--eq-line);">
    @if($vendor->banner)
      <div style="position: absolute; inset: 0; opacity: 0.25; z-index: 1;">
        <img src="{{ asset($vendor->banner) }}" alt="{{ $vendor->name }} Cover" style="width: 100%; height: 100%; object-fit: cover;" />
      </div>
    @endif

    <div class="eq-container eq-reveal" style="position: relative; z-index: 2; padding: 3rem 1rem 2.5rem;">
      <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
        
        <!-- Brand Logo / Monogram -->
        <div style="width: 80px; height: 80px; border-radius: 50%; background: #ffffff; border: 3px solid rgba(255,255,255,0.3); box-shadow: 0 4px 15px rgba(0,0,0,0.25); display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-weight: 700; color: var(--eq-navy); font-size: 1.6rem; overflow: hidden; flex-shrink: 0;">
          @if($vendor->logo)
            <img src="{{ asset($vendor->logo) }}" alt="{{ $vendor->name }}" style="width: 100%; height: 100%; object-fit: cover;" />
          @else
            {{ substr($vendor->name, 0, 2) }}
          @endif
        </div>

        <!-- Brand Identity Info -->
        <div style="flex: 1; min-width: 260px;">
          <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.25rem;">
            <h1 style="font-family: var(--font-display); font-size: 2rem; margin: 0; font-weight: 600; color: #ffffff; line-height: 1.2;">
              {{ $vendor->name }}
            </h1>
            <span style="background: rgba(201, 150, 47, 0.25); color: #f3cf7a; border: 1px solid rgba(201, 150, 47, 0.45); font-size: 0.65rem; font-weight: 600; letter-spacing: 0.08em; padding: 0.15rem 0.5rem; border-radius: 4px; text-transform: uppercase;">
              {{ $vendor->vendor_code }}
            </span>
          </div>

          @if($vendor->tagline)
            <p style="font-size: 0.95rem; color: #f3cf7a; margin: 0 0 0.5rem; font-weight: 500;">
              {{ $vendor->tagline }}
            </p>
          @endif

          @if($vendor->description)
            <p style="font-size: 0.86rem; line-height: 1.5; color: rgba(255,255,255,0.8); margin: 0; max-width: 700px;">
              {{ $vendor->description }}
            </p>
          @endif
        </div>

        <!-- Store Stats -->
        <div style="display: flex; gap: 1rem; align-items: center; margin-left: auto;">
          <div style="text-align: center; background: rgba(255,255,255,0.08); backdrop-filter: blur(4px); padding: 0.65rem 1.25rem; border-radius: 6px; border: 1px solid rgba(255,255,255,0.15);">
            <div style="font-size: 1.35rem; font-weight: 700; color: #ffffff; font-family: var(--font-display);">{{ $allProductsCount }}</div>
            <div style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.08em; color: rgba(255,255,255,0.65);">Creations</div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Storefront Products Section -->
  <section style="padding: 2.5rem 0 5rem;">
    <div class="eq-container">
      
      @php
        $selectedFilters = array_intersect_key($storeQuery, array_flip(['product_type', 'delivery_class', 'returnable', 'min_price', 'max_price']));
        $clearFilterQuery = array_intersect_key($storeQuery, array_flip(['category', 'sort']));
        $showProductTypeFilter = $availableProductTypes->count() > 1 || isset($storeQuery['product_type']);
        $showDeliveryFilter = $availableDeliveryClasses->count() > 1 || isset($storeQuery['delivery_class']);
        $showReturnFilter = $hasMixedReturnPolicies || isset($storeQuery['returnable']);
      @endphp

      <div class="eq-store-toolbar">
        <nav class="eq-store-categories" aria-label="Browse {{ $vendor->name }} categories">
          <a class="eq-store-category {{ !isset($storeQuery['category']) ? 'is-active' : '' }}" @if(!isset($storeQuery['category'])) aria-current="page" @endif
             href="{{ route('stores.show', array_merge(['slug' => $vendor->slug], array_diff_key($storeQuery, ['category' => true]))) }}">All Pieces ({{ $allProductsCount }})</a>
          @foreach($categories as $cat)
            <a class="eq-store-category {{ ($storeQuery['category'] ?? null) === $cat->slug ? 'is-active' : '' }}" @if(($storeQuery['category'] ?? null) === $cat->slug) aria-current="page" @endif
               href="{{ route('stores.show', array_merge(['slug' => $vendor->slug], $storeQuery, ['category' => $cat->slug])) }}">{{ $cat->name }}</a>
          @endforeach
        </nav>

        @if($allProductsCount > 0)
          <div class="eq-store-tools">
            <p class="eq-store-results">{{ $products->total() }} {{ $products->total() === 1 ? 'piece' : 'pieces' }} found</p>
            <form class="eq-store-sort" method="GET" action="{{ route('stores.show', $vendor->slug) }}">
              @foreach(array_diff_key($storeQuery, ['sort' => true]) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
              @endforeach
              <label for="store-sort">Sort by</label>
              <select class="eq-store-control" name="sort" id="store-sort" onchange="this.form.submit()">
                <option value="newest" @selected(!in_array($storeQuery['sort'] ?? null, ['price-asc', 'price-desc'], true))>Newest Arrivals</option>
                <option value="price-asc" @selected(($storeQuery['sort'] ?? null) === 'price-asc')>Price: Low to High</option>
                <option value="price-desc" @selected(($storeQuery['sort'] ?? null) === 'price-desc')>Price: High to Low</option>
              </select>
              <noscript><button type="submit" class="eq-btn eq-btn--outline">Sort</button></noscript>
            </form>
          </div>

          <details class="eq-store-filters">
            <summary class="eq-store-filter-toggle">Filters @if(count($selectedFilters)) <span class="eq-store-filter-count">{{ count($selectedFilters) }}</span> @endif</summary>
            <form class="eq-store-filter-panel {{ !$showProductTypeFilter && !$showDeliveryFilter && !$showReturnFilter ? 'is-compact' : '' }}" method="GET" action="{{ route('stores.show', $vendor->slug) }}">
              @foreach(array_intersect_key($storeQuery, array_flip(['category', 'sort'])) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
              @endforeach
              @if($showProductTypeFilter)
                <label class="eq-store-filter-field">Product type
                  <select class="eq-store-control" name="product_type">
                    <option value="">All types</option>
                    @foreach($availableProductTypes as $type)
                      <option value="{{ $type }}" @selected(($storeQuery['product_type'] ?? null) === $type)>{{ config("catalog.product_types.{$type}.label", ucfirst($type)) }}</option>
                    @endforeach
                  </select>
                </label>
              @endif
              @if($showDeliveryFilter)
                <label class="eq-store-filter-field">Delivery type
                  <select class="eq-store-control" name="delivery_class">
                    <option value="">All delivery types</option>
                    @foreach($availableDeliveryClasses as $deliveryClass)
                      <option value="{{ $deliveryClass }}" @selected(($storeQuery['delivery_class'] ?? null) === $deliveryClass)>{{ config("catalog.delivery_classes.{$deliveryClass}", ucfirst($deliveryClass)) }}</option>
                    @endforeach
                  </select>
                </label>
              @endif
              @if($showReturnFilter)
                <label class="eq-store-filter-field">Return eligibility
                  <select class="eq-store-control" name="returnable">
                    <option value="">All items</option>
                    <option value="1" @selected(($storeQuery['returnable'] ?? null) === '1')>Return eligible</option>
                    <option value="0" @selected(($storeQuery['returnable'] ?? null) === '0')>Final sale</option>
                  </select>
                </label>
              @endif
              <div class="eq-store-filter-field">
                <span>Price range (৳)</span>
                <div class="eq-store-price-fields">
                  <input class="eq-store-control" type="number" name="min_price" value="{{ $storeQuery['min_price'] ?? '' }}" min="0" step="1" placeholder="Min" aria-label="Minimum price" />
                  <input class="eq-store-control" type="number" name="max_price" value="{{ $storeQuery['max_price'] ?? '' }}" min="0" step="1" placeholder="Max" aria-label="Maximum price" />
                </div>
              </div>
              <div class="eq-store-filter-actions">
                <button type="submit" class="eq-btn eq-btn--outline" style="padding: .5rem .9rem; font-size: .8rem;">Apply filters</button>
                @if(count($selectedFilters))
                  <a class="eq-store-clear" href="{{ route('stores.show', array_merge(['slug' => $vendor->slug], $clearFilterQuery)) }}">Clear filters</a>
                @endif
              </div>
            </form>
          </details>

          @if(count($selectedFilters))
            <div class="eq-store-active" aria-label="Applied filters">
              @foreach($selectedFilters as $key => $value)
                <span>{{ match ($key) {
                  'product_type' => config("catalog.product_types.{$value}.label", ucfirst($value)),
                  'delivery_class' => config("catalog.delivery_classes.{$value}", ucfirst($value)),
                  'returnable' => $value === '1' ? 'Return eligible' : 'Final sale',
                  'min_price' => 'From ৳'.$value,
                  'max_price' => 'Up to ৳'.$value,
                } }}</span>
              @endforeach
              <a class="eq-store-clear" href="{{ route('stores.show', array_merge(['slug' => $vendor->slug], $clearFilterQuery)) }}">Clear filters</a>
            </div>
          @endif
        @endif
      </div>

      <!-- Catalog Grid -->
      <div class="eq-catalog-grid eq-reveal" id="store-catalog-grid">
        @forelse($products as $product)
          <article class="eq-product-card" id="card-{{ $product->id }}" data-id="{{ $product->id }}">
            <div class="eq-product-card__frame">
              @if($product->badge)
                <span class="eq-product-badge eq-product-badge--{{ $product->badge_type ?? 'ready' }}">{{ $product->badge }}</span>
              @endif

              <button type="button" class="eq-card-wishlist-btn" aria-label="Add {{ $product->name }} to wishlist" data-wishlist-id="{{ $product->id }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
              </button>

              <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" loading="lazy" />
              @if($product->alt_image)
                <img src="{{ asset($product->alt_image) }}" alt="{{ $product->name }} alternate view" class="eq-product-card__img--alt" loading="lazy" />
              @endif

              <button type="button" class="eq-product-card__quick-add" data-action="quick-view" aria-label="Quick view {{ $product->name }}">
                Quick view
              </button>
            </div>

            <div class="eq-product-card__body">
              <span class="eq-product-card__category">{{ $product->subcategory ? $product->subcategory->name : ($product->category ? $product->category->name : '') }}@if($product->fabric) &bull; {{ $product->fabric }}@endif</span>
              <h3 class="eq-product-card__name">
                <a href="{{ route('product.show', $product->slug) }}" class="eq-product-card__link">{{ $product->name }}</a>
              </h3>
              <div class="eq-product-card__price">
                @if($product->old_price)
                  <span class="eq-price--old">৳{{ number_format($product->old_price) }}</span>
                @endif
                <span>৳{{ number_format($product->price) }}</span>
              </div>
              <span class="eq-product-stock-tag">{{ $product->stock_quantity <= 3 ? '⚡ Only ' . $product->stock_quantity . ' left in stock' : 'Ready to Ship' }}</span>
            </div>
          </article>
        @empty
          <div class="eq-catalog-empty" style="grid-column: 1 / -1; padding: 4.5rem 1.5rem; text-align: center;">
            <svg class="eq-catalog-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 44px; height: 44px; margin: 0 auto 1rem; color: var(--eq-gold-dark);">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            @if($allProductsCount > 0)
              <h3 style="font-family: var(--font-display); font-size: 1.35rem; color: var(--eq-navy); margin-bottom: 0.5rem; font-weight: 600;">No pieces match this selection</h3>
              <p style="max-width: 460px; margin: 0 auto 1.75rem; color: var(--eq-charcoal-soft); font-size: 0.88rem; line-height: 1.6;">Try another category or clear the filters to see more from {{ $vendor->name }}.</p>
              <a href="{{ route('stores.show', $vendor->slug) }}" class="eq-btn eq-btn--outline" style="padding: 0.55rem 1.25rem; font-size: 0.82rem; text-decoration: none;">View all pieces</a>
            @else
              <h3 style="font-family: var(--font-display); font-size: 1.35rem; color: var(--eq-navy); margin-bottom: 0.5rem; font-weight: 600;">Collection in Preparation</h3>
              <p style="max-width: 460px; margin: 0 auto 1.75rem; color: var(--eq-charcoal-soft); font-size: 0.88rem; line-height: 1.6;">The {{ $vendor->name }} catalog is currently being prepared for the Earthquick collective. New curated arrivals will debut here shortly.</p>
              <a href="{{ route('stores.index') }}" class="eq-btn eq-btn--outline" style="padding: 0.55rem 1.25rem; font-size: 0.82rem; text-decoration: none;">&larr; Discover All Partner Brands</a>
            @endif
          </div>
        @endforelse
      </div>

      <!-- Pagination -->
      @if($products->hasPages())
        @include('partials.pagination-polished', ['paginator' => $products, 'summaryMode' => 'pages'])
      @endif

    </div>
  </section>

</main>
@endsection

@extends('layouts.app')

@section('title', ($query ? 'Search: "' . e($query) . '"' : 'Search Collection') . ' — Earthquick / Nous Telos')
@section('meta_description', 'Discover handcrafted sarees, bespoke three-piece ensembles, and accessories from Earthquick Studio.')
@section('body_class', 'eq-catalog-page')

@section('content')
  <main id="main-content">

    <!-- Breadcrumb Navigation -->
    <div class="eq-container">
      <nav class="eq-breadcrumbs" aria-label="Breadcrumb">
        <ol>
          <li><a href="{{ route('home') }}">Home</a></li>
          @if($query)
            <li><a href="{{ route('search') }}">Search</a></li>
            <li aria-current="page">&ldquo;{{ $query }}&rdquo;</li>
          @else
            <li aria-current="page">Catalog Search</li>
          @endif
        </ol>
      </nav>
    </div>

    <!-- Search Hero / Banner -->
    <section class="eq-cat-banner" id="search-banner">
      <div class="eq-container">
        <div class="eq-cat-banner__inner">
          <span class="eq-cat-banner__eyebrow">NOUS TELOS STUDIO</span>
          <h1 class="eq-cat-banner__title" id="search-title">
            @if($query)
              Search Results for &ldquo;{{ $query }}&rdquo;
            @else
              Explore the Full Collection
            @endif
          </h1>
          <p class="eq-cat-banner__desc">
            @if($query)
              Discovered <strong>{{ $products->total() }}</strong> curated handcrafted piece{{ $products->total() === 1 ? '' : 's' }} matching your search.
            @else
              Browse our complete catalog of handloom weaves, tailored garments, and heritage essentials.
            @endif
          </p>

          <!-- In-Page Search Bar -->
          <form action="{{ route('search') }}" method="GET" style="max-width: 540px; margin: 1.25rem auto 0; position: relative;">
            <div style="display: flex; align-items: center; background: #ffffff; border: 1px solid var(--eq-line); border-radius: 999px; padding: 0.3rem 0.5rem 0.3rem 1.25rem; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="color: var(--eq-charcoal-soft); flex-shrink: 0; margin-right: 0.65rem;">
                <circle cx="11" cy="11" r="7"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
              <input 
                type="search" 
                name="q" 
                value="{{ $query }}" 
                placeholder="Search sarees, sets, bags, home decor..." 
                style="flex: 1; border: none; outline: none; background: transparent; font-family: var(--font-body); font-size: 0.95rem; color: var(--eq-charcoal);" 
              />
              <button type="submit" class="eq-btn eq-btn--primary eq-btn--pill" style="padding: 0.55rem 1.4rem; font-size: 0.84rem; border: none; cursor: pointer;">
                Search
              </button>
            </div>
          </form>

        </div>
      </div>
    </section>

    <!-- Search Results Body -->
    <div class="eq-container" style="margin-top: 2rem; margin-bottom: 5rem;">

      <!-- Controls Toolbar -->
      <div class="eq-cat-toolbar" id="catalog-toolbar">
        <div class="eq-cat-toolbar__left">
          <!-- Result Counter -->
          <span class="eq-cat-counter" id="catalog-counter">
            Showing <strong>{{ $products->total() }}</strong> handcrafted {{ $products->total() === 1 ? 'piece' : 'pieces' }}
          </span>
        </div>

        <div class="eq-cat-toolbar__right">
          <!-- Sorting Selector -->
          <div class="eq-sort-wrapper">
            <label for="search-sort-select" class="eq-sort-label">Sort by:</label>
            <select class="eq-sort-select" id="search-sort-select" onchange="updateSortParam(this.value)">
              <option value="featured" {{ (!request('sort') || request('sort') == 'featured') ? 'selected' : '' }}>Featured Curations</option>
              <option value="price-asc" {{ (request('sort') == 'price-asc' || request('sort') == 'price-low') ? 'selected' : '' }}>Price: Low to High</option>
              <option value="price-desc" {{ (request('sort') == 'price-desc' || request('sort') == 'price-high') ? 'selected' : '' }}>Price: High to Low</option>
              <option value="latest" {{ (request('sort') == 'latest' || request('sort') == 'newest') ? 'selected' : '' }}>Newest Arrivals</option>
            </select>
          </div>

          <!-- Grid Display View Switcher (Desktop) -->
          <div class="eq-grid-view-switch" aria-label="Grid layout switcher">
            <button type="button" class="eq-grid-view-btn" data-cols="2" aria-label="2 column layout">
              <svg viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="8" height="18" rx="1"/><rect x="13" y="3" width="8" height="18" rx="1"/></svg>
            </button>
            <button type="button" class="eq-grid-view-btn is-active" data-cols="3" aria-label="3 column layout">
              <svg viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="5" height="18" rx="1"/><rect x="9.5" y="3" width="5" height="18" rx="1"/><rect x="16" y="3" width="5" height="18" rx="1"/></svg>
            </button>
            <button type="button" class="eq-grid-view-btn" data-cols="4" aria-label="4 column layout">
              <svg viewBox="0 0 24 24" fill="currentColor"><rect x="2" y="3" width="4" height="18" rx="1"/><rect x="7.33" y="3" width="4" height="18" rx="1"/><rect x="12.66" y="3" width="4" height="18" rx="1"/><rect x="18" y="3" width="4" height="18" rx="1"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Main Results Area -->
      <section class="eq-catalog-main" aria-label="Search results listings" style="margin-top: 1.5rem;">
        
        @if($products->count() > 0)
          <div class="eq-catalog-grid view-3col" id="catalog-grid">
            @foreach($products as $product)
              <article class="eq-product-card" id="card-{{ $product->id }}" data-id="{{ $product->id }}" data-category="{{ $product->category ? $product->category->slug : '' }}">
                <div class="eq-product-card__frame">
                  <!-- Status Badge -->
                  @if($product->badge)
                    <span class="eq-product-badge eq-product-badge--{{ $product->badge_type ?? 'ready' }}">{{ $product->badge }}</span>
                  @endif
                  
                  <!-- Wishlist Toggle -->
                  <button type="button" class="eq-card-wishlist-btn" aria-label="Add {{ $product->name }} to wishlist" data-wishlist-id="{{ $product->id }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                  </button>

                  <!-- Primary & Alternate Images -->
                  <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" loading="lazy" />
                  @if($product->alt_image)
                    <img src="{{ asset($product->alt_image) }}" alt="{{ $product->name }} alternate view" class="eq-product-card__img--alt" loading="lazy" />
                  @endif

                  <!-- Quick Inspect / View Button -->
                  <button type="button" class="eq-product-card__quick-add" data-action="quick-view">
                    Quick Inspect &bull; Add
                  </button>
                </div>

                <div class="eq-product-card__body">
                  <span class="eq-product-card__category">
                    {{ $product->subcategory ? $product->subcategory->name : ($product->category ? $product->category->name : 'Nous Telos') }} &bull; {{ $product->fabric ?? 'Handloom' }}
                  </span>
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
            @endforeach
          </div>

          <!-- Pagination -->
          @if($products->hasPages())
            <div class="eq-pagination-wrap" id="catalog-pagination" style="margin-top: 3.5rem; text-align: center;">
              {{ $products->links() }}
            </div>
          @endif

        @else
          <!-- Elegant No Results Empty State -->
          <div class="eq-catalog-empty" style="text-align: center; padding: 4rem 1.5rem; background: #ffffff; border: 1px solid var(--eq-line); border-radius: var(--radius-sm); margin: 1rem 0 3rem;">
            <svg class="eq-catalog-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 54px; height: 54px; margin: 0 auto 1.25rem; color: var(--eq-charcoal-muted);">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              <line x1="8" y1="11" x2="14" y2="11"></line>
            </svg>
            <h2 class="eq-catalog-empty__title" style="font-family: var(--font-display); font-size: 1.45rem; color: var(--eq-navy); margin-bottom: 0.5rem;">
              No matching pieces found
            </h2>
            <p class="eq-catalog-empty__text" style="color: var(--eq-charcoal-soft); max-width: 480px; margin: 0 auto 1.75rem; font-size: 0.92rem; line-height: 1.5;">
              @if($query)
                We couldn't find any creations matching &ldquo;<strong>{{ $query }}</strong>&rdquo;. Try searching with broader terms or explore our curated signature collections below.
              @else
                Please enter a search term above to explore our handcrafted collections.
              @endif
            </p>

            <div style="margin-bottom: 1.75rem;">
              <a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'saree']) }}" class="eq-btn eq-btn--primary eq-btn--pill" style="padding: 0.65rem 1.8rem; margin: 0 0.35rem; text-decoration: none; display: inline-block;">
                Explore All Sarees &rarr;
              </a>
            </div>

            <!-- Direct shortcut buttons to popular collections -->
            <div style="margin-top: 2rem; border-top: 1px solid var(--eq-line); padding-top: 1.75rem;">
              <span style="font-size: 0.8rem; letter-spacing: 0.08em; text-transform: uppercase; color: var(--eq-charcoal-soft); font-weight: 500;">
                Popular Featured Collections
              </span>
              <div class="eq-shop-shortcuts" style="max-width: 650px; margin: 1rem auto 0; grid-template-columns: repeat(4, 1fr);">
                <a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'saree']) }}" class="eq-shop-shortcut-card">
                  <strong>Saree</strong>
                  <span>Jamdani &amp; Katan</span>
                </a>
                <a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'three-piece']) }}" class="eq-shop-shortcut-card">
                  <strong>Three Piece</strong>
                  <span>Artisanal Sets</span>
                </a>
                <a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'two-piece']) }}" class="eq-shop-shortcut-card">
                  <strong>Two Piece</strong>
                  <span>Modern Co-ords</span>
                </a>
                <a href="{{ route('category.show', 'bags') }}" class="eq-shop-shortcut-card">
                  <strong>Bags</strong>
                  <span>Leather &amp; Canvas</span>
                </a>
              </div>
            </div>

          </div>
        @endif

      </section>
    </div>

  </main>
@endsection

@push('scripts')
<script>
  // Sorting helper
  function updateSortParam(sortValue) {
    const url = new URL(window.location.href);
    if (sortValue) {
      url.searchParams.set('sort', sortValue);
    } else {
      url.searchParams.delete('sort');
    }
    url.searchParams.delete('page');
    window.location.href = url.toString();
  }

  // Grid view switcher (2, 3, 4 columns)
  document.addEventListener('DOMContentLoaded', function () {
    const grid = document.querySelector('#catalog-grid');
    const viewButtons = document.querySelectorAll('.eq-grid-view-btn');

    if (grid && viewButtons.length) {
      viewButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          viewButtons.forEach(b => b.classList.remove('is-active'));
          btn.classList.add('is-active');

          const cols = btn.getAttribute('data-cols');
          grid.classList.remove('view-2col', 'view-3col', 'view-4col');
          grid.classList.add('view-' + cols + 'col');
        });
      });
    }
  });
</script>
@endpush


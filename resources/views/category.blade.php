@extends('layouts.app')

@section('title', (isset($subcategory) ? $subcategory->name : $category->name) . ' — Earthquick / Nous Telos')
@section('meta_description', $category->description ?? 'Explore artisanal handloom sarees, bespoke ensembles, and lifestyle essentials from Earthquick.')
@section('canonical_url', url()->current())
@section('og_title', (isset($subcategory) ? $subcategory->name : $category->name) . ' — Nous Telos | Earthquick')
@section('og_description', $category->description ?? 'Explore artisanal handloom sarees, bespoke ensembles, and lifestyle essentials from Earthquick.')
@section('og_image', asset($category->image ?? 'images/hero/hero-main-saree-2.jpg'))
@section('body_class', 'eq-catalog-page')

@section('content')
  <!-- ===================================================================
       MAIN CONTENT CONTAINER
       =================================================================== -->
  <main id="main-content">

    <!-- Breadcrumb Navigation -->
    <div class="eq-container">
      <nav class="eq-breadcrumbs" aria-label="Breadcrumb">
        <ol>
          <li><a href="{{ route('home') }}">Home</a></li>
          @if(isset($subcategory))
            <li><a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a></li>
            <li aria-current="page">{{ $subcategory->name }}</li>
          @else
            <li aria-current="page">{{ $category->name }}</li>
          @endif
        </ol>
      </nav>
    </div>

    <!-- Category Banner & Subcategory Pills -->
    <section class="eq-cat-banner" id="cat-banner">
      <div class="eq-container">
        <div class="eq-cat-banner__inner">
          <span class="eq-cat-banner__eyebrow" id="cat-banner-eyebrow">NOUS TELOS STUDIO</span>
          <h1 class="eq-cat-banner__title" id="cat-banner-title">
            {{ isset($subcategory) ? $subcategory->name : $category->name }} Collection
          </h1>
          <p class="eq-cat-banner__desc" id="cat-banner-desc">
            {{ $category->description ?? 'Artisanal sarees woven on heritage wooden looms, alongside impeccably tailored three-piece and modern co-ord ensembles.' }}
          </p>

          <!-- Subcategory Filter Pills Container -->
          @if(isset($subcategories) && $subcategories->count() > 0)
            <div class="eq-cat-pills" id="cat-pills-container">
              <a href="{{ route('category.show', $category->slug) }}" 
                 class="eq-cat-pill {{ !isset($subcategory) ? 'is-active' : '' }}">
                All {{ $category->name }}
              </a>
              @foreach($subcategories as $sub)
                <a href="{{ route('subcategory.show', ['categorySlug' => $category->slug, 'subcategorySlug' => $sub->slug]) }}" 
                   class="eq-cat-pill {{ (isset($subcategory) && $subcategory->id === $sub->id) ? 'is-active' : '' }}">
                  {{ $sub->name }}
                </a>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </section>

    <!-- Catalog Body Layout: Filter Sidebar + Product Grid -->
    <div class="eq-container">
      
      <!-- Catalog Controls Toolbar -->
      <div class="eq-cat-toolbar" id="catalog-toolbar">
        <div class="eq-cat-toolbar__left">
          <!-- Mobile Filter Drawer Trigger Button -->
          <button type="button" class="eq-mobile-filter-trigger" id="mobile-filter-trigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="4" y1="21" x2="4" y2="14"></line>
              <line x1="4" y1="10" x2="4" y2="3"></line>
              <line x1="12" y1="21" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12" y2="3"></line>
              <line x1="20" y1="21" x2="20" y2="16"></line>
              <line x1="20" y1="12" x2="20" y2="3"></line>
              <line x1="1" y1="14" x2="7" y2="14"></line>
              <line x1="9" y1="8" x2="15" y2="8"></line>
              <line x1="17" y1="16" x2="23" y2="16"></line>
            </svg>
            <span>Filters</span>
          </button>

          <!-- Result Counter -->
          <span class="eq-cat-counter" id="catalog-counter">
            Showing <strong>{{ $products->total() }}</strong> handcrafted pieces
          </span>
        </div>

        <div class="eq-cat-toolbar__right">
          <!-- Sorting Selector -->
          <div class="eq-sort-wrapper">
            <label for="catalog-sort-select" class="eq-sort-label">Sort by:</label>
            <select class="eq-sort-select" id="catalog-sort-select" onchange="updateFilterParam('sort', this.value)">
              <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Featured Curations</option>
              <option value="price-low" {{ request('sort') == 'price-low' ? 'selected' : '' }}>Price: Low to High</option>
              <option value="price-high" {{ request('sort') == 'price-high' ? 'selected' : '' }}>Price: High to Low</option>
              <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
              <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
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

      <!-- Active Filter Tags / Chips -->
      <div class="eq-active-filters" id="active-filter-chips">
        @php
          $hasActiveFilters = request('fabric') || request('in_stock') || (request('max_price') && request('max_price') < 30000);
        @endphp

        @if($hasActiveFilters)
          @if(request('fabric'))
            <span class="eq-active-chip">
              <span>Fabric: {{ request('fabric') }}</span>
              <button type="button" aria-label="Remove filter" onclick="updateFilterParam('fabric', null)">&times;</button>
            </span>
          @endif
          @if(request('max_price') && request('max_price') < 30000)
            <span class="eq-active-chip">
              <span>Under ৳{{ number_format(request('max_price')) }}</span>
              <button type="button" aria-label="Remove filter" onclick="updateFilterParam('max_price', null)">&times;</button>
            </span>
          @endif
          @if(request('in_stock'))
            <span class="eq-active-chip">
              <span>In Stock Only</span>
              <button type="button" aria-label="Remove filter" onclick="updateFilterParam('in_stock', null)">&times;</button>
            </span>
          @endif
          <button type="button" class="eq-clear-all-btn" onclick="window.location.href='{{ url()->current() }}'">Clear All</button>
        @endif
      </div>

      <!-- Main Two-Column Layout -->
      <div class="eq-catalog-layout">

        <!-- Mobile Filter Backdrop -->
        <div class="eq-filter-backdrop" id="filter-backdrop"></div>

        <!-- =============================================================
             LEFT COLUMN: FILTER SIDEBAR
             ============================================================= -->
        <aside class="eq-filter-sidebar" id="filter-sidebar" aria-label="Product Filters">
          <div class="eq-filter-sidebar__header">
            <h2 class="eq-filter-sidebar__title">Filter Collection</h2>
            <button type="button" class="eq-filter-sidebar__reset" id="filter-sidebar-reset" onclick="window.location.href='{{ url()->current() }}'">
              <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
              <span>Reset All</span>
            </button>
          </div>

          <!-- Filter Group 1: Price Range -->
          <div class="eq-filter-group" id="filter-group-price">
            <button type="button" class="eq-filter-group__header" aria-expanded="true">
              <span>Price Range</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="eq-filter-group__body">
              <div class="eq-price-slider-wrap">
                <input type="range" class="eq-price-range-slider" id="filter-price-slider" min="2000" max="30000" step="500" value="{{ request('max_price', 30000) }}" oninput="document.getElementById('filter-price-max').value = this.value;" onchange="updateFilterParam('max_price', this.value)" />
                <div class="eq-price-inputs">
                  <div class="eq-price-input-box">
                    <span>৳</span>
                    <input type="number" id="filter-price-min" value="2000" readonly />
                  </div>
                  <span style="color: var(--eq-charcoal-muted);">-</span>
                  <div class="eq-price-input-box">
                    <span>৳</span>
                    <input type="number" id="filter-price-max" value="{{ request('max_price', 30000) }}" min="2000" max="30000" step="500" onchange="document.getElementById('filter-price-slider').value = this.value; updateFilterParam('max_price', this.value);" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Filter Group 2: Fabric & Material -->
          <div class="eq-filter-group" id="filter-group-fabric">
            <button type="button" class="eq-filter-group__header" aria-expanded="true">
              <span>Fabric &amp; Weave</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="eq-filter-group__body">
              @php
                $defaultFabrics = ['Jamdani', 'Tangail', 'Pure Silk', 'Half Silk', 'Mulmul', 'Linen', 'Leather', 'Cotton', 'Muslin'];
                $displayFabrics = ($availableFabrics && $availableFabrics->count() > 0) ? $availableFabrics : $defaultFabrics;
              @endphp

              @foreach($displayFabrics as $fab)
                <label class="eq-filter-option">
                  <span class="eq-filter-option__left">
                    <input type="checkbox" data-filter="fabric" value="{{ $fab }}" {{ request('fabric') == $fab ? 'checked' : '' }} onchange="updateFilterParam('fabric', this.checked ? this.value : null)" />
                    <span>{{ $fab }}</span>
                  </span>
                </label>
              @endforeach
            </div>
          </div>

          <!-- Filter Group 3: Availability -->
          <div class="eq-filter-group" id="filter-group-stock">
            <button type="button" class="eq-filter-group__header" aria-expanded="true">
              <span>Availability</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="eq-filter-group__body">
              <label class="eq-filter-option">
                <span class="eq-filter-option__left">
                  <input type="checkbox" id="filter-in-stock" {{ request('in_stock') ? 'checked' : '' }} onchange="updateFilterParam('in_stock', this.checked ? '1' : null)" />
                  <span>In Stock Only</span>
                </span>
              </label>
            </div>
          </div>

        </aside>

        <!-- =============================================================
             RIGHT COLUMN: PRODUCT CATALOG GRID
             ============================================================= -->
        <section class="eq-catalog-main" aria-label="Product listings">
          <div class="eq-catalog-grid" id="catalog-grid">
            @forelse($products as $product)
              <article class="eq-product-card" id="card-{{ $product->id }}" data-id="{{ $product->id }}" data-category="{{ $category->slug }}">
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

                  <!-- Primary & Alternate Images with Smooth Hover Zoom -->
                  <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" loading="lazy" />
                  @if($product->alt_image)
                    <img src="{{ asset($product->alt_image) }}" alt="{{ $product->name }} alternate view" class="eq-product-card__img--alt" loading="lazy" />
                  @endif

                  <!-- Quick View / Add Button -->
                  <button type="button" class="eq-product-card__quick-add" data-action="quick-view" onclick="window.location.href='{{ route('product.show', $product->slug) }}'">
                    Quick Inspect &bull; Add
                  </button>
                </div>

                <div class="eq-product-card__body">
                  <span class="eq-product-card__category">{{ $product->subcategory ? $product->subcategory->name : $product->category->name }} &bull; {{ $product->fabric ?? 'Handloom' }}</span>
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
              <div class="eq-catalog-empty">
                <svg class="eq-catalog-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <circle cx="11" cy="11" r="8"></circle>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                  <line x1="8" y1="11" x2="14" y2="11"></line>
                </svg>
                <h3 class="eq-catalog-empty__title">No matching pieces found</h3>
                <p class="eq-catalog-empty__text">Try adjusting your price range or clearing fabric filters to discover other curated items.</p>
                <a href="{{ url()->current() }}" class="eq-btn eq-btn--primary eq-btn--pill" id="empty-reset-filters">
                  Reset All Filters
                </a>
              </div>
            @endforelse
          </div>

          <!-- Reusable Catalog Pagination -->
          @if($products->hasPages())
            <div class="eq-pagination-wrap" id="catalog-pagination">
              {{ $products->links() }}
            </div>
          @endif
        </section>

      </div>
    </div>

  </main>
@endsection

@push('scripts')
<script>
  // Helper for reactive URL filter updating
  function updateFilterParam(param, value) {
    const url = new URL(window.location.href);
    if (value === null || value === undefined || value === '') {
      url.searchParams.delete(param);
    } else {
      url.searchParams.set(param, value);
    }
    url.searchParams.delete('page'); // Reset to first page
    window.location.href = url.toString();
  }

  // Accordion Toggles for Filter Groups
  document.querySelectorAll(".eq-filter-group__header").forEach(header => {
    header.addEventListener("click", () => {
      const group = header.closest(".eq-filter-group");
      if (group) group.classList.toggle("is-collapsed");
    });
  });

  // Mobile Filter Drawer Toggle
  const mobileTrigger = document.querySelector("#mobile-filter-trigger");
  const sidebar = document.querySelector("#filter-sidebar");
  const backdrop = document.querySelector("#filter-backdrop");

  const openDrawer = () => {
    if (sidebar) {
      sidebar.classList.add("is-open");
      sidebar.setAttribute("aria-hidden", "false");
    }
    if (backdrop) backdrop.classList.add("is-open");
    document.body.style.overflow = "hidden";
  };

  const closeDrawer = () => {
    if (sidebar) {
      sidebar.classList.remove("is-open");
      sidebar.setAttribute("aria-hidden", "true");
    }
    if (backdrop) backdrop.classList.remove("is-open");
    document.body.style.overflow = "";
  };

  if (mobileTrigger) mobileTrigger.addEventListener("click", openDrawer);
  if (backdrop) backdrop.addEventListener("click", closeDrawer);

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && sidebar && sidebar.classList.contains("is-open")) {
      closeDrawer();
    }
  });

  // Grid switcher logic (2, 3, 4 cols)
  document.querySelectorAll(".eq-grid-view-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".eq-grid-view-btn").forEach(b => b.classList.remove("is-active"));
      btn.classList.add("is-active");
      const cols = btn.getAttribute("data-cols");
      const grid = document.querySelector("#catalog-grid");
      if (grid) {
        grid.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
      }
    });
  });

  // Wishlist toggle button toast feedback
  document.querySelectorAll(".eq-card-wishlist-btn").forEach(btn => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      const isFav = btn.classList.toggle("is-favorited");
      const card = btn.closest(".eq-product-card");
      const title = card ? card.querySelector(".eq-product-card__name")?.textContent.trim() : "Item";
      if (window.showToast) {
        window.showToast(isFav ? `Added "${title}" to wishlist.` : `Removed "${title}" from wishlist.`, "info");
      }
    });
  });
</script>
@endpush

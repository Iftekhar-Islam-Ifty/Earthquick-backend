@extends('layouts.app')

@section('title', $vendor->name . ' — Storefront | Earthquick')
@section('meta_description', $vendor->description ?? ('Discover exclusive creations by ' . $vendor->name . ' on Earthquick.'))
@section('canonical_url', route('stores.show', $vendor->slug))
@section('body_class', 'eq-storefront-page')

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

    <div class="eq-container" style="position: relative; z-index: 2; padding: 3rem 1rem 2.5rem;">
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
            <div style="font-size: 1.35rem; font-weight: 700; color: #ffffff; font-family: var(--font-display);">{{ $products->total() }}</div>
            <div style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.08em; color: rgba(255,255,255,0.65);">Creations</div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Storefront Products Section -->
  <section style="padding: 2.5rem 0 5rem;">
    <div class="eq-container">
      
      <!-- Toolbar: Categories Tabs & Sort -->
      <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid var(--eq-line);">
        
        <!-- Category Filter Pills -->
        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
          <a href="{{ route('stores.show', $vendor->slug) }}" 
             style="padding: 0.4rem 0.85rem; border-radius: 999px; font-size: 0.8rem; font-weight: 500; text-decoration: none; transition: all 0.2s; {{ !request('category') ? 'background: var(--eq-navy); color: #ffffff;' : 'background: var(--eq-cream); color: var(--eq-charcoal); border: 1px solid var(--eq-line);' }}">
            All Pieces ({{ $products->total() }})
          </a>

          @foreach($categories as $cat)
            <a href="{{ route('stores.show', ['slug' => $vendor->slug, 'category' => $cat->slug]) }}" 
               style="padding: 0.4rem 0.85rem; border-radius: 999px; font-size: 0.8rem; font-weight: 500; text-decoration: none; transition: all 0.2s; {{ request('category') === $cat->slug ? 'background: var(--eq-navy); color: #ffffff;' : 'background: var(--eq-cream); color: var(--eq-charcoal); border: 1px solid var(--eq-line);' }}">
              {{ $cat->name }}
            </a>
          @endforeach
        </div>

        <!-- Sort Select -->
        @if($products->total() > 0)
          <form method="GET" action="{{ route('stores.show', $vendor->slug) }}" style="margin: 0; display: flex; align-items: center; gap: 0.45rem;">
            @if(request('category'))
              <input type="hidden" name="category" value="{{ request('category') }}" />
            @endif
            <label for="sort-select" style="font-size: 0.8rem; color: var(--eq-charcoal-soft);">Sort by:</label>
            <select name="sort" id="sort-select" onchange="this.form.submit()" style="padding: 0.35rem 0.65rem; border-radius: 4px; border: 1px solid var(--eq-line); font-size: 0.82rem; background: #ffffff; cursor: pointer;">
              <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
              <option value="price-asc" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>Price: Low to High</option>
              <option value="price-desc" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>Price: High to Low</option>
              <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
            </select>
          </form>
        @endif

      </div>

      <!-- Catalog Grid -->
      <div class="eq-catalog-grid" id="store-catalog-grid">
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

              <button type="button" class="eq-product-card__quick-add" data-action="quick-view">
                Quick Inspect &bull; Add
              </button>
            </div>

            <div class="eq-product-card__body">
              <span class="eq-product-card__category">{{ $product->subcategory ? $product->subcategory->name : ($product->category ? $product->category->name : '') }}{{ $product->fabric ? ' &bull; ' . $product->fabric : '' }}</span>
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
          <!-- Elegant empty state for brands currently onboarding or out of stock (e.g. Bright) -->
          <div class="eq-catalog-empty" style="grid-column: 1 / -1; padding: 4.5rem 1.5rem; text-align: center;">
            <svg class="eq-catalog-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 44px; height: 44px; margin: 0 auto 1rem; color: var(--eq-gold-dark);">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <h3 style="font-family: var(--font-display); font-size: 1.35rem; color: var(--eq-navy); margin-bottom: 0.5rem; font-weight: 600;">
              Atelier Collection in Preparation
            </h3>
            <p style="max-width: 460px; margin: 0 auto 1.75rem; color: var(--eq-charcoal-soft); font-size: 0.88rem; line-height: 1.6;">
              The {{ $vendor->name }} catalog is currently being prepared for the Earthquick collective. New curated arrivals will debut here shortly.
            </p>
            <a href="{{ route('stores.index') }}" class="eq-btn eq-btn--outline" style="padding: 0.55rem 1.25rem; font-size: 0.82rem; text-decoration: none;">
              &larr; Discover All Partner Brands
            </a>
          </div>
        @endforelse
      </div>

      <!-- Pagination -->
      @if($products->hasPages())
        <div style="margin-top: 3rem; display: flex; justify-content: center;">
          {{ $products->links() }}
        </div>
      @endif

    </div>
  </section>

</main>
@endsection


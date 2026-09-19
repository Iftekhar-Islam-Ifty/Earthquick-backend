@extends('layouts.app')

@section('title', $product->name . ' — ' . ($product->vendor ? $product->vendor->name . ' | Earthquick' : 'Earthquick'))
@section('meta_description', $product->short_desc ?? ('Discover authentic craftsmanship with ' . $product->name . ' from ' . ($product->vendor->name ?? 'Earthquick') . '.'))
@section('meta_keywords', $product->name . ', ' . ($product->fabric ? $product->fabric . ' fabric, ' : '') . ($product->category ? $product->category->name . ', ' : '') . ($product->vendor ? strtolower($product->vendor->name) . ', ' : '') . 'earthquick')
@section('canonical_url', route('product.show', $product->slug))
@section('og_type', 'product')
@section('og_title', $product->name . ' — ' . ($product->vendor->name ?? 'Earthquick') . ' | Earthquick')
@section('og_description', $product->short_desc ?? ($product->description ?? 'Discover curated collections at Earthquick.'))
@section('og_image', asset($product->image))
@section('body_class', 'eq-product-page')

@section('content')
  <!-- ===================================================================
       BREADCRUMB BAR
       =================================================================== -->
  <nav class="eq-breadcrumb" aria-label="Breadcrumb" style="padding: 0.85rem 0; border-bottom: 1px solid var(--eq-line);">
    <div class="eq-container">
      <ol class="eq-breadcrumb__list" id="eq-breadcrumb-list" style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.45rem; list-style: none; margin: 0; padding: 0; font-size: 0.82rem; color: var(--eq-charcoal-soft);">
        <li><a href="{{ route('home') }}" style="color: inherit; text-decoration: none;">Home</a></li>
        <li style="color: var(--eq-line-dark);">&rsaquo;</li>
        <li id="bc-category"><a href="{{ route('category.show', $product->category->slug) }}" style="color: inherit; text-decoration: none;">{{ $product->category->name }}</a></li>
        @if($product->subcategory)
          <li style="color: var(--eq-line-dark);">&rsaquo;</li>
          <li id="bc-subcategory"><a href="{{ route('subcategory.show', ['categorySlug' => $product->category->slug, 'subcategorySlug' => $product->subcategory->slug]) }}" style="color: inherit; text-decoration: none;">{{ $product->subcategory->name }}</a></li>
        @endif
        <li style="color: var(--eq-line-dark);">&rsaquo;</li>
        <li id="bc-product" style="color: var(--eq-charcoal); font-weight: 500;" aria-current="page">{{ $product->name }}</li>
      </ol>
    </div>
  </nav>

  <!-- ===================================================================
       PRODUCT DETAIL SECTION
       =================================================================== -->
  <main id="main-content" class="eq-product-main">
    <div class="eq-container">
      <div class="eq-product-detail" id="product-detail-container">
        
        <!-- TOP SECTION / HERO: GALLERY + PRIMARY INFO (SIDE-BY-SIDE ON MOBILE & DESKTOP) -->
        <div class="eq-product-hero">
          <!-- LEFT: GALLERY -->
          <div class="eq-product-gallery">
            <div class="eq-product-gallery__main" id="gallery-main-wrap">
              <img id="main-product-img" src="{{ asset($product->image) }}" alt="{{ $product->name }}" />
              @if($product->badge)
                <span class="eq-product-card__badge" id="product-badge">{{ $product->badge }}</span>
              @endif
            </div>

            <!-- Thumbnails Strip -->
            <div class="eq-product-gallery__thumbs" id="gallery-thumbs">
              <button type="button" class="is-active" data-src="{{ asset($product->image) }}" aria-label="View product image 1" onclick="switchMainImage(this, '{{ asset($product->image) }}')">
                <img id="thumb-1" src="{{ asset($product->image) }}" alt="{{ $product->name }} thumbnail 1" />
              </button>
              @if($product->alt_image)
                <button type="button" data-src="{{ asset($product->alt_image) }}" aria-label="View product image 2" onclick="switchMainImage(this, '{{ asset($product->alt_image) }}')">
                  <img id="thumb-2" src="{{ asset($product->alt_image) }}" alt="{{ $product->name }} thumbnail 2" />
                </button>
              @endif
              @if($product->images && $product->images->count() > 0)
                @foreach($product->images as $idx => $img)
                  <button type="button" data-src="{{ asset($img->image_path) }}" aria-label="View product image {{ $idx + 3 }}" onclick="switchMainImage(this, '{{ asset($img->image_path) }}')">
                    <img src="{{ asset($img->image_path) }}" alt="{{ $product->name }} thumbnail {{ $idx + 3 }}" />
                  </button>
                @endforeach
              @endif
            </div>
          </div>

          <!-- RIGHT: PRIMARY INFO (SITS BESIDE THE PICTURE) -->
          <div class="eq-product-header">
            <div class="eq-product-info__category" id="product-category-label">
              {{ strtoupper($product->vendor->name ?? 'EARTHQUICK') }} &bull; {{ strtoupper($product->category->name) }}{{ $product->subcategory ? ' &bull; ' . strtoupper($product->subcategory->name) : '' }}
            </div>
            <h1 id="product-name">{{ $product->name }}</h1>

            <!-- Reviews rating summary -->
            <div class="eq-product-rating-row" id="product-rating-row">
              <div class="eq-rating-stars" id="rating-stars">★★★★★</div>
              <span class="eq-rating-text" id="product-rating-text">{{ $product->rating ?? '4.9' }} ({{ $product->reviews_count ?? 48 }} customer reviews)</span>
            </div>

            <!-- Price row -->
            <div class="eq-product-info__price" id="product-price-row">
              <span id="product-price">৳{{ number_format($product->price) }}</span>
              @if($product->old_price)
                <span id="product-old-price">৳{{ number_format($product->old_price) }}</span>
                @php
                  $savings = round((($product->old_price - $product->price) / $product->old_price) * 100);
                @endphp
                <span id="product-discount-tag">SAVE {{ $savings }}%</span>
              @endif
            </div>

            <!-- Stock & Urgency Notice -->
            <div class="eq-stock-notice" id="stock-notice">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <span id="stock-text">
                @if($product->stock_quantity > 0)
                  In Stock &mdash; Ready to ship ({{ $product->stock_quantity }} left in stock)
                @else
                  Available to Order &mdash; Ready to ship
                @endif
              </span>
            </div>

            <!-- Size / Dimensions Option -->
            <div class="eq-product-info__row eq-size-row" id="product-size-row">
              <span class="eq-product-info__label">Size / Fit:</span>
              <div class="eq-size-options" id="size-options">
                <button type="button" class="is-active" data-size="Standard / Free Size" onclick="selectSize(this, 'Standard / Free Size')">Free Size (5.5m)</button>
                <button type="button" data-size="Custom Stitched" onclick="selectSize(this, 'Custom Stitched')">Custom Blouse</button>
              </div>
            </div>
          </div>
        </div>

        <!-- FULL-WIDTH BODY SECTION: ACTIONS, DESCRIPTION, ASSURANCES -->
        <div class="eq-product-body">
          <!-- Quantity and Action Buttons -->
          <form action="{{ route('cart.add') }}" method="POST" id="product-actions-form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="size" id="product-size-input" value="Standard / Free Size">
            <input type="hidden" name="quantity" id="product-qty-input" value="1">

            <div class="eq-product-actions" id="product-actions-bar">
              <!-- Qty Stepper -->
              <div class="eq-qty-selector" id="product-qty-selector">
                <button type="button" id="btn-qty-minus" aria-label="Decrease quantity" onclick="changeQty(-1)">&minus;</button>
                <span id="qty-display">1</span>
                <button type="button" id="btn-qty-plus" aria-label="Increase quantity" onclick="changeQty(1)">&plus;</button>
              </div>

              <!-- Add to Cart -->
              <button type="submit" class="eq-btn eq-btn--primary" id="btn-add-to-cart">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                  <line x1="3" y1="6" x2="21" y2="6"></line>
                  <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                <span>Add to Bag</span>
              </button>

              <!-- Buy Now -->
              <button type="button" class="eq-btn eq-btn--secondary" id="btn-buy-now" onclick="buyNowDirect()">
                Buy Now
              </button>
            </div>
          </form>

          <!-- Short Description -->
          <p class="eq-product-info__desc" id="product-description">
            {{ $product->short_desc ?? 'Intricately hand-woven by master heritage artisans using traditional motifs. Made from breathable, pure combed threads offering timeless elegance and effortless drape for celebratory and festive gatherings.' }}
          </p>

          <!-- Highlights & Assurance Metadata -->
          <div class="eq-product-meta-list">
            <div class="eq-product-meta-list__item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="1" y="3" width="15" height="13"></rect>
                <polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon>
                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                <circle cx="18.5" cy="18.5" r="2.5"></circle>
              </svg>
              <span><strong>Free Delivery:</strong> Complimentary inside Chattogram within 48 hours (৳80). Express nationwide courier available (৳150).</span>
            </div>
            <div class="eq-product-meta-list__item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
              <span><strong>100% Authentic Handloom:</strong> Certified craft heritage sourced directly from verified artisan looms.</span>
            </div>
            <div class="eq-product-meta-list__item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 4 23 10 17 10"></polyline>
                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
              </svg>
              <span><strong>Easy Returns:</strong> 7-day doorstep exchange for sizing or quality assurance.</span>
            </div>
          </div>
        </div>

      </div>

      <!-- ===============================================================
           SECTION: ARTISANAL DETAILS ACCORDION
           =============================================================== -->
      <section class="eq-product-accordion-section">
        <div style="max-width: 800px; margin: 0 auto;">
          <h2 class="eq-product-accordion-section__title">
            Craftsmanship &amp; Details
          </h2>

          <div class="eq-product-accordion">
            <!-- Tab 1 -->
            <details open class="eq-product-accordion__item">
              <summary class="eq-product-accordion__summary">
                Fabric &amp; Artisan Origin
              </summary>
              <div class="eq-product-accordion__body" id="tab-fabric-desc">
                {{ $product->description ?? 'Hand-spun pure threads woven by master craftsmen. Each warp and weft is tensioned manually to achieve supreme drape, softness, and resilience that softens with every wash.' }}
              </div>
            </details>

            <!-- Tab 2 -->
            <details class="eq-product-accordion__item">
              <summary class="eq-product-accordion__summary">
                Care &amp; Longevity Instructions
              </summary>
              <div class="eq-product-accordion__body">
                &bull; Dry clean recommended for initial cleaning to preserve dye radiance.<br />
                &bull; Hand wash gently in cold water with mild silk/cotton detergent thereafter.<br />
                &bull; Avoid wringing or direct harsh sunlight drying.<br />
                &bull; Warm iron on reverse side under a cotton pressing cloth.
              </div>
            </details>

            <!-- Tab 3 -->
            <details class="eq-product-accordion__item">
              <summary class="eq-product-accordion__summary">
                Delivery, Returns &amp; Warranty
              </summary>
              <div class="eq-product-accordion__body">
                Every order is packaged with utmost care in our signature eco-friendly cloth bag. We offer 7-day hassle-free doorstep returns across Bangladesh. Cash on Delivery is available in all major metropolitan areas.
              </div>
            </details>
          </div>
        </div>
      </section>

      <!-- ===============================================================
           SECTION: RELATED PRODUCTS
           =============================================================== -->
      @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <section style="margin-top: 4.5rem;">
          <div style="display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 1.75rem; border-bottom: 1px solid var(--eq-line); padding-bottom: 0.75rem;">
            <div>
              <span style="font-size: 0.82rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: var(--eq-teal-dark);">Curated Suggestions</span>
              <h2 style="font-family: var(--font-display); font-size: 1.65rem; color: var(--eq-charcoal); margin: 0.25rem 0 0;">You May Also Admire</h2>
            </div>
            <a href="{{ route('category.show', $product->category->slug) }}" id="related-view-all" style="font-size: 0.88rem; font-weight: 600; color: var(--eq-teal-dark); text-decoration: none;">View Entire Category &rarr;</a>
          </div>

          <div class="eq-catalog-grid" id="related-products-grid">
            @foreach($relatedProducts as $rel)
              <article class="eq-product-card" id="card-{{ $rel->id }}">
                <div class="eq-product-card__frame">
                  @if($rel->badge)
                    <span class="eq-product-badge eq-product-badge--{{ $rel->badge_type ?? 'ready' }}">{{ $rel->badge }}</span>
                  @endif
                  <img src="{{ asset($rel->image) }}" alt="{{ $rel->name }}" loading="lazy" />
                  @if($rel->alt_image)
                    <img src="{{ asset($rel->alt_image) }}" alt="{{ $rel->name }} alternate" class="eq-product-card__img--alt" loading="lazy" />
                  @endif
                  <button type="button" class="eq-product-card__quick-add" data-action="quick-view">
                    Quick Inspect &bull; Add
                  </button>
                </div>
                <div class="eq-product-card__body">
                  <span class="eq-product-card__category">{{ $rel->category->name }} &bull; {{ $rel->fabric ?? 'Handloom' }}</span>
                  <h3 class="eq-product-card__name">
                    <a href="{{ route('product.show', $rel->slug) }}" class="eq-product-card__link">{{ $rel->name }}</a>
                  </h3>
                  <div class="eq-product-card__price">
                    @if($rel->old_price)
                      <span class="eq-price--old">৳{{ number_format($rel->old_price) }}</span>
                    @endif
                    <span>৳{{ number_format($rel->price) }}</span>
                  </div>
                </div>
              </article>
            @endforeach
          </div>
        </section>
      @endif

    </div>
  </main>
@endsection

@push('scripts')
<script>
  // Image switcher for gallery
  function switchMainImage(button, src) {
    const mainImg = document.getElementById('main-product-img');
    if (mainImg) mainImg.src = src;
    document.querySelectorAll('#gallery-thumbs button').forEach(btn => btn.classList.remove('is-active'));
    button.classList.add('is-active');
  }

  // Size option selector
  function selectSize(button, size) {
    document.querySelectorAll('#size-options button').forEach(btn => btn.classList.remove('is-active'));
    button.classList.add('is-active');
    const input = document.getElementById('product-size-input');
    if (input) input.value = size;
  }

  // Quantity stepper
  function changeQty(delta) {
    const qtySpan = document.getElementById('qty-display');
    const qtyInput = document.getElementById('product-qty-input');
    let current = parseInt(qtySpan.textContent, 10) || 1;
    current = Math.max(1, current + delta);
    qtySpan.textContent = current;
    qtyInput.value = current;
  }

  // Direct Buy Now handler
  function buyNowDirect() {
    const form = document.getElementById('product-actions-form');
    if (form) {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'buy_now';
      input.value = '1';
      form.appendChild(input);
      form.submit();
    }
  }
</script>

<!-- Schema.org Product Structured Data (JSON-LD) -->
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org/',
  '@type' => 'Product',
  'name' => $product->name,
  'image' => [
    asset($product->image)
  ],
  'description' => strip_tags($product->short_desc ?? ($product->description ?? '')),
  'sku' => $product->sku ?? ('NT-' . $product->id),
  'brand' => [
    '@type' => 'Brand',
    'name' => $product->vendor->name ?? 'Earthquick',
  ],
  'offers' => [
    '@type' => 'Offer',
    'url' => route('product.show', $product->slug),
    'priceCurrency' => 'BDT',
    'price' => (string) $product->price,
    'itemCondition' => 'https://schema.org/NewCondition',
    'availability' => $product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
    'seller' => [
      '@type' => 'Organization',
      'name' => 'Earthquick',
    ],
  ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

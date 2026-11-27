@extends('layouts.app')

@section('title', $vendor->name . ' — Electrical Products | Rthquick')
@section('meta_description', 'Explore electrical products from Bright Electrical Company on Rthquick.')
@section('canonical_url', route('stores.show', $vendor->slug))
@section('body_class', 'eq-store-home eq-bright-home')

@section('content')
<main id="main-content" class="eq-bright-home__main">
  @include('vendor.partials.bright-hero')

  <section class="eq-bright-section eq-store-trust" aria-label="Shopping information">
    <div class="eq-store-trust__item">
      <span class="eq-store-trust__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h11v11H3zM14 10h4l3 3v4h-7z"/><circle cx="7.5" cy="18" r="1.8"/><circle cx="18" cy="18" r="1.8"/></svg></span>
      <span>Doorstep delivery<br>across Bangladesh</span>
    </div>
    <div class="eq-store-trust__item">
      <span class="eq-store-trust__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 20 6v5c0 5-3.4 8.4-8 10-4.6-1.6-8-5-8-10V6z"/><path d="M8 12h8M10 9l-3 3 3 3"/></svg></span>
      <span>Returns &amp; support<br>through Rthquick</span>
    </div>
    <div class="eq-store-trust__item eq-store-trust__brand-item">
      <span class="eq-store-trust__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m20.6 13.4-7.2 7.2a2 2 0 0 1-2.8 0L3 13V4h9l8.6 8.6a.6.6 0 0 1 0 .8Z"/><circle cx="7.5" cy="8.5" r="1.2"/><path d="m13 11 1 2 2.2.3-1.6 1.5.4 2.2-2-1.1-2 1.1.4-2.2-1.6-1.5L12 13z"/></svg></span>
      <span>Heaven brand &amp;<br>warranty by Bright</span>
    </div>
  </section>

  <section class="eq-bright-section eq-bright-category-section" aria-labelledby="bright-categories-title">
    <div class="eq-bright-section__heading">
      <div><h2 id="bright-categories-title">Shop by category</h2></div>
    </div>
    @php
      $categoryIcons = [
        'switches' => '<rect x="3.5" y="3" width="17" height="18" rx="2.5"/><rect class="eq-bright-category__accent" x="8" y="6.5" width="8" height="11" rx="1.5"/><path d="M9.5 12h5"/>',
        'sockets' => '<rect x="3.5" y="3" width="17" height="18" rx="2.5"/><circle cx="9" cy="10" r="1.2"/><circle cx="15" cy="10" r="1.2"/><path class="eq-bright-category__accent" d="M12 14.5v2.5"/>',
        'led-lights' => '<path d="M9 16c0-2-3-3.4-3-7a6 6 0 0 1 12 0c0 3.6-3 5-3 7H9Z"/><path class="eq-bright-category__accent" d="M9 18.5h6m-5 2.5h4"/>',
        'circuit-breakers' => '<rect x="5" y="2.5" width="14" height="19" rx="2"/><path d="M8 6h8m-8 13h8"/><rect class="eq-bright-category__accent" x="9" y="10" width="6" height="6" rx="1"/><path d="m11 12 2 2"/>',
        'fans' => '<path d="M12 2v5M11 10c-3-4.5-2.4-6.5-.4-7 2.6-.6 3.5 2.8 2.3 7m1.3 1.3c5-1.6 6.7-.5 7 1.5.5 2.6-3 3.5-7 1.4M10.6 14c-2 5-4 5.9-5.7 4.6-2.1-1.7-.1-4.2 4.7-6"/><circle class="eq-bright-category__accent" cx="12" cy="12" r="2"/>',
        'electrical-accessories' => '<path d="M8 2.5v5m5-5v5M5.5 8h10v3a5 5 0 0 1-5 5v1.5c0 2 1.5 3 3.5 3H19"/><path class="eq-bright-category__accent" d="M7.5 12h6"/>',
      ];
    @endphp
    <div class="eq-bright-categories">
      @forelse($subcategories as $subcategory)
        @php($categoryIcon = $categoryIcons[$subcategory->slug] ?? $categoryIcons['electrical-accessories'])
        <a class="eq-bright-category" href="{{ route('stores.catalog', ['slug' => $vendor->slug, 'subcategory' => $subcategory->slug]) }}">
          <span class="eq-bright-category__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $categoryIcon !!}</svg></span>
          <span class="eq-bright-category__name">{{ $subcategory->name }}</span>
          <span class="eq-bright-category__arrow" aria-hidden="true">&rarr;</span>
        </a>
      @empty
        <p>Bright's electrical categories are being prepared.</p>
      @endforelse
    </div>
  </section>

  <section class="eq-bright-section eq-bright-products" aria-labelledby="bright-products-title">
    <div class="eq-bright-section__heading">
      <div><h2 id="bright-products-title">Bright products</h2></div>
      <a href="{{ route('stores.catalog', $vendor->slug) }}" class="eq-bright-view-all">View all products <span aria-hidden="true">&rarr;</span></a>
    </div>
    @if($featuredProducts->isNotEmpty())
      <div class="eq-bright-product-grid">
        @foreach($featuredProducts as $product)
          @include('partials.home-product-card', ['product' => $product, 'cardId' => 'bright-home-product-'.$product->id, 'showCategory' => true, 'cardClass' => 'eq-bright-product-card'])
        @endforeach
      </div>
    @else
      <p class="eq-bright-empty">Bright's product listings are being prepared. Check back as products are added to the catalog.</p>
    @endif
  </section>

  <section class="eq-bright-section eq-bright-footer-link">
    <a class="eq-bright-info-link" href="{{ route('stores.bright-information') }}">Bright store information &amp; policies <span aria-hidden="true">&rarr;</span></a>
  </section>
</main>
@endsection

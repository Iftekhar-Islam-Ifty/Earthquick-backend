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
      <div><span class="eq-bright-eyebrow">Find what you need</span><h2 id="bright-categories-title">Shop by category</h2></div>
      <a href="{{ route('stores.catalog', $vendor->slug) }}" class="eq-bright-view-all">View all products <span aria-hidden="true">&rarr;</span></a>
    </div>
    @php
      $categoryIcons = [
        'switches' => '<rect x="5" y="3" width="14" height="18" rx="3"/><path class="eq-bright-category__accent" d="M12 7v5m0 0-2 2m2-2 2 2"/>',
        'sockets' => '<circle cx="12" cy="12" r="8"/><path class="eq-bright-category__accent" d="M9 10v3m6-3v3m-3 3v1"/>',
        'led-lights' => '<path d="M8 16a7 7 0 1 1 8 0c-.8.6-1 1.3-1 3H9c0-1.7-.2-2.4-1-3Z"/><path class="eq-bright-category__accent" d="M9 19h6m-5 2h4"/>',
        'circuit-breakers' => '<rect x="4" y="3" width="16" height="18" rx="3"/><path d="M7 7h10"/><path class="eq-bright-category__accent" d="m9 11 6 2-4 4"/>',
        'fans' => '<path d="M11 10C8 7 8 3 10 3c3 0 3 5 3 7m1 2c4-1 7 1 6 3-1 3-5 0-7-1m-2 2c0 4-3 7-5 5-2-2 2-5 4-7m-1-2c-4 0-7-3-5-5 2-2 5 2 7 4"/><circle class="eq-bright-category__accent" cx="12" cy="12" r="2"/>',
        'electrical-accessories' => '<path d="M8 3v6m8-6v6M6 9h12v3a6 6 0 0 1-6 6v3"/><path class="eq-bright-category__accent" d="M10 12v2m4-2v2"/>',
      ];
    @endphp
    <div class="eq-bright-categories">
      @forelse($subcategories as $subcategory)
        @php($categoryIcon = $categoryIcons[$subcategory->slug] ?? $categoryIcons['electrical-accessories'])
        <a class="eq-bright-category" href="{{ route('subcategory.show', ['categorySlug' => 'electronics', 'subcategorySlug' => $subcategory->slug]) }}">
          <span class="eq-bright-category__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $categoryIcon !!}</svg></span>
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
      <div><span class="eq-bright-eyebrow">Electrical essentials</span><h2 id="bright-products-title">Bright products</h2></div>
      <a href="{{ route('stores.catalog', $vendor->slug) }}" class="eq-bright-view-all">View all <span aria-hidden="true">&rarr;</span></a>
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

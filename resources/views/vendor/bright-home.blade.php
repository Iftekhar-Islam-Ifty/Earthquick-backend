@extends('layouts.app')

@section('title', $vendor->name . ' — Electrical Products | Rthquick')
@section('meta_description', 'Explore electrical products from Bright Electrical Company on Rthquick.')
@section('canonical_url', route('stores.show', $vendor->slug))
@section('body_class', 'eq-store-home eq-bright-home')

@section('content')
<main id="main-content" class="eq-bright-home__main">
  @include('vendor.partials.bright-hero')

  <section class="eq-bright-section eq-bright-trust" aria-label="Shopping information">
    <div class="eq-bright-trust__item"><span class="eq-bright-trust__icon" aria-hidden="true">01</span><span>Sold by Bright</span></div>
    <div class="eq-bright-trust__item"><span class="eq-bright-trust__icon" aria-hidden="true">02</span><span>Heaven is Bright's product brand</span></div>
    <div class="eq-bright-trust__item"><span class="eq-bright-trust__icon" aria-hidden="true">03</span><span>Rthquick checkout</span></div>
  </section>

  <section class="eq-bright-section eq-bright-category-section" aria-labelledby="bright-categories-title">
    <div class="eq-bright-section__heading">
      <div><span class="eq-bright-eyebrow">Find what you need</span><h2 id="bright-categories-title">Shop by category</h2></div>
      <a href="{{ route('stores.catalog', $vendor->slug) }}" class="eq-bright-view-all">View all products <span aria-hidden="true">&rarr;</span></a>
    </div>
    @php
      $categoryIcons = [
        'switches' => 'switch.svg',
        'sockets' => 'socket.svg',
        'led-lights' => 'led-bulb.svg',
        'circuit-breakers' => 'breaker.svg',
        'fans' => 'fan.svg',
        'electrical-accessories' => 'accessories.svg',
      ];
    @endphp
    <div class="eq-bright-categories">
      @forelse($subcategories as $subcategory)
        @php($categoryIcon = $categoryIcons[$subcategory->slug] ?? 'accessories.svg')
        <a class="eq-bright-category" href="{{ route('subcategory.show', ['categorySlug' => 'electronics', 'subcategorySlug' => $subcategory->slug]) }}">
          <span class="eq-bright-category__icon"><img src="{{ asset('images/bright-demo/' . $categoryIcon) }}" alt="" loading="lazy" decoding="async"></span>
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

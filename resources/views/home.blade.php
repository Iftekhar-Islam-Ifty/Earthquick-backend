@extends('layouts.app')

@section('title', 'Rthquick — Explore Independent Stores')
@section('meta_description', 'Choose a store on Rthquick: Bengali heritage and handloom from Nous Telos, or electrical products from Bright.')
@section('canonical_url', route('home'))
@section('body_class', 'eq-marketplace-home')

@section('content')
<main id="main-content" class="eq-market-home">
  <div class="eq-market-home__inner">
    <header class="eq-market-home__hero">
      <div class="eq-market-home__hero-copy">
        <h1>A marketplace for<br><em>everyday discoveries.</em></h1>
        <p>Discover distinctive products from independent Bangladeshi businesses.</p>
        <div class="eq-market-home__hero-actions">
          <a class="eq-market-home__action eq-market-home__action--gold" href="#market-stores">Explore stores <span aria-hidden="true">&darr;</span></a>
          <a class="eq-market-home__action eq-market-home__action--blue" href="#market-categories">Browse categories <span aria-hidden="true">&darr;</span></a>
        </div>
      </div>
      <div class="eq-market-home__hero-art" aria-hidden="true">
        <span class="eq-market-home__network-link eq-market-home__network-link--one"></span>
        <span class="eq-market-home__network-link eq-market-home__network-link--two"></span>
        <span class="eq-market-home__network-link eq-market-home__network-link--three"></span>
        <span class="eq-market-home__network-link eq-market-home__network-link--four"></span>
        <span class="eq-market-home__network-link eq-market-home__network-link--five"></span>
        <span class="eq-market-home__network-node eq-market-home__network-node--center"></span>
        <span class="eq-market-home__network-node eq-market-home__network-node--one"></span>
        <span class="eq-market-home__network-node eq-market-home__network-node--two"></span>
        <span class="eq-market-home__network-node eq-market-home__network-node--three"></span>
        <span class="eq-market-home__network-node eq-market-home__network-node--four"></span>
        <span class="eq-market-home__network-node eq-market-home__network-node--five"></span>
      </div>
    </header>
    <section id="market-stores" class="eq-market-home__stores" aria-labelledby="market-stores-title">
      <div class="eq-market-home__section-heading">
        <span class="eyebrow">A marketplace of independent stores</span>
        <h2 id="market-stores-title">Find the store you came for.</h2>
      </div>
      <div class="eq-market-home__cards">
      @if($store = $stores->get('nous-telos'))
        <article class="eq-market-home__card">
          <div>
            <div class="eq-market-home__logo-box" role="img" aria-label="{{ $store->name }} logo">
              @if($store->logo)
                <img class="eq-market-home__logo" src="{{ asset($store->logo) }}" alt="{{ $store->name }} logo" width="160" height="148" />
              @else
                <span class="eq-market-home__logo-fallback" aria-hidden="true">{{ strtoupper(substr($store->name, 0, 1)) }}</span>
              @endif
            </div>
            <span class="eyebrow">Bengali heritage &amp; handloom</span><h2>{{ $store->name }}</h2><p>{{ $store->tagline ?: 'Handloom, clothing and crafted collections.' }}</p>
          </div>
          <a class="eq-btn eq-btn--outline" href="{{ route('stores.show', $store->slug) }}">Enter Nous Telos <span aria-hidden="true">&rarr;</span></a>
        </article>
      @endif
      @if($store = $stores->get('bright'))
        <article class="eq-market-home__card eq-market-home__card--bright">
          <div>
            <div class="eq-market-home__logo-box eq-market-home__logo-box--empty" role="img" aria-label="{{ $store->name }} logo">
              @if($store->logo)
                <img class="eq-market-home__logo" src="{{ asset($store->logo) }}" alt="{{ $store->name }} logo" width="160" height="148" />
              @else
                <span class="eq-market-home__logo-fallback" aria-hidden="true">{{ strtoupper(substr($store->name, 0, 1)) }}</span>
              @endif
            </div>
            <span class="eyebrow">Electrical products</span><h2>{{ $store->name }}</h2><p>{{ $store->tagline ?: 'Switches, sockets, lights and electrical accessories.' }} Heaven is Bright product brand.</p>
          </div>
          <a class="eq-btn eq-btn--light" href="{{ route('stores.show', $store->slug) }}">Enter Bright <span aria-hidden="true">&rarr;</span></a>
        </article>
      @endif
      </div>
    </section>
    <section id="market-categories" class="eq-market-home__categories" aria-labelledby="market-categories-title">
      <div class="eq-market-home__section-heading">
        <span class="eyebrow">Or browse by interest</span>
        <h2 id="market-categories-title">Shop by category</h2>
      </div>
      <div class="eq-market-home__category-links">
        <a class="eq-market-home__category-link" href="{{ route('category.group', 'fashion-accessories') }}">
          <span class="eq-market-home__category-icon" aria-hidden="true">01</span><span class="eq-market-home__category-name">Fashion &amp; Accessories<small>Clothing, bags and more</small></span><span class="eq-market-home__category-arrow" aria-hidden="true">&rarr;</span>
        </a>
        <a class="eq-market-home__category-link" href="{{ route('category.show', 'home-decor') }}">
          <span class="eq-market-home__category-icon" aria-hidden="true">02</span><span class="eq-market-home__category-name">Home &amp; Living<small>Useful pieces for your home</small></span><span class="eq-market-home__category-arrow" aria-hidden="true">&rarr;</span>
        </a>
        <a class="eq-market-home__category-link" href="{{ route('category.show', 'electronics') }}">
          <span class="eq-market-home__category-icon" aria-hidden="true">03</span><span class="eq-market-home__category-name">Electronics<small>Everyday electrical essentials</small></span><span class="eq-market-home__category-arrow" aria-hidden="true">&rarr;</span>
        </a>
      </div>
    </section>
    <p class="eq-market-home__foot">One marketplace. Distinct stores. Shared Rthquick checkout.</p>
  </div>
</main>
@endsection

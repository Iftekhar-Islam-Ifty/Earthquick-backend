@extends('layouts.app')

@section('title', 'Rthquick — Explore Independent Stores')
@section('meta_description', 'Choose a store on Rthquick: Bengali heritage and handloom from Nous Telos, or electrical products from Bright.')
@section('canonical_url', route('home'))
@section('body_class', 'eq-marketplace-home')

@push('styles')
<style>
  .eq-market-home { background: #f8f4eb; padding: clamp(3rem, 8vw, 6.5rem) 1.25rem; }
  .eq-market-home__inner { max-width: 1120px; margin: auto; }
  .eq-market-home__intro { max-width: 700px; margin: 0 auto 2.5rem; text-align: center; }
  .eq-market-home__intro .eyebrow { color: #927331; letter-spacing: .16em; font-size: .72rem; font-weight: 700; text-transform: uppercase; }
  .eq-market-home__intro h1 { margin: .7rem 0; color: #203e4c; font: 600 clamp(2rem, 5vw, 3.5rem)/1.12 var(--font-display); }
  .eq-market-home__intro p { color: #655f56; line-height: 1.7; }
  .eq-market-home__cards { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.25rem; }
  .eq-market-home__card { display: flex; min-height: 280px; flex-direction: column; justify-content: space-between; padding: clamp(1.5rem, 4vw, 2.5rem); border: 1px solid #e5dccb; border-radius: 16px; background: #fffdf8; box-shadow: 0 12px 32px #35281512; }
  .eq-market-home__card--bright { background: #203e4c; border-color: #203e4c; color: #fff; }
  .eq-market-home__card--bright .eyebrow { color: #f1d58d; }
  .eq-market-home__card.eq-market-home__card--bright h2 { color: #fff; }
  .eq-market-home__logo-box { display: grid; width: 160px; height: 148px; place-items: center; overflow: hidden; margin-bottom: 1.25rem; border: 1px solid #e8e0d2; border-radius: 12px; background: #fff; box-shadow: 0 8px 22px #35281516; }
  .eq-market-home__logo { display: block; width: 100%; height: 100%; object-fit: contain; padding: .25rem; }
  .eq-market-home__logo-fallback { color: #203e4c; font: 600 3rem/1 var(--font-display); }
  .eq-market-home__logo-box--empty { border-color: rgba(255,255,255,.3); }
  .eq-market-home__card h2 { margin: .7rem 0; font: 600 clamp(1.55rem, 3vw, 2.2rem)/1.2 var(--font-display); }
  .eq-market-home__card p { max-width: 40ch; line-height: 1.65; color: inherit; opacity: .82; }
  .eq-market-home__card a { align-self: flex-start; margin-top: 1.5rem; }
  .eq-market-home__foot { margin: 2rem 0 0; color: #777064; text-align: center; font-size: .86rem; }
  @media(max-width:700px) { .eq-market-home__cards { grid-template-columns: 1fr; } .eq-market-home__card { min-height: 230px; } }
</style>
@endpush

@section('content')
<main id="main-content" class="eq-market-home">
  <div class="eq-market-home__inner">
    <header class="eq-market-home__intro">
      <span class="eyebrow">A marketplace of independent stores</span>
      <h1>Find the store you came for.</h1>
      <p>Rthquick brings distinct businesses together. Choose a store to explore its own collections and products.</p>
    </header>
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
    <p class="eq-market-home__foot">Shop with each store through the Rthquick shared cart and checkout.</p>
  </div>
</main>
@endsection

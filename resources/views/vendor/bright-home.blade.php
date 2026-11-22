@extends('layouts.app')

@section('title', $vendor->name . ' — Electrical Products | Rthquick')
@section('meta_description', 'Explore electrical products from Bright Electrical Company on Rthquick.')
@section('canonical_url', route('stores.show', $vendor->slug))
@section('body_class', 'eq-store-home eq-bright-home')

@push('styles')
<style>
  .eq-bright-home { background: #f8f4eb; }
  .eq-bright-store-hero { min-height: 420px; height: min(62vh, 620px); max-height: 620px; background: #14384b; }
  .eq-bright-store-hero .eq-hero__media::after { background: linear-gradient(90deg, rgba(12, 33, 46, .88) 0%, rgba(12, 33, 46, .56) 48%, rgba(12, 33, 46, .08) 100%); }
  .eq-bright-store-hero .eq-hero__content { max-width: 700px; padding-bottom: clamp(2rem, 5vw, 4rem); }
  .eq-bright-store-hero .eq-hero__content .eq-eyebrow { color: #efd38b; }
  .eq-bright-store-hero .eq-hero__content h1 { max-width: 14ch; }
  .eq-bright-store-hero .eq-hero__dot { border-color: rgba(255,255,255,.8); }
  .eq-bright-store-hero .eq-hero__dot.is-active { background: #efd38b; border-color: #efd38b; }
  @media (max-width: 768px) {
    .eq-bright-store-hero { min-height: 285px; height: clamp(285px, 52vh, 390px); max-height: 390px; }
    .eq-bright-store-hero .eq-hero__media::after { background: linear-gradient(to top, rgba(12,33,46,.92) 0%, rgba(12,33,46,.48) 58%, rgba(12,33,46,.06) 100%) !important; }
    .eq-bright-store-hero .eq-hero__media img { object-position: 72% center !important; }
    .eq-bright-store-hero .eq-hero__content { max-width: 82%; padding-bottom: 2rem !important; }
  }
  .eq-bright-section { max-width: 1100px; margin: auto; padding: 3rem 1.25rem; }
  .eq-bright-section h2 { color: #203e4c; font: 600 clamp(1.6rem,3vw,2.3rem) var(--font-display); }
  .eq-bright-categories { display: grid; grid-template-columns: repeat(auto-fit,minmax(150px,1fr)); gap: .8rem; margin: 1.5rem 0 2rem; }
  .eq-bright-categories a { display:flex; min-height:72px; align-items:center; justify-content:center; padding:1rem; border:1px solid #e3d9c8; border-radius:10px; background:#fffdf8; color:#203e4c; font-weight:600; text-align:center; text-decoration:none; transition:transform .2s,box-shadow .2s; }
  .eq-bright-categories a:hover { transform:translateY(-2px); box-shadow:0 8px 20px #203e4c14; }
  .eq-bright-note { margin:1rem 0 1.5rem; padding:1rem 1.1rem; border-left:3px solid #c4a55c; background:#fffdf8; color:#5c574e; line-height:1.6; }
  .eq-bright-info-link { display:inline-flex; align-items:center; gap:.35rem; margin-top:.35rem; color:#1f5a58; font-weight:700; text-underline-offset:3px; }
</style>
@endpush

@section('content')
<main id="main-content">
  @include('vendor.partials.bright-hero')

  <section class="eq-bright-section" aria-labelledby="bright-categories-title">
    <h2 id="bright-categories-title">Shop by category</h2>
    <div class="eq-bright-categories">
      @forelse($subcategories as $subcategory)
        <a href="{{ route('stores.catalog', ['slug' => $vendor->slug, 'subcategory' => $subcategory->slug]) }}">{{ $subcategory->name }}</a>
      @empty
        <p>Bright’s electrical categories are being prepared.</p>
      @endforelse
    </div>
    <h2>Product brand</h2>
    <p class="eq-bright-note"><strong>Heaven is a product brand sold by Bright.</strong></p>
    @if($catalogCount === 0)
      <p>Bright’s product listings are being prepared. Check back as products are added to the catalog.</p>
    @endif
    <a class="eq-bright-info-link" href="{{ route('stores.bright-information') }}">Bright store information &amp; policies <span aria-hidden="true">&rarr;</span></a>
  </section>
</main>
@endsection

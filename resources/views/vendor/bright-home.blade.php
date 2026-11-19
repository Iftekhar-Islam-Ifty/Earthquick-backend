@extends('layouts.app')

@section('title', $vendor->name . ' — Electrical Products | Rthquick')
@section('meta_description', 'Explore electrical products from Bright Electrical Company on Rthquick.')
@section('canonical_url', route('stores.show', $vendor->slug))
@section('body_class', 'eq-store-home eq-bright-home')

@push('styles')
<style>
  .eq-bright-home { background: #f8f4eb; }
  .eq-bright-intro { padding: clamp(3rem, 7vw, 6rem) 1.25rem; background: linear-gradient(120deg,#193b4a,#285f79); color: #fff; }
  .eq-bright-intro__inner { max-width: 1100px; margin: auto; }
  .eq-bright-intro .eyebrow { color: #efd38b; letter-spacing: .15em; font-size: .75rem; font-weight: 700; text-transform: uppercase; }
  .eq-bright-intro h1 { margin: .75rem 0; font: 600 clamp(2.1rem,5vw,3.8rem)/1.1 var(--font-display); }
  .eq-bright-intro p { max-width: 620px; line-height: 1.7; opacity: .86; }
  .eq-bright-section { max-width: 1100px; margin: auto; padding: 3rem 1.25rem; }
  .eq-bright-section h2 { color: #203e4c; font: 600 clamp(1.6rem,3vw,2.3rem) var(--font-display); }
  .eq-bright-categories { display: grid; grid-template-columns: repeat(auto-fit,minmax(150px,1fr)); gap: .8rem; margin: 1.5rem 0 2rem; }
  .eq-bright-categories a { display:flex; min-height:72px; align-items:center; justify-content:center; padding:1rem; border:1px solid #e3d9c8; border-radius:10px; background:#fffdf8; color:#203e4c; font-weight:600; text-align:center; text-decoration:none; transition:transform .2s,box-shadow .2s; }
  .eq-bright-categories a:hover { transform:translateY(-2px); box-shadow:0 8px 20px #203e4c14; }
  .eq-bright-note { margin:1rem 0 1.5rem; padding:1rem 1.1rem; border-left:3px solid #c4a55c; background:#fffdf8; color:#5c574e; line-height:1.6; }
</style>
@endpush

@section('content')
<main id="main-content">
  <section class="eq-bright-intro">
    <div class="eq-bright-intro__inner">
      <span class="eyebrow">Electrical products · Chattogram</span>
      <h1>{{ $vendor->name }}</h1>
      <p>{{ $vendor->description ?: 'Browse electrical products and accessories from Bright.' }}</p>
      <a class="eq-btn eq-btn--light" href="{{ route('stores.catalog', $vendor->slug) }}">Shop all products <span aria-hidden="true">&rarr;</span></a>
    </div>
  </section>
  <section class="eq-bright-section" aria-labelledby="bright-categories-title">
    <h2 id="bright-categories-title">Shop by category</h2>
    <div class="eq-bright-categories">
      @forelse($subcategories as $subcategory)
        <a href="{{ route('stores.catalog', ['slug' => $vendor->slug, 'subcategory' => $subcategory->slug]) }}">{{ $subcategory->name }}</a>
      @empty
        <p>Bright’s electrical categories are being prepared.</p>
      @endforelse
    </div>
    <h2>Bright product brand</h2>
    <p class="eq-bright-note"><strong>Heaven</strong> is presented as Bright’s product brand. Bright is the store and provides the warranty for its Heaven products; warranty terms are shown with products when supplied by Bright.</p>
    @if($catalogCount === 0)
      <p>Bright’s product listings are being prepared. Check back as products are added to the catalog.</p>
    @endif
  </section>
</main>
@endsection

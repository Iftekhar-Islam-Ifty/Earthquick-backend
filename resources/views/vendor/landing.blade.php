@extends('layouts.app')

@section('title', $vendor->name . ' — Store | Rthquick')
@section('meta_description', $vendor->description ?: ('Explore products from ' . $vendor->name . ' on Rthquick.'))
@section('canonical_url', route('stores.show', $vendor->slug))

@section('content')
<main id="main-content" class="eq-market-home" style="padding:4rem 1.25rem; background:#f8f4eb;">
  <div style="max-width:900px; margin:auto; text-align:center;">
    @if($vendor->logo)<img src="{{ asset($vendor->logo) }}" alt="{{ $vendor->name }}" style="width:88px;height:88px;object-fit:contain;border-radius:50%;background:#fff;padding:.5rem;" />@endif
    <h1 style="color:#203e4c; font:600 clamp(2rem,5vw,3.5rem) var(--font-display);">{{ $vendor->name }}</h1>
    @if($vendor->tagline)<p>{{ $vendor->tagline }}</p>@endif
    @if($vendor->description)<p style="max-width:620px;margin:1rem auto;line-height:1.7;">{{ $vendor->description }}</p>@endif
    <a class="eq-btn eq-btn--outline" href="{{ route('stores.catalog', $vendor->slug) }}">Browse {{ $vendor->name }} products <span aria-hidden="true">&rarr;</span></a>
  </div>
</main>
@endsection

@extends('layouts.app')

@section('title', 'Brand Partners & Stores — Earthquick Collective')
@section('meta_description', 'Discover curated partner ateliers, heritage textile houses, and innovative lifestyle brands across the Earthquick collective.')
@section('canonical_url', route('stores.index'))
@section('body_class', 'eq-stores-page')

@section('content')
<main id="main-content" tabindex="-1">

  <!-- Breadcrumb Navigation -->
  <nav class="eq-breadcrumb" aria-label="Breadcrumb" style="padding: 0.85rem 0; border-bottom: 1px solid var(--eq-line); background: var(--eq-cream-soft, #faf8f5);">
    <div class="eq-container">
      <ol class="eq-breadcrumb__list" style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.45rem; list-style: none; margin: 0; padding: 0; font-size: 0.82rem; color: var(--eq-charcoal-soft);">
        <li><a href="{{ route('home') }}" style="color: inherit; text-decoration: none;">Home</a></li>
        <li><span style="opacity: 0.5;">&rsaquo;</span></li>
        <li style="color: var(--eq-gold-dark); font-weight: 500;">Brand Stores</li>
      </ol>
    </div>
  </nav>

  <!-- Hero Banner / Page Intro -->
  <section style="padding: 3rem 0 2rem; background: linear-gradient(180deg, var(--eq-cream-soft, #faf8f5) 0%, #ffffff 100%); border-bottom: 1px solid var(--eq-line);">
    <div class="eq-container" style="text-align: center; max-width: 760px; margin: 0 auto;">
      <span style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.12em; font-weight: 600; color: var(--eq-gold-dark); display: inline-block; margin-bottom: 0.5rem;">
        Earthquick Collective
      </span>
      <h1 style="font-family: var(--font-display); font-size: 2.2rem; color: var(--eq-navy); margin-bottom: 0.75rem; font-weight: 600; line-height: 1.2;">
        Partner Ateliers &amp; Brands
      </h1>
      <p style="font-size: 0.95rem; line-height: 1.6; color: var(--eq-charcoal-soft); margin: 0;">
        Earthquick unites artisanal design houses, master handloom weavers, and visionary lifestyle innovators into one seamless luxury shopping destination.
      </p>
    </div>
  </section>

  <!-- Brands Grid -->
  <section style="padding: 3.5rem 0 5rem;">
    <div class="eq-container">
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
        @forelse($vendors as $vendor)
          <article style="background: #ffffff; border: 1px solid var(--eq-line); border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, box-shadow 0.25s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            
            <!-- Store Cover Banner / Top Graphic -->
            <div style="height: 140px; background: linear-gradient(135deg, var(--eq-navy) 0%, #203a4c 100%); position: relative; display: flex; align-items: center; justify-content: center;">
              @if($vendor->banner)
                <img src="{{ asset($vendor->banner) }}" alt="{{ $vendor->name }} Banner" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.4;" />
              @endif
              <div style="position: absolute; bottom: -28px; left: 1.5rem; width: 58px; height: 58px; border-radius: 50%; background: #ffffff; border: 3px solid #ffffff; box-shadow: 0 3px 10px rgba(0,0,0,0.12); display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-weight: 700; color: var(--eq-navy); font-size: 1.15rem; overflow: hidden;">
                @if($vendor->logo)
                  <img src="{{ asset($vendor->logo) }}" alt="{{ $vendor->name }}" style="width: 100%; height: 100%; object-fit: cover;" />
                @else
                  {{ substr($vendor->name, 0, 2) }}
                @endif
              </div>

              <!-- Vendor Code Badge -->
              <span style="position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.15); backdrop-filter: blur(4px); color: #ffffff; border: 1px solid rgba(255,255,255,0.25); font-size: 0.65rem; font-weight: 600; letter-spacing: 0.08em; padding: 0.2rem 0.55rem; border-radius: 4px; text-transform: uppercase;">
                {{ $vendor->vendor_code }}
              </span>
            </div>

            <!-- Store Details Body -->
            <div style="padding: 2.2rem 1.5rem 1.5rem; flex: 1; display: flex; flex-direction: column;">
              <h2 style="font-family: var(--font-display); font-size: 1.35rem; color: var(--eq-navy); margin-bottom: 0.25rem; font-weight: 600;">
                <a href="{{ route('stores.show', $vendor->slug) }}" style="color: inherit; text-decoration: none;">
                  {{ $vendor->name }}
                </a>
              </h2>

              @if($vendor->tagline)
                <div style="font-size: 0.82rem; font-weight: 500; color: var(--eq-gold-dark); margin-bottom: 0.75rem;">
                  {{ $vendor->tagline }}
                </div>
              @endif

              <p style="font-size: 0.86rem; line-height: 1.55; color: var(--eq-charcoal-soft); margin-bottom: 1.25rem; flex: 1;">
                {{ Str::limit($vendor->description ?? 'Discover exclusive designer collections crafted with timeless quality and contemporary elegance.', 130) }}
              </p>

              <!-- Footer Meta & Link -->
              <div style="border-top: 1px solid var(--eq-line); padding-top: 1rem; display: flex; align-items: center; justify-content: space-between; margin-top: auto;">
                <span style="font-size: 0.78rem; color: var(--eq-charcoal-muted);">
                  @if($vendor->products_count > 0)
                    <strong style="color: var(--eq-charcoal);">{{ $vendor->products_count }}</strong> Available Pieces
                  @else
                    <span style="font-style: italic; color: var(--eq-gold-dark);">Collection in Curation</span>
                  @endif
                </span>

                <a href="{{ route('stores.show', $vendor->slug) }}" class="eq-btn eq-btn--outline" style="font-size: 0.76rem; padding: 0.45rem 0.9rem; text-decoration: none;">
                  Visit Boutique &rarr;
                </a>
              </div>

            </div>

          </article>
        @empty
          <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem;">
            <p style="font-size: 1rem; color: var(--eq-charcoal-soft);">No partner stores currently published.</p>
          </div>
        @endforelse
      </div>
    </div>
  </section>

</main>
@endsection


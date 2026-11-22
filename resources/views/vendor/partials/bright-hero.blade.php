<section class="eq-hero eq-hero--slider eq-bright-store-hero" id="hero-section" tabindex="0" aria-label="Bright store highlights" aria-roledescription="carousel">
  <div class="eq-hero__slides" id="hero-slides">
    <div class="eq-hero__slide is-active" data-slide-index="0">
      <div class="eq-hero__media"><img src="{{ asset('images/bright-demo/hero-electrical-essentials.svg') }}" alt="Demo illustration of electrical switches and sockets" fetchpriority="high" decoding="async" /></div>
      <div class="eq-hero__content">
        <span class="eq-eyebrow">WHOLESALE &amp; RETAIL · CHATTOGRAM</span>
        <h1>Bright Electrical Company</h1>
        <p>{{ $vendor->description ?: 'Explore electrical products and accessories from Bright.' }} Browse verified listings as they are added.</p>
        <div class="eq-hero__actions"><a href="{{ route('stores.catalog', $vendor->slug) }}" class="eq-btn eq-btn--light eq-btn--pill">Browse products</a></div>
      </div>
    </div>
    <div class="eq-hero__slide" data-slide-index="1">
      <div class="eq-hero__media"><img src="{{ asset('images/bright-demo/hero-lighting-essentials.svg') }}" alt="Demo illustration of a light bulb" loading="lazy" decoding="async" /></div>
      <div class="eq-hero__content">
        <span class="eq-eyebrow">BRIGHT ELECTRICAL · DEMO COVER</span>
        <h1>Lighting and everyday electricals</h1>
        <p>Demo cover artwork only. Product details will be shown after Bright confirms them.</p>
        <div class="eq-hero__actions"><a href="{{ route('stores.catalog', ['slug' => $vendor->slug, 'subcategory' => 'led-lights']) }}" class="eq-btn eq-btn--light eq-btn--pill">Explore lighting</a></div>
      </div>
    </div>
    <div class="eq-hero__slide" data-slide-index="2">
      <div class="eq-hero__media"><img src="{{ asset('images/bright-demo/hero-circuit-protection.svg') }}" alt="Demo illustration of an electrical panel" loading="lazy" decoding="async" /></div>
      <div class="eq-hero__content">
        <span class="eq-eyebrow">BRIGHT ELECTRICAL · DEMO COVER</span>
        <h1>Questions about an item?</h1>
        <p>Review Bright's store details and the terms that apply before ordering.</p>
        <div class="eq-hero__actions"><a href="{{ route('stores.bright-information') }}" class="eq-btn eq-btn--light eq-btn--pill">Store information</a></div>
      </div>
    </div>
  </div>
  <div class="eq-hero__nav" aria-label="Bright store slider controls">
    <button type="button" class="eq-hero__arrow eq-hero__arrow--prev" id="hero-prev" aria-label="Previous Bright store slide"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></button>
    <button type="button" class="eq-hero__arrow eq-hero__arrow--next" id="hero-next" aria-label="Next Bright store slide"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button>
  </div>
  <div class="eq-hero__dots" id="hero-dots" role="tablist" aria-label="Bright store slides">
    <button type="button" class="eq-hero__dot is-active" data-index="0" role="tab" aria-selected="true" aria-label="Slide 1: Electrical essentials"></button>
    <button type="button" class="eq-hero__dot" data-index="1" role="tab" aria-selected="false" aria-label="Slide 2: Lighting essentials"></button>
    <button type="button" class="eq-hero__dot" data-index="2" role="tab" aria-selected="false" aria-label="Slide 3: Bright Electrical"></button>
  </div>
</section>

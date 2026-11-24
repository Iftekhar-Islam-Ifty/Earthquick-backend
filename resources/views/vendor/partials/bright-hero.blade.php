<section class="eq-hero eq-hero--slider eq-bright-store-hero" id="hero-section" tabindex="0" aria-label="Bright electrical product images" aria-roledescription="carousel">
  <div class="eq-hero__slides" id="hero-slides">
    @foreach(['bright-hero-1', 'bright-hero-2', 'bright-hero-3'] as $index => $slot)
      <div class="eq-hero__slide{{ $index === 0 ? ' is-active' : '' }}" data-slide-index="{{ $index }}">
        <div class="eq-hero__media">
          <img src="{{ asset($homepageMedia[$slot]['image']) }}" alt="{{ $homepageMedia[$slot]['alt'] }}" fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}" {{ $index === 0 ? '' : 'loading=lazy' }} decoding="async" />
        </div>
      </div>
    @endforeach
  </div>
  <div class="eq-hero__nav" aria-label="Bright image controls">
    <button type="button" class="eq-hero__arrow eq-hero__arrow--prev" id="hero-prev" aria-label="Previous image"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></button>
    <button type="button" class="eq-hero__arrow eq-hero__arrow--next" id="hero-next" aria-label="Next image"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button>
  </div>
  <div class="eq-hero__dots" id="hero-dots" role="tablist" aria-label="Choose hero image">
    @foreach(['Electrical essentials', 'Lighting essentials', 'Circuit protection'] as $index => $label)
      <button type="button" class="eq-hero__dot{{ $index === 0 ? ' is-active' : '' }}" data-index="{{ $index }}" role="tab" aria-selected="{{ $index === 0 ? 'true' : 'false' }}" aria-label="{{ $label }}"></button>
    @endforeach
  </div>
</section>

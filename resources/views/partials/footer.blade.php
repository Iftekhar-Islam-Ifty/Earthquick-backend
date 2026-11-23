<!-- =====================================================================
     RTHQUICK — REUSABLE FOOTER COMPONENT
     Blade Partial: resources/views/partials/footer.blade.php
     ===================================================================== -->
<footer class="eq-footer" id="eq-main-footer">
  <div class="eq-container">
    <div class="eq-footer__top">

      <!-- Brand column -->
      <div class="eq-footer__brand" id="footer-col-brand">
        <a href="{{ route('home') }}" style="display: block; text-decoration: none;">
          <img src="{{ asset('images/logo/rthquick-logo.png') }}" alt="Rthquick" />
        </a>
        <p>Rthquick brings independent Bangladeshi stores together in one marketplace.</p>
      </div>

      <!-- Store links have their own identity and business details. -->
      <div class="eq-footer__nav" id="footer-col-stores">
        <h4 class="eq-footer__heading">OUR STORES</h4>
        <ul class="eq-footer__links eq-footer__store-list">
          @foreach(['nous-telos', 'bright'] as $storeSlug)
            @if($footerStore = $storeLogos->get($storeSlug))
              <li><a href="{{ route('stores.show', $footerStore->slug) }}">{{ $footerStore->name }}</a></li>
            @endif
          @endforeach
        </ul>
      </div>

      <!-- Marketplace category groups -->
      <div class="eq-footer__nav" id="footer-col-categories">
        <h4 class="eq-footer__heading">SHOP BY CATEGORY</h4>
        <ul class="eq-footer__links">
          <li><a href="{{ route('category.group', 'fashion-accessories') }}">Fashion &amp; Accessories</a></li>
          <li><a href="{{ route('category.show', 'home-decor') }}">Home &amp; Living</a></li>
          <li><a href="{{ route('category.show', 'electronics') }}">Electronics</a></li>
        </ul>
      </div>

      <!-- Company links -->
      <div class="eq-footer__nav" id="footer-col-company">
        <h4 class="eq-footer__heading">HELP &amp; INFO</h4>
        <ul class="eq-footer__links">
          <li><a href="{{ route('about') }}" id="footer-link-about">About Us</a></li>
          <li><a href="{{ route('about') }}#contact-support" id="footer-link-contact">Contact &amp; Support</a></li>
          <li><a href="{{ route('about') }}#help-faq" id="footer-link-faq">Help &amp; FAQ</a></li>
          <li><a href="{{ route('policies.delivery-returns') }}" id="footer-link-delivery-returns">Delivery, Returns &amp; Refunds</a></li>
          <li><a href="{{ route('stores.bright-information') }}" id="footer-link-bright-information">Bright store information &amp; policies</a></li>
        </ul>
      </div>

      <!-- Contact address -->
      <div class="eq-footer__nav" id="footer-col-contact">
        <h4 class="eq-footer__heading">CONTACT</h4>
        <ul class="eq-footer__links">
          @if(config('communications.support_email'))
            <li><a href="mailto:{{ config('communications.support_email') }}" aria-label="Email Rthquick at {{ config('communications.support_email') }}"><span class="eq-footer__email-full">{{ config('communications.support_email') }}</span><span class="eq-footer__email-short" aria-hidden="true">Email us &rarr;</span></a></li>
          @endif
          @unless(config('communications.support_email'))
            <li><a href="{{ route('about') }}#contact-support">Use the support form</a></li>
          @endunless
        </ul>
      </div>

    </div>

    <!-- Footer bottom copyright bar -->
    <div class="eq-footer__bottom" id="footer-bottom-bar">
      <span>&copy; {{ date('Y') }} Rthquick. All rights reserved.</span>
    </div>
  </div>
</footer>

<!-- Search Modal Overlay (Shared across all pages) -->
<div class="eq-search-modal" id="eq-search-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Search products">
  <div class="eq-search-box">
    <form id="eq-search-form" action="{{ route('search') }}" method="GET" style="margin: 0;">
      <div class="eq-search-box__input-row">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="11" cy="11" r="7"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input 
          type="search" 
          name="q"
          class="eq-search-box__input" 
          id="eq-search-input" 
          placeholder="Search sarees, sets, bags, home decor..." 
          aria-label="Search term" 
          autocomplete="off"
        />
        <button type="button" class="eq-search-box__close" id="eq-search-close" aria-label="Close search">&times;</button>
      </div>
    </form>

    <!-- Live Instant Search Suggestions Container -->
    <div class="eq-search-live-dropdown" id="eq-search-live-dropdown" style="display: none;"></div>

    <div class="eq-search-box__suggestions" id="eq-search-popular-tags">
      <span>Popular:</span>
      <a href="{{ route('search', ['q' => 'Jamdani']) }}">Jamdani</a>
      <a href="{{ route('search', ['q' => 'Cotton']) }}">Cotton Kameez</a>
      <a href="{{ route('search', ['q' => 'Linen']) }}">Linen Sets</a>
      <a href="{{ route('search', ['q' => 'Leather']) }}">Leather Bags</a>
      <a href="{{ route('search', ['q' => 'Kantha']) }}">Kantha</a>
    </div>
  </div>
</div>

<!-- Toast Notification Container (Shared) -->
<div class="eq-toast-container" id="eq-toast-container" role="status" aria-live="polite"></div>

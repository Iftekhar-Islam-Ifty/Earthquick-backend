<!-- =====================================================================
     EARTHQUICK / NOUS TELOS — REUSABLE FOOTER COMPONENT
     Blade Partial: resources/views/partials/footer.blade.php
     ===================================================================== -->
<footer class="eq-footer" id="eq-main-footer">
  <div class="eq-container">
    <div class="eq-footer__top">

      <!-- Brand column -->
      <div class="eq-footer__brand" id="footer-col-brand">
        <a href="{{ route('home') }}" style="display: block; text-decoration: none;">
          <img src="{{ asset('images/logo/earthquick-logo.png') }}" alt="Earthquick" />
        </a>
        <p>A curated marketplace for independent Bangladeshi brands. Explore Nous Telos heritage handloom, Bright electronics, and partner stores.</p>
        <nav class="eq-footer__social" aria-label="Social links">
          <a href="https://www.facebook.com/noustelos" class="eq-footer__social-link eq-footer__social-link--facebook" target="_blank" rel="noopener noreferrer" aria-label="Facebook (opens in a new tab)" title="Facebook" id="social-facebook">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 22v-9h3l.5-3.5h-3.5V7.25c0-1 .3-1.75 1.75-1.75H17V2.3c-.3-.05-1.3-.15-2.5-.15-2.5 0-4.25 1.55-4.25 4.4V9.5H7V13h3.25v9h3.25Z"/></svg>
          </a>
          <a href="https://instagram.com/nous.telos" class="eq-footer__social-link eq-footer__social-link--instagram" target="_blank" rel="noopener noreferrer" aria-label="Instagram (opens in a new tab)" title="Instagram" id="social-instagram">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
          </a>
          @if(config('communications.support_whatsapp'))
            <a href="https://wa.me/{{ preg_replace('/\D/', '', config('communications.support_whatsapp')) }}" class="eq-footer__social-link eq-footer__social-link--whatsapp" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp (opens in a new tab)" title="Chat on WhatsApp" id="social-whatsapp">
              <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2a9.93 9.93 0 0 0-8.6 14.9L2 22l5.25-1.37A9.96 9.96 0 1 0 12.04 2Zm0 18.18a8.2 8.2 0 0 1-4.18-1.14l-.3-.18-3.12.82.83-3.04-.2-.32A8.21 8.21 0 1 1 12.04 20.18Zm4.5-6.16c-.25-.12-1.47-.73-1.7-.81-.23-.09-.39-.13-.56.12-.17.25-.64.81-.78.97-.14.17-.28.19-.53.06-.25-.12-1.05-.39-2-1.24-.74-.66-1.25-1.48-1.4-1.73-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.44.12-.14.16-.25.24-.41.08-.17.04-.31-.02-.44-.06-.13-.56-1.34-.77-1.83-.2-.48-.4-.41-.56-.42h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.09 0 1.23.9 2.42 1.02 2.58.12.16 1.77 2.7 4.3 3.78.6.26 1.07.41 1.44.52.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.08.15-1.18-.07-.1-.23-.16-.48-.29Z"/></svg>
            </a>
          @endif
        </nav>
      </div>

      <!-- Shop navigation links -->
      <div class="eq-footer__nav" id="footer-col-shop">
        <h4 class="eq-footer__heading">SHOP</h4>
        <ul class="eq-footer__links">
          <li><a href="{{ route('category.show', 'men') }}" id="footer-link-men">Men</a></li>
          <li><a href="{{ route('category.show', 'women') }}" id="footer-link-women">Women</a></li>
          <li><a href="{{ route('category.show', 'kids') }}" id="footer-link-kids">Kids</a></li>
          <li><a href="{{ route('category.show', 'ornaments') }}" id="footer-link-ornaments">Ornaments</a></li>
          <li><a href="{{ route('category.show', 'bags') }}" id="footer-link-bags">Bags</a></li>
          <li><a href="{{ route('category.show', 'home-decor') }}" id="footer-link-decor">Home Decor</a></li>
          <li><a href="{{ route('stores.index') }}" id="footer-link-stores">Brand Stores</a></li>
        </ul>
      </div>

      <!-- Company links -->
      <div class="eq-footer__nav" id="footer-col-company">
        <h4 class="eq-footer__heading">COMPANY</h4>
        <ul class="eq-footer__links">
          <li><a href="{{ route('about') }}" id="footer-link-about">About Us</a></li>
          <li><a href="{{ route('about') }}#brand-ecosystem" id="footer-link-ecosystem">Our Brands &amp; Artisans</a></li>
          <li><a href="{{ route('about') }}#contact-support" id="footer-link-contact">Contact &amp; Support</a></li>
          <li><a href="{{ route('about') }}#help-faq" id="footer-link-faq">Help &amp; FAQ</a></li>
          <li><a href="{{ route('policies.delivery-returns') }}" id="footer-link-delivery-returns">Delivery, Returns &amp; Refunds</a></li>
        </ul>
      </div>

      <!-- Contact address -->
      <div class="eq-footer__nav" id="footer-col-contact">
        <h4 class="eq-footer__heading">CONTACT</h4>
        <ul class="eq-footer__links">
          <li>GEC Circle, Nasirabad</li>
          <li>Chattogram 4000, Bangladesh</li>
          @if(config('communications.support_email'))<li><a href="mailto:{{ config('communications.support_email') }}">{{ config('communications.support_email') }}</a></li>@endif
          @unless(config('communications.support_email'))
            <li><a href="{{ route('about') }}#contact-support">Use the support form</a></li>
          @endunless
        </ul>
      </div>

    </div>

    <!-- Footer bottom copyright bar -->
    <div class="eq-footer__bottom" id="footer-bottom-bar">
      <span>&copy; {{ date('Y') }} Earthquick. All rights reserved.</span>
      <span>Bangladesh's curated multi-vendor marketplace.</span>
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

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
        <p>Premium fashion and lifestyle pieces from Nous Telos — handloom sarees, tailored sets and considered accessories.</p>
        <div class="eq-footer__social" aria-label="Social links">
          <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook" id="social-facebook">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
          </a>
          <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram" id="social-instagram">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
          </a>
          <a href="https://pinterest.com" target="_blank" rel="noopener noreferrer" aria-label="Pinterest" id="social-pinterest">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M9 10a3 3 0 1 1 4.5 2.6c-.7.4-1.1 1-1.1 1.9"></path></svg>
          </a>
        </div>
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
        </ul>
      </div>

      <!-- Contact address -->
      <div class="eq-footer__nav" id="footer-col-contact">
        <h4 class="eq-footer__heading">CONTACT</h4>
        <ul class="eq-footer__links">
          <li>GEC Circle, Nasirabad</li>
          <li>Chattogram 4000, Bangladesh</li>
          <li>iftekharislamifty@gmail.com</li>
          <li>+880 017931***87</li>
        </ul>
      </div>

    </div>

    <!-- Footer bottom copyright bar -->
    <div class="eq-footer__bottom" id="footer-bottom-bar">
      <span>&copy; {{ date('Y') }} Earthquick. All rights reserved.</span>
      <span>A Nous Telos brand.</span>
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

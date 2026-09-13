/* =====================================================================
   EARTHQUICK / NOUS TELOS — REUSABLE COMPONENT LOADER
   Loads components/navbar.html and components/footer.html dynamically
   into any page. In the future with Laravel, these become:
     @include("partials.navbar")
     @include("partials.footer")
   ===================================================================== */

(function () {
  "use strict";

  // 1. Detect if current page is inside /pages/ or at project root
  const isSubpage = window.location.pathname.includes("/pages/");
  const basePath = isSubpage ? "../" : "./";
  const prefix = isSubpage ? "../" : "";

  // Embedded defaults for instant zero-flash render
  const DEFAULT_NAVBAR = "<!-- =====================================================================\n     EARTHQUICK / NOUS TELOS — REUSABLE HEADER & NAVIGATION COMPONENT\n     Future Laravel Blade Partial: resources/views/partials/navbar.blade.php\n     Changes made here will automatically update across all pages!\n     ===================================================================== -->\n<a href=\"#main-content\" class=\"eq-skip-link\">Skip to main content</a>\n<header class=\"eq-navbar\" id=\"eq-main-navbar\">\n  <div class=\"eq-navbar__inner\">\n    <!-- Brand Logo -->\n    <a href=\"{{BASE}}index.html\" class=\"eq-navbar__logo\" id=\"eq-brand-logo\" aria-label=\"Earthquick Home\">\n      <img src=\"{{BASE}}images/logo/earthquick-logo.png?v=3\" alt=\"Earthquick — Crafted for the Modern You\" width=\"150\" height=\"32\" decoding=\"async\" />\n    </a>\n\n    <!-- Desktop Navigation Menu -->\n    <nav id=\"eq-nav-menu\" aria-label=\"Primary Navigation\">\n      <ul class=\"eq-navbar__links\" id=\"eq-nav-links\">\n        <!-- Mobile Drawer Pinned Header with Brand Logo -->\n        <li class=\"eq-drawer-header\" aria-hidden=\"true\">\n          <a href=\"{{BASE}}index.html\" class=\"eq-drawer-logo\" aria-label=\"Earthquick Home\">\n            <img src=\"{{BASE}}images/logo/earthquick-logo.png?v=3\" alt=\"Earthquick — Crafted for the Modern You\" />\n          </a>\n          <button type=\"button\" class=\"eq-drawer-close-btn\" id=\"eq-drawer-close\" aria-label=\"Close navigation menu\">\n            <svg viewBox=\"0 0 24 24\" width=\"18\" height=\"18\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">\n              <line x1=\"18\" y1=\"6\" x2=\"6\" y2=\"18\"></line>\n              <line x1=\"6\" y1=\"6\" x2=\"18\" y2=\"18\"></line>\n            </svg>\n          </button>\n        </li>\n\n        <li><a href=\"{{BASE}}index.html\" id=\"nav-link-home\">Home</a></li>\n        <li><a href=\"{{BASE}}pages/men.html\" id=\"nav-link-men\">Men</a></li>\n        \n        <!-- Women Category with Mega-Dropdown -->\n        <li class=\"eq-nav-item eq-nav-item--has-dropdown\" id=\"nav-item-women\">\n          <div class=\"eq-nav-link-wrapper\">\n            <a href=\"{{BASE}}pages/women.html\" id=\"nav-link-women\">Women</a>\n            <button type=\"button\" class=\"eq-dropdown-toggle-btn\" id=\"btn-toggle-women-sub\" aria-expanded=\"false\" aria-label=\"Toggle Women subcategories\">\n              <svg viewBox=\"0 0 24 24\" width=\"14\" height=\"14\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">\n                <polyline points=\"6 9 12 15 18 9\"></polyline>\n              </svg>\n            </button>\n          </div>\n\n          <!-- Women Dropdown Mega-Panel -->\n          <div class=\"eq-megamenu\" id=\"megamenu-women\" role=\"region\" aria-label=\"Women Subcategories\">\n            <div class=\"eq-megamenu__grid\">\n              <!-- Column 1: Saree (with expandable subcategory list) -->\n              <div class=\"eq-megamenu__col eq-submenu-nested\" id=\"submenu-saree\">\n                <div class=\"eq-nested-header\">\n                  <a href=\"{{BASE}}pages/saree.html\" class=\"eq-megamenu__heading eq-nested-title\">Saree</a>\n                  <button type=\"button\" class=\"eq-nested-toggle-btn\" id=\"btn-toggle-saree\" aria-expanded=\"false\" aria-label=\"Toggle Saree subcategories\">\n                    <svg viewBox=\"0 0 24 24\" width=\"12\" height=\"12\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">\n                      <polyline points=\"6 9 12 15 18 9\"></polyline>\n                    </svg>\n                  </button>\n                </div>\n                <ul class=\"eq-megamenu__list eq-nested-list\" id=\"list-saree-sub\">\n                  <li><a href=\"{{BASE}}pages/saree.html\" class=\"eq-megamenu__view-all\">All Sarees &rarr;</a></li>\n                  <li><a href=\"{{BASE}}pages/saree-jamdani.html\">Jamdani Weaves</a></li>\n                  <li><a href=\"{{BASE}}pages/saree-tantuj.html\">Tantuj &amp; Tangail</a></li>\n                  <li><a href=\"{{BASE}}pages/saree-half-silk.html\">Half Silk</a></li>\n                  <li><a href=\"{{BASE}}pages/saree-full-silk.html\">Pure Silk</a></li>\n                </ul>\n              </div>\n\n              <!-- Column 2: Ready to Wear (Three Piece, Two Piece) -->\n              <div class=\"eq-megamenu__col\">\n                <div class=\"eq-submenu-nested\" id=\"submenu-three-piece\">\n                  <div class=\"eq-nested-header\">\n                    <a href=\"{{BASE}}pages/three-piece.html\" class=\"eq-megamenu__heading eq-nested-title\">Three Piece Sets</a>\n                  </div>\n                  <ul class=\"eq-megamenu__list eq-nested-list\" id=\"list-three-piece-sub\">\n                    <li><a href=\"{{BASE}}pages/three-piece.html\" class=\"eq-megamenu__view-all\">All Three Piece &rarr;</a></li>\n                  </ul>\n                </div>\n\n                <div class=\"eq-submenu-nested\" id=\"submenu-two-piece\" style=\"margin-top: 0.65rem;\">\n                  <div class=\"eq-nested-header\">\n                    <a href=\"{{BASE}}pages/two-piece.html\" class=\"eq-megamenu__heading eq-nested-title\">Two Piece Ensembles</a>\n                  </div>\n                  <ul class=\"eq-megamenu__list eq-nested-list\" id=\"list-two-piece-sub\">\n                    <li><a href=\"{{BASE}}pages/two-piece.html\" class=\"eq-megamenu__view-all\">All Two Piece &rarr;</a></li>\n                  </ul>\n                </div>\n\n                <div class=\"eq-megamenu__all-link\" style=\"margin-top: 0.85rem;\">\n                  <a href=\"{{BASE}}pages/women.html\" class=\"eq-megamenu__view-all\" style=\"font-weight: 600; color: var(--eq-gold-dark);\">Explore Women's Hub &rarr;</a>\n                </div>\n              </div>\n\n              <!-- Column 3: Visual Spotlight Card -->\n              <div class=\"eq-megamenu__feature\">\n                <a href=\"{{BASE}}pages/women.html\" class=\"eq-megamenu__feature-card\">\n                  <div class=\"eq-megamenu__feature-media\">\n                    <img src=\"{{BASE}}images/hero/hero-main-saree-2.jpg?v=3\" alt=\"Heritage Women Collection\" />\n                  </div>\n                  <div class=\"eq-megamenu__feature-content\">\n                    <span class=\"eq-megamenu__tag\">NOUS TELOS</span>\n                    <strong class=\"eq-megamenu__feature-title\">Heritage Weaves</strong>\n                    <span class=\"eq-megamenu__feature-link\">Explore Hub &rarr;</span>\n                  </div>\n                </a>\n              </div>\n            </div>\n          </div>\n        </li>\n\n        <li><a href=\"{{BASE}}pages/kids.html\" id=\"nav-link-kids\">Kids</a></li>\n        <li><a href=\"{{BASE}}pages/ornaments.html\" id=\"nav-link-ornaments\">Ornaments</a></li>\n        <li><a href=\"{{BASE}}pages/bags.html\" id=\"nav-link-bags\">Bags</a></li>\n        \n        <!-- Home Decor Category with Dropdown (Kantha, Bedsheet, Cushion Cover) -->\n        <li class=\"eq-nav-item eq-nav-item--has-dropdown\" id=\"nav-item-home-decor\">\n          <div class=\"eq-nav-link-wrapper\">\n            <a href=\"{{BASE}}pages/home-decor.html\" id=\"nav-link-home-decor\">Home Decor</a>\n            <button type=\"button\" class=\"eq-dropdown-toggle-btn\" id=\"btn-toggle-decor-sub\" aria-expanded=\"false\" aria-label=\"Toggle Home Decor subcategories\">\n              <svg viewBox=\"0 0 24 24\" width=\"14\" height=\"14\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">\n                <polyline points=\"6 9 12 15 18 9\"></polyline>\n              </svg>\n            </button>\n          </div>\n\n          <!-- Home Decor Dropdown Panel -->\n          <div class=\"eq-megamenu eq-megamenu--compact\" id=\"megamenu-home-decor\" role=\"region\" aria-label=\"Home Decor Subcategories\">\n            <div class=\"eq-megamenu__grid eq-megamenu__grid--2col\">\n              <!-- Column 1: Subcategories -->\n              <div class=\"eq-megamenu__col\">\n                <div class=\"eq-megamenu__heading\">Home Sanctuary</div>\n                <ul class=\"eq-megamenu__list\">\n                  <li><a href=\"{{BASE}}pages/home-decor-kantha.html\">Kantha (Nakshi Quilt)</a></li>\n                  <li><a href=\"{{BASE}}pages/home-decor-bedsheet.html\">Bedsheet Sets</a></li>\n                  <li><a href=\"{{BASE}}pages/home-decor-cushion.html\">Cushion Cover</a></li>\n                  <li><a href=\"{{BASE}}pages/home-decor.html\" class=\"eq-megamenu__view-all\">All Home Decor &rarr;</a></li>\n                </ul>\n              </div>\n\n              <!-- Column 2: Visual Spotlight Card -->\n              <div class=\"eq-megamenu__feature\">\n                <a href=\"{{BASE}}pages/home-decor.html\" class=\"eq-megamenu__feature-card\">\n                  <div class=\"eq-megamenu__feature-media\">\n                    <img src=\"{{BASE}}images/categories/decor-kantha.svg\" alt=\"Artisanal Nakshi Kantha Quilt\" />\n                  </div>\n                  <div class=\"eq-megamenu__feature-content\">\n                    <span class=\"eq-megamenu__tag\">NOUS TELOS LIVING</span>\n                    <strong class=\"eq-megamenu__feature-title\">Nakshi Kantha</strong>\n                    <span class=\"eq-megamenu__feature-link\">Explore Living &rarr;</span>\n                  </div>\n                </a>\n              </div>\n            </div>\n          </div>\n        </li>\n\n        <li><a href=\"{{BASE}}pages/about.html\" id=\"nav-link-about\">About Us</a></li>\n        <!-- Mobile Drawer Minimal Footer with Quick Actions -->\n        <li class=\"eq-drawer-footer\" aria-hidden=\"true\">\n          <div class=\"eq-drawer-actions\">\n            <a href=\"{{BASE}}pages/account.html\" class=\"eq-drawer-action-btn\" id=\"drawer-btn-account\">\n              <svg viewBox=\"0 0 24 24\" width=\"16\" height=\"16\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.8\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">\n                <path d=\"M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2\"></path>\n                <circle cx=\"12\" cy=\"7\" r=\"4\"></circle>\n              </svg>\n              <span>Account</span>\n            </a>\n            <button type=\"button\" class=\"eq-drawer-action-btn\" data-action=\"open-search\" id=\"drawer-btn-search\">\n              <svg viewBox=\"0 0 24 24\" width=\"16\" height=\"16\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.8\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">\n                <circle cx=\"11\" cy=\"11\" r=\"7\"></circle>\n                <line x1=\"21\" y1=\"21\" x2=\"16.65\" y2=\"16.65\"></line>\n              </svg>\n              <span>Search</span>\n            </button>\n          </div>\n          <p class=\"eq-drawer-tagline\">Handloom Heritage &bull; Nous Telos</p>\n        </li>\n      </ul>\n    </nav>\n\n    <!-- Utility Action Icons -->\n    <div class=\"eq-navbar__icons\" id=\"eq-navbar-actions\">\n      <!-- Search Button -->\n      <button type=\"button\" aria-label=\"Search collection\" id=\"eq-btn-search\" data-action=\"open-search\">\n        <svg viewBox=\"0 0 24 24\" fill=\"none\" stroke-width=\"1.6\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">\n          <circle cx=\"11\" cy=\"11\" r=\"7\"></circle>\n          <line x1=\"21\" y1=\"21\" x2=\"16.65\" y2=\"16.65\"></line>\n        </svg>\n      </button>\n\n      <!-- Account Button -->\n      <a href=\"{{BASE}}pages/account.html\" aria-label=\"My Account\" id=\"eq-btn-account\" data-action=\"open-account\" style=\"display: flex; align-items: center; justify-content: center; color: inherit; text-decoration: none;\">\n        <svg viewBox=\"0 0 24 24\" fill=\"none\" stroke-width=\"1.6\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">\n          <path d=\"M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2\"></path>\n          <circle cx=\"12\" cy=\"7\" r=\"4\"></circle>\n        </svg>\n      </a>\n\n      <!-- Shopping Bag / Cart Button -->\n      <button type=\"button\" aria-label=\"Shopping Bag\" id=\"eq-btn-cart\" data-action=\"open-cart\">\n        <svg viewBox=\"0 0 24 24\" fill=\"none\" stroke-width=\"1.6\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">\n          <path d=\"M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z\"></path>\n          <path d=\"M3 6h18\"></path>\n          <path d=\"M16 10a4 4 0 0 1-8 0\"></path>\n        </svg>\n        <span class=\"eq-cart-count\" id=\"eq-cart-count\">0</span>\n      </button>\n    </div>\n\n    <!-- Mobile Hamburger Menu Toggle -->\n    <button type=\"button\" class=\"eq-navbar__toggle\" id=\"eq-nav-toggle\" aria-label=\"Toggle navigation menu\" aria-expanded=\"false\" aria-controls=\"eq-nav-links\">\n      <span></span><span></span><span></span>\n    </button>\n  </div>\n\n  <!-- Mobile Drawer Backdrop Overlay -->\n  <div class=\"eq-drawer-backdrop\" id=\"eq-nav-backdrop\"></div>\n</header>\n";
  const DEFAULT_FOOTER = "<!-- =====================================================================\n     EARTHQUICK / NOUS TELOS — REUSABLE FOOTER COMPONENT\n     Future Laravel Blade Partial: resources/views/partials/footer.blade.php\n     Changes made here will automatically update across all pages!\n     ===================================================================== -->\n<footer class=\"eq-footer\" id=\"eq-main-footer\">\n  <div class=\"eq-container\">\n    <div class=\"eq-footer__top\">\n\n      <!-- Brand column -->\n      <div class=\"eq-footer__brand\" id=\"footer-col-brand\">\n        <a href=\"{{BASE}}index.html\" style=\"display: block; text-decoration: none;\">\n          <img src=\"{{BASE}}images/logo/earthquick-logo.png?v=3\" alt=\"Earthquick\" />\n        </a>\n        <p>Premium fashion and lifestyle pieces from Nous Telos — handloom sarees, tailored sets and considered accessories.</p>\n        <div class=\"eq-footer__social\" aria-label=\"Social links\">\n          <a href=\"https://facebook.com\" target=\"_blank\" rel=\"noopener noreferrer\" aria-label=\"Facebook\" id=\"social-facebook\">\n            <svg viewBox=\"0 0 24 24\" fill=\"none\" stroke-width=\"1.6\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\"><path d=\"M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z\"></path></svg>\n          </a>\n          <a href=\"https://instagram.com\" target=\"_blank\" rel=\"noopener noreferrer\" aria-label=\"Instagram\" id=\"social-instagram\">\n            <svg viewBox=\"0 0 24 24\" fill=\"none\" stroke-width=\"1.6\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\"><rect x=\"2\" y=\"2\" width=\"20\" height=\"20\" rx=\"5\"></rect><circle cx=\"12\" cy=\"12\" r=\"4\"></circle><line x1=\"17.5\" y1=\"6.5\" x2=\"17.51\" y2=\"6.5\"></line></svg>\n          </a>\n          <a href=\"https://pinterest.com\" target=\"_blank\" rel=\"noopener noreferrer\" aria-label=\"Pinterest\" id=\"social-pinterest\">\n            <svg viewBox=\"0 0 24 24\" fill=\"none\" stroke-width=\"1.6\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\"><circle cx=\"12\" cy=\"12\" r=\"10\"></circle><path d=\"M12 16v-4\"></path><path d=\"M9 10a3 3 0 1 1 4.5 2.6c-.7.4-1.1 1-1.1 1.9\"></path></svg>\n          </a>\n        </div>\n      </div>\n\n      <!-- Shop navigation links -->\n      <div class=\"eq-footer__nav\" id=\"footer-col-shop\">\n        <h4 class=\"eq-footer__heading\">SHOP</h4>\n        <ul class=\"eq-footer__links\">\n          <li><a href=\"{{BASE}}pages/men.html\" id=\"footer-link-men\">Men</a></li>\n          <li><a href=\"{{BASE}}pages/women.html\" id=\"footer-link-women\">Women</a></li>\n          <li><a href=\"{{BASE}}pages/kids.html\" id=\"footer-link-kids\">Kids</a></li>\n          <li><a href=\"{{BASE}}pages/ornaments.html\" id=\"footer-link-ornaments\">Ornaments</a></li>\n          <li><a href=\"{{BASE}}pages/bags.html\" id=\"footer-link-bags\">Bags</a></li>\n          <li><a href=\"{{BASE}}pages/home-decor.html\" id=\"footer-link-decor\">Home Decor</a></li>\n        </ul>\n      </div>\n\n      <!-- Company links -->\n      <div class=\"eq-footer__nav\" id=\"footer-col-company\">\n        <h4 class=\"eq-footer__heading\">COMPANY</h4>\n        <ul class=\"eq-footer__links\">\n          <li><a href=\"{{BASE}}pages/about.html\" id=\"footer-link-about\">About Us</a></li>\n          <li><a href=\"{{BASE}}pages/about.html#brand-ecosystem\" id=\"footer-link-ecosystem\">Our Brands &amp; Artisans</a></li>\n          <li><a href=\"{{BASE}}pages/about.html#contact-support\" id=\"footer-link-contact\">Contact &amp; Support</a></li>\n          <li><a href=\"{{BASE}}pages/about.html#help-faq\" id=\"footer-link-faq\">Help &amp; FAQ</a></li>\n        </ul>\n      </div>\n\n      <!-- Contact address -->\n      <div class=\"eq-footer__nav\" id=\"footer-col-contact\">\n        <h4 class=\"eq-footer__heading\">CONTACT</h4>\n        <ul class=\"eq-footer__links\">\n          <li>GEC Circle, Nasirabad</li>\n          <li>Chattogram 4000, Bangladesh</li>\n          <li>iftekharislamifty@gmail.com</li>\n          <li>+880 017931***87</li>\n        </ul>\n      </div>\n\n    </div>\n\n    <!-- Footer bottom copyright bar -->\n    <div class=\"eq-footer__bottom\" id=\"footer-bottom-bar\">\n      <span>&copy; 2026 Earthquick. All rights reserved.</span>\n      <span>A Nous Telos brand.</span>\n    </div>\n  </div>\n</footer>\n\n<!-- Search Modal Overlay (Shared across all pages) -->\n<div class=\"eq-search-modal\" id=\"eq-search-modal\" aria-hidden=\"true\" role=\"dialog\" aria-modal=\"true\" aria-label=\"Search products\">\n  <div class=\"eq-search-box\">\n    <div class=\"eq-search-box__input-row\">\n      <svg viewBox=\"0 0 24 24\" fill=\"none\" stroke-width=\"1.6\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">\n        <circle cx=\"11\" cy=\"11\" r=\"7\"></circle>\n        <line x1=\"21\" y1=\"21\" x2=\"16.65\" y2=\"16.65\"></line>\n      </svg>\n      <input type=\"text\" class=\"eq-search-box__input\" id=\"eq-search-input\" placeholder=\"Search sarees, sets, bags, home decor...\" aria-label=\"Search term\" />\n      <button type=\"button\" class=\"eq-search-box__close\" id=\"eq-search-close\" aria-label=\"Close search\">&times;</button>\n    </div>\n    <div class=\"eq-search-box__suggestions\">\n      <span>Popular:</span>\n      <a href=\"{{BASE}}pages/saree.html\">Jamdani</a>\n      <a href=\"{{BASE}}pages/three-piece.html\">Cotton Kameez</a>\n      <a href=\"{{BASE}}pages/two-piece.html\">Linen Sets</a>\n      <a href=\"{{BASE}}pages/bags.html\">Leather Sling</a>\n      <a href=\"{{BASE}}pages/home-decor.html\">Kantha</a>\n    </div>\n  </div>\n</div>\n\n<!-- Toast Notification Container (Shared) -->\n<div class=\"eq-toast-container\" id=\"eq-toast-container\" role=\"status\" aria-live=\"polite\"></div>\n\n";

  /**
   * Replaces {{BASE}} placeholders in HTML with the appropriate relative path
   */
  function resolveBasePaths(html) {
    return html.replace(/\{\{BASE\}\}/g, prefix);
  }

  /**
   * Highlights the active link in the navbar based on the current URL
   */
  function highlightActiveNav() {
    const currentPath = window.location.pathname;
    const currentFile = currentPath.split("/").pop() || "index.html";
    
    document.querySelectorAll("#eq-nav-links a").forEach(link => {
      if (link.classList.contains("eq-drawer-logo") || link.closest(".eq-drawer-header") || link.closest(".eq-drawer-footer")) {
        link.classList.remove("is-active");
        return;
      }
      const href = link.getAttribute("href") || "";
      const linkFile = href.split("/").pop();

      link.classList.remove("is-active");

      if (linkFile === currentFile && currentFile !== "") {
        link.classList.add("is-active");
      } else if ((currentFile === "" || currentFile === "index.html") && linkFile === "index.html") {
        link.classList.add("is-active");
      }
    });
  }

  /**
   * Inject and initialize navbar
   */
  async function mountNavbar() {
    // If navbar is already mounted, simply update active state
    const existingHeader = document.querySelector("#eq-main-navbar");
    if (existingHeader) {
      highlightActiveNav();
      rebindEvents();
      return;
    }

    const headerSelector = "#site-header, #eq-site-header, [data-component=\"header\"]";
    const targetEl = document.querySelector(headerSelector);
    if (!targetEl) return;

    // Instant synchronous render with embedded default
    targetEl.outerHTML = resolveBasePaths(DEFAULT_NAVBAR);
    highlightActiveNav();
    rebindEvents();
  }

  /**
   * Inject and initialize footer
   */
  async function mountFooter() {
    // If footer is already mounted, do nothing
    const existingFooter = document.querySelector("#eq-main-footer");
    if (existingFooter) return;

    const footerSelector = "#site-footer, #eq-site-footer, [data-component=\"footer\"]";
    const targetEl = document.querySelector(footerSelector);
    if (!targetEl) return;

    // Instant synchronous render with embedded default
    targetEl.outerHTML = resolveBasePaths(DEFAULT_FOOTER);
  }

  function rebindEvents() {
    if (typeof window.initNavbar === "function") window.initNavbar();
    if (typeof window.initSearchModal === "function") window.initSearchModal();
    if (window.EarthquickCart && typeof window.EarthquickCart.init === "function") {
      window.EarthquickCart.init();
    }
  }

  /**
   * Initialize layout components on DOM ready
   */
  async function initLayoutComponents() {
    await Promise.all([mountNavbar(), mountFooter()]);
    document.dispatchEvent(new CustomEvent("eq:components-loaded"));
  }

  // Run automatically when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initLayoutComponents);
  } else {
    initLayoutComponents();
  }

  // Expose globally
  window.EarthquickLayout = {
    load: initLayoutComponents,
    isSubpage: isSubpage,
    basePath: basePath
  };

})();

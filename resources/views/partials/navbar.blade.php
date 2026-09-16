<!-- =====================================================================
     EARTHQUICK / NOUS TELOS — REUSABLE HEADER & NAVIGATION COMPONENT
     Blade Partial: resources/views/partials/navbar.blade.php
     ===================================================================== -->
<a href="#main-content" class="eq-skip-link">Skip to main content</a>
<header class="eq-navbar" id="eq-main-navbar">
  <div class="eq-navbar__inner">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="eq-navbar__logo" id="eq-brand-logo" aria-label="Earthquick Home">
      <img src="{{ asset('images/logo/earthquick-logo.png') }}" alt="Earthquick — Crafted for the Modern You" width="150" height="32" decoding="async" />
    </a>

    <!-- Desktop Navigation Menu -->
    <nav id="eq-nav-menu" aria-label="Primary Navigation">
      <ul class="eq-navbar__links" id="eq-nav-links">
        <!-- Mobile Drawer Pinned Header with Brand Logo -->
        <li class="eq-drawer-header">
          <a href="{{ route('home') }}" class="eq-drawer-logo" aria-label="Earthquick Home">
            <img src="{{ asset('images/logo/earthquick-logo.png') }}" alt="Earthquick — Crafted for the Modern You" width="130" height="28" decoding="async" />
          </a>
          <button type="button" class="eq-drawer-close-btn" id="eq-drawer-close" aria-label="Close navigation menu">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </li>

        <li><a href="{{ route('home') }}" id="nav-link-home" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a></li>
        <li><a href="{{ route('category.show', 'men') }}" id="nav-link-men" class="{{ request()->is('shop/men*') ? 'is-active' : '' }}">Men</a></li>
        
        <!-- Women Category with Mega-Dropdown -->
        <li class="eq-nav-item eq-nav-item--has-dropdown" id="nav-item-women">
          <div class="eq-nav-link-wrapper">
            <a href="{{ route('category.show', 'women') }}" id="nav-link-women" class="{{ request()->is('shop/women*') ? 'is-active' : '' }}">Women</a>
            <button type="button" class="eq-dropdown-toggle-btn" id="btn-toggle-women-sub" aria-expanded="false" aria-label="Toggle Women subcategories">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </button>
          </div>

          <!-- Women Dropdown Mega-Panel -->
          <div class="eq-megamenu" id="megamenu-women" role="region" aria-label="Women Subcategories">
            <div class="eq-megamenu__grid">
              <!-- Column 1: Saree (with expandable subcategory list) -->
              <div class="eq-megamenu__col eq-submenu-nested" id="submenu-saree">
                <div class="eq-nested-header">
                  <a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'saree']) }}" class="eq-megamenu__heading eq-nested-title">Saree</a>
                  <button type="button" class="eq-nested-toggle-btn" id="btn-toggle-saree" aria-expanded="false" aria-label="Toggle Saree subcategories">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </button>
                </div>
                <ul class="eq-megamenu__list eq-nested-list" id="list-saree-sub">
                  <li><a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'saree']) }}" class="eq-megamenu__view-all">All Sarees &rarr;</a></li>
                  <li><a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'saree']) }}?fabric=Jamdani">Jamdani Weaves</a></li>
                  <li><a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'saree']) }}?fabric=Tangail">Tantuj &amp; Tangail</a></li>
                  <li><a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'saree']) }}?fabric=Half%20Silk">Half Silk</a></li>
                  <li><a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'saree']) }}?fabric=Silk">Pure Silk</a></li>
                </ul>
              </div>

              <!-- Column 2: Ready to Wear (Three Piece, Two Piece) -->
              <div class="eq-megamenu__col">
                <div class="eq-submenu-nested" id="submenu-three-piece">
                  <div class="eq-nested-header">
                    <a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'three-piece']) }}" class="eq-megamenu__heading eq-nested-title">Three Piece Sets</a>
                  </div>
                  <ul class="eq-megamenu__list eq-nested-list" id="list-three-piece-sub">
                    <li><a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'three-piece']) }}" class="eq-megamenu__view-all">All Three Piece &rarr;</a></li>
                  </ul>
                </div>

                <div class="eq-submenu-nested" id="submenu-two-piece" style="margin-top: 0.65rem;">
                  <div class="eq-nested-header">
                    <a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'two-piece']) }}" class="eq-megamenu__heading eq-nested-title">Two Piece Ensembles</a>
                  </div>
                  <ul class="eq-megamenu__list eq-nested-list" id="list-two-piece-sub">
                    <li><a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'two-piece']) }}" class="eq-megamenu__view-all">All Two Piece &rarr;</a></li>
                  </ul>
                </div>

                <div class="eq-megamenu__all-link" style="margin-top: 0.85rem;">
                  <a href="{{ route('category.show', 'women') }}" class="eq-megamenu__view-all" style="font-weight: 600; color: var(--eq-gold-dark);">Explore Women's Hub &rarr;</a>
                </div>
              </div>

              <!-- Column 3: Visual Spotlight Card -->
              <div class="eq-megamenu__feature">
                <a href="{{ route('category.show', 'women') }}" class="eq-megamenu__feature-card">
                  <div class="eq-megamenu__feature-media">
                    <img src="{{ asset('images/hero/hero-main-saree-2.jpg') }}" alt="Heritage Women Collection" />
                  </div>
                  <div class="eq-megamenu__feature-content">
                    <span class="eq-megamenu__tag">NOUS TELOS</span>
                    <strong class="eq-megamenu__feature-title">Heritage Weaves</strong>
                    <span class="eq-megamenu__feature-link">Explore Hub &rarr;</span>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </li>

        <li><a href="{{ route('category.show', 'kids') }}" id="nav-link-kids" class="{{ request()->is('shop/kids*') ? 'is-active' : '' }}">Kids</a></li>
        <li><a href="{{ route('category.show', 'ornaments') }}" id="nav-link-ornaments" class="{{ request()->is('shop/ornaments*') ? 'is-active' : '' }}">Ornaments</a></li>
        <li><a href="{{ route('category.show', 'bags') }}" id="nav-link-bags" class="{{ request()->is('shop/bags*') ? 'is-active' : '' }}">Bags</a></li>
        
        <!-- Home Decor Category with Dropdown (Kantha, Bedsheet, Cushion Cover) -->
        <li class="eq-nav-item eq-nav-item--has-dropdown" id="nav-item-home-decor">
          <div class="eq-nav-link-wrapper">
            <a href="{{ route('category.show', 'home-decor') }}" id="nav-link-home-decor" class="{{ request()->is('shop/home-decor*') ? 'is-active' : '' }}">Home Decor</a>
            <button type="button" class="eq-dropdown-toggle-btn" id="btn-toggle-decor-sub" aria-expanded="false" aria-label="Toggle Home Decor subcategories">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </button>
          </div>

          <!-- Home Decor Dropdown Panel -->
          <div class="eq-megamenu eq-megamenu--compact" id="megamenu-home-decor" role="region" aria-label="Home Decor Subcategories">
            <div class="eq-megamenu__grid eq-megamenu__grid--2col">
              <!-- Column 1: Subcategories -->
              <div class="eq-megamenu__col">
                <div class="eq-megamenu__heading">Home Sanctuary</div>
                <ul class="eq-megamenu__list">
                  <li><a href="{{ route('subcategory.show', ['categorySlug' => 'home-decor', 'subcategorySlug' => 'kantha']) }}">Kantha (Nakshi Quilt)</a></li>
                  <li><a href="{{ route('subcategory.show', ['categorySlug' => 'home-decor', 'subcategorySlug' => 'bedsheet']) }}">Bedsheet Sets</a></li>
                  <li><a href="{{ route('category.show', 'home-decor') }}?sub=cushion-cover">Cushion Cover</a></li>
                  <li><a href="{{ route('category.show', 'home-decor') }}" class="eq-megamenu__view-all">All Home Decor &rarr;</a></li>
                </ul>
              </div>

              <!-- Column 2: Visual Spotlight Card -->
              <div class="eq-megamenu__feature">
                <a href="{{ route('category.show', 'home-decor') }}" class="eq-megamenu__feature-card">
                  <div class="eq-megamenu__feature-media">
                    <img src="{{ asset('images/categories/decor-kantha.svg') }}" alt="Artisanal Nakshi Kantha Quilt" />
                  </div>
                  <div class="eq-megamenu__feature-content">
                    <span class="eq-megamenu__tag">NOUS TELOS LIVING</span>
                    <strong class="eq-megamenu__feature-title">Nakshi Kantha</strong>
                    <span class="eq-megamenu__feature-link">Explore Living &rarr;</span>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </li>

        <li><a href="{{ route('about') }}" id="nav-link-about" class="{{ request()->routeIs('about') ? 'is-active' : '' }}">About Us</a></li>

        <!-- Mobile Drawer Minimal Footer with Quick Actions -->
        <li class="eq-drawer-footer">
          <div class="eq-drawer-actions">
            @auth
              <a href="{{ route('account.dashboard') }}" class="eq-drawer-action-btn eq-drawer-action-btn--account" id="drawer-btn-account" title="My Account">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>{{ explode(' ', Auth::user()->name ?? 'Earthquick')[0] }}</span>
              </a>

              <form method="POST" action="{{ route('logout') }}" class="eq-drawer-form">
                @csrf
                <button type="submit" class="eq-drawer-action-btn eq-drawer-action-btn--logout" id="drawer-btn-logout" title="Sign Out">
                  <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                  </svg>
                  <span>Logout</span>
                </button>
              </form>
            @else
              <a href="{{ route('login') }}" class="eq-drawer-action-btn eq-drawer-action-btn--login" id="drawer-btn-account" title="Sign In">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Sign In</span>
              </a>
            @endauth
          </div>
          <p class="eq-drawer-tagline">Handloom Heritage &bull; Nous Telos</p>
        </li>
      </ul>
    </nav>

    <!-- Utility Action Icons -->
    <div class="eq-navbar__icons" id="eq-navbar-actions">
      <!-- Search Button -->
      <button type="button" aria-label="Search collection" id="eq-btn-search" data-action="open-search">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="11" cy="11" r="7"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </button>

      <!-- Account & Admin Buttons -->
      @auth
        <div class="eq-navbar-auth-group">
          @if(Auth::user()->is_admin)
            <a href="{{ route('admin.dashboard') }}" aria-label="Admin Panel" id="eq-btn-admin-panel" class="eq-btn-admin-badge" title="Store Executive Admin Panel">
              <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="var(--eq-gold)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
              <span class="eq-btn-admin-text eq-btn-admin-text--full">Admin Panel</span>
              <span class="eq-btn-admin-text eq-btn-admin-text--short">Admin</span>
            </a>
          @endif
          <a href="{{ route('account.dashboard') }}" aria-label="My Account" id="eq-btn-account" class="eq-btn-account-link" title="{{ Auth::user()->name }} (My Account)">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span class="eq-navbar-user-name">
              {{ explode(' ', Auth::user()->name ?? 'Earthquick')[0] }}
            </span>
          </a>
          <form method="POST" action="{{ route('logout') }}" class="eq-navbar-logout-form">
            @csrf
            <button type="submit" class="eq-navbar-logout-btn" title="Sign Out">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
            </button>
          </form>
        </div>
      @else
        <a href="{{ route('login') }}" aria-label="Customer Login" id="eq-btn-account" style="display: flex; align-items: center; justify-content: center; color: inherit; text-decoration: none;" title="Sign In">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
        </a>
      @endauth

      <!-- Shopping Bag / Cart Button -->
      <a href="{{ route('cart.index') }}" aria-label="Shopping Bag" id="eq-btn-cart" data-action="open-cart" style="display: flex; align-items: center; justify-content: center; color: inherit; text-decoration: none; position: relative;">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
          <path d="M3 6h18"></path>
          <path d="M16 10a4 4 0 0 1-8 0"></path>
        </svg>
        <span class="eq-cart-count" id="eq-cart-count">{{ count(session('cart', [])) }}</span>
      </a>
    </div>

    <!-- Mobile Hamburger Menu Toggle -->
    <button type="button" class="eq-navbar__toggle" id="eq-nav-toggle" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="eq-nav-links">
      <span></span><span></span><span></span>
    </button>
  </div>

  <!-- Mobile Drawer Backdrop Overlay -->
  <div class="eq-drawer-backdrop" id="eq-nav-backdrop"></div>
</header>

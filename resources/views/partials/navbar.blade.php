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
        <!-- 1. Shop Mega-Dropdown (Option A) -->
        <li class="eq-nav-item eq-nav-item--has-dropdown" id="nav-item-shop">
          <div class="eq-nav-link-wrapper">
            <a href="{{ route('shop.index') }}" id="nav-link-shop" class="{{ request()->is('shop*') ? 'is-active' : '' }}">Shop</a>
            <button type="button" class="eq-dropdown-toggle-btn" id="btn-toggle-shop-sub" aria-expanded="false" aria-label="Toggle Shop categories">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </button>
          </div>

          <!-- Shop Dropdown Mega-Panel -->
          <div class="eq-megamenu eq-megamenu--shop" id="megamenu-shop" role="region" aria-label="Shop Categories">
            <div class="eq-megamenu__grid eq-megamenu__grid--shop">
              <!-- Column 1: Women's Hub -->
              <div class="eq-megamenu__col eq-submenu-nested" id="submenu-women-hub">
                <div class="eq-nested-header">
                  <a href="{{ route('category.show', 'women') }}" class="eq-megamenu__heading eq-nested-title">Women</a>
                  <button type="button" class="eq-nested-toggle-btn" id="btn-toggle-women-nested" aria-expanded="false" aria-label="Toggle Women subcategories">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </button>
                </div>
                <ul class="eq-megamenu__list eq-nested-list" id="list-women-nested">
                  <li><a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'saree']) }}">Jamdani &amp; Sarees</a></li>
                  <li><a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'three-piece']) }}">Three Piece Sets</a></li>
                  <li><a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'two-piece']) }}">Two Piece Ensembles</a></li>
                  <li><a href="{{ route('subcategory.show', ['categorySlug' => 'women', 'subcategorySlug' => 'saree']) }}?fabric=Jamdani">Jamdani Weaves</a></li>
                  <li><a href="{{ route('category.show', 'women') }}" class="eq-megamenu__view-all">All Women &rarr;</a></li>
                </ul>
              </div>

              <!-- Column 2: Men, Kids & Accessories -->
              <div class="eq-megamenu__col eq-submenu-nested" id="submenu-men-kids-hub">
                <div class="eq-nested-header">
                  <a href="{{ route('category.show', 'men') }}" class="eq-megamenu__heading eq-nested-title">Men &amp; Lifestyle</a>
                  <button type="button" class="eq-nested-toggle-btn" id="btn-toggle-men-kids-nested" aria-expanded="false" aria-label="Toggle Men & Kids subcategories">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </button>
                </div>
                <ul class="eq-megamenu__list eq-nested-list" id="list-men-kids-nested">
                  <li><a href="{{ route('category.show', 'men') }}" id="nav-link-men">Men's Panjabi &amp; Wear</a></li>
                  <li><a href="{{ route('category.show', 'kids') }}" id="nav-link-kids">Kids Collection</a></li>
                  <li><a href="{{ route('category.show', 'ornaments') }}" id="nav-link-ornaments">Artisanal Ornaments</a></li>
                  <li><a href="{{ route('category.show', 'bags') }}" id="nav-link-bags">Bags &amp; Leathercraft</a></li>
                </ul>
              </div>

              <!-- Column 3: Home Decor & Tech -->
              <div class="eq-megamenu__col eq-submenu-nested" id="submenu-home-tech-hub">
                <div class="eq-nested-header">
                  <a href="{{ route('category.show', 'home-decor') }}" class="eq-megamenu__heading eq-nested-title">Home &amp; Modern</a>
                  <button type="button" class="eq-nested-toggle-btn" id="btn-toggle-home-tech-nested" aria-expanded="false" aria-label="Toggle Home & Tech subcategories">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </button>
                </div>
                <ul class="eq-megamenu__list eq-nested-list" id="list-home-tech-nested">
                  <li><a href="{{ route('subcategory.show', ['categorySlug' => 'home-decor', 'subcategorySlug' => 'kantha']) }}">Nakshi Kantha</a></li>
                  <li><a href="{{ route('subcategory.show', ['categorySlug' => 'home-decor', 'subcategorySlug' => 'bedsheet']) }}">Bedsheet Sets</a></li>
                  <li><a href="{{ route('category.show', 'home-decor') }}?sub=cushion-cover">Cushion Covers</a></li>
                  <li><a href="{{ route('stores.show', 'bright') }}">Bright Electronics <span style="font-size: 0.68rem; padding: 2px 6px; background: rgba(201,150,47,0.12); color: var(--eq-gold-dark); border-radius: 4px; font-weight: 600; margin-left: 4px;">Soon</span></a></li>
                  <li><a href="{{ route('category.show', 'home-decor') }}" class="eq-megamenu__view-all">All Home Sanctuary &rarr;</a></li>
                </ul>
              </div>

              <!-- Column 4: Marketplace Spotlight Card -->
              <div class="eq-megamenu__feature">
                <a href="{{ route('category.show', 'women') }}" class="eq-megamenu__feature-card">
                  <div class="eq-megamenu__feature-media">
                    <img src="{{ asset('images/hero/hero-main-saree-2.jpg') }}" alt="Curated Marketplace Catalog" />
                  </div>
                  <div class="eq-megamenu__feature-content">
                    <span class="eq-megamenu__tag">EARTHQUICK CURATED</span>
                    <strong class="eq-megamenu__feature-title">Heritage to Modern</strong>
                    <span class="eq-megamenu__feature-link">Explore Catalog &rarr;</span>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </li>

        <!-- 2. Stores Dropdown -->
        <li class="eq-nav-item eq-nav-item--has-dropdown" id="nav-item-stores">
          <div class="eq-nav-link-wrapper">
            <a href="{{ route('stores.index') }}" id="nav-link-stores" class="{{ request()->is('stores*') ? 'is-active' : '' }}">Stores</a>
            <button type="button" class="eq-dropdown-toggle-btn" id="btn-toggle-stores-sub" aria-expanded="false" aria-label="Toggle Stores list">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </button>
          </div>

          <!-- Stores Dropdown Panel -->
          <div class="eq-megamenu eq-megamenu--stores" id="megamenu-stores" role="region" aria-label="Brand Stores">
            <div class="eq-megamenu__grid eq-megamenu__grid--stores">
              <div class="eq-megamenu__col">
                <div class="eq-megamenu__heading">Brand Partners &amp; Ateliers</div>
                <ul class="eq-megamenu__list">
                  @php
                    try {
                      $navVendors = \App\Models\Vendor::where('is_active', true)->orderBy('display_order')->orderBy('name')->get();
                    } catch (\Throwable $e) {
                      $navVendors = collect();
                    }
                  @endphp
                  @forelse($navVendors as $nv)
                    <li>
                      <a href="{{ route('stores.show', $nv->slug) }}" style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                        <span>{{ $nv->name }}</span>
                        <span style="font-size: 0.72rem; color: var(--eq-charcoal-soft); font-weight: 500;">{{ $nv->vendor_code }}</span>
                      </a>
                    </li>
                  @empty
                    <li><a href="{{ route('stores.show', 'nous-telos') }}">Nous Telos</a></li>
                    <li><a href="{{ route('stores.show', 'bright') }}">Bright</a></li>
                  @endforelse
                  <li style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--eq-line);">
                    <a href="{{ route('stores.index') }}" class="eq-megamenu__view-all" style="font-weight: 600; color: var(--eq-gold-dark);">All Brand Stores &rarr;</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </li>

        <!-- 3. About Us -->
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
          <p class="eq-drawer-tagline">Independent brands, one marketplace</p>
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

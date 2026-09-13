/* =====================================================================
   EARTHQUICK — CORE JAVASCRIPT
   Structured modular script powering site interactions, navigation,
   carousels, accessibility, and interactive feedback.

   MODULE SEQUENCE:
   1. TOAST NOTIFICATION SYSTEM   -> Non-blocking visual feedback for actions
   2. NAVBAR & MOBILE MENU        -> Sticky shadow, mobile drawer toggle
   3. SEARCH MODAL DIALOG         -> Search overlay toggle & input handling
   4. SHOPPING CART & QUICK ADD   -> In-memory cart counter with badge updates
   5. ACCOUNT ACTIONS             -> Account drawer / login feedback
   6. PRODUCT CAROUSELS           -> Arrow buttons + touch swipe navigation
   7. SCROLL REVEAL ANIMATIONS    -> IntersectionObserver viewport entrance
   8. NEWSLETTER SUBSCRIPTION     -> Form validation and submission feedback
   9. INITIALIZATION BOOTSTRAP    -> DOMContentLoaded event listener
   ===================================================================== */


/* =====================================================================
   1. TOAST NOTIFICATION SYSTEM
   Provides non-intrusive, styled alerts matching Earthquick brand tokens.
   Replaces window.alert() which is blocked in sandboxed iframes.
   ===================================================================== */
const Toast = {
  container: null,

  init() {
    if (!this.container) {
      this.container = document.querySelector("#eq-toast-container");
      if (!this.container) {
        this.container = document.createElement("div");
        this.container.id = "eq-toast-container";
        this.container.className = "eq-toast-container";
        this.container.setAttribute("role", "status");
        this.container.setAttribute("aria-live", "polite");
        document.body.appendChild(this.container);
      }
    }
  },

  show(message, duration = 3200) {
    this.init();

    const toast = document.createElement("div");
    toast.className = "eq-toast";
    toast.innerHTML = `
      <svg class="eq-toast__icon" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
      <span>${message}</span>
    `;

    this.container.appendChild(toast);

    // Trigger enter transition
    requestAnimationFrame(() => {
      toast.classList.add("is-show");
    });

    // Auto-dismiss
    setTimeout(() => {
      toast.classList.remove("is-show");
      toast.addEventListener("transitionend", () => {
        toast.remove();
      });
    }, duration);
  }
};


/* =====================================================================
   2. NAVBAR & MOBILE MENU
   Handles sticky scroll elevation and mobile navigation drawer.
   ===================================================================== */
function initNavbar() {
  const navbar = document.querySelector("#eq-main-navbar") || document.querySelector(".eq-navbar");
  if (!navbar) return;

  // Prevent duplicate initialization on the same DOM element
  if (navbar.dataset.eqNavbarInitialized === "true") {
    return;
  }
  navbar.dataset.eqNavbarInitialized = "true";

  const toggle = document.querySelector("#eq-nav-toggle") || document.querySelector(".eq-navbar__toggle");
  const links = document.querySelector("#eq-nav-links") || document.querySelector(".eq-navbar__links");

  // Add elevation shadow when scrolled
  const onScroll = () => {
    navbar.classList.toggle("is-scrolled", window.scrollY > 15);
  };
  onScroll();
  window.addEventListener("scroll", onScroll, { passive: true });

  let backdrop = document.getElementById("eq-nav-backdrop") || navbar.querySelector(".eq-drawer-backdrop");
  if (backdrop && backdrop.parentElement !== document.body) {
    document.body.appendChild(backdrop);
  }
  const drawerCloseBtn = document.getElementById("eq-drawer-close") || navbar.querySelector(".eq-drawer-close-btn");
  let mobileNavLastFocused = null;

  const openMobileNav = () => {
    if (!links || !toggle) return;
    mobileNavLastFocused = document.activeElement;
    links.classList.add("is-open");
    toggle.classList.add("is-open");
    toggle.setAttribute("aria-expanded", "true");
    navbar.classList.add("has-drawer-open");
    if (backdrop) {
      backdrop.classList.add("is-active");
    }
    document.documentElement.classList.add("eq-drawer-open");
    document.body.classList.add("eq-drawer-open");
    if (drawerCloseBtn) {
      setTimeout(() => drawerCloseBtn.focus(), 100);
    }
  };

  const closeMobileNav = () => {
    if (!links || !toggle) return;
    links.classList.remove("is-open");
    toggle.classList.remove("is-open");
    toggle.setAttribute("aria-expanded", "false");
    navbar.classList.remove("has-drawer-open");
    if (backdrop) {
      backdrop.classList.remove("is-active");
    }
    document.documentElement.classList.remove("eq-drawer-open");
    document.body.classList.remove("eq-drawer-open");
    if (mobileNavLastFocused && typeof mobileNavLastFocused.focus === "function") {
      mobileNavLastFocused.focus();
    }
  };

  const toggleMobileNav = (e) => {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }
    if (!links || !toggle) return;
    const isOpen = links.classList.contains("is-open");
    if (isOpen) {
      closeMobileNav();
    } else {
      openMobileNav();
    }
  };

  // Mobile toggle behavior
  if (toggle) {
    toggle.addEventListener("click", toggleMobileNav);
  }

  // Drawer close button inside drawer header
  if (drawerCloseBtn) {
    drawerCloseBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      closeMobileNav();
    });
  }

  // Drawer search button inside footer
  const drawerSearchBtn = document.getElementById("drawer-btn-search");
  if (drawerSearchBtn) {
    drawerSearchBtn.addEventListener("click", () => {
      closeMobileNav();
    });
  }

  // Close and block touch when clicking outside on backdrop
  if (backdrop) {
    const handleBackdropDismiss = (e) => {
      e.preventDefault();
      e.stopPropagation();
      closeMobileNav();
    };
    backdrop.addEventListener("click", handleBackdropDismiss);
    backdrop.addEventListener("touchstart", handleBackdropDismiss, { passive: false });
    backdrop.addEventListener("touchmove", (e) => {
      e.preventDefault();
      e.stopPropagation();
    }, { passive: false });
  }

  // Intercept touchmove on background when drawer is open to prevent page scrolling behind drawer
  document.addEventListener("touchmove", (e) => {
    if (!document.body.classList.contains("eq-drawer-open")) return;
    // Allow touch scrolling ONLY inside the drawer links container
    if (links && !links.contains(e.target)) {
      e.preventDefault();
    }
  }, { passive: false });

  // Handle window resize: auto-close mobile drawer if resized to desktop
  window.addEventListener("resize", () => {
    if (window.innerWidth > 768 && links && links.classList.contains("is-open")) {
      closeMobileNav();
    }
  });

  // Close menu when clicking navigation links, or toggle subcategories when tapping category parent header on mobile
  if (links) {
    links.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", (e) => {
        const parentDropdown = link.closest(".eq-nav-item--has-dropdown");
        // If clicking category header ("Women" or "Home Decor") on mobile, toggle subcategory accordion
        if (parentDropdown && link.parentElement && link.parentElement.classList.contains("eq-nav-link-wrapper") && window.innerWidth <= 768) {
          e.preventDefault();
          e.stopPropagation();
          const isExpanded = parentDropdown.classList.toggle("is-expanded");
          const toggleBtn = parentDropdown.querySelector(".eq-dropdown-toggle-btn");
          if (toggleBtn) {
            toggleBtn.setAttribute("aria-expanded", String(isExpanded));
          }
          return;
        }
        closeMobileNav();
      });
    });
  }

  // Close on Escape key press
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && links && links.classList.contains("is-open")) {
      closeMobileNav();
    }
  });

  // Global click outside drawer check
  document.addEventListener("click", (e) => {
    if (!links || !toggle || !links.classList.contains("is-open")) return;
    if (!links.contains(e.target) && !toggle.contains(e.target)) {
      closeMobileNav();
    }
  });

  // Dropdown toggle chevron handling
  const dropdownToggles = navbar.querySelectorAll(".eq-dropdown-toggle-btn");
  dropdownToggles.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      const parentDropdown = btn.closest(".eq-nav-item--has-dropdown");
      if (parentDropdown) {
        const isExpanded = parentDropdown.classList.toggle("is-expanded");
        btn.setAttribute("aria-expanded", String(isExpanded));
      }
    });
  });

  // Nested subcategory toggle chevron handling (Women > Saree > Subcategories)
  const nestedToggles = navbar.querySelectorAll(".eq-nested-toggle-btn");
  nestedToggles.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      const parentNested = btn.closest(".eq-submenu-nested");
      if (parentNested) {
        const isExpanded = parentNested.classList.toggle("is-expanded");
        btn.setAttribute("aria-expanded", String(isExpanded));
      }
    });
  });
}


/* =====================================================================
   3. SEARCH MODAL DIALOG & LIVE INSTANT SEARCH
   Allows quick search access from the navbar search icon, debounced
   instant suggestions via AJAX, and full-catalog redirection.
   ===================================================================== */
function initSearchModal() {
  const openButtons = document.querySelectorAll('[data-action="open-search"]');
  const modal = document.querySelector("#eq-search-modal");
  const closeButton = document.querySelector("#eq-search-close");
  const searchInput = document.querySelector("#eq-search-input");
  const searchForm = document.querySelector("#eq-search-form");
  const liveDropdown = document.querySelector("#eq-search-live-dropdown");
  const popularTags = document.querySelector("#eq-search-popular-tags");
  let searchLastFocused = null;
  let debounceTimer = null;
  let currentAbortController = null;

  if (!modal) return;

  const openSearch = () => {
    searchLastFocused = document.activeElement;
    modal.classList.add("is-open");
    modal.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
    if (searchInput) {
      setTimeout(() => searchInput.focus(), 150);
    }
  };

  const closeSearch = () => {
    modal.classList.remove("is-open");
    modal.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
    if (searchLastFocused && typeof searchLastFocused.focus === "function") {
      searchLastFocused.focus();
    }
  };

  openButtons.forEach((btn) => btn.addEventListener("click", openSearch));
  if (closeButton) closeButton.addEventListener("click", closeSearch);

  // Close when clicking modal backdrop
  modal.addEventListener("click", (e) => {
    if (e.target === modal) closeSearch();
  });

  // Close on Escape key press
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal.classList.contains("is-open")) {
      closeSearch();
    }
  });

  // Helper function to safely escape HTML
  const escapeHtml = (str) => {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  };

  // Fetch Live Instant Suggestions
  const fetchSuggestions = (query) => {
    if (!query || query.length < 2) {
      if (liveDropdown) {
        liveDropdown.style.display = "none";
        liveDropdown.innerHTML = "";
      }
      if (popularTags) popularTags.style.display = "";
      return;
    }

    if (currentAbortController) {
      currentAbortController.abort();
    }
    currentAbortController = new AbortController();

    if (liveDropdown) {
      liveDropdown.style.display = "block";
      liveDropdown.innerHTML = `
        <div class="eq-search-loading">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" class="eq-spin">
            <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
            <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
          </svg>
          <span>Searching handcrafted atelier pieces...</span>
        </div>
      `;
    }
    if (popularTags) popularTags.style.display = "none";

    fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`, {
      signal: currentAbortController.signal,
      headers: {
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest"
      }
    })
      .then((res) => res.json())
      .then((data) => {
        if (!liveDropdown) return;

        const items = data.suggestions || [];
        if (items.length > 0) {
          let html = '';
          items.forEach((item) => {
            html += `
              <a href="${item.url}" class="eq-search-item">
                <img src="${item.image}" alt="${escapeHtml(item.name)}" class="eq-search-item__thumb" />
                <div class="eq-search-item__info">
                  <span class="eq-search-item__category">${escapeHtml(item.category_name)}</span>
                  <span class="eq-search-item__title">${escapeHtml(item.name)}</span>
                </div>
                <span class="eq-search-item__price">${item.formatted_price || ('৳' + item.price)}</span>
              </a>
            `;
          });

          html += `
            <a href="/search?q=${encodeURIComponent(query)}" class="eq-search-view-all-btn">
              View all results for &ldquo;${escapeHtml(query)}&rdquo; (${data.count} items) &rarr;
            </a>
          `;
          liveDropdown.innerHTML = html;
        } else {
          liveDropdown.innerHTML = `
            <div class="eq-search-no-results">
              No creations found for &ldquo;<strong>${escapeHtml(query)}</strong>&rdquo;.
              <div style="margin-top: 0.65rem;">
                <a href="/search?q=${encodeURIComponent(query)}" class="eq-search-view-all-btn">
                  Explore full catalog for &ldquo;${escapeHtml(query)}&rdquo; &rarr;
                </a>
              </div>
            </div>
          `;
        }
      })
      .catch((err) => {
        if (err.name === 'AbortError') return;
        if (liveDropdown) {
          liveDropdown.innerHTML = `
            <div class="eq-search-no-results">
              <a href="/search?q=${encodeURIComponent(query)}" class="eq-search-view-all-btn">
                Search &ldquo;${escapeHtml(query)}&rdquo; in catalog &rarr;
              </a>
            </div>
          `;
        }
      });
  };

  // Debounced input handler (300ms)
  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      const val = e.target.value.trim();
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        fetchSuggestions(val);
      }, 300);
    });

    // Handle Form Submit / Enter Key
    if (searchForm) {
      searchForm.addEventListener("submit", (e) => {
        const query = searchInput.value.trim();
        if (!query) {
          e.preventDefault();
        }
      });
    } else {
      searchInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
          e.preventDefault();
          const query = searchInput.value.trim();
          if (query) {
            window.location.href = `/search?q=${encodeURIComponent(query)}`;
          }
        }
      });
    }
  }
}


/* =====================================================================
   4. SHOPPING CART & SLIDE-IN DRAWER
   Manages client-side cart storage, badge updates, quick-add actions,
   and dynamic interactive slide-in cart drawer.
   ===================================================================== */
const EarthquickCart = {
  drawerEl: null,
  backdropEl: null,
  freeShippingThreshold: 3000,
  items: [],
  subtotal: 0,
  count: 0,

  getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute("content") : "";
  },

  formatMoney(num) {
    return "৳" + Number(num).toLocaleString("en-IN");
  },

  updateBadges() {
    const count = this.count;
    document.querySelectorAll("#eq-cart-count, .eq-cart-count").forEach((badge) => {
      badge.textContent = String(count);
      badge.style.display = count > 0 ? "flex" : "none";
    });
  },

  async fetchCart() {
    try {
      const res = await fetch("/cart", {
        headers: {
          "Accept": "application/json",
          "X-Requested-With": "XMLHttpRequest"
        }
      });
      if (res.ok) {
        const data = await res.json();
        this.items = data.items || [];
        this.count = data.count || 0;
        this.subtotal = data.subtotal || 0;
        this.updateBadges();
        this.renderDrawer();
      }
    } catch (e) {
      console.warn("EarthquickCart fetch error:", e);
    }
  },

  async addItem(productOrId, qty = 1, size = "Standard") {
    let productId = null;

    if (typeof productOrId === "object" && productOrId !== null) {
      productId = productOrId.product_id || productOrId.id;
      if (productOrId.size) size = productOrId.size;
      if (productOrId.qty) qty = productOrId.qty;
    } else {
      productId = productOrId;
    }

    try {
      const res = await fetch("/cart/add", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": this.getCsrfToken(),
          "X-Requested-With": "XMLHttpRequest"
        },
        body: JSON.stringify({
          product_id: productId,
          quantity: qty,
          size: size
        })
      });

      const data = await res.json();
      if (data.success) {
        this.items = data.items || [];
        this.count = data.count || 0;
        this.subtotal = data.subtotal || 0;
        this.updateBadges();
        this.renderDrawer();
        this.openDrawer();
        Toast.show(data.message || "Item added to your bag.");
      } else {
        Toast.show(data.message || "Could not add item to bag.");
      }
    } catch (e) {
      console.error("Cart add error:", e);
      Toast.show("Error connecting to server. Please try again.");
    }
  },

  async updateQty(key, delta) {
    try {
      const res = await fetch("/cart/update", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": this.getCsrfToken(),
          "X-Requested-With": "XMLHttpRequest"
        },
        body: JSON.stringify({
          key: key,
          delta: delta
        })
      });

      const data = await res.json();
      if (data.success) {
        this.items = data.items || [];
        this.count = data.count || 0;
        this.subtotal = data.subtotal || 0;
        this.updateBadges();
        this.renderDrawer();
      }
    } catch (e) {
      console.error("Cart update error:", e);
    }
  },

  async removeItem(key) {
    try {
      const res = await fetch("/cart/remove", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": this.getCsrfToken(),
          "X-Requested-With": "XMLHttpRequest"
        },
        body: JSON.stringify({
          key: key
        })
      });

      const data = await res.json();
      if (data.success) {
        this.items = data.items || [];
        this.count = data.count || 0;
        this.subtotal = data.subtotal || 0;
        this.updateBadges();
        this.renderDrawer();
        Toast.show(data.message || "Item removed from bag.");
      }
    } catch (e) {
      console.error("Cart remove error:", e);
    }
  },

  async clearCart() {
    try {
      const res = await fetch("/cart/clear", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": this.getCsrfToken(),
          "X-Requested-With": "XMLHttpRequest"
        }
      });
      const data = await res.json();
      this.items = [];
      this.count = 0;
      this.subtotal = 0;
      this.updateBadges();
      this.renderDrawer();
    } catch (e) {
      console.error("Cart clear error:", e);
    }
  },

  buildDrawerDOM() {
    if (document.querySelector("#eq-cart-drawer")) {
      this.drawerEl = document.querySelector("#eq-cart-drawer");
      this.backdropEl = document.querySelector("#eq-cart-backdrop");
      return;
    }

    // Backdrop
    const backdrop = document.createElement("div");
    backdrop.id = "eq-cart-backdrop";
    backdrop.className = "eq-cart-backdrop";
    document.body.appendChild(backdrop);
    this.backdropEl = backdrop;

    // Drawer container
    const drawer = document.createElement("aside");
    drawer.id = "eq-cart-drawer";
    drawer.className = "eq-cart-drawer";
    drawer.setAttribute("role", "dialog");
    drawer.setAttribute("aria-modal", "true");
    drawer.setAttribute("aria-hidden", "true");
    drawer.setAttribute("aria-label", "Shopping Bag");
    drawer.innerHTML = `
      <div class="eq-cart-drawer__header">
        <h2 class="eq-cart-drawer__title">
          Shopping Bag
          <span class="eq-cart-drawer__count-badge" id="eq-drawer-count">0</span>
        </h2>
        <button type="button" class="eq-cart-drawer__close" id="eq-btn-close-cart" aria-label="Close Shopping Bag">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="eq-cart-drawer__shipping-bar" id="eq-shipping-bar">
        <div class="eq-cart-drawer__shipping-text" id="eq-shipping-text">
          Free delivery unlocked on orders over ৳3,000
        </div>
        <div class="eq-cart-drawer__progress-track">
          <div class="eq-cart-drawer__progress-fill" id="eq-shipping-fill" style="width: 0%;"></div>
        </div>
      </div>

      <div class="eq-cart-drawer__body" id="eq-cart-items-container">
        <!-- Items dynamically injected -->
      </div>

      <div class="eq-cart-drawer__footer">
        <div class="eq-cart-drawer__subtotal-row">
          <span class="eq-cart-drawer__subtotal-label">Subtotal</span>
          <span class="eq-cart-drawer__subtotal-amount" id="eq-cart-subtotal">৳0</span>
        </div>
        <p class="eq-cart-drawer__note">Delivery fee and taxes calculated at checkout</p>
        <button type="button" class="eq-cart-drawer__btn-checkout" id="eq-btn-checkout">
          Proceed to Checkout &rarr;
        </button>
        <button type="button" class="eq-cart-drawer__btn-continue" id="eq-btn-continue-shopping">
          Continue Shopping
        </button>
      </div>
    `;

    document.body.appendChild(drawer);
    this.drawerEl = drawer;

    // Attach listener events
    backdrop.addEventListener("click", () => this.closeDrawer());
    drawer.querySelector("#eq-btn-close-cart").addEventListener("click", () => this.closeDrawer());
    drawer.querySelector("#eq-btn-continue-shopping").addEventListener("click", () => this.closeDrawer());

    drawer.querySelector("#eq-btn-checkout").addEventListener("click", () => {
      if (this.items.length === 0) {
        Toast.show("Your bag is empty! Add products first.");
        return;
      }
      Toast.show("Directing to secure checkout...");
      setTimeout(() => {
        window.location.href = "/checkout";
      }, 250);
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && this.drawerEl.classList.contains("is-open")) {
        this.closeDrawer();
      }
    });
  },

  renderDrawer() {
    this.buildDrawerDOM();
    const items = this.items;
    const count = this.count;
    const subtotal = this.subtotal;

    // Update count badge
    const countBadge = this.drawerEl.querySelector("#eq-drawer-count");
    if (countBadge) countBadge.textContent = String(count);

    // Update Subtotal
    const subtotalEl = this.drawerEl.querySelector("#eq-cart-subtotal");
    if (subtotalEl) subtotalEl.textContent = this.formatMoney(subtotal);

    // Update Shipping Bar
    const shippingText = this.drawerEl.querySelector("#eq-shipping-text");
    const shippingFill = this.drawerEl.querySelector("#eq-shipping-fill");
    if (shippingText && shippingFill) {
      if (subtotal >= this.freeShippingThreshold && subtotal > 0) {
        shippingText.innerHTML = `<strong>Congratulations!</strong> You have unlocked Free Delivery.`;
        shippingFill.style.width = "100%";
      } else {
        const remaining = Math.max(0, this.freeShippingThreshold - subtotal);
        const pct = Math.min(100, Math.round((subtotal / this.freeShippingThreshold) * 100));
        shippingText.innerHTML = `Add <strong>${this.formatMoney(remaining)}</strong> more to unlock <strong>Free Delivery</strong>`;
        shippingFill.style.width = `${pct}%`;
      }
    }

    // Body items container
    const container = this.drawerEl.querySelector("#eq-cart-items-container");
    if (!container) return;

    if (!items || items.length === 0) {
      container.innerHTML = `
        <div class="eq-cart-empty">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <path d="M16 10a4 4 0 0 1-8 0"></path>
          </svg>
          <div class="eq-cart-empty__title">Your bag is empty</div>
          <p class="eq-cart-empty__desc">Explore our handpicked collection and discover pieces crafted for you.</p>
        </div>
      `;
      return;
    }

    container.innerHTML = items
      .map((item) => {
        let imgSrc = item.image || "images/hero/hero-main.jpg";
        if (!imgSrc.startsWith("/") && !imgSrc.startsWith("http")) {
          imgSrc = "/" + imgSrc;
        }

        const safeKey = item.key || (item.id + "_" + (item.size || "Standard"));

        return `
          <div class="eq-cart-item" data-key="${safeKey}">
            <img class="eq-cart-item__image" src="${imgSrc}" alt="${item.name}" onerror="this.src='/images/hero/hero-main.jpg'" />
            <div class="eq-cart-item__info">
              <div>
                <h4 class="eq-cart-item__title">${item.name}</h4>
                <div class="eq-cart-item__variant">${item.size || "Standard"}</div>
                <div class="eq-cart-item__price">${this.formatMoney(item.price)}</div>
              </div>
              <div class="eq-cart-item__bottom">
                <div class="eq-cart-item__qty">
                  <button type="button" class="eq-cart-item__qty-btn" onclick="EarthquickCart.updateQty('${safeKey}', -1)" aria-label="Decrease quantity">&minus;</button>
                  <span class="eq-cart-item__qty-val">${item.quantity}</span>
                  <button type="button" class="eq-cart-item__qty-btn" onclick="EarthquickCart.updateQty('${safeKey}', 1)" aria-label="Increase quantity">&plus;</button>
                </div>
                <button type="button" class="eq-cart-item__remove" onclick="EarthquickCart.removeItem('${safeKey}')" aria-label="Remove item">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        `;
      })
      .join("");
  },

  openDrawer() {
    this.lastActiveElement = document.activeElement;
    this.buildDrawerDOM();
    this.renderDrawer();
    this.drawerEl.classList.add("is-open");
    this.drawerEl.setAttribute("aria-hidden", "false");
    this.backdropEl.classList.add("is-open");
    document.body.style.overflow = "hidden";
    const closeBtn = this.drawerEl.querySelector("#eq-btn-close-cart");
    if (closeBtn) {
      setTimeout(() => closeBtn.focus(), 120);
    }
  },

  closeDrawer() {
    if (this.drawerEl) {
      this.drawerEl.classList.remove("is-open");
      this.drawerEl.setAttribute("aria-hidden", "true");
    }
    if (this.backdropEl) this.backdropEl.classList.remove("is-open");
    document.body.style.overflow = "";
    if (this.lastActiveElement && typeof this.lastActiveElement.focus === "function") {
      this.lastActiveElement.focus();
    }
  },

  init() {
    this.buildDrawerDOM();
    this.fetchCart();

    // Attach to any open-cart buttons
    document.querySelectorAll('[data-action="open-cart"], #eq-btn-cart').forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        this.openDrawer();
      });
    });

    // Product detail page AJAX intercept
    const productForm = document.querySelector("#product-actions-form");
    if (productForm) {
      productForm.addEventListener("submit", (e) => {
        if (productForm.querySelector('input[name="buy_now"]')) {
          return; // Let standard form submission proceed to checkout
        }
        e.preventDefault();
        const prodIdInput = productForm.querySelector('input[name="product_id"]');
        const sizeInput = productForm.querySelector('input[name="size"]');
        const qtyInput = productForm.querySelector('input[name="quantity"]');
        const prodId = prodIdInput ? prodIdInput.value : null;
        const size = sizeInput ? sizeInput.value : "Standard";
        const qty = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;

        if (prodId) {
          EarthquickCart.addItem(prodId, qty, size);
        } else {
          productForm.submit();
        }
      });
    }

    // Quick add handlers on product cards
    document.querySelectorAll(".eq-product-card__quick-add").forEach((btn) => {
      btn.addEventListener("click", (event) => {
        const card = btn.closest(".eq-product-card") || btn.closest(".eq-saree-masterpiece");
        const prodId = card ? (card.dataset.id || card.id?.replace("card-", "")) : null;

        if (prodId && !isNaN(parseInt(prodId, 10))) {
          event.preventDefault();
          event.stopPropagation();
          EarthquickCart.addItem(parseInt(prodId, 10), 1, "Standard");
        }
      });
    });
  }
};

// Global expose
window.EarthquickCart = EarthquickCart;

function initCart() {
  EarthquickCart.init();
}


/* =====================================================================
   5. ACCOUNT ACTIONS
   Handles navigation to Customer Account & Orders portal.
   ===================================================================== */
function initAccount() {
  // Allow native navigation to Laravel routes (/account or /login)
  document.addEventListener("click", (e) => {
    const accountButton = e.target.closest('[data-action="open-account"], #eq-btn-account');
    if (!accountButton) return;
    
    const href = accountButton.getAttribute("href");
    if (href && href !== "#" && !href.startsWith("javascript:")) {
      return; // Allow browser to follow Laravel route
    }
  });
}


/* =====================================================================
   6. PRODUCT CAROUSELS
   Handles New Arrivals and Three Piece carousels with arrow controls,
   resize re-calculations, and touch swipe gestures.
   ===================================================================== */
function initCarousels() {
  document.querySelectorAll(".eq-carousel").forEach((carousel) => {
    const viewport = carousel.querySelector(".eq-carousel__viewport");
    const track = carousel.querySelector(".eq-carousel__track");
    if (!viewport || !track) return;

    const scope = carousel.closest(".eq-section") || carousel;
    const prevBtn = scope.querySelector('[data-action="prev"]');
    const nextBtn = scope.querySelector('[data-action="next"]');

    let position = 0;

    // Calculate dynamic step per card based on card width + gap
    function cardStep() {
      const card = track.querySelector(".eq-product-card");
      if (!card) return 300;
      const style = window.getComputedStyle(track);
      const gap = parseFloat(style.columnGap || style.gap || "0");
      return card.getBoundingClientRect().width + gap;
    }

    // Maximum scroll boundary
    function maxScroll() {
      return Math.max(0, track.scrollWidth - viewport.clientWidth);
    }

    // Update track position and toggle arrow disabled state
    function update() {
      const max = maxScroll();
      position = Math.min(Math.max(position, 0), max);
      track.style.transform = `translateX(-${position}px)`;

      if (prevBtn) prevBtn.disabled = position <= 0;
      if (nextBtn) nextBtn.disabled = position >= max - 2;
    }

    // Arrow button controls
    if (prevBtn) {
      prevBtn.addEventListener("click", () => {
        position -= cardStep();
        update();
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener("click", () => {
        position += cardStep();
        update();
      });
    }

    // Touch Swipe Support for Mobile & Tablet
    let startX = 0;
    let currentX = 0;
    let isSwiping = false;

    viewport.addEventListener(
      "touchstart",
      (e) => {
        if (!e.touches[0]) return;
        startX = e.touches[0].clientX;
        currentX = startX;
        isSwiping = true;
      },
      { passive: true }
    );

    viewport.addEventListener(
      "touchmove",
      (e) => {
        if (!isSwiping || !e.touches[0]) return;
        currentX = e.touches[0].clientX;
      },
      { passive: true }
    );

    viewport.addEventListener("touchend", () => {
      if (!isSwiping) return;
      isSwiping = false;
      const diffX = startX - currentX;
      const threshold = 40; // minimum swipe distance

      if (diffX > threshold) {
        // Swiped Left -> Move to next
        position += cardStep();
        update();
      } else if (diffX < -threshold) {
        // Swiped Right -> Move to previous
        position -= cardStep();
        update();
      }
    });

    // Re-evaluate on window resize & image load completion
    window.addEventListener("resize", update);
    window.addEventListener("load", update);
    update();
  });
}


/* =====================================================================
   7. SCROLL REVEAL ANIMATIONS
   Applies a subtle, one-time fade and rise to elements with .eq-reveal
   as they enter the user's viewport.
   ===================================================================== */
function initScrollReveal() {
  const items = document.querySelectorAll(".eq-reveal");
  if (!items.length) return;

  // Fail-safe: ensure all items become visible after 1.2s regardless of scroll state
  setTimeout(() => {
    items.forEach((el) => el.classList.add("is-visible"));
  }, 1200);

  if (!("IntersectionObserver" in window)) {
    items.forEach((el) => el.classList.add("is-visible"));
    return;
  }

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          obs.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.05, rootMargin: "0px 0px 120px 0px" }
  );

  items.forEach((el) => observer.observe(el));
}


/* =====================================================================
   8. NEWSLETTER SUBSCRIPTION
   Validates and simulates email signup for the Earthquick house drops.
   ===================================================================== */
function initNewsletterForm() {
  const form = document.querySelector("#eq-newsletter-form") || document.querySelector("[data-newsletter-form]");
  if (!form) return;

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const input = form.querySelector('input[type="email"]');
    const email = input ? input.value.trim() : "";

    if (email && email.includes("@")) {
      Toast.show("Welcome to the House of Earthquick. Thank you for subscribing!");
      form.reset();
    } else {
      Toast.show("Please enter a valid email address.");
    }
  });
}


/* =====================================================================
   9. HERO CAMPAIGN SLIDER
   Smooth crossfade slider with pause-on-hover, arrow keys, and dots.
   ===================================================================== */
function initHeroSlider() {
  const slider = document.querySelector("#hero-section");
  if (!slider) return;

  const slides = slider.querySelectorAll(".eq-hero__slide");
  const dots = slider.querySelectorAll(".eq-hero__dot");
  const prevBtn = slider.querySelector("#hero-prev");
  const nextBtn = slider.querySelector("#hero-next");

  if (!slides.length) return;

  let currentIndex = 0;
  let timer = null;
  const slideCount = slides.length;

  const goToSlide = (index) => {
    currentIndex = (index + slideCount) % slideCount;

    slides.forEach((slide, i) => {
      const isActive = i === currentIndex;
      slide.classList.toggle("is-active", isActive);
      slide.setAttribute("aria-hidden", String(!isActive));
    });

    dots.forEach((dot, i) => {
      const isActive = i === currentIndex;
      dot.classList.toggle("is-active", isActive);
      dot.setAttribute("aria-selected", String(isActive));
    });
  };

  const nextSlide = () => goToSlide(currentIndex + 1);
  const prevSlide = () => goToSlide(currentIndex - 1);

  const startAutoPlay = () => {
    stopAutoPlay();
    timer = setInterval(nextSlide, 6500);
  };

  const stopAutoPlay = () => {
    if (timer) {
      clearInterval(timer);
      timer = null;
    }
  };

  if (nextBtn) {
    nextBtn.addEventListener("click", () => {
      nextSlide();
      startAutoPlay();
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener("click", () => {
      prevSlide();
      startAutoPlay();
    });
  }

  dots.forEach((dot) => {
    dot.addEventListener("click", () => {
      const idx = parseInt(dot.getAttribute("data-index"), 10);
      if (!isNaN(idx)) {
        goToSlide(idx);
        startAutoPlay();
      }
    });
  });

  // Pause autoplay on mouse hover to allow patron reading
  slider.addEventListener("mouseenter", stopAutoPlay);
  slider.addEventListener("mouseleave", startAutoPlay);

  // Keyboard navigation when focused
  slider.addEventListener("keydown", (e) => {
    if (e.key === "ArrowLeft") {
      prevSlide();
      startAutoPlay();
    } else if (e.key === "ArrowRight") {
      nextSlide();
      startAutoPlay();
    }
  });

  goToSlide(0);
  startAutoPlay();
}


/* =====================================================================
   10. QUICK VIEW MODAL
   Allows patrons to inspect pieces, select sizing, and add to bag
   without interrupting page flow.
   ===================================================================== */
function initQuickViewModal() {
  const modal = document.querySelector("#eq-quickview-modal");
  const backdrop = document.querySelector("#eq-quickview-backdrop");
  const closeBtn = document.querySelector("#eq-quickview-close");
  const body = document.querySelector("#eq-quickview-body");
  let quickviewLastFocused = null;

  if (!modal || !body) return;

  const closeModal = () => {
    modal.classList.remove("is-open");
    modal.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
    if (quickviewLastFocused && typeof quickviewLastFocused.focus === "function") {
      quickviewLastFocused.focus();
    }
  };

  if (backdrop) backdrop.addEventListener("click", closeModal);
  if (closeBtn) closeBtn.addEventListener("click", closeModal);

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal.classList.contains("is-open")) {
      closeModal();
    }
  });

  // Global handler to open quick view modal with card data
  window.openQuickView = function(card) {
    if (!card) return;

    const name = card.querySelector(".eq-product-card__name, .eq-saree-masterpiece__name")?.textContent.trim() || "Earthquick Piece";
    const category = card.querySelector(".eq-product-card__category")?.textContent.trim() || "Artisanal Collection";
    const priceEl = card.querySelector(".eq-product-card__price, .eq-saree-masterpiece__price");
    const priceHtml = priceEl ? priceEl.innerHTML.trim() : "৳4,200";
    const imgEl = card.querySelector(".eq-product-card__frame img, .eq-saree-masterpiece__frame img");
    const imgSrc = imgEl ? imgEl.getAttribute("src") : "images/saree/saree-01.jpg";
    const linkEl = card.querySelector(".eq-product-card__link, .eq-saree-masterpiece__link");
    const linkHref = linkEl ? linkEl.getAttribute("href") : "pages/product.html";

    const isSaree = category.toLowerCase().includes("saree") || name.toLowerCase().includes("saree");

    body.innerHTML = `
      <div class="eq-quickview-layout">
        <div class="eq-quickview-gallery">
          <div class="eq-quickview-main-image">
            <img src="${imgSrc}" alt="${name}" />
          </div>
        </div>
        <div class="eq-quickview-details">
          <span class="eq-quickview-category">${category}</span>
          <h2 class="eq-quickview-title">${name}</h2>
          <div class="eq-quickview-price-row">
            <span class="eq-quickview-price">${priceHtml}</span>
          </div>
          <p class="eq-quickview-desc">
            Handcrafted with meticulous detail using heritage looms and artisanal dyes. Designed for longevity, effortless draping, and timeless occasion wear.
          </p>
          <div class="eq-quickview-spec-list">
            <div class="eq-quickview-spec-item">
              <span class="eq-quickview-spec-label">Origin:</span>
              <span>Tangail &amp; Narayanganj Handlooms</span>
            </div>
            <div class="eq-quickview-spec-item">
              <span class="eq-quickview-spec-label">Care:</span>
              <span>Dry Clean Recommended &bull; Azo-free Natural Dyes</span>
            </div>
            <div class="eq-quickview-spec-item">
              <span class="eq-quickview-spec-label">Sizing:</span>
              <span>${isSaree ? "5.5m Standard Drape with 0.8m Blouse Piece" : "Tailored Relaxed Silhouette"}</span>
            </div>
          </div>
          <div class="eq-quickview-actions">
            <button type="button" class="eq-btn eq-btn--primary eq-btn--pill" id="quickview-add-bag">
              Add to Bag
            </button>
            <a href="${linkHref}" class="eq-btn eq-btn--outline eq-btn--pill">
              View Full Details
            </a>
          </div>
        </div>
      </div>
    `;

    // Bind Add to Bag button inside modal
    const addBagBtn = body.querySelector("#quickview-add-bag");
    if (addBagBtn) {
      addBagBtn.addEventListener("click", () => {
        cartCount++;
        const countBadge = document.querySelector("#eq-cart-count") || document.querySelector(".eq-cart-count");
        if (countBadge) {
          countBadge.textContent = String(cartCount);
          countBadge.style.display = "flex";
        }
        Toast.show(`Added "${name}" to your shopping bag.`);
        closeModal();
      });
    }

    quickviewLastFocused = document.activeElement;
    modal.classList.add("is-open");
    modal.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
    if (closeBtn) {
      setTimeout(() => closeBtn.focus(), 120);
    }
  };
}


/* =====================================================================
   10B. 3D COVERFLOW CAROUSEL & EDITORIAL VISUAL DIARY
   Interactive 3D coverflow carousel with touch swipe, category pills,
   keyboard navigation, and responsive depth perspective.
   ===================================================================== */
function init3DCoverflow() {
  const stage = document.querySelector("#eq-coverflow-stage");
  const track = document.querySelector("#eq-coverflow-track");
  const cards = Array.from(document.querySelectorAll(".eq-coverflow-card"));
  const btnPrev = document.querySelector("#coverflow-btn-prev");
  const btnNext = document.querySelector("#coverflow-btn-next");
  const filterPills = document.querySelectorAll(".eq-coverflow-pill[data-coverflow-filter]");

  if (!stage || !track || cards.length === 0) return;

  let activeIndex = 0;
  const total = cards.length;

  function updateCoverflow() {
    const width = window.innerWidth;
    let spacing = 250;
    if (width < 480) {
      spacing = Math.min(width * 0.3, 110);
    } else if (width < 640) {
      spacing = 150;
    } else if (width < 991) {
      spacing = 200;
    } else if (width < 1200) {
      spacing = 230;
    }

    cards.forEach((card, index) => {
      // Calculate shortest directional offset
      let offset = index - activeIndex;
      while (offset > total / 2) offset -= total;
      while (offset < -total / 2) offset += total;

      if (offset === 0) {
        // Active Center Slide (Prominent, elevated, crisp shadow)
        card.style.transform = `translateX(0px) scale(1.05) rotateY(0deg)`;
        card.style.zIndex = "10";
        card.style.opacity = "1";
        card.style.filter = "none";
        card.style.boxShadow = "0 22px 48px -8px rgba(27, 58, 75, 0.28), 0 8px 20px -4px rgba(0, 0, 0, 0.15)";
        card.style.pointerEvents = "auto";
        card.classList.add("is-active");
        card.setAttribute("aria-hidden", "false");
      } else if (offset === -1) {
        // Immediate Left (Flanking behind center)
        card.style.transform = `translateX(-${spacing}px) scale(0.88) rotateY(7deg)`;
        card.style.zIndex = "6";
        card.style.opacity = "0.92";
        card.style.filter = "none";
        card.style.boxShadow = "0 12px 28px -6px rgba(0, 0, 0, 0.16)";
        card.style.pointerEvents = "auto";
        card.classList.remove("is-active");
        card.setAttribute("aria-hidden", "true");
      } else if (offset === 1) {
        // Immediate Right (Flanking behind center)
        card.style.transform = `translateX(${spacing}px) scale(0.88) rotateY(-7deg)`;
        card.style.zIndex = "6";
        card.style.opacity = "0.92";
        card.style.filter = "none";
        card.style.boxShadow = "0 12px 28px -6px rgba(0, 0, 0, 0.16)";
        card.style.pointerEvents = "auto";
        card.classList.remove("is-active");
        card.setAttribute("aria-hidden", "true");
      } else if (offset === -2) {
        // Outer Left (Peeking)
        const spacing2 = spacing * 1.8;
        card.style.transform = `translateX(-${spacing2}px) scale(0.74) rotateY(14deg)`;
        card.style.zIndex = "3";
        card.style.opacity = width < 480 ? "0" : "0.65";
        card.style.filter = "none";
        card.style.boxShadow = "none";
        card.style.pointerEvents = width < 480 ? "none" : "auto";
        card.classList.remove("is-active");
        card.setAttribute("aria-hidden", "true");
      } else if (offset === 2) {
        // Outer Right (Peeking)
        const spacing2 = spacing * 1.8;
        card.style.transform = `translateX(${spacing2}px) scale(0.74) rotateY(-14deg)`;
        card.style.zIndex = "3";
        card.style.opacity = width < 480 ? "0" : "0.65";
        card.style.filter = "none";
        card.style.boxShadow = "none";
        card.style.pointerEvents = width < 480 ? "none" : "auto";
        card.classList.remove("is-active");
        card.setAttribute("aria-hidden", "true");
      } else {
        // Hidden Off-Stage
        const dir = offset > 0 ? 1 : -1;
        card.style.transform = `translateX(${dir * spacing * 2.5}px) scale(0.6)`;
        card.style.zIndex = "1";
        card.style.opacity = "0";
        card.style.filter = "none";
        card.style.pointerEvents = "none";
        card.classList.remove("is-active");
        card.setAttribute("aria-hidden", "true");
      }
    });
  }

  function goToSlide(index) {
    activeIndex = (index % total + total) % total;
    updateCoverflow();
  }

  // Click on cards
  cards.forEach((card, i) => {
    card.addEventListener("click", (e) => {
      // If clicking button/link inside active card, allow normal navigation
      if (card.classList.contains("is-active")) {
        if (e.target.closest("a, button")) {
          return;
        }
      } else {
        e.preventDefault();
        goToSlide(i);
      }
    });
  });

  // Prev / Next button navigation
  if (btnPrev) {
    btnPrev.addEventListener("click", () => {
      goToSlide(activeIndex - 1);
    });
  }
  if (btnNext) {
    btnNext.addEventListener("click", () => {
      goToSlide(activeIndex + 1);
    });
  }

  // Filter Pills interaction
  if (filterPills.length > 0) {
    filterPills.forEach(pill => {
      pill.addEventListener("click", () => {
        filterPills.forEach(p => p.classList.remove("is-active"));
        pill.classList.add("is-active");

        const filter = pill.getAttribute("data-coverflow-filter");
        if (!filter || filter === "all") {
          goToSlide(0);
          return;
        }

        // Find the first slide matching this filter
        const targetIndex = cards.findIndex(c => {
          const cat = c.getAttribute("data-category") || "";
          return cat.toLowerCase().includes(filter.toLowerCase());
        });

        if (targetIndex !== -1) {
          goToSlide(targetIndex);
        }
      });
    });
  }

  // Touch Swipe & Drag Support
  let touchStartX = 0;
  let touchCurrentX = 0;
  let isSwiping = false;

  stage.addEventListener("touchstart", (e) => {
    if (e.touches.length === 1) {
      touchStartX = e.touches[0].clientX;
      touchCurrentX = touchStartX;
      isSwiping = true;
    }
  }, { passive: true });

  stage.addEventListener("touchmove", (e) => {
    if (isSwiping && e.touches.length === 1) {
      touchCurrentX = e.touches[0].clientX;
    }
  }, { passive: true });

  stage.addEventListener("touchend", () => {
    if (!isSwiping) return;
    isSwiping = false;
    const diff = touchCurrentX - touchStartX;
    if (diff < -40) {
      goToSlide(activeIndex + 1);
    } else if (diff > 40) {
      goToSlide(activeIndex - 1);
    }
  });

  // Mouse Drag Support
  let mouseStartX = 0;
  let mouseCurrentX = 0;
  let isMouseDown = false;

  stage.addEventListener("mousedown", (e) => {
    if (e.button !== 0) return;
    mouseStartX = e.clientX;
    mouseCurrentX = mouseStartX;
    isMouseDown = true;
  });

  window.addEventListener("mousemove", (e) => {
    if (isMouseDown) {
      mouseCurrentX = e.clientX;
    }
  });

  window.addEventListener("mouseup", () => {
    if (!isMouseDown) return;
    isMouseDown = false;
    const diff = mouseCurrentX - mouseStartX;
    if (diff < -50) {
      goToSlide(activeIndex + 1);
    } else if (diff > 50) {
      goToSlide(activeIndex - 1);
    }
  });

  // Keyboard Navigation
  stage.addEventListener("keydown", (e) => {
    if (e.key === "ArrowLeft") {
      e.preventDefault();
      goToSlide(activeIndex - 1);
    } else if (e.key === "ArrowRight") {
      e.preventDefault();
      goToSlide(activeIndex + 1);
    }
  });

  // Window Resize
  let resizeTimer;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(updateCoverflow, 100);
  });

  // Initial render
  updateCoverflow();
}


/* =====================================================================
   11. INITIALIZATION BOOTSTRAP & IN-PAGE ANCHOR SCROLL CONTROLLER
   Accurately handles all in-page jumps (Navbar, CTA, Footer, Drawer)
   with precise sticky header offset and breathing room.
   ===================================================================== */

/**
 * Accurately scrolls to a target section accounting for sticky navbar height and breathing space.
 * Guarantees zero header overlap on both desktop and mobile.
 */
function scrollToSection(target, behavior) {
  if (!target) return;
  const navbar = document.querySelector("#eq-main-navbar") || document.querySelector(".eq-navbar");
  const navHeight = navbar ? navbar.offsetHeight : (window.innerWidth <= 768 ? 60 : 70);
  const breathingSpace = window.innerWidth <= 768 ? 16 : 24;

  const elementPosition = target.getBoundingClientRect().top + window.pageYOffset;
  const offsetPosition = Math.max(0, elementPosition - navHeight - breathingSpace);

  window.scrollTo({
    top: offsetPosition,
    behavior: behavior || "smooth"
  });
}

// Universal click listener for all in-page anchor links (Navbar, Drawer, Hero CTA, Footer)
document.addEventListener("click", function (e) {
  const link = e.target.closest("a");
  if (!link) return;

  const href = link.getAttribute("href") || "";
  if (!href.includes("#")) return;

  const hashIndex = href.indexOf("#");
  const hash = href.substring(hashIndex);
  if (!hash || hash === "#") return;

  const path = href.substring(0, hashIndex);
  const isHome = window.location.pathname.endsWith("index.html") || 
                 window.location.pathname.endsWith("/") || 
                 window.location.pathname === "" || 
                 !window.location.pathname.includes("/pages/");

  // Verify if link targets an anchor on the current homepage
  const isCurrentPageAnchor = path === "" || path === "index.html" || path === "./index.html" || path === window.location.pathname;

  if (isHome && isCurrentPageAnchor) {
    const target = document.querySelector(hash);
    if (target) {
      e.preventDefault();

      // Close mobile navigation drawer if open
      const navLinks = document.querySelector("#eq-nav-links");
      const navToggle = document.querySelector("#eq-nav-toggle");
      if (navLinks && navLinks.classList.contains("is-open")) {
        navLinks.classList.remove("is-open");
        if (navToggle) {
          navToggle.classList.remove("is-open");
          navToggle.setAttribute("aria-expanded", "false");
        }
        document.body.style.overflow = "";
      }

      // Smooth scroll with precise offset & breathing room
      scrollToSection(target, "smooth");

      try {
        history.pushState(null, "", hash);
      } catch (err) {}
    }
  }
});

let hashNavHandled = false;

function handleHashNavigationOnLoad() {
  if (hashNavHandled || !window.location.hash) return;
  const hash = window.location.hash;
  const target = document.querySelector(hash);
  if (!target) return;

  hashNavHandled = true;

  // Prevent browser scroll restoration jump
  if ("scrollRestoration" in history) {
    history.scrollRestoration = "manual";
  }

  // Instant scroll on first load: eliminates awkward delayed millisecond jump
  scrollToSection(target, "auto");
}

function initEarthquickApp() {
  Toast.init();
  initNavbar();
  initHeroSlider();
  initSearchModal();
  initQuickViewModal();
  initCart();
  initAccount();
  initCarousels();
  init3DCoverflow();
  initScrollReveal();
  initNewsletterForm();
  handleHashNavigationOnLoad();
}

// Re-bind navbar, search, and cart if common layout components load dynamically
document.addEventListener("eq:components-loaded", () => {
  Toast.init();
  initNavbar();
  initSearchModal();
  if (window.EarthquickCart && typeof window.EarthquickCart.init === "function") {
    window.EarthquickCart.init();
  }
  handleHashNavigationOnLoad();
});

// Expose globally for layout loader
window.initNavbar = initNavbar;
window.initSearchModal = initSearchModal;
window.initEarthquickApp = initEarthquickApp;

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initEarthquickApp);
} else {
  initEarthquickApp();
}


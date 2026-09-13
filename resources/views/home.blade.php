<!DOCTYPE html>
<html lang="en">
<head>
  <!-- ===================================================================
       DOCUMENT METADATA & RESOURCE HINTS
       =================================================================== -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Earthquick — Crafted for the Modern You | Nous Telos</title>
  <meta name="description" content="Earthquick is a luxury fashion and lifestyle destination featuring authentic handloom Sarees, Three-piece ensembles, Two-piece co-ords, artisan bags, and home sanctuaries from Nous Telos." />
  <meta name="keywords" content="handloom saree, jamdani, tantuj, three piece, two piece, leather bags, nakshi kantha, nous telos, earthquick, bangladesh luxury fashion" />
  <link rel="canonical" href="{{ url('/') }}" />

  <!-- Open Graph Protocol -->
  <meta property="og:site_name" content="Earthquick | Nous Telos" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Earthquick — Crafted for the Modern You | Nous Telos" />
  <meta property="og:description" content="Discover handcrafted handloom Sarees, Three-piece sets, artisanal leather bags, and home sanctuaries by Nous Telos." />
  <meta property="og:url" content="{{ url('/') }}" />
  <meta property="og:image" content="{{ asset('images/hero/hero-main-saree-2.jpg') }}" />

  <!-- Twitter Card Protocol -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Earthquick — Crafted for the Modern You | Nous Telos" />
  <meta name="twitter:description" content="Discover handcrafted handloom Sarees, Three-piece sets, artisanal leather bags, and home sanctuaries by Nous Telos." />
  <meta name="twitter:image" content="{{ asset('images/hero/hero-main-saree-2.jpg') }}" />

  <!-- Google Fonts: Fraunces (display/serif) + Jost (body/sans) -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet" />

  <!-- Earthquick Custom Stylesheet -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />

  <!-- Reusable Components Loader -->
  <script src="{{ asset('js/layout.js') }}"></script>

  <!-- Resilient Image Fallback for static hosting -->
  <script>
    window.addEventListener('error', function(e) {
      if (e.target && e.target.tagName === 'IMG') {
        var img = e.target;
        var src = img.getAttribute('src') || '';
        var retryCount = parseInt(img.dataset.retry || '0', 10);
        if (retryCount < 1) {
          img.dataset.retry = String(retryCount + 1);
          var cleanPath = src.split('?')[0];
          if (!cleanPath.startsWith('./') && !cleanPath.startsWith('/')) {
            img.src = './' + src;
          }
        }
      }
    }, true);
  </script>
</head>
<body>

  <!-- ===================================================================
       SECTION 1: HEADER & PRIMARY NAVIGATION (REUSABLE COMPONENT)
       Mapped to Laravel Blade: resources/views/partials/navbar.blade.php
       Changes in /components/navbar.html automatically update here!
       =================================================================== -->
  @include('partials.navbar')


  <!-- ===================================================================
       MAIN CONTENT CONTAINER
       =================================================================== -->
  <main id="main-content">

    <!-- =================================================================
         SECTION 2: HERO CAMPAIGN SLIDER (INTERACTIVE MOTION BANNER)
         Multi-slide editorial campaign showcase with smooth crossfade,
         slide dots, and previous/next navigation.
         ================================================================= -->
    <section class="eq-hero eq-hero--slider" id="hero-section" aria-label="Featured Campaigns">
      <div class="eq-hero__slides" id="hero-slides">

        <!-- Slide 1: Saree Masterpiece & Handloom Heritage -->
        <div class="eq-hero__slide is-active" data-slide-index="0">
          <div class="eq-hero__media">
            <img src="{{ asset('images/hero/hero-main-saree-2.jpg') }}" alt="Earthquick handloom saree collection campaign" fetchpriority="high" decoding="async" />
          </div>
          <div class="eq-hero__content">
            <span class="eq-eyebrow">FLAGSHIP ATELIER</span>
            <h1>The Art of Bangladeshi Handlooms</h1>
            <p>Heritage Jamdani, Rajshahi silks and Tangail weaves shaped with quiet modern grace for the contemporary wardrobe.</p>
            <div class="eq-hero__actions">
              <a href="{{ url('/shop/women/saree') }}" class="eq-btn eq-btn--light eq-btn--pill" id="hero-cta-explore-1">Explore Sarees</a>
              <a href="#story" class="eq-btn eq-btn--outline eq-btn--pill" id="hero-cta-story-1" style="border-color:rgba(255,253,249,.6); color:#fffdf9;">Our Story</a>
            </div>
          </div>
        </div>

        <!-- Slide 2: Contemporary Three-Piece Sets -->
        <div class="eq-hero__slide" data-slide-index="1">
          <div class="eq-hero__media">
            <img src="{{ asset('images/hero/hero-three-piece-page.jpg') }}" alt="Contemporary Three Piece collection campaign" loading="lazy" decoding="async" />
          </div>
          <div class="eq-hero__content">
            <span class="eq-eyebrow">FESTIVE & READY TO WEAR</span>
            <h1>Poise in Every Silhouette</h1>
            <p>Breathable mulmul, organza dupattas, and delicate zardozi embroidery crafted for celebratory moments and everyday grace.</p>
            <div class="eq-hero__actions">
              <a href="{{ url('/shop/women/three-piece') }}" class="eq-btn eq-btn--light eq-btn--pill" id="hero-cta-explore-2">Shop Three-Piece</a>
              <a href="#new-arrivals" class="eq-btn eq-btn--outline eq-btn--pill" id="hero-cta-story-2" style="border-color:rgba(255,253,249,.6); color:#fffdf9;">View Arrivals</a>
            </div>
          </div>
        </div>

        <!-- Slide 3: Artisanal Bags & Everyday Sets -->
        <div class="eq-hero__slide" data-slide-index="2">
          <div class="eq-hero__media">
            <img src="{{ asset('images/hero/hero-bags-page.jpg') }}" alt="Handcrafted leather bags and everyday bags" loading="lazy" decoding="async" />
          </div>
          <div class="eq-hero__content">
            <span class="eq-eyebrow">CONSIDERED ACCESSORIES</span>
            <h1>Functional Form & Natural Texture</h1>
            <p>Handcrafted leather bags, archival totes, and easy everyday coordinates designed for purposeful, effortless movement.</p>
            <div class="eq-hero__actions">
              <a href="{{ route('category.show', 'bags') }}" class="eq-btn eq-btn--light eq-btn--pill" id="hero-cta-explore-3">Discover Bags</a>
              <a href="{{ url('/shop/women/two-piece') }}" class="eq-btn eq-btn--outline eq-btn--pill" id="hero-cta-story-3" style="border-color:rgba(255,253,249,.6); color:#fffdf9;">Two-Piece Sets</a>
            </div>
          </div>
        </div>

      </div>

      <!-- Hero Navigation Arrows -->
      <div class="eq-hero__nav" aria-label="Hero slider controls">
        <button type="button" class="eq-hero__arrow eq-hero__arrow--prev" id="hero-prev" aria-label="Previous slide">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"></path></svg>
        </button>
        <button type="button" class="eq-hero__arrow eq-hero__arrow--next" id="hero-next" aria-label="Next slide">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"></path></svg>
        </button>
      </div>

      <!-- Hero Dot Indicators -->
      <div class="eq-hero__dots" id="hero-dots" role="tablist" aria-label="Hero campaign slides">
        <button type="button" class="eq-hero__dot is-active" data-index="0" role="tab" aria-selected="true" aria-label="Slide 1: Handloom Sarees"></button>
        <button type="button" class="eq-hero__dot" data-index="1" role="tab" aria-selected="false" aria-label="Slide 2: Three-Piece Sets"></button>
        <button type="button" class="eq-hero__dot" data-index="2" role="tab" aria-selected="false" aria-label="Slide 3: Bags and Accessories"></button>
      </div>

      <div class="eq-hero__scroll-cue" aria-hidden="true">
        <span>SCROLL</span>
        <span class="eq-scroll-line"></span>
      </div>
    </section>


    <!-- =================================================================
         SECTION 2.5: HOMEPAGE CATEGORY DISCOVERY STRIP
         Visual discovery showcasing the 6 main customer-facing categories:
         Women, Men, Kids, Ornaments, Bags, Home Decor.
         ================================================================= -->
    <section class="eq-category-strip" id="eq-categories" aria-label="Explore Collections by Category">
      <div class="eq-container">
        <!-- Section Header (Compact) -->
        <div class="eq-category-strip__header eq-reveal">
          <div class="eq-category-strip__title-wrap">
            <h2 class="eq-category-strip__title">Shop by Category</h2>
          </div>
          <p class="eq-category-strip__desc">Select a category to explore handcrafted collections</p>
        </div>

        <!-- 6 Main Categories Grid -->
        <div class="eq-category-strip__grid eq-reveal">

          <!-- 1. WOMEN -->
          <a href="{{ route('category.show', 'women') }}" class="eq-category-card" id="cat-card-women">
            <div class="eq-category-card__media">
              <img src="{{ asset('images/categories/women.jpg') }}" alt="Women fashion collection — Sarees, Three Piece and Co-ords" loading="lazy" decoding="async" />
              <div class="eq-category-card__overlay"></div>
            </div>
            <div class="eq-category-card__info">
              <h3 class="eq-category-card__name">Women</h3>
              <span class="eq-category-card__meta">Saree &amp; Sets</span>
            </div>
          </a>

          <!-- 2. MEN -->
          <a href="{{ route('category.show', 'men') }}" class="eq-category-card" id="cat-card-men">
            <div class="eq-category-card__media">
              <img src="{{ asset('images/hero/hero-main.jpg') }}" alt="Men's collection — Panjabi and festive wear" loading="lazy" decoding="async" />
              <div class="eq-category-card__overlay"></div>
            </div>
            <div class="eq-category-card__info">
              <h3 class="eq-category-card__name">Men</h3>
              <span class="eq-category-card__meta">Panjabi &amp; Sets</span>
            </div>
          </a>

          <!-- 3. KIDS -->
          <a href="{{ route('category.show', 'kids') }}" class="eq-category-card" id="cat-card-kids">
            <div class="eq-category-card__media">
              <img src="{{ asset('images/saree/saree-5.jpg') }}" alt="Kids collection — Festive wear and celebrations" loading="lazy" decoding="async" />
              <div class="eq-category-card__overlay"></div>
            </div>
            <div class="eq-category-card__info">
              <h3 class="eq-category-card__name">Kids</h3>
              <span class="eq-category-card__meta">Festive Wear</span>
            </div>
          </a>

          <!-- 4. ORNAMENTS -->
          <a href="{{ route('category.show', 'ornaments') }}" class="eq-category-card" id="cat-card-ornaments">
            <div class="eq-category-card__media">
              <img src="{{ asset('images/saree/saree-03-detail.jpg') }}" alt="Artisan ornaments and handcrafted jewellery" loading="lazy" decoding="async" />
              <div class="eq-category-card__overlay"></div>
            </div>
            <div class="eq-category-card__info">
              <h3 class="eq-category-card__name">Ornaments</h3>
              <span class="eq-category-card__meta">Handcrafted Jewellery</span>
            </div>
          </a>

          <!-- 5. BAGS -->
          <a href="{{ route('category.show', 'bags') }}" class="eq-category-card" id="cat-card-bags">
            <div class="eq-category-card__media">
              <img src="{{ asset('images/categories/bags.jpg') }}" alt="Artisanal handcrafted leather and everyday bags" loading="lazy" decoding="async" />
              <div class="eq-category-card__overlay"></div>
            </div>
            <div class="eq-category-card__info">
              <h3 class="eq-category-card__name">Bags</h3>
              <span class="eq-category-card__meta">Everyday Totes</span>
            </div>
          </a>

          <!-- 6. HOME DECOR -->
          <a href="{{ route('category.show', 'home-decor') }}" class="eq-category-card" id="cat-card-home-decor">
            <div class="eq-category-card__media">
              <img src="{{ asset('images/hero/story-craft.jpg') }}" alt="Home Decor — Handloom Living and Artisanal Accents" loading="lazy" decoding="async" />
              <div class="eq-category-card__overlay"></div>
            </div>
            <div class="eq-category-card__info">
              <h3 class="eq-category-card__name">Home Decor</h3>
              <span class="eq-category-card__meta">Living &amp; Accents</span>
            </div>
          </a>

        </div>
      </div>
    </section>


    <!-- =================================================================
         TRUST & SERVICE PROPOSITION STRIP
         Authentic handlooms, nationwide delivery, easy exchange, and complimentary
         fall & pico service for discerning patrons.
         ================================================================= -->
    <section class="eq-trust-strip" id="trust-strip" aria-label="Earthquick promises and services">
      <div class="eq-container">
        <div class="eq-trust-grid eq-reveal">

          <div class="eq-trust-card" id="trust-item-artisan">
            <div class="eq-trust-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 6h16M4 12h16M4 18h16M8 3v18M16 3v18"/>
              </svg>
            </div>
            <div class="eq-trust-content">
              <h3 class="eq-trust-title">Authentic Handlooms</h3>
              <p class="eq-trust-desc">Direct from Tangail, Rajshahi &amp; Narayanganj master weavers</p>
            </div>
          </div>

          <div class="eq-trust-card" id="trust-item-delivery">
            <div class="eq-trust-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 3h15v13H1z"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
              </svg>
            </div>
            <div class="eq-trust-content">
              <h3 class="eq-trust-title">Fast Home Delivery</h3>
              <p class="eq-trust-desc">Reliable doorstep delivery across all Bangladesh &amp; worldwide</p>
            </div>
          </div>

          <div class="eq-trust-card" id="trust-item-exchange">
            <div class="eq-trust-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
            </div>
            <div class="eq-trust-content">
              <h3 class="eq-trust-title">Easy 7-Day Exchange</h3>
              <p class="eq-trust-desc">Hassle-free size and product exchange at your convenience</p>
            </div>
          </div>

        </div>
      </div>
    </section>


    <!-- =================================================================
         SECTION 3: NEW ARRIVALS (INTERACTIVE CAROUSEL)
         Horizontal product carousel with arrow controls and swipe gestures.
         ================================================================= -->
    <section class="eq-section" id="new-arrivals">
      <div class="eq-container">
        <!-- Section Header -->
        <div class="eq-section-head eq-reveal">
          <div class="eq-section-head__text">
            <span class="eq-eyebrow">JUST LANDED</span>
            <h2 class="eq-heading-lg">New Arrivals</h2>
          </div>
          <div class="eq-carousel__controls" aria-label="New arrivals carousel navigation">
            <button type="button" class="eq-arrow-btn" data-action="prev" id="new-arrivals-prev" aria-label="Previous products">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"></path></svg>
            </button>
            <button type="button" class="eq-arrow-btn" data-action="next" id="new-arrivals-next" aria-label="Next products">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"></path></svg>
            </button>
          </div>
        </div>

        <!-- Carousel Track -->
        <div class="eq-carousel eq-reveal">
          <div class="eq-carousel__viewport">
            <div class="eq-carousel__track">

              <!-- Item 1: Muslin Jamdani Saree -->
              <article class="eq-product-card" id="product-card-saree-01">
                <a href="{{ url('/product/crimson-heirloom-jamdani') }}" class="eq-product-card__link">
                  <div class="eq-product-card__frame">
                    <span class="eq-badge">New</span>
                    <img src="{{ asset('images/saree/saree-01.jpg') }}" alt="Muslin Jamdani Saree" loading="lazy" decoding="async" />
                    <img class="eq-product-card__img--alt" src="{{ asset('images/saree/saree-01-alt.jpg') }}" alt="" loading="lazy" decoding="async" />
                    <span class="eq-product-card__quick-add">View product</span>
                  </div>
                  <div class="eq-product-card__body">
                    <span class="eq-product-card__category">Saree</span>
                    <h3 class="eq-product-card__name">Muslin Jamdani Saree</h3>
                    <p class="eq-product-card__price">৳8,500</p>
                  </div>
                </a>
              </article>

              <!-- Item 2: Aria Linen Two Piece -->
              <article class="eq-product-card" id="product-card-two-piece-01">
                <a href="{{ url('/product/minimalist-sand-linen-co-ord') }}" class="eq-product-card__link">
                  <div class="eq-product-card__frame">
                    <span class="eq-badge">New</span>
                    <img src="{{ asset('images/two-piece/2pc-1.jpg') }}" alt="Aria Linen Two Piece" loading="lazy" decoding="async" />
                    <img class="eq-product-card__img--alt" src="{{ asset('images/two-piece/2pc-2.jpg') }}" alt="" loading="lazy" decoding="async" />
                    <span class="eq-product-card__quick-add">View product</span>
                  </div>
                  <div class="eq-product-card__body">
                    <span class="eq-product-card__category">Two Piece</span>
                    <h3 class="eq-product-card__name">Aria Linen Two Piece</h3>
                    <p class="eq-product-card__price">৳2,600</p>
                  </div>
                </a>
              </article>

              <!-- Item 3: Heritage Handcrafted Bag -->
              <article class="eq-product-card" id="product-card-bag-01">
                <a href="{{ url('/product/artisanal-terracotta-leather-tote-bag') }}" class="eq-product-card__link">
                  <div class="eq-product-card__frame">
                    <span class="eq-badge">New</span>
                    <img src="{{ asset('images/bags/bag-1.jpg') }}" alt="Heritage Handcrafted Bag" loading="lazy" decoding="async" />
                    <img class="eq-product-card__img--alt" src="{{ asset('images/bags/bag-2.jpg') }}" alt="" loading="lazy" decoding="async" />
                    <span class="eq-product-card__quick-add">View product</span>
                  </div>
                  <div class="eq-product-card__body">
                    <span class="eq-product-card__category">Bags</span>
                    <h3 class="eq-product-card__name">Heritage Handcrafted Bag</h3>
                    <p class="eq-product-card__price">৳4,500</p>
                  </div>
                </a>
              </article>

              <!-- Item 4: Handloom Tangail Saree -->
              <article class="eq-product-card" id="product-card-saree-03">
                <a href="{{ url('/product/royal-champagne-half-silk') }}" class="eq-product-card__link">
                  <div class="eq-product-card__frame">
                    <span class="eq-badge">New</span>
                    <img src="{{ asset('images/saree/saree-03.jpg') }}" alt="Handloom Tangail Saree" loading="lazy" decoding="async" />
                    <img class="eq-product-card__img--alt" src="{{ asset('images/saree/saree-03-alt.jpg') }}" alt="" loading="lazy" decoding="async" />
                    <span class="eq-product-card__quick-add">View product</span>
                  </div>
                  <div class="eq-product-card__body">
                    <span class="eq-product-card__category">Saree</span>
                    <h3 class="eq-product-card__name">Handloom Tangail Saree</h3>
                    <p class="eq-product-card__price">৳4,200</p>
                  </div>
                </a>
              </article>

              <!-- Item 5: Amara Everyday Two Piece -->
              <article class="eq-product-card" id="product-card-two-piece-02">
                <a href="{{ url('/product/ochre-terracotta-kurti-culotte') }}" class="eq-product-card__link">
                  <div class="eq-product-card__frame">
                    <img src="{{ asset('images/two-piece/2pc-3.jpg') }}" alt="Amara Everyday Two Piece" loading="lazy" decoding="async" />
                    <img class="eq-product-card__img--alt" src="{{ asset('images/two-piece/2pc-4.jpg') }}" alt="" loading="lazy" decoding="async" />
                    <span class="eq-product-card__quick-add">View product</span>
                  </div>
                  <div class="eq-product-card__body">
                    <span class="eq-product-card__category">Two Piece</span>
                    <h3 class="eq-product-card__name">Amara Everyday Co-ord</h3>
                    <p class="eq-product-card__price">৳2,750</p>
                  </div>
                </a>
              </article>

              <!-- Item 6: Nomad Structured Bag -->
              <article class="eq-product-card" id="product-card-bag-02">
                <a href="{{ url('/product/saddle-brown-crossbody-sling') }}" class="eq-product-card__link">
                  <div class="eq-product-card__frame">
                    <img src="{{ asset('images/bags/bag-3.jpg') }}" alt="Nomad Structured Bag" loading="lazy" decoding="async" />
                    <img class="eq-product-card__img--alt" src="{{ asset('images/bags/bag-4.jpg') }}" alt="" loading="lazy" decoding="async" />
                    <span class="eq-product-card__quick-add">View product</span>
                  </div>
                  <div class="eq-product-card__body">
                    <span class="eq-product-card__category">Bags</span>
                    <h3 class="eq-product-card__name">Nomad Structured Bag</h3>
                    <p class="eq-product-card__price">৳3,800</p>
                  </div>
                </a>
              </article>

            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- =================================================================
         SECTION 4: SAREE (FLAGSHIP ATELIER SHOWCASE)
         Unique flagship layout: Signature Masterpiece card + 2 rows of curated saree
         cards, honoring Saree as Earthquick's primary product with balanced proportions.
         ================================================================= -->
    <section class="eq-section eq-section--deep eq-section--arched" id="saree-section">
      <div class="eq-arch-divider eq-arch-divider--top" aria-hidden="true">
        <svg viewBox="0 0 1440 40" fill="none" preserveAspectRatio="none">
          <path d="M0,0 Q720,40 1440,0 L1440,40 L0,40 Z" fill="var(--eq-cream-deep)"/>
        </svg>
      </div>
      <div class="eq-container">
        
        <!-- Section Header with Flagship Atelier Identity -->
        <div class="eq-section-head eq-reveal">
          <div class="eq-section-head__text">
            <span class="eq-eyebrow">FLAGSHIP ATELIER</span>
            <h2 class="eq-heading-lg">The Saree Collection</h2>
            <p class="eq-body-lg" style="margin-top: 0.35rem; max-width: 54ch;">Handwoven by master artisans across Narayanganj, Tangail and Rajshahi — Earthquick’s signature craft, cut for an effortless contemporary drape.</p>
          </div>
          <a href="{{ url('/shop/women/saree') }}" class="eq-text-link" id="link-view-all-sarees">
            View All Sarees
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg>
          </a>
        </div>

        <!-- Saree Flagship Showcase: Featured Spotlight + 2 Rows of Curated Gallery -->
        <div class="eq-saree-showcase eq-reveal" id="saree-showcase-container">

          <!-- Featured Masterpiece Spotlight (Clean image, aligned height) -->
          <article class="eq-saree-masterpiece" id="saree-feature-spotlight">
            <a href="{{ url('/product/crimson-heirloom-jamdani') }}" class="eq-saree-masterpiece__link" aria-label="Katan Silk Heritage Saree">
              <div class="eq-saree-masterpiece__frame">
                <img src="{{ asset('images/saree/saree-1.jpg') }}" alt="Katan Silk Heritage Saree" loading="lazy" decoding="async" />
              </div>
              <div class="eq-saree-masterpiece__body">
                <h3 class="eq-saree-masterpiece__name">Katan Silk Heritage</h3>
                <p class="eq-saree-masterpiece__price">৳12,500</p>
              </div>
            </a>
          </article>

          <!-- Curated Saree Gallery (2 rows of 4 balanced cards) -->
          <div class="eq-saree-gallery" id="saree-grid">
            <!-- Row 1: Saree 1 -->
            <article class="eq-product-card eq-saree-card" id="saree-grid-item-04">
              <a href="{{ url('/product/emerald-rajshahi-pure-silk') }}" class="eq-product-card__link">
                <div class="eq-product-card__frame">
                  <img src="{{ asset('images/saree/saree-2.jpg') }}" alt="Organza Pearl Saree" loading="lazy" decoding="async" />
                  <span class="eq-product-card__quick-add">Quick add</span>
                </div>
                <div class="eq-product-card__body">
                  <h3 class="eq-product-card__name">Organza Pearl</h3>
                  <p class="eq-product-card__price">৳6,800</p>
                </div>
              </a>
            </article>

            <!-- Row 1: Saree 2 -->
            <article class="eq-product-card eq-saree-card" id="saree-grid-item-03">
              <a href="{{ url('/product/royal-champagne-half-silk') }}" class="eq-product-card__link">
                <div class="eq-product-card__frame">
                  <img src="{{ asset('images/saree/saree-3.jpg') }}" alt="Handloom Tangail Saree" loading="lazy" decoding="async" />
                  <span class="eq-product-card__quick-add">Quick add</span>
                </div>
                <div class="eq-product-card__body">
                  <h3 class="eq-product-card__name">Handloom Tangail</h3>
                  <p class="eq-product-card__price">৳4,200</p>
                </div>
              </a>
            </article>

            <!-- Row 1: Saree 3 -->
            <article class="eq-product-card eq-saree-card" id="saree-grid-item-02">
              <a href="{{ url('/product/midnight-indigo-tantuj-drape') }}" class="eq-product-card__link">
                <div class="eq-product-card__frame">
                  <img src="{{ asset('images/saree/saree-4.jpg') }}" alt="Rajshahi Silk Saree" loading="lazy" decoding="async" />
                  <span class="eq-product-card__quick-add">Quick add</span>
                </div>
                <div class="eq-product-card__body">
                  <h3 class="eq-product-card__name">Rajshahi Silk</h3>
                  <p class="eq-product-card__price">৳5,400</p>
                </div>
              </a>
            </article>

            <!-- Row 1: Saree 4 -->
            <article class="eq-product-card eq-saree-card" id="saree-grid-item-06">
              <a href="{{ url('/shop/women/saree') }}" class="eq-product-card__link">
                <div class="eq-product-card__frame">
                  <img src="{{ asset('images/saree/saree-5.jpg') }}" alt="Cotton Nakshi Saree" loading="lazy" decoding="async" />
                  <span class="eq-product-card__quick-add">Quick add</span>
                </div>
                <div class="eq-product-card__body">
                  <h3 class="eq-product-card__name">Cotton Nakshi</h3>
                  <p class="eq-product-card__price">৳3,500</p>
                </div>
              </a>
            </article>

            <!-- Row 2: Saree 5 (Organza Pearl) -->
            <article class="eq-product-card eq-saree-card" id="saree-grid-item-04-b">
              <a href="{{ url('/product/emerald-rajshahi-pure-silk') }}" class="eq-product-card__link">
                <div class="eq-product-card__frame">
                  <img src="{{ asset('images/saree/saree-2.jpg') }}" alt="Organza Pearl Saree" loading="lazy" decoding="async" />
                  <span class="eq-product-card__quick-add">Quick add</span>
                </div>
                <div class="eq-product-card__body">
                  <h3 class="eq-product-card__name">Organza Pearl</h3>
                  <p class="eq-product-card__price">৳6,800</p>
                </div>
              </a>
            </article>

            <!-- Row 2: Saree 6 (Handloom Tangail) -->
            <article class="eq-product-card eq-saree-card" id="saree-grid-item-03-b">
              <a href="{{ url('/product/royal-champagne-half-silk') }}" class="eq-product-card__link">
                <div class="eq-product-card__frame">
                  <img src="{{ asset('images/saree/saree-3.jpg') }}" alt="Handloom Tangail Saree" loading="lazy" decoding="async" />
                  <span class="eq-product-card__quick-add">Quick add</span>
                </div>
                <div class="eq-product-card__body">
                  <h3 class="eq-product-card__name">Handloom Tangail</h3>
                  <p class="eq-product-card__price">৳4,200</p>
                </div>
              </a>
            </article>

            <!-- Row 2: Saree 7 (Rajshahi Silk) -->
            <article class="eq-product-card eq-saree-card" id="saree-grid-item-02-b">
              <a href="{{ url('/product/midnight-indigo-tantuj-drape') }}" class="eq-product-card__link">
                <div class="eq-product-card__frame">
                  <img src="{{ asset('images/saree/saree-4.jpg') }}" alt="Rajshahi Silk Saree" loading="lazy" decoding="async" />
                  <span class="eq-product-card__quick-add">Quick add</span>
                </div>
                <div class="eq-product-card__body">
                  <h3 class="eq-product-card__name">Rajshahi Silk</h3>
                  <p class="eq-product-card__price">৳5,400</p>
                </div>
              </a>
            </article>

            <!-- Row 2: Saree 8 (Cotton Nakshi) -->
            <article class="eq-product-card eq-saree-card" id="saree-grid-item-06-b">
              <a href="{{ url('/shop/women/saree') }}" class="eq-product-card__link">
                <div class="eq-product-card__frame">
                  <img src="{{ asset('images/saree/saree-5.jpg') }}" alt="Cotton Nakshi Saree" loading="lazy" decoding="async" />
                  <span class="eq-product-card__quick-add">Quick add</span>
                </div>
                <div class="eq-product-card__body">
                  <h3 class="eq-product-card__name">Cotton Nakshi</h3>
                  <p class="eq-product-card__price">৳3,500</p>
                </div>
              </a>
            </article>
          </div>

        </div>

      </div>
      <div class="eq-arch-divider eq-arch-divider--bottom" aria-hidden="true">
        <svg viewBox="0 0 1440 40" fill="none" preserveAspectRatio="none">
          <path d="M0,40 Q720,0 1440,40 L1440,0 L0,0 Z" fill="var(--eq-cream-deep)"/>
        </svg>
      </div>
    </section>


    <!-- =================================================================
         SECTION 5: THREE PIECE (READY TO WEAR CAROUSEL)
         Refined light cream ground featuring daily rotation kameez, salwar & dupatta sets.
         ================================================================= -->
    <section class="eq-section eq-three-piece" id="three-piece-section">
      <div class="eq-container">
        <!-- Section Header -->
        <div class="eq-section-head eq-reveal">
          <div class="eq-section-head__text">
            <span class="eq-eyebrow">READY TO WEAR</span>
            <h2 class="eq-heading-lg">Three Piece</h2>
            <p class="eq-body-muted" style="margin-top: 0.75rem;">Kameez, salwar and dupatta sets built for daily rotation — from block-printed cotton to fine chikankari.</p>
          </div>
          <div class="eq-carousel__controls" data-carousel-id="three-piece" aria-label="Three piece carousel navigation">
            <button type="button" class="eq-arrow-btn" data-action="prev" id="three-piece-prev" aria-label="Previous three piece products">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"></path></svg>
            </button>
            <button type="button" class="eq-arrow-btn" data-action="next" id="three-piece-next" aria-label="Next three piece products">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"></path></svg>
            </button>
          </div>
        </div>

        <!-- Carousel Track -->
        <div class="eq-carousel eq-reveal">
          <div class="eq-carousel__viewport">
            <div class="eq-carousel__track">

              <!-- Item 1: Aarna Embroidered -->
              <article class="eq-product-card" id="card-three-piece-01">
                <a href="{{ url('/product/ivory-organza-embroidered-set') }}" class="eq-product-card__link">
                  <div class="eq-product-card__frame">
                    <span class="eq-badge">New</span>
                    <img src="{{ asset('images/two-piece/2pc-1.jpg') }}" alt="Aarna Embroidered Set" loading="lazy" decoding="async" />
                    <img class="eq-product-card__img--alt" src="{{ asset('images/two-piece/two-piece-01-alt.jpg') }}" alt="" loading="lazy" decoding="async" />
                    <span class="eq-product-card__quick-add">View product</span>
                  </div>
                  <div class="eq-product-card__body">
                    <span class="eq-product-card__category">Three Piece</span>
                    <h3 class="eq-product-card__name">Aarna Embroidered</h3>
                    <p class="eq-product-card__price">৳4,500</p>
                  </div>
                </a>
              </article>

              <!-- Item 2: Noor Block Print -->
              <article class="eq-product-card" id="card-three-piece-02">
                <a href="{{ url('/product/blush-pink-hand-embroidered-kameez') }}" class="eq-product-card__link">
                  <div class="eq-product-card__frame">
                    <span class="eq-badge">Sale</span>
                    <img src="{{ asset('images/two-piece/2pc-2.jpg') }}" alt="Noor Block Print Set" loading="lazy" decoding="async" />
                    <img class="eq-product-card__img--alt" src="{{ asset('images/two-piece/two-piece-02-alt.jpg') }}" alt="" loading="lazy" decoding="async" />
                    <span class="eq-product-card__quick-add">View product</span>
                  </div>
                  <div class="eq-product-card__body">
                    <span class="eq-product-card__category">Three Piece</span>
                    <h3 class="eq-product-card__name">Noor Block Print</h3>
                    <p class="eq-product-card__price"><span class="eq-price--old">৳3,600</span>৳3,200</p>
                  </div>
                </a>
              </article>

              <!-- Item 3: Zara Chikankari -->
              <article class="eq-product-card" id="card-three-piece-03">
                <a href="{{ url('/shop/women/three-piece') }}" class="eq-product-card__link">
                  <div class="eq-product-card__frame">
                    <img src="{{ asset('images/two-piece/2pc-3.jpg') }}" alt="Zara Chikankari Set" loading="lazy" decoding="async" />
                    <img class="eq-product-card__img--alt" src="{{ asset('images/two-piece/two-piece-03-alt.jpg') }}" alt="" loading="lazy" decoding="async" />
                    <span class="eq-product-card__quick-add">View product</span>
                  </div>
                  <div class="eq-product-card__body">
                    <span class="eq-product-card__category">Three Piece</span>
                    <h3 class="eq-product-card__name">Zara Chikankari</h3>
                    <p class="eq-product-card__price">৳5,100</p>
                  </div>
                </a>
              </article>

              <!-- Item 4: Elan Georgette -->
              <article class="eq-product-card" id="card-three-piece-05">
                <a href="{{ url('/shop/women/three-piece') }}" class="eq-product-card__link">
                  <div class="eq-product-card__frame">
                    <img src="{{ asset('images/two-piece/2pc-4.jpg') }}" alt="Elan Georgette Set" loading="lazy" decoding="async" />
                    <img class="eq-product-card__img--alt" src="{{ asset('images/two-piece/two-piece-04-alt.jpg') }}" alt="" loading="lazy" decoding="async" />
                    <span class="eq-product-card__quick-add">View product</span>
                  </div>
                  <div class="eq-product-card__body">
                    <span class="eq-product-card__category">Three Piece</span>
                    <h3 class="eq-product-card__name">Elan Georgette</h3>
                    <p class="eq-product-card__price">৳4,700</p>
                  </div>
                </a>
              </article>

              <!-- Item 5: Iris Linen -->
              <article class="eq-product-card" id="card-three-piece-06">
                <a href="{{ url('/shop/women/three-piece') }}" class="eq-product-card__link">
                  <div class="eq-product-card__frame">
                    <img src="{{ asset('images/two-piece/two-piece-01.jpg') }}" alt="Iris Linen Set" loading="lazy" decoding="async" />
                    <img class="eq-product-card__img--alt" src="{{ asset('images/two-piece/two-piece-01-detail.jpg') }}" alt="" loading="lazy" decoding="async" />
                    <span class="eq-product-card__quick-add">View product</span>
                  </div>
                  <div class="eq-product-card__body">
                    <span class="eq-product-card__category">Three Piece</span>
                    <h3 class="eq-product-card__name">Iris Linen</h3>
                    <p class="eq-product-card__price">৳3,900</p>
                  </div>
                </a>
              </article>

            </div>
          </div>
        </div>

        <div class="eq-reveal" style="margin-top: var(--space-lg); text-align:center;">
          <a href="{{ url('/shop/women/three-piece') }}" class="eq-text-link" id="link-view-all-three-piece">
            View All Three Piece
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg>
          </a>
        </div>
      </div>
    </section>


    <!-- =================================================================
         SECTION 6: TWO PIECE (ASYMMETRIC FEATURE & STACK)
         Asymmetric layout pairing a hero set with supporting side cards.
         ================================================================= -->
    <section class="eq-section eq-section--deep" id="two-piece-section">
      <div class="eq-container">
        <!-- Section Header -->
        <div class="eq-section-head eq-reveal">
          <div class="eq-section-head__text">
            <span class="eq-eyebrow">EASY EVERYDAY</span>
            <h2 class="eq-heading-lg">Two Piece</h2>
          </div>
          <a href="{{ url('/shop/women/two-piece') }}" class="eq-text-link" id="link-view-all-two-piece">
            View All Two Piece
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg>
          </a>
        </div>

        <!-- Featured Spotlight & Compact Grid Layout -->
        <div class="eq-two-piece__layout eq-reveal" id="two-piece-container">
          <!-- Featured Set (Larger than the grid items) -->
          <a href="{{ url('/shop/women/two-piece') }}" class="eq-two-piece__feature" id="two-piece-feature-card" aria-label="Selene Tunic Two Piece - Featured Set">
            <img src="{{ asset('images/two-piece/2pc-1.jpg') }}" alt="Selene Tunic Two Piece" loading="lazy" decoding="async" />
            <div class="eq-two-piece__feature-info">
              <span class="eq-two-piece__feature-badge">Featured Set</span>
              <h3 class="eq-two-piece__feature-title">Selene Tunic Two Piece</h3>
              <p class="eq-two-piece__feature-price">৳2,900</p>
            </div>
          </a>

          <!-- Compact Product Grid (Smaller image cards) -->
          <div class="eq-two-piece__grid" id="two-piece-grid">
            <!-- Item 1: Aria Linen -->
            <article class="eq-product-card" id="two-piece-card-01">
              <a href="{{ url('/product/minimalist-sand-linen-co-ord') }}" class="eq-product-card__link">
                <div class="eq-product-card__frame">
                  <span class="eq-badge">New</span>
                  <img src="{{ asset('images/two-piece/2pc-2.jpg') }}" alt="Aria Linen Two Piece" loading="lazy" decoding="async" />
                  <span class="eq-product-card__quick-add">Quick add</span>
                </div>
                <div class="eq-product-card__body">
                  <h3 class="eq-product-card__name">Aria Linen</h3>
                  <p class="eq-product-card__price">৳2,600</p>
                </div>
              </a>
            </article>

            <!-- Item 2: Amara Printed -->
            <article class="eq-product-card" id="two-piece-card-04">
              <a href="{{ url('/shop/women/two-piece') }}" class="eq-product-card__link">
                <div class="eq-product-card__frame">
                  <span class="eq-badge">Sale</span>
                  <img src="{{ asset('images/two-piece/2pc-3.jpg') }}" alt="Amara Printed Two Piece" loading="lazy" decoding="async" />
                  <span class="eq-product-card__quick-add">Quick add</span>
                </div>
                <div class="eq-product-card__body">
                  <h3 class="eq-product-card__name">Amara Printed</h3>
                  <p class="eq-product-card__price"><span class="eq-price--old">৳2,800</span>৳2,400</p>
                </div>
              </a>
            </article>

            <!-- Item 3: Rumi Everyday Co-ord -->
            <article class="eq-product-card" id="two-piece-card-02">
              <a href="{{ url('/product/ochre-terracotta-kurti-culotte') }}" class="eq-product-card__link">
                <div class="eq-product-card__frame">
                  <img src="{{ asset('images/two-piece/2pc-4.jpg') }}" alt="Rumi Everyday Co-ord" loading="lazy" decoding="async" />
                  <span class="eq-product-card__quick-add">Quick add</span>
                </div>
                <div class="eq-product-card__body">
                  <h3 class="eq-product-card__name">Rumi Everyday Co-ord</h3>
                  <p class="eq-product-card__price">৳2,750</p>
                </div>
              </a>
            </article>

            <!-- Item 4: Nori Modal Set -->
            <article class="eq-product-card" id="two-piece-card-03">
              <a href="{{ url('/shop/women/two-piece') }}" class="eq-product-card__link">
                <div class="eq-product-card__frame">
                  <img src="{{ asset('images/two-piece/two-piece-01.jpg') }}" alt="Nori Modal Set" loading="lazy" decoding="async" />
                  <span class="eq-product-card__quick-add">Quick add</span>
                </div>
                <div class="eq-product-card__body">
                  <h3 class="eq-product-card__name">Nori Modal Set</h3>
                  <p class="eq-product-card__price">৳3,100</p>
                </div>
              </a>
            </article>
          </div>
        </div>
      </div>
    </section>


    <!-- =================================================================
         SECTION 7: BRAND HERITAGE & CRAFTSMANSHIP STORY
         High-contrast deep heritage navy statement band with Nous Telos artisan story.
         ================================================================= -->
    <section class="eq-section eq-story-section eq-section--dark eq-section--arched" id="story">
      <div class="eq-arch-divider eq-arch-divider--top" aria-hidden="true">
        <svg viewBox="0 0 1440 40" fill="none" preserveAspectRatio="none">
          <path d="M0,0 Q720,40 1440,0 L1440,40 L0,40 Z" fill="var(--eq-navy)"/>
        </svg>
      </div>
      <div class="eq-container">
        <div class="eq-story eq-reveal">
          <div class="eq-story__media" id="story-media-container">
            <img src="{{ asset('images/hero/story-heritage.jpg') }}" alt="Traditional weaving craft behind Earthquick pieces" loading="lazy" decoding="async" />
          </div>
          <div class="eq-story__text" id="story-text-container">
            <svg class="eq-story__mark" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
              <path d="M4 30c8 0 8-16 16-16s8 16 16 16" />
            </svg>
            <span class="eq-eyebrow">OUR PHILOSOPHY</span>
            <h2 class="eq-heading-lg">Tradition, Reimagined.</h2>
            <p class="eq-body-lg eq-story__desc-desktop">Nous Telos has spent years working directly with handloom weavers and embroidery collectives across Bangladesh. Earthquick takes that same material honesty and cuts it for how people actually get dressed today — considered, uncomplicated, built to last past one season.</p>
            <p class="eq-story__desc-mobile">Rooted in Bengal handlooms — crafted with master artisans for effortless contemporary living.</p>
          </div>
        </div>
      </div>
      <div class="eq-arch-divider eq-arch-divider--bottom" aria-hidden="true">
        <svg viewBox="0 0 1440 40" fill="none" preserveAspectRatio="none">
          <path d="M0,40 Q720,0 1440,40 L1440,0 L0,0 Z" fill="var(--eq-navy)"/>
        </svg>
      </div>
    </section>


    <!-- =================================================================
         SECTION 8: BAGS & ACCESSORIES (GRID)
         Accessory showcase with one large hero card and supporting handbag cards.
         ================================================================= -->
    <section class="eq-section" id="bags-section">
      <div class="eq-container">
        <!-- Section Header -->
        <div class="eq-section-head eq-reveal">
          <div class="eq-section-head__text">
            <span class="eq-eyebrow">FINISHING TOUCH</span>
            <h2 class="eq-heading-lg">Bags</h2>
          </div>
          <a href="{{ route('category.show', 'bags') }}" class="eq-text-link" id="link-view-all-bags">
            View All Bags
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg>
          </a>
        </div>

        <!-- Bags 5x2 Compact Grid (10 items total, 5 per row) -->
        <div class="eq-bags__layout eq-reveal" id="bags-grid">
          <!-- Row 1: Item 1 -->
          <article class="eq-product-card" id="card-bag-01">
            <a href="{{ url('/product/artisanal-terracotta-leather-tote-bag') }}" class="eq-product-card__link">
              <div class="eq-product-card__frame">
                <span class="eq-badge">Popular</span>
                <img src="{{ asset('images/bags/bag-1.jpg') }}" alt="Heritage Canvas Tote" loading="lazy" decoding="async" />
                <span class="eq-product-card__quick-add">Quick add</span>
              </div>
              <div class="eq-product-card__body">
                <h3 class="eq-product-card__name">Heritage Canvas Tote</h3>
                <p class="eq-product-card__price">৳3,800</p>
              </div>
            </a>
          </article>

          <!-- Row 1: Item 2 -->
          <article class="eq-product-card" id="card-bag-02">
            <a href="{{ url('/product/saddle-brown-crossbody-sling') }}" class="eq-product-card__link">
              <div class="eq-product-card__frame">
                <span class="eq-badge">New</span>
                <img src="{{ asset('images/bags/bag-2.jpg') }}" alt="Nomad Leather Sling" loading="lazy" decoding="async" />
                <span class="eq-product-card__quick-add">Quick add</span>
              </div>
              <div class="eq-product-card__body">
                <h3 class="eq-product-card__name">Nomad Leather Sling</h3>
                <p class="eq-product-card__price">৳4,500</p>
              </div>
            </a>
          </article>

          <!-- Row 1: Item 3 -->
          <article class="eq-product-card" id="card-bag-03">
            <a href="{{ route('category.show', 'bags') }}" class="eq-product-card__link">
              <div class="eq-product-card__frame">
                <span class="eq-badge">Sale</span>
                <img src="{{ asset('images/bags/bag-3.jpg') }}" alt="Terra Structured Handbag" loading="lazy" decoding="async" />
                <span class="eq-product-card__quick-add">Quick add</span>
              </div>
              <div class="eq-product-card__body">
                <h3 class="eq-product-card__name">Terra Handbag</h3>
                <p class="eq-product-card__price"><span class="eq-price--old">৳5,800</span>৳5,200</p>
              </div>
            </a>
          </article>

          <!-- Row 1: Item 4 -->
          <article class="eq-product-card" id="card-bag-04">
            <a href="{{ route('category.show', 'bags') }}" class="eq-product-card__link">
              <div class="eq-product-card__frame">
                <img src="{{ asset('images/bags/bag-4.jpg') }}" alt="Wicker Weekend Bag" loading="lazy" decoding="async" />
                <span class="eq-product-card__quick-add">Quick add</span>
              </div>
              <div class="eq-product-card__body">
                <h3 class="eq-product-card__name">Wicker Weekend Bag</h3>
                <p class="eq-product-card__price">৳3,200</p>
              </div>
            </a>
          </article>

          <!-- Row 1: Item 5 -->
          <article class="eq-product-card" id="card-bag-05">
            <a href="{{ route('category.show', 'bags') }}" class="eq-product-card__link">
              <div class="eq-product-card__frame">
                <span class="eq-badge">New</span>
                <img src="{{ asset('images/bags/bag-5.jpg') }}" alt="Mini Canvas Crossbody" loading="lazy" decoding="async" />
                <span class="eq-product-card__quick-add">Quick add</span>
              </div>
              <div class="eq-product-card__body">
                <h3 class="eq-product-card__name">Mini Crossbody</h3>
                <p class="eq-product-card__price">৳2,900</p>
              </div>
            </a>
          </article>

          <!-- Row 2: Item 6 -->
          <article class="eq-product-card" id="card-bag-06">
            <a href="{{ route('category.show', 'bags') }}" class="eq-product-card__link">
              <div class="eq-product-card__frame">
                <img src="{{ asset('images/bags/bag-6.jpg') }}" alt="Saddle Leather Clutch" loading="lazy" decoding="async" />
                <span class="eq-product-card__quick-add">Quick add</span>
              </div>
              <div class="eq-product-card__body">
                <h3 class="eq-product-card__name">Saddle Clutch</h3>
                <p class="eq-product-card__price">৳3,600</p>
              </div>
            </a>
          </article>

          <!-- Row 2: Item 7 -->
          <article class="eq-product-card" id="card-bag-07">
            <a href="{{ route('category.show', 'bags') }}" class="eq-product-card__link">
              <div class="eq-product-card__frame">
                <span class="eq-badge">Popular</span>
                <img src="{{ asset('images/bags/bag-7.jpg') }}" alt="Olive Structured Satchel" loading="lazy" decoding="async" />
                <span class="eq-product-card__quick-add">Quick add</span>
              </div>
              <div class="eq-product-card__body">
                <h3 class="eq-product-card__name">Olive Satchel</h3>
                <p class="eq-product-card__price">৳4,800</p>
              </div>
            </a>
          </article>

          <!-- Row 2: Item 8 -->
          <article class="eq-product-card" id="card-bag-08">
            <a href="{{ route('category.show', 'bags') }}" class="eq-product-card__link">
              <div class="eq-product-card__frame">
                <img src="{{ asset('images/bags/bag-8.jpg') }}" alt="Woven Raffia Shoulder Bag" loading="lazy" decoding="async" />
                <span class="eq-product-card__quick-add">Quick add</span>
              </div>
              <div class="eq-product-card__body">
                <h3 class="eq-product-card__name">Woven Raffia</h3>
                <p class="eq-product-card__price">৳3,400</p>
              </div>
            </a>
          </article>

          <!-- Row 2: Item 9 -->
          <article class="eq-product-card" id="card-bag-09">
            <a href="{{ route('category.show', 'bags') }}" class="eq-product-card__link">
              <div class="eq-product-card__frame">
                <img src="{{ asset('images/bags/bag-9.jpg') }}" alt="Handcrafted Stitch Tote" loading="lazy" decoding="async" />
                <span class="eq-product-card__quick-add">Quick add</span>
              </div>
              <div class="eq-product-card__body">
                <h3 class="eq-product-card__name">Stitch Canvas Tote</h3>
                <p class="eq-product-card__price">৳4,100</p>
              </div>
            </a>
          </article>

          <!-- Row 2: Item 10 -->
          <article class="eq-product-card" id="card-bag-10">
            <a href="{{ route('category.show', 'bags') }}" class="eq-product-card__link">
              <div class="eq-product-card__frame">
                <span class="eq-badge">Sale</span>
                <img src="{{ asset('images/bags/bag-10.jpg') }}" alt="Sienna Leather Pouch" loading="lazy" decoding="async" />
                <span class="eq-product-card__quick-add">Quick add</span>
              </div>
              <div class="eq-product-card__body">
                <h3 class="eq-product-card__name">Sienna Pouch</h3>
                <p class="eq-product-card__price"><span class="eq-price--old">৳3,200</span>৳2,800</p>
              </div>
            </a>
          </article>
        </div>
      </div>
    </section>


    <!-- =================================================================
         SECTION 8: STYLED BY YOU — 3D COVERFLOW CAROUSEL & VISUAL DIARY
         Curated editorial gallery showcasing authentic styling, archival
         draping, and real moments with Earthquick pieces.
         ================================================================= -->
    <section class="eq-section eq-coverflow-section" id="styled-by-you" aria-label="Styled by our community">
      <div class="eq-container">
        
        <!-- Section Header (Centered display with Glassy Facebook community button) -->
        <div class="eq-coverflow-header eq-reveal">
          <span class="eq-eyebrow">COMMUNITY &bull; @RTHQUICK ON FACEBOOK</span>
          <h2 class="eq-heading-lg">Styled by You</h2>
          <p class="eq-body-lg">
            Real moments, archival draping, and daily movement: tagged by our patrons across Bangladesh and beyond.
          </p>
          <div class="eq-coverflow-header__actions">
            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="eq-glassy-fb-btn" id="btn-fb-community" aria-label="Join our Facebook Community">
              <svg class="eq-fb-icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
              <span>Follow on Facebook</span>
            </a>
          </div>
        </div>

        <!-- Filter Pills Bar (matching the pill buttons in reference + glassy Facebook option) -->
        <div class="eq-coverflow-pills eq-reveal" role="tablist" aria-label="Filter looks by style">
          <button type="button" class="eq-coverflow-pill is-active" data-coverflow-filter="all" id="pill-all">All Looks</button>
          <button type="button" class="eq-coverflow-pill" data-coverflow-filter="saree" id="pill-saree">Saree</button>
          <button type="button" class="eq-coverflow-pill" data-coverflow-filter="jamdani" id="pill-jamdani">Jamdani</button>
          <button type="button" class="eq-coverflow-pill" data-coverflow-filter="two-piece" id="pill-twopiece">Two Piece</button>
          <button type="button" class="eq-coverflow-pill" data-coverflow-filter="three-piece" id="pill-threepiece">Three Piece</button>
          <button type="button" class="eq-coverflow-pill" data-coverflow-filter="bags" id="pill-bags">Bags</button>
          <button type="button" class="eq-coverflow-pill" data-coverflow-filter="silk" id="pill-silk">Pure Silk</button>
          <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="eq-coverflow-pill eq-coverflow-pill--fb-glassy" id="pill-view-more" aria-label="Visit Facebook Page">
            <svg class="eq-fb-icon" viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            <span>Facebook Page &rarr;</span>
          </a>
        </div>

        <!-- 3D Coverflow Stage Viewport (Clean, sharp, unobstructed photography cards) -->
        <div class="eq-coverflow-stage" id="eq-coverflow-stage" tabindex="0" role="region" aria-roledescription="carousel" aria-label="Community style gallery 3D coverflow">
          <div class="eq-coverflow-track" id="eq-coverflow-track">
            
            <!-- Slide 1: Jamdani Saree -->
            <div class="eq-coverflow-card" data-category="saree jamdani" id="coverflow-card-1" role="group" aria-roledescription="slide" aria-label="1 of 7">
              <img src="{{ asset('images/saree/saree-01.jpg') }}" alt="Muslin Jamdani draped by Tasnia in Banani" class="eq-coverflow-card__img" loading="lazy" />
            </div>

            <!-- Slide 2: Aria Linen Two Piece -->
            <div class="eq-coverflow-card" data-category="two-piece" id="coverflow-card-2" role="group" aria-roledescription="slide" aria-label="2 of 7">
              <img src="{{ asset('images/two-piece/2pc-1.jpg') }}" alt="Aria Linen Two Piece styled by Samira" class="eq-coverflow-card__img" loading="lazy" />
            </div>

            <!-- Slide 3: Heritage Canvas Bag -->
            <div class="eq-coverflow-card" data-category="bags" id="coverflow-card-3" role="group" aria-roledescription="slide" aria-label="3 of 7">
              <img src="{{ asset('images/bags/bag-1.jpg') }}" alt="Heritage Canvas Tote with Zarin in Gulshan" class="eq-coverflow-card__img" loading="lazy" />
            </div>

            <!-- Slide 4: Tangail Handloom Saree -->
            <div class="eq-coverflow-card" data-category="saree" id="coverflow-card-4" role="group" aria-roledescription="slide" aria-label="4 of 7">
              <img src="{{ asset('images/saree/saree-03.jpg') }}" alt="Tangail Handloom Saree styled by Nawrin" class="eq-coverflow-card__img" loading="lazy" />
            </div>

            <!-- Slide 5: Three Piece Kurti Set -->
            <div class="eq-coverflow-card" data-category="three-piece" id="coverflow-card-5" role="group" aria-roledescription="slide" aria-label="5 of 7">
              <img src="{{ asset('images/three-piece/three-piece-01.jpg') }}" alt="Artisanal Three Piece ensemble styled by Anika" class="eq-coverflow-card__img" loading="lazy" />
            </div>

            <!-- Slide 6: Terra Leather Bag -->
            <div class="eq-coverflow-card" data-category="bags" id="coverflow-card-6" role="group" aria-roledescription="slide" aria-label="6 of 7">
              <img src="{{ asset('images/bags/bag-3.jpg') }}" alt="Terra Handbag styled by Maheen in Chattogram" class="eq-coverflow-card__img" loading="lazy" />
            </div>

            <!-- Slide 7: Emerald Rajshahi Pure Silk -->
            <div class="eq-coverflow-card" data-category="saree silk" id="coverflow-card-7" role="group" aria-roledescription="slide" aria-label="7 of 7">
              <img src="{{ asset('images/saree/saree-04.jpg') }}" alt="Rajshahi Pure Silk Saree draped by Fariha" class="eq-coverflow-card__img" loading="lazy" />
            </div>

          </div>
        </div>

        <!-- Centered Circular Navigation Arrows (matching reference image) -->
        <div class="eq-coverflow-controls eq-reveal">
          <button type="button" class="eq-coverflow-arrow eq-coverflow-arrow--prev" id="coverflow-btn-prev" aria-label="Previous look">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <line x1="19" y1="12" x2="5" y2="12"></line>
              <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
          </button>
          <button type="button" class="eq-coverflow-arrow eq-coverflow-arrow--next" id="coverflow-btn-next" aria-label="Next look">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </button>
        </div>

      </div>
    </section>
    <section class="eq-final-cta eq-reveal" id="newsletter-section">
      <div class="eq-container">
        <div class="eq-newsletter-card">
          <span class="eq-eyebrow eq-newsletter-eyebrow">JOIN THE HOUSE OF EARTHQUICK</span>
          <h2 class="eq-heading-lg eq-newsletter-title">Be first to see what's next.</h2>
          <p class="eq-body-lg eq-newsletter-desc">Sign up for early access to new drops, restocks, and the stories behind the makers we work with.</p>

          <form class="eq-newsletter" id="eq-newsletter-form" data-newsletter-form novalidate>
            <div class="eq-newsletter-field">
              <svg class="eq-newsletter-input-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
              </svg>
              <input type="email" id="eq-newsletter-email" required placeholder="Enter your email address" aria-label="Email address" autocomplete="email" />
            </div>
            <button type="submit" class="eq-btn eq-btn--primary eq-newsletter-submit" id="eq-newsletter-submit">Subscribe</button>
          </form>
          <p class="eq-newsletter-guarantee">No spam, ever. Unsubscribe with one click anytime.</p>
        </div>
      </div>
    </section>

  </main>


  <!-- ===================================================================
       SECTION 10: FOOTER & SEARCH MODAL (REUSABLE COMPONENT)
       Mapped to Laravel Blade: resources/views/partials/footer.blade.php
       Changes in /components/footer.html automatically update here!
       =================================================================== -->
  @include('partials.footer')

  <!-- Quick View Modal -->
  <div class="eq-quickview-modal" id="eq-quickview-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Product quick view">
    <div class="eq-quickview-backdrop" id="eq-quickview-backdrop"></div>
    <div class="eq-quickview-panel" id="eq-quickview-panel">
      <button type="button" class="eq-quickview-close" id="eq-quickview-close" aria-label="Close quick view">&times;</button>
      <div class="eq-quickview-body" id="eq-quickview-body">
        <!-- Content populated dynamically by script.js -->
      </div>
    </div>
  </div>

  <!-- Toast Notification Container -->
  <div class="eq-toast-container" id="eq-toast-container" role="status" aria-live="polite"></div>

  <!-- Earthquick Core Script -->
  <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>

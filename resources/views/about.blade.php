@extends('layouts.app')

@section('title', 'About Earthquick — Independent Bangladeshi brands')
@section('meta_description', 'Learn about Earthquick, the marketplace for independent Bangladeshi brands, including Nous Telos heritage handloom and Bright electronics.')
@section('body_class', 'eq-about-page')

@section('content')
  <!-- ===================================================================
       MAIN CONTENT CONTAINER
       =================================================================== -->
  <main id="main-content">

    <!-- Breadcrumb Navigation -->
    <div class="eq-container">
      <nav class="eq-breadcrumbs" aria-label="Breadcrumb">
        <ol>
          <li><a href="{{ route('home') }}">Home</a></li>
          <li aria-current="page">About Us</li>
        </ol>
      </nav>
    </div>

    <!-- Hero Section with Quick Navigation Pills -->
    <section class="eq-about-hero" id="about-hero">
      <div class="eq-container">
        <span class="eq-eyebrow">EARTHQUICK MARKETPLACE</span>
        <h1 class="eq-heading-xl">Independent Brands. Thoughtful Choice.</h1>
        <p class="eq-about-hero__lead">
          Earthquick brings independent Bangladeshi brands together in one considered marketplace.
          Nous Telos leads with heritage handloom, while Bright is preparing its electronics storefront.
        </p>

        <!-- Quick Anchor Pill Links -->
        <nav class="eq-about-pill-nav" aria-label="Page sub-sections">
          <a href="#our-story" class="eq-about-pill" id="pill-story">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
            Our Journey
          </a>
          <a href="#brand-ecosystem" class="eq-about-pill" id="pill-ecosystem">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
            Our Stores
          </a>
          <a href="#contact-support" class="eq-about-pill" id="pill-contact">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            Contact &amp; Studio
          </a>
          <a href="#help-faq" class="eq-about-pill" id="pill-faq">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            Help &amp; FAQ
          </a>
          <a href="#shop-hubs" class="eq-about-pill" id="pill-shop">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
            Shop Collections
          </a>
        </nav>
      </div>
    </section>

    <!-- ===================================================================
         SECTION 1: OUR JOURNEY & CORE VALUES
         =================================================================== -->
    <section class="eq-section" id="our-story">
      <div class="eq-container">
        <div class="eq-section-head">
          <div class="eq-section-head__text">
            <span class="eq-eyebrow">THE PHILOSOPHY</span>
            <h2 class="eq-heading-lg">Tradition, Consciously Reimagined</h2>
          </div>
        </div>

        <div class="eq-philosophy-highlight">
          <p class="eq-philosophy-lead">
            Rooted in Bengal’s handloom heritage, <strong>Nous Telos</strong> unites generational artisan craftsmanship with refined modern silhouettes—partnering directly with master weavers across Tangail, Sonargaon, and Rajshahi to create conscious, timeless attire.
          </p>
        </div>

        <!-- 3 Core Craft Pillars Grid -->
        <div class="row g-3 g-md-4" id="story-pillars-grid">
          <div class="col-md-4">
            <div class="eq-story-card" id="pillar-card-integrity">
              <div class="eq-story-card__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
              </div>
              <h3 class="eq-story-card__title">Artisanal Honesty</h3>
              <p class="eq-story-card__desc">
                We reject industrial imitation. Our handloom sarees and Nakshi textiles are woven thread by thread on pit looms, 
                preserving authentic motifs and natural tactile depth.
              </p>
            </div>
          </div>

          <div class="col-md-4">
            <div class="eq-story-card" id="pillar-card-sustainability">
              <div class="eq-story-card__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
              </div>
              <h3 class="eq-story-card__title">Fair &amp; Ethical Maker Guilds</h3>
              <p class="eq-story-card__desc">
                By shortening the distance between makers and consumers, we provide sustainable year-round livelihood to rural weaving families 
                and independent creative collectives.
              </p>
            </div>
          </div>

          <div class="col-md-4">
            <div class="eq-story-card" id="pillar-card-modern">
              <div class="eq-story-card__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
              </div>
              <h3 class="eq-story-card__title">Considered Modern Silhouettes</h3>
              <p class="eq-story-card__desc">
                From relaxed two-piece linen sets to structured bags and understated ornaments, our designs are made to move seamlessly 
                from boardroom to evening dinner.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================================
         SECTION 2: BRAND ECOSYSTEM & VENDOR COLLECTIVE
         =================================================================== -->
    <section class="eq-section eq-section--deep" id="brand-ecosystem">
      <div class="eq-container">
        <div class="eq-section-head">
          <div class="eq-section-head__text">
            <span class="eq-eyebrow">OUR CURATED HOUSE</span>
            <h2 class="eq-heading-lg">The Earthquick Maker Collective</h2>
          </div>
          <a href="{{ route('stores.index') }}" class="eq-text-link" id="link-join-collective">
            Explore Our Stores &rarr;
          </a>
        </div>

        <p class="eq-body-lg eq-about-intro">
          Earthquick is a marketplace for independent Bangladeshi brands. <strong>Nous Telos</strong> is our
          flagship heritage handloom store, while <strong>Bright</strong> is our electronics storefront in preparation.
        </p>

        <!-- Brand Collective Cards Grid -->
        <div class="eq-vendor-grid" id="vendor-collective-grid">

          <!-- Brand 1: Nous Telos (Flagship Heritage) -->
          <article class="eq-vendor-card" id="vendor-card-nous-telos">
            <div class="eq-vendor-card__header">
              <div>
                <span class="eq-vendor-card__badge eq-vendor-card__badge--featured">Flagship Brand</span>
              </div>
              <span style="font-size: 0.8rem; color: var(--eq-charcoal-soft); font-weight: 500;">Est. Heritage</span>
            </div>
            <div class="eq-vendor-card__body">
              <h3 class="eq-vendor-card__name">Nous Telos</h3>
              <div class="eq-vendor-card__specialty">Handloom Sarees &bull; Heritage Textiles</div>
              <p class="eq-vendor-card__story">
                The founding soul of our platform. Specializing in master-grade Jamdani, Tangail weaves, half-silk ensembles, 
                and pure silk bridal pieces crafted on traditional pit looms.
              </p>
              <div class="eq-vendor-card__tags">
                <span class="eq-vendor-card__tag">Jamdani Weaves</span>
                <span class="eq-vendor-card__tag">Tantuj</span>
                <span class="eq-vendor-card__tag">Pure Silk</span>
                <span class="eq-vendor-card__tag">Festive Sets</span>
              </div>
              <div class="eq-vendor-card__footer">
                <a href="{{ route('category.show', 'women') }}" class="eq-text-link" style="font-weight: 600;">Shop Nous Telos &rarr;</a>
              </div>
            </div>
          </article>

          <!-- Brand 2: Bright electronics -->
          <article class="eq-vendor-card" id="vendor-card-bright">
            <div class="eq-vendor-card__header">
              <div>
                <span class="eq-vendor-card__badge">Electronics</span>
              </div>
              <span style="font-size: 0.8rem; color: var(--eq-charcoal-soft); font-weight: 500;">Store in Preparation</span>
            </div>
            <div class="eq-vendor-card__body">
              <h3 class="eq-vendor-card__name">Bright</h3>
              <div class="eq-vendor-card__specialty">Electronics &bull; Everyday Technology</div>
              <p class="eq-vendor-card__story">
                Bright is Earthquick's second storefront, being prepared as a focused home for practical electronics
                and everyday technology.
              </p>
              <div class="eq-vendor-card__tags">
                <span class="eq-vendor-card__tag">Electronics</span>
                <span class="eq-vendor-card__tag">Coming Soon</span>
              </div>
              <div class="eq-vendor-card__footer">
                <a href="{{ route('stores.show', 'bright') }}" class="eq-text-link" style="font-weight: 600;">Visit Bright &rarr;</a>
              </div>
            </div>
          </article>

          <!-- Brand 3: Future independent brands -->
          <article class="eq-vendor-card" id="vendor-card-future-stores">
            <div class="eq-vendor-card__header">
              <div>
                <span class="eq-vendor-card__badge">Marketplace</span>
              </div>
              <span style="font-size: 0.8rem; color: var(--eq-charcoal-soft); font-weight: 500;">Growing Carefully</span>
            </div>
            <div class="eq-vendor-card__body">
              <h3 class="eq-vendor-card__name">More Independent Stores</h3>
              <div class="eq-vendor-card__specialty">New Categories &bull; New Brands</div>
              <p class="eq-vendor-card__story">
                Earthquick will add independently managed brands and categories over time, while keeping the
                marketplace clear and dependable for customers.
              </p>
              <div class="eq-vendor-card__tags">
                <span class="eq-vendor-card__tag">Curated Growth</span>
                <span class="eq-vendor-card__tag">Future Categories</span>
              </div>
              <div class="eq-vendor-card__footer">
                <a href="{{ route('stores.index') }}" class="eq-text-link" style="font-weight: 600;">View Stores &rarr;</a>
              </div>
            </div>
          </article>

        </div>

      </div>
    </section>

    <!-- ===================================================================
         SECTION 3: CONTACT US & CUSTOMER CARE SUPPORT
         =================================================================== -->
    <section class="eq-section" id="contact-support">
      <div class="eq-container">
        <div class="eq-section-head">
          <div class="eq-section-head__text">
            <span class="eq-eyebrow">GET IN TOUCH</span>
            <h2 class="eq-heading-lg">Contact &amp; Customer Care</h2>
          </div>
        </div>

        <div class="eq-contact-grid">
          
          <!-- Column 1: Studio & Direct Contact Details -->
          <div class="eq-contact-info-card" id="contact-info-card">
            <h3 class="eq-contact-info-heading">The Studio &amp; Showroom</h3>
            <p class="eq-contact-info-desc">
              Visit us in person to feel our handloom weaves, try on bespoke fits, or consult directly with our textile stylists.
            </p>

            <!-- Address Item -->
            <div class="eq-contact-info-item">
              <div class="eq-contact-info-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
              </div>
              <div>
                <div class="eq-contact-info-title">Showroom Location</div>
                <p class="eq-contact-info-val">
                  GEC Circle, Nasirabad<br />
                  Chattogram 4000, Bangladesh
                </p>
              </div>
            </div>

            <!-- Hotline Item -->
            <div class="eq-contact-info-item">
              <div class="eq-contact-info-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
              </div>
              <div>
                <div class="eq-contact-info-title">Customer Care Hotline</div>
                <p class="eq-contact-info-val">
                  <a href="tel:+8801793100087">+880 017931***87</a><br />
                  <span style="font-size: 0.82rem; color: #64748b;">Instant assistance on WhatsApp &amp; Calls</span>
                </p>
              </div>
            </div>

            <!-- Email Item -->
            <div class="eq-contact-info-item">
              <div class="eq-contact-info-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
              </div>
              <div>
                <div class="eq-contact-info-title">Email Inquiries</div>
                <p class="eq-contact-info-val">
                  <a href="mailto:iftekharislamifty@gmail.com">iftekharislamifty@gmail.com</a><br />
                  <span style="font-size: 0.82rem; color: #64748b;">Average response time: within 24 hours</span>
                </p>
              </div>
            </div>

            <!-- Hours Item -->
            <div class="eq-contact-info-item">
              <div class="eq-contact-info-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              </div>
              <div>
                <div class="eq-contact-info-title">Studio &amp; Dispatch Hours</div>
                <p class="eq-contact-info-val">
                  Saturday &ndash; Thursday: 10:00 AM &ndash; 8:00 PM<br />
                  Friday: Closed (Online orders processed continuously)
                </p>
              </div>
            </div>

          </div>

          <!-- Column 2: Send Us a Message Form -->
          <div class="eq-contact-form-card" id="contact-form-card">
            <h3 class="eq-contact-form-title">Send a Note or Inquiry</h3>
            <p class="eq-contact-form-desc">
              Have a question about a saree, need custom sizing advice, or wish to partner as a maker? 
              Fill out the form below and our team will get back to you promptly.
            </p>

            <form id="contact-form" novalidate onsubmit="handleContactSubmit(event)">
              <div class="eq-form-row">
                <div class="eq-form-group">
                  <label for="contact-name">Your Full Name <span style="color:#c93b2b;">*</span></label>
                  <input type="text" class="eq-form-input" id="contact-name" name="name" required placeholder="e.g. Iftekhar Islam" />
                </div>
                <div class="eq-form-group">
                  <label for="contact-phone">Phone Number <span style="color:#c93b2b;">*</span></label>
                  <input type="tel" class="eq-form-input" id="contact-phone" name="phone" required placeholder="017XXXXXXXX" />
                </div>
              </div>

              <div class="eq-form-group">
                <label for="contact-email">Email Address <span style="color:#c93b2b;">*</span></label>
                <input type="email" class="eq-form-input" id="contact-email" name="email" required placeholder="your.name@example.com" />
              </div>

              <div class="eq-form-group">
                <label for="contact-subject">Inquiry Type / Subject</label>
                <select class="eq-form-select" id="contact-subject" name="subject">
                  <option value="General Inquiry">General Product Inquiry</option>
                  <option value="Order Status & Delivery">Order Status &amp; Nationwide Delivery</option>
                  <option value="Vendor / Artisan Partnership">Vendor / Artisan Partnership (Sell on Earthquick)</option>
                  <option value="Custom Tailoring & Sizing">Custom Tailoring, Saree Blouse or Bulk Order</option>
                  <option value="Exchange or Return">Exchange or Return Assistance</option>
                </select>
              </div>

              <div class="eq-form-group">
                <label for="contact-message">Your Message / Requirements <span style="color:#c93b2b;">*</span></label>
                <textarea class="eq-form-textarea" id="contact-message" name="message" required placeholder="Tell us how we can help you, or share details about your craft/store..."></textarea>
              </div>

              <button type="submit" class="eq-btn eq-btn--primary" id="btn-submit-contact">
                Send Message &rarr;
              </button>
            </form>
          </div>

        </div>
      </div>
    </section>

    <!-- ===================================================================
         SECTION 4: HELP & FREQUENTLY ASKED QUESTIONS (FAQ)
         =================================================================== -->
    <section class="eq-section eq-section--deep" id="help-faq">
      <div class="eq-container">
        <div class="eq-section-head">
          <div class="eq-section-head__text">
            <span class="eq-eyebrow">FREQUENTLY ASKED QUESTIONS</span>
            <h2 class="eq-heading-lg">Help &amp; Customer Support</h2>
          </div>
        </div>

        <div class="eq-faq-container" id="faq-accordion-group">

          <!-- FAQ Item 1 -->
          <div class="eq-faq-item is-open" id="faq-1">
            <button type="button" class="eq-faq-question" aria-expanded="true" onclick="toggleFaq(this)">
              <span>How do I place an order and what payment methods are accepted?</span>
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <div class="eq-faq-answer" style="display: block;">
              <p>
                Placing an order is effortless: browse our collections, select your desired piece, and click <strong>Add to Bag</strong> or <strong>Buy Now</strong>. 
                During checkout, you can pay via <strong>Cash on Delivery (nationwide across Bangladesh)</strong>, 
                instant mobile banking (<strong>bKash, Nagad, Rocket</strong>), or debit/credit cards (Visa, Mastercard, Amex).
              </p>
            </div>
          </div>

          <!-- FAQ Item 2 -->
          <div class="eq-faq-item" id="faq-2">
            <button type="button" class="eq-faq-question" aria-expanded="false" onclick="toggleFaq(this)">
              <span>What are the shipping times and delivery charges across Bangladesh?</span>
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <div class="eq-faq-answer">
              <p>
                Standard delivery inside <strong>Chattogram and Dhaka takes 2 to 3 business days</strong> (৳80). 
                Delivery to all other district towns across Bangladesh takes <strong>3 to 5 business days</strong> (৳150). 
                All packages are packaged in protective waterproof inner layers to ensure your handlooms arrive pristine.
              </p>
            </div>
          </div>

          <!-- FAQ Item 3 -->
          <div class="eq-faq-item" id="faq-3">
            <button type="button" class="eq-faq-question" aria-expanded="false" onclick="toggleFaq(this)">
              <span>How should I care for authentic handloom Jamdani, pure silk, and Nakshi Kantha?</span>
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <div class="eq-faq-answer">
              <p>
                Handloom textiles are living craft:
              </p>
              <ul style="padding-left: 1.25rem; margin-bottom: 0.5rem;">
                <li><strong>Pure Silk &amp; Jamdani Sarees:</strong> Dry clean strictly recommended for the first 3 washes. Store in a breathable cotton saree bag with neem leaves or silica gel; avoid direct naphthalene contact.</li>
                <li><strong>Cotton Two-Piece &amp; Linen:</strong> Gentle hand wash or delicate cold machine cycle with mild detergent. Dry in indirect shade to preserve natural dye luster.</li>
                <li><strong>Nakshi Kantha:</strong> Spot clean when possible. If washing, use gentle cold hand-wash without heavy wringing.</li>
              </ul>
            </div>
          </div>

          <!-- FAQ Item 4 -->
          <div class="eq-faq-item" id="faq-4">
            <button type="button" class="eq-faq-question" aria-expanded="false" onclick="toggleFaq(this)">
              <span>What is your return and exchange policy?</span>
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <div class="eq-faq-answer">
              <p>
                We stand wholeheartedly behind our craftsmanship. You can request an exchange within <strong>7 days of delivery</strong> 
                provided the item is unworn, unwashed, and retains all original brand tags and packaging. 
                Contact our care hotline at <code>+880 017931***87</code> or email us with your Order ID to initiate the courier pickup.
              </p>
            </div>
          </div>

          <!-- FAQ Item 5 -->
          <div class="eq-faq-item" id="faq-5">
            <button type="button" class="eq-faq-question" aria-expanded="false" onclick="toggleFaq(this)">
              <span>I am an artisan, weaver, or boutique brand — how do I sell on Earthquick?</span>
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <div class="eq-faq-answer">
              <p>
                We welcome ethical makers into our vendor ecosystem! Please scroll up to our 
                <a href="#contact-support" onclick="prefillVendorInquiry()" style="color: var(--eq-gold-dark); font-weight:600;">Contact Form</a>, 
                select <em>"Vendor / Artisan Partnership"</em>, and provide a short summary of your craft, catalog, or workshop location. 
                Our curation team evaluates each application within 48 hours for craft authenticity and fair production standards.
              </p>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ===================================================================
         SECTION 5: EXPLORE THE STORE (SHOP HUBS)
         =================================================================== -->
    <section class="eq-section" id="shop-hubs">
      <div class="eq-container">
        <div class="eq-section-head">
          <div class="eq-section-head__text">
            <span class="eq-eyebrow">SHOP BY CATEGORY</span>
            <h2 class="eq-heading-lg">Discover the Earthquick Collections</h2>
          </div>
          <a href="{{ route('home') }}" class="eq-text-link" id="link-explore-home">Back to Homepage &rarr;</a>
        </div>

        <div class="eq-shop-shortcuts" id="shop-shortcuts-grid">
          <a href="{{ route('category.show', 'men') }}" class="eq-shop-shortcut-card" id="shortcut-men">
            <strong>Men</strong>
            <span>Panjabi &amp; Essentials &rarr;</span>
          </a>
          <a href="{{ route('category.show', 'women') }}" class="eq-shop-shortcut-card" id="shortcut-women">
            <strong>Women</strong>
            <span>Sarees &amp; Sets &rarr;</span>
          </a>
          <a href="{{ route('category.show', 'kids') }}" class="eq-shop-shortcut-card" id="shortcut-kids">
            <strong>Kids</strong>
            <span>Festive &amp; Daily &rarr;</span>
          </a>
          <a href="{{ route('category.show', 'ornaments') }}" class="eq-shop-shortcut-card" id="shortcut-ornaments">
            <strong>Ornaments</strong>
            <span>Jewelry &amp; Accents &rarr;</span>
          </a>
          <a href="{{ route('category.show', 'bags') }}" class="eq-shop-shortcut-card" id="shortcut-bags">
            <strong>Bags</strong>
            <span>Slings &amp; Totes &rarr;</span>
          </a>
          <a href="{{ route('category.show', 'home-decor') }}" class="eq-shop-shortcut-card" id="shortcut-decor">
            <strong>Home Decor</strong>
            <span>Kantha &amp; Living &rarr;</span>
          </a>
        </div>
      </div>
    </section>

  </main>
@endsection

@push('scripts')
<script>
  // FAQ Accordion Toggle
  function toggleFaq(button) {
    var item = button.closest('.eq-faq-item');
    var answer = item.querySelector('.eq-faq-answer');
    var isOpen = item.classList.contains('is-open');

    if (isOpen) {
      item.classList.remove('is-open');
      button.setAttribute('aria-expanded', 'false');
      if (answer) answer.style.display = 'none';
    } else {
      item.classList.add('is-open');
      button.setAttribute('aria-expanded', 'true');
      if (answer) answer.style.display = 'block';
    }
  }

  // Pre-fill vendor inquiry when clicking maker links
  function prefillVendorInquiry() {
    var subjectSelect = document.getElementById('contact-subject');
    if (subjectSelect) {
      subjectSelect.value = 'Vendor / Artisan Partnership';
    }
    var contactSection = document.getElementById('contact-support');
    if (contactSection) {
      contactSection.scrollIntoView({ behavior: 'smooth' });
    }
    var nameInput = document.getElementById('contact-name');
    if (nameInput) {
      setTimeout(function() { nameInput.focus(); }, 400);
    }
  }

  // Contact Form Submission Handler
  function handleContactSubmit(event) {
    event.preventDefault();
    var form = event.target;
    var name = document.getElementById('contact-name').value.trim();
    var phone = document.getElementById('contact-phone').value.trim();
    var email = document.getElementById('contact-email').value.trim();
    var subject = document.getElementById('contact-subject').value;
    var message = document.getElementById('contact-message').value.trim();

    if (!name || !phone || !email || !message) {
      if (window.showToast) {
        window.showToast('Please fill in all required fields.', 'error');
      } else {
        alert('Please fill in all required fields.');
      }
      return;
    }

    // Display success feedback
    if (window.showToast) {
      window.showToast('Thank you, ' + name + '! Your message regarding "' + subject + '" has been received. Our team will contact you shortly.', 'success');
    } else {
      alert('Thank you! Your message has been received.');
    }

    // Reset form
    form.reset();
  }
</script>
@endpush

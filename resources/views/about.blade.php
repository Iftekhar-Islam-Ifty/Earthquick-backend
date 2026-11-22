@extends('layouts.app')

@section('title', 'About Rthquick — Independent Bangladeshi Stores')
@section('meta_description', 'Meet Rthquick, a marketplace for independent Bangladeshi stores, including Nous Telos and Bright Electrical Company.')
@section('body_class', 'eq-about-page')
@section('scroll_motion', '1')

@push('styles')
<style>
  .eq-about-page { background: var(--eq-cream, #f7f2e9); }
  .eq-about-page * { box-sizing: border-box; }
  .eq-about-page main { color: var(--eq-charcoal); }
  .eq-about-hero { padding: clamp(2rem, 4vw, 3.25rem) 0; background: var(--eq-navy); color: #fff; text-align: left; }
  .eq-about-hero__inner { max-width: 1000px; }
  .eq-about-kicker { display: block; margin-bottom: .55rem; color: #e4ba6e; font-size: .8rem; font-weight: 700; letter-spacing: .13em; text-transform: uppercase; }
  .eq-about-hero h1 { max-width: 780px; margin: 0; color: #fff; font: 500 clamp(2.2rem, 4.4vw, 3.6rem)/1.15 var(--font-display); }
  .eq-about-hero__lead { max-width: 740px; margin: .85rem 0 0; color: #e6eeed; font-size: 1.1rem; line-height: 1.7; }
  .eq-about-quick { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1.25rem; }
  .eq-about-quick a { display: inline-flex; align-items: center; justify-content: center; min-height: 2.45rem; padding: .4rem .9rem; border: 1px solid rgba(255,255,255,.38); border-radius: 999px; color: #fff; font-size: .9rem; font-weight: 600; text-decoration: none; }
  .eq-about-quick a:hover, .eq-about-quick a:focus-visible { background: #fff; color: var(--eq-navy); }
  .eq-about-section { padding: clamp(2rem, 4vw, 3.2rem) 0; scroll-margin-top: 90px; }
  .eq-about-section--tint { background: #f0ede5; }
  .eq-about-head { display: flex; align-items: end; justify-content: space-between; flex-wrap: wrap; gap: .75rem; margin-bottom: 1.15rem; }
  .eq-about-head span { display: block; margin-bottom: .35rem; color: var(--eq-gold-dark); font-size: .8rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
  .eq-about-head h2 { margin: 0; color: var(--eq-navy); font: 500 clamp(1.75rem, 2.7vw, 2.25rem)/1.2 var(--font-display); }
  .eq-about-head a, .eq-about-link { color: var(--eq-navy); font-size: .94rem; font-weight: 600; text-underline-offset: 3px; }
  .eq-about-intro { max-width: 850px; margin: 0 0 1.2rem; color: var(--eq-charcoal); font-size: 1.08rem; line-height: 1.7; }
  .eq-about-values { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .85rem; }
  .eq-about-value { min-width: 0; padding: 1.15rem; border: 1px solid var(--eq-line); border-radius: 10px; background: #fff; }
  .eq-about-value h3 { margin: 0 0 .4rem; color: var(--eq-navy); font: 600 1.2rem/1.3 var(--font-display); }
  .eq-about-value p { margin: 0; color: var(--eq-charcoal-soft); font-size: 1rem; line-height: 1.6; }
  .eq-about-brands { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .85rem; }
  .eq-about-brand { display: flex; flex-direction: column; min-width: 0; padding: 1.35rem; border: 1px solid var(--eq-line); border-radius: 10px; background: #fff; }
  .eq-about-brand__status { align-self: flex-start; margin-bottom: .5rem; color: var(--eq-gold-dark); font-size: .78rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
  .eq-about-brand h3 { margin: 0 0 .4rem; color: var(--eq-navy); font: 600 1.5rem/1.25 var(--font-display); }
  .eq-about-brand p { flex: 1; margin: 0 0 .85rem; color: var(--eq-charcoal-soft); font-size: 1rem; line-height: 1.6; }
  .eq-about-brand a { align-self: flex-start; }
  .eq-about-contact { display: grid; grid-template-columns: minmax(0, .85fr) minmax(0, 1.15fr); gap: 1rem; align-items: start; }
  .eq-about-contact-card, .eq-about-form-card { min-width: 0; padding: clamp(1.1rem, 2.4vw, 1.5rem); border: 1px solid var(--eq-line); border-radius: 10px; background: #fff; }
  .eq-about-contact-card h3, .eq-about-form-card h3 { margin: 0 0 .5rem; color: var(--eq-navy); font: 600 1.45rem/1.25 var(--font-display); }
  .eq-about-contact-card > p, .eq-about-form-card > p { margin: 0 0 1rem; color: var(--eq-charcoal-soft); font-size: 1rem; line-height: 1.6; }
  .eq-about-contact-options { display: grid; gap: .55rem; }
  .eq-about-contact-option { min-width: 0; padding: .75rem .9rem; border: 1px solid var(--eq-line); border-radius: 8px; background: #faf9f6; }
  .eq-about-contact-option strong { display: block; margin-bottom: .12rem; color: var(--eq-navy); font-size: .9rem; }
  .eq-about-contact-option a { color: var(--eq-charcoal); font-size: 1rem; font-weight: 600; overflow-wrap: anywhere; text-underline-offset: 2px; }
  .eq-about-contact-option span { display: block; margin-top: .15rem; color: var(--eq-charcoal-soft); font-size: .9rem; line-height: 1.45; }
  .eq-about-form-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .75rem; }
  .eq-about-field { display: grid; gap: .3rem; margin-bottom: .8rem; }
  .eq-about-field label { color: var(--eq-charcoal); font-size: .95rem; font-weight: 600; }
  .eq-about-field input, .eq-about-field select, .eq-about-field textarea { width: 100%; min-width: 0; padding: .65rem .75rem; border: 1px solid var(--eq-line); border-radius: 7px; background: #fff; color: var(--eq-charcoal); font: inherit; font-size: 1rem; }
  .eq-about-field textarea { min-height: 6rem; resize: vertical; }
  .eq-about-field input:focus-visible, .eq-about-field select:focus-visible, .eq-about-field textarea:focus-visible { outline: 2px solid var(--eq-gold-dark); outline-offset: 1px; }
  .eq-about-submit { min-height: 2.75rem; }
  .eq-about-faq-list { display: grid; gap: .55rem; max-width: 980px; }
  .eq-about-faq { padding: 0 1rem; border: 1px solid var(--eq-line); border-radius: 8px; background: #fff; }
  .eq-about-faq summary { padding: .9rem 0; color: var(--eq-navy); font-size: 1.05rem; font-weight: 600; line-height: 1.4; cursor: pointer; }
  .eq-about-faq summary:focus-visible { outline: 2px solid var(--eq-gold-dark); outline-offset: 2px; }
  .eq-about-faq p { margin: 0 0 .95rem; color: var(--eq-charcoal-soft); font-size: 1rem; line-height: 1.65; }
  .eq-about-faq a { color: var(--eq-navy); text-underline-offset: 2px; }
  .eq-about-categories { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: .6rem; }
  .eq-about-categories a { padding: .85rem .6rem; border: 1px solid var(--eq-line); border-radius: 8px; background: #fff; color: var(--eq-navy); font-size: 1rem; font-weight: 600; text-align: center; text-decoration: none; }
  .eq-about-categories a:hover, .eq-about-categories a:focus-visible { border-color: var(--eq-gold); background: #fffaf0; }
  @media (max-width: 900px) {
    .eq-about-contact { grid-template-columns: minmax(0, 1fr); }
    .eq-about-categories { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  }
  @media (max-width: 768px) {
    .eq-about-page #shop-hubs, .eq-about-page #pill-shop { display: none; }
    .eq-about-hero { padding: 1.8rem 0 1.55rem; }
    .eq-about-hero h1 { font-size: clamp(1.9rem, 7vw, 2.35rem); }
    .eq-about-hero__lead { font-size: 1rem; line-height: 1.6; }
    .eq-about-quick { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .eq-about-quick a { min-width: 0; padding: .4rem .55rem; font-size: .88rem; }
    .eq-about-section { padding: 1.65rem 0; }
    .eq-about-values, .eq-about-brands { grid-template-columns: minmax(0, 1fr); gap: .65rem; }
    .eq-about-value, .eq-about-brand { padding: 1rem; }
    .eq-about-intro { font-size: 1rem; line-height: 1.6; }
  }
  @media (max-width: 480px) {
    .eq-about-hero { padding: 1.5rem 0 1.3rem; }
    .eq-about-section { padding: 1.4rem 0; }
    .eq-about-head h2 { font-size: 1.7rem; }
    .eq-about-value p, .eq-about-brand p, .eq-about-faq p { font-size: .98rem; }
    .eq-about-form-row { grid-template-columns: minmax(0, 1fr); gap: 0; }
    .eq-about-contact-card, .eq-about-form-card { padding: 1rem; }
  }
</style>
@endpush

@section('content')
<main id="main-content">
  <div class="eq-container">
    <nav class="eq-breadcrumbs" aria-label="Breadcrumb">
      <ol><li><a href="{{ route('home') }}">Home</a></li><li aria-current="page">About Us</li></ol>
    </nav>
  </div>

  <header class="eq-about-hero">
    <div class="eq-container eq-about-hero__inner">
      <span class="eq-about-kicker">RTHQUICK MARKETPLACE</span>
      <h1>Independent stores. One place to shop.</h1>
      <p class="eq-about-hero__lead">Explore distinct Bangladeshi stores in one marketplace. Each store keeps its own identity, while product brands are shown separately on their products.</p>
      <nav class="eq-about-quick" aria-label="On this page">
        <a href="#our-story">Why Rthquick</a>
        <a href="#store-ecosystem">Our Stores</a>
        <a href="#contact-support">Contact Us</a>
        <a href="#help-faq">Help &amp; FAQ</a>
        <a href="#shop-hubs" id="pill-shop">Shop Categories</a>
      </nav>
    </div>
  </header>

  <section class="eq-about-section" id="our-story" aria-labelledby="about-story-title">
    <div class="eq-container">
      <div class="eq-about-head eq-reveal"><div><span>How it works</span><h2 id="about-story-title">Different stores. One clear experience.</h2></div></div>
      <p class="eq-about-intro">Rthquick is a shared marketplace for stores such as Nous Telos and Bright Electrical Company. Our central team manages checkout, order updates and after-sales requests across the marketplace.</p>
      <div class="eq-about-values eq-reveal">
        <article class="eq-about-value"><h3>Distinct stores</h3><p>Explore each store's products and story. Product brands, such as Heaven, are identified separately.</p></article>
        <article class="eq-about-value"><h3>One checkout</h3><p>Choose from different stores in one order, with the delivery charge shown before you place it.</p></article>
        <article class="eq-about-value"><h3>Central customer care</h3><p>Our team reviews order, delivery, return and refund questions across all stores.</p></article>
      </div>
    </div>
  </section>

  <section class="eq-about-section eq-about-section--tint" id="store-ecosystem" aria-labelledby="about-stores-title">
    <div class="eq-container">
      <div class="eq-about-head eq-reveal"><div><span>Our stores</span><h2 id="about-stores-title">Meet the stores</h2></div><a href="{{ route('stores.index') }}">Explore all stores &rarr;</a></div>
      <div class="eq-about-brands eq-reveal">
        <article class="eq-about-brand" id="vendor-card-nous-telos">
          <span class="eq-about-brand__status">Flagship store</span>
          <h3>Nous Telos</h3>
          <p>Heritage-led clothing and accessories, from sarees and sets to bags and home textiles.</p>
          <a class="eq-about-link" href="{{ route('stores.show', 'nous-telos') }}">Explore Nous Telos &rarr;</a>
        </article>
        <article class="eq-about-brand" id="vendor-card-bright">
          <span class="eq-about-brand__status">Catalog in preparation</span>
          <h3>Bright Electrical Company</h3>
          <p>A Chattogram electrical supplier offering switches, sockets, LED lights, circuit breakers, fans and other accessories.</p>
          <a class="eq-about-link" href="{{ route('stores.show', 'bright') }}">Preview store &rarr;</a>
          <a class="eq-about-link" href="{{ route('stores.bright-information') }}">Bright store information &amp; policies &rarr;</a>
        </article>
      </div>
    </div>
  </section>

  <section class="eq-about-section" id="contact-support" aria-labelledby="about-contact-title">
    <div class="eq-container">
      <div class="eq-about-head eq-reveal"><div><span>Get in touch</span><h2 id="about-contact-title">Contact Rthquick</h2></div></div>
      <div class="eq-about-contact">
        <div class="eq-about-contact-card" id="contact-info-card">
          <h3>Choose how to reach us</h3>
          <p>For a question about a product or order, use the form for a tracked inquiry. Calls, email and WhatsApp are handled by our team, not an automated live chat.</p>
          <div class="eq-about-contact-options">
            @if(config('communications.support_phone'))
              <div class="eq-about-contact-option"><strong>Call</strong><a href="tel:{{ preg_replace('/[^+0-9]/', '', config('communications.support_phone')) }}">{{ config('communications.support_phone') }}</a><span>For an urgent conversation.</span></div>
            @endif
            @if(config('communications.support_email'))
              <div class="eq-about-contact-option"><strong>Email</strong><a href="mailto:{{ config('communications.support_email') }}">{{ config('communications.support_email') }}</a><span>For detailed, non-urgent questions.</span></div>
            @endif
            @if(config('communications.support_whatsapp'))
              <div class="eq-about-contact-option"><strong>WhatsApp</strong><a href="https://wa.me/{{ preg_replace('/\D/', '', config('communications.support_whatsapp')) }}" target="_blank" rel="noopener noreferrer">Open WhatsApp chat</a><span>Starts a manual conversation in WhatsApp.</span></div>
            @endif
            @unless(config('communications.support_phone') || config('communications.support_email') || config('communications.support_whatsapp'))
              <div class="eq-about-contact-option"><strong>Support form</strong><span>Send a message using the form on this page.</span></div>
            @endunless
          </div>
        </div>

        <div class="eq-about-form-card" id="contact-form-card">
          <h3>Send an inquiry</h3>
          <p>Your message is saved for our support team. Include an order number if your question is about an existing order.</p>
          @if(session('success'))<p role="status" style="color:#176a3a;">{{ session('success') }}</p>@endif
          @if($errors->any())<p role="alert" style="color:#a32727;">{{ $errors->first() }}</p>@endif
          <form id="contact-form" method="POST" action="{{ route('contact.store') }}">
            @csrf
            <div style="position:absolute;left:-9999px;" aria-hidden="true"><label for="contact-website">Leave this blank</label><input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off"></div>
            <div class="eq-about-form-row">
              <div class="eq-about-field"><label for="contact-name">Name *</label><input type="text" id="contact-name" name="name" value="{{ old('name') }}" required minlength="2" maxlength="150" autocomplete="name" /></div>
              <div class="eq-about-field"><label for="contact-phone">Phone *</label><input type="tel" id="contact-phone" name="phone" value="{{ old('phone') }}" required maxlength="20" autocomplete="tel" placeholder="017XXXXXXXX" /></div>
            </div>
            <div class="eq-about-field"><label for="contact-email">Email (optional)</label><input type="email" id="contact-email" name="email" value="{{ old('email') }}" maxlength="255" autocomplete="email" /></div>
            <div class="eq-about-field"><label for="contact-subject">What is this about?</label><select id="contact-subject" name="subject">
              <option value="General Inquiry" @selected(old('subject') === 'General Inquiry')>Product or general question</option>
              <option value="Order Status & Delivery" @selected(old('subject') === 'Order Status & Delivery')>Order or delivery</option>
              <option value="Exchange or Return" @selected(old('subject') === 'Exchange or Return')>Return or refund</option>
              <option value="Vendor / Artisan Partnership" @selected(old('subject') === 'Vendor / Artisan Partnership')>Store partnership</option>
              <option value="Custom Tailoring & Sizing" @selected(old('subject') === 'Custom Tailoring & Sizing')>Sizing or other request</option>
            </select></div>
            <div class="eq-about-field"><label for="contact-message">Message *</label><textarea id="contact-message" name="message" required minlength="10" maxlength="5000" placeholder="How can we help?">{{ old('message') }}</textarea></div>
            <button type="submit" class="eq-btn eq-btn--primary eq-about-submit" id="btn-submit-contact">Send inquiry &rarr;</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <section class="eq-about-section eq-about-section--tint" id="help-faq" aria-labelledby="about-faq-title">
    <div class="eq-container">
      <div class="eq-about-head eq-reveal"><div><span>Quick answers</span><h2 id="about-faq-title">Help &amp; FAQ</h2></div><a href="{{ route('policies.delivery-returns') }}">Full delivery &amp; returns policy &rarr;</a></div>
      <div class="eq-about-faq-list eq-reveal">
        <details class="eq-about-faq"><summary>How can I pay for an order?</summary><p>Cash on Delivery is available. bKash online payment, manual mobile banking and card payments are not currently available; use the options shown at checkout.</p></details>
        <details class="eq-about-faq"><summary>What does delivery cost?</summary><p>The charge depends on delivery zone and order subtotal. Checkout shows the amount before you place the order. <a href="{{ route('policies.delivery-returns') }}#delivery">See delivery charges and terms</a>.</p></details>
        <details class="eq-about-faq"><summary>How do returns and refunds work?</summary><p>Eligibility and return windows are item-specific. Submit an eligible request from your order page and wait for Rthquick's decision before sending an item. <a href="{{ route('policies.delivery-returns') }}#returns">Read the returns and refunds policy</a>.</p></details>
        <details class="eq-about-faq"><summary>Can my store join Rthquick?</summary><p>Tell us about your store and its product brands using the <a href="#contact-support" onclick="prefillVendorInquiry()">store partnership inquiry form</a>. Our team will review your message; there is no separate vendor dashboard or automatic approval.</p></details>
      </div>
    </div>
  </section>

  <section class="eq-about-section" id="shop-hubs" aria-labelledby="about-shop-title">
    <div class="eq-container">
      <div class="eq-about-head eq-reveal"><div><span>Shop by category</span><h2 id="about-shop-title">Explore the collections</h2></div></div>
      <nav class="eq-about-categories eq-reveal" aria-label="Shop categories">
        <a href="{{ route('category.show', 'men') }}">Men</a>
        <a href="{{ route('category.show', 'women') }}">Women</a>
        <a href="{{ route('category.show', 'kids') }}">Kids</a>
        <a href="{{ route('category.show', 'ornaments') }}">Ornaments</a>
        <a href="{{ route('category.show', 'bags') }}">Bags</a>
        <a href="{{ route('category.show', 'home-decor') }}">Home Decor</a>
      </nav>
    </div>
  </section>
</main>
@endsection

@push('scripts')
<script>
  function prefillVendorInquiry() {
    const subject = document.getElementById('contact-subject');
    if (subject) subject.value = 'Vendor / Artisan Partnership';
  }
</script>
@endpush

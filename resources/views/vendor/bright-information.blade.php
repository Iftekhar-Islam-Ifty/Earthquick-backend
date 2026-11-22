@extends('layouts.app')

@section('title', 'About Bright & Store Information | Rthquick')
@section('meta_description', 'Learn about Bright Electrical Company, its product-brand relationship and store-specific customer information on Rthquick.')
@section('canonical_url', route('stores.bright-information'))
@section('body_class', 'eq-bright-information-page')

@push('styles')
<style>
  .eq-bright-info { background: #f8f4eb; padding-bottom: 4rem; color: #302f2b; }
  .eq-bright-info__hero { padding: clamp(2.4rem, 5vw, 4rem) 0; background: linear-gradient(130deg, #14384b, #203e4c); color: #fff; }
  .eq-bright-info__hero small { color: #efd38b; font-size: .78rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
  .eq-bright-info__hero h1 { margin: .55rem 0; color: #fff; font: 600 clamp(2rem, 4vw, 3.1rem)/1.15 var(--font-display, Georgia, serif); }
  .eq-bright-info__hero p { max-width: 720px; margin: 0; color: #e3ebed; font-size: 1.02rem; line-height: 1.7; }
  .eq-bright-info__content { max-width: 1040px; margin: 0 auto; padding: 2rem 1.25rem 0; }
  .eq-bright-info__section { margin: 0 0 1.4rem; padding: clamp(1.2rem, 3vw, 2rem); border: 1px solid #e5dfd2; border-radius: 12px; background: #fffdf8; }
  .eq-bright-info__section h2 { margin: 0 0 .8rem; color: #203e4c; font: 600 clamp(1.35rem, 2.5vw, 1.8rem)/1.25 var(--font-display, Georgia, serif); }
  .eq-bright-info__section h3 { margin: 1.1rem 0 .35rem; color: #203e4c; font-size: 1rem; }
  .eq-bright-info__section p, .eq-bright-info__section li { color: #57564f; font-size: .96rem; line-height: 1.7; }
  .eq-bright-info__section p { margin: .45rem 0; }
  .eq-bright-info__section ul { margin: .55rem 0 0; padding-left: 1.25rem; }
  .eq-bright-info__grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
  .eq-bright-info__note { margin-top: .9rem !important; padding: .9rem 1rem; border-left: 3px solid #c4a55c; border-radius: 0 7px 7px 0; background: #fbf3e3; }
  .eq-bright-info__actions { display: flex; flex-wrap: wrap; gap: .7rem; margin-top: 1rem; }
  .eq-bright-info__actions a { display: inline-flex; align-items: center; min-height: 2.65rem; padding: .6rem .95rem; border: 1px solid #203e4c; border-radius: 6px; color: #203e4c; font-size: .88rem; font-weight: 600; text-decoration: none; }
  .eq-bright-info__actions a:first-child { background: #203e4c; color: #fff; }
  .eq-bright-info__actions a:hover, .eq-bright-info__actions a:focus-visible { background: #1f5a58; border-color: #1f5a58; color: #fff; }
  @media (max-width: 680px) {
    .eq-bright-info__grid { grid-template-columns: minmax(0, 1fr); gap: .7rem; }
    .eq-bright-info__content { padding: 1.2rem 1rem 0; }
    .eq-bright-info { padding-bottom: 2.5rem; }
  }
</style>
@endpush

@section('content')
<main id="main-content" class="eq-bright-info">
  <div class="eq-container">
    <nav class="eq-breadcrumbs" aria-label="Breadcrumb">
      <ol>
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('stores.show', 'bright') }}">Bright</a></li>
        <li aria-current="page">Store information</li>
      </ol>
    </nav>
  </div>

  <header class="eq-bright-info__hero">
    <div class="eq-container">
      <small>Bright Electrical Company</small>
      <h1>About Bright &amp; store information</h1>
      <p>Store information and customer terms for Bright orders placed through Rthquick.</p>
    </div>
  </header>

  <div class="eq-bright-info__content">
    <section class="eq-bright-info__section" aria-labelledby="bright-profile-title">
      <h2 id="bright-profile-title">About the store</h2>
      <div class="eq-bright-info__grid">
        <div>
          <p>Bright Electrical Company supplies electrical products for wholesale and retail customers. Its listed range includes switches, sockets, LED lights, circuit breakers, fans and other electrical accessories.</p>
          <p><strong>Page-listed office:</strong> Police Plaza, Nandankanan, Chattogram, Bangladesh.</p>
        </div>
        <div>
          <p><strong>Store / seller:</strong> Bright Electrical Company.</p>
          <p><strong>Product brand:</strong> Heaven is a product brand sold by Bright, not a separate store.</p>
        </div>
      </div>
    </section>

    <section class="eq-bright-info__section" aria-labelledby="bright-policies-title">
      <h2 id="bright-policies-title">Bright store policies</h2>
      <h3>Product details and availability</h3>
      <p>Use the product page for its current price, brand, specifications, stock and item-specific terms. Those details can differ between products.</p>

      <h3>Orders, delivery, returns and refunds</h3>
      <p>Orders from Bright are placed through Rthquick's central checkout. Rthquick's marketplace-wide delivery, cancellation, return and refund policy applies. Return eligibility and the return window are item-specific and are shown with the product and saved order details.</p>
      <p>Exchange requests are not automatic. Contact Rthquick support with the item or order details so the available options can be reviewed under the marketplace after-sales policy.</p>

      <h3>Warranty and after-sales</h3>
      <p>Bright is the warranty provider for Heaven-branded products. The product listing or order information must state the applicable coverage and period; ask Rthquick for clarification if those details are missing. This page does not set a blanket warranty term.</p>
      <p>For other brands, use only the warranty provider and terms shown for that product.</p>

      <p class="eq-bright-info__note"><strong>Need help?</strong> Include the product name or order number when contacting Rthquick.</p>
      <div class="eq-bright-info__actions">
        <a href="{{ route('stores.catalog', 'bright') }}">Browse Bright products <span aria-hidden="true">&rarr;</span></a>
        <a href="{{ route('policies.delivery-returns') }}">Rthquick delivery &amp; returns policy</a>
        <a href="{{ route('about') }}#contact-support">Contact Rthquick support</a>
      </div>
    </section>
  </div>
</main>
@endsection

@extends('layouts.app')

@section('title', 'Delivery, Returns & Refunds | Rthquick')
@section('meta_description', 'Understand Rthquick delivery charges, cancellation requests, return eligibility, courier responsibility and COD refunds.')
@section('body_class', 'eq-policy-page')
@section('scroll_motion', '1')

@push('styles')
<style>
  .eq-policy-page { background: #faf8f3; }
  .eq-policy-page * { box-sizing: border-box; }
  .eq-policy-page .eq-policy { padding-bottom: clamp(3rem, 7vw, 6rem); color: var(--eq-charcoal, #30302d); }
  .eq-policy-hero { padding: clamp(2.5rem, 6vw, 5.5rem) 0; background: #163846; color: #fff; }
  .eq-policy-hero__inner { max-width: 860px; }
  .eq-policy-kicker { display: block; margin-bottom: .9rem; color: #e7bd65; font-size: .78rem; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; }
  .eq-policy-hero h1 { margin: 0; color: #fff; font-family: var(--font-display, Georgia, serif); font-size: clamp(2.35rem, 5vw, 4.6rem); line-height: 1.12; }
  .eq-policy-hero p { max-width: 720px; margin: 1rem 0 0; color: #e1e9e8; font-size: clamp(1rem, 1.5vw, 1.13rem); line-height: 1.7; }
  .eq-policy-quick { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: 1.7rem; }
  .eq-policy-quick a { display: inline-flex; align-items: center; min-height: 2.65rem; padding: .5rem .95rem; border: 1px solid rgba(255,255,255,.36); border-radius: 999px; color: #fff; font-size: .85rem; font-weight: 600; text-decoration: none; }
  .eq-policy-quick a:hover, .eq-policy-quick a:focus-visible { background: #fff; color: #163846; }
  .eq-policy-shell { max-width: 1100px; margin: 0 auto; padding: 0 1.25rem; }
  .eq-policy-highlights { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; margin-top: -1.4rem; position: relative; }
  .eq-policy-highlight { min-width: 0; padding: 1.3rem; border: 1px solid #e5e2d9; border-radius: 12px; background: #fff; box-shadow: 0 8px 24px rgba(17,39,48,.06); }
  .eq-policy-highlight strong { display: block; color: #173c49; font-family: var(--font-display, Georgia, serif); font-size: 1.14rem; }
  .eq-policy-highlight span { display: block; margin-top: .4rem; color: #52636a; font-size: .86rem; line-height: 1.55; }
  .eq-policy-section { scroll-margin-top: 100px; padding-top: clamp(2.7rem, 5vw, 4.5rem); }
  .eq-policy-section__head { max-width: 780px; margin-bottom: 1.5rem; }
  .eq-policy-section__head span { color: #9d7227; font-size: .78rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
  .eq-policy-section__head h2 { margin: .3rem 0 .65rem; color: #173c49; font-family: var(--font-display, Georgia, serif); font-size: clamp(1.8rem, 3vw, 2.6rem); line-height: 1.2; }
  .eq-policy-section__head p { margin: 0; color: #52636a; line-height: 1.7; }
  .eq-policy-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
  .eq-policy-card { min-width: 0; padding: clamp(1.15rem, 2.5vw, 1.75rem); border: 1px solid #e5e2d9; border-radius: 12px; background: #fff; }
  .eq-policy-card h3 { margin: 0 0 .6rem; color: #173c49; font-family: var(--font-display, Georgia, serif); font-size: 1.18rem; }
  .eq-policy-card p, .eq-policy-card li { color: #46575d; font-size: .94rem; line-height: 1.7; }
  .eq-policy-card p { margin: .5rem 0 0; }
  .eq-policy-card ul { margin: .65rem 0 0; padding-left: 1.25rem; }
  .eq-policy-card li + li { margin-top: .4rem; }
  .eq-policy-card--tint { background: #eef5f3; border-color: #d5e5df; }
  .eq-policy-card--warm { background: #fcf5e8; border-color: #ecdcb8; }
  .eq-policy-callout { margin-top: 1rem; padding: 1rem 1.25rem; border-left: 4px solid #b9852e; border-radius: 0 8px 8px 0; background: #fff; color: #46575d; font-size: .94rem; line-height: 1.7; }
  .eq-policy-callout strong { color: #173c49; }
  .eq-policy-steps { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .8rem; counter-reset: policy-step; }
  .eq-policy-step { min-width: 0; padding: 1.15rem; border: 1px solid #e5e2d9; border-radius: 10px; background: #fff; }
  .eq-policy-step:before { counter-increment: policy-step; content: counter(policy-step, decimal-leading-zero); display: block; margin-bottom: .75rem; color: #b9852e; font-family: var(--font-display, Georgia, serif); font-size: 1.4rem; }
  .eq-policy-step h3 { margin: 0 0 .4rem; color: #173c49; font-size: .98rem; }
  .eq-policy-step p { margin: 0; color: #52636a; font-size: .85rem; line-height: 1.6; }
  .eq-policy-example { margin-top: 1rem; padding: 1.1rem 1.25rem; border: 1px dashed #c9b18a; border-radius: 10px; background: #fffcf6; color: #46575d; line-height: 1.65; }
  .eq-policy-example strong { color: #173c49; }
  .eq-policy-cta { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-top: clamp(2.8rem, 5vw, 4.5rem); padding: clamp(1.5rem, 4vw, 2.5rem); border-radius: 14px; background: #173c49; color: #fff; }
  .eq-policy-cta h2 { margin: 0 0 .35rem; color: #fff; font-family: var(--font-display, Georgia, serif); font-size: clamp(1.5rem, 3vw, 2rem); }
  .eq-policy-cta p { max-width: 590px; margin: 0; color: #d8e3e2; line-height: 1.6; }
  .eq-policy-cta a { display: inline-flex; align-items: center; min-height: 2.8rem; padding: .65rem 1rem; border-radius: 6px; background: #d9a83e; color: #173c49; font-weight: 700; text-decoration: none; }
  .eq-policy-cta a:hover, .eq-policy-cta a:focus-visible { background: #f0c662; }
  @media (max-width: 800px) {
    .eq-policy-steps { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .eq-policy-highlights { grid-template-columns: 1fr; }
    .eq-policy-highlight { padding: 1rem 1.15rem; }
  }
  @media (max-width: 600px) {
    .eq-policy-shell { padding: 0 1rem; }
    .eq-policy-grid, .eq-policy-steps { grid-template-columns: minmax(0, 1fr); }
    .eq-policy-quick { gap: .45rem; }
    .eq-policy-quick a { min-height: 2.4rem; padding: .4rem .75rem; font-size: .78rem; }
    .eq-policy-highlights { gap: .65rem; }
  }
</style>
@endpush

@section('content')
<main id="main-content" class="eq-policy">
  <div class="eq-container">
    <nav class="eq-breadcrumbs" aria-label="Breadcrumb">
      <ol><li><a href="{{ route('home') }}">Home</a></li><li aria-current="page">Delivery, Returns &amp; Refunds</li></ol>
    </nav>
  </div>

  <header class="eq-policy-hero">
    <div class="eq-container eq-policy-hero__inner">
      <span class="eq-policy-kicker">Rthquick customer care</span>
      <h1>Clear steps, from delivery to resolution.</h1>
      <p>Know what you pay, when you can request a change, and what happens before a return or refund is complete. Rthquick's central team coordinates these decisions for every brand on the marketplace.</p>
      <nav class="eq-policy-quick" aria-label="On this page">
        <a href="#delivery">Delivery</a>
        <a href="#cancellations">Cancellations</a>
        <a href="#returns">Returns</a>
        <a href="#refunds">Refunds</a>
        <a href="#need-help">Get help</a>
      </nav>
    </div>
  </header>

  <div class="eq-policy-shell">
    <div class="eq-policy-highlights eq-reveal" aria-label="Key policy points">
      <div class="eq-policy-highlight"><strong>One order, one delivery</strong><span>Delivery charge and tracking are handled for the whole order, including orders with items from different brands.</span></div>
      <div class="eq-policy-highlight"><strong>Check each product</strong><span>Return eligibility and the number of return days can differ by item. Your order keeps the rules shown at checkout.</span></div>
      <div class="eq-policy-highlight"><strong>Approval is not payment</strong><span>A return request, physical receipt or approved refund does not mean money has already been sent.</span></div>
    </div>

    <section class="eq-policy-section eq-reveal" id="delivery" aria-labelledby="policy-delivery-title">
      <div class="eq-policy-section__head"><span>01 / Delivery</span><h2 id="policy-delivery-title">What delivery costs</h2><p>The charge is calculated once for the combined order. Your final amount is shown at checkout before you place it.</p></div>
      <div class="eq-policy-grid">
        <div class="eq-policy-card"><h3>Inside Chattogram</h3><p>৳80 delivery for an order with a product subtotal below ৳3,000.</p></div>
        <div class="eq-policy-card"><h3>Outside Chattogram</h3><p>৳150 delivery for an order with a product subtotal below ৳3,000, including Dhaka and other districts.</p></div>
        <div class="eq-policy-card eq-policy-card--tint"><h3>Free-delivery threshold</h3><p>When the product subtotal reaches ৳3,000 or more, the standard delivery charge is ৳0. This threshold is checked before any coupon discount.</p></div>
        <div class="eq-policy-card"><h3>Updates and payment</h3><p>Rthquick tracks delivery at order level. Cash on Delivery (COD) is available; bKash online payment is not active yet. Delivery being marked complete does not by itself confirm that COD cash has been received.</p></div>
      </div>
      <div class="eq-policy-callout"><strong>Timing:</strong> Delivery timing depends on the address and fulfilment. We do not promise a fixed number of days here; contact support with your order number for a current update.</div>
    </section>

    <section class="eq-policy-section eq-reveal" id="cancellations" aria-labelledby="policy-cancel-title">
      <div class="eq-policy-section__head"><span>02 / Before fulfilment</span><h2 id="policy-cancel-title">Cancelling an order</h2><p>You can request cancellation online while an unpaid COD order is still pending or confirmed.</p></div>
      <div class="eq-policy-grid">
        <div class="eq-policy-card"><h3>What you do</h3><p>Open your order page, send a cancellation request and explain why. Sending a request does <strong>not</strong> cancel the order immediately.</p></div>
        <div class="eq-policy-card eq-policy-card--warm"><h3>What Rthquick does</h3><p>The central admin reviews and approves or declines it. If approved, the order is cancelled; if declined, the order remains active. For paid, dispatched or delivered orders, contact support instead of relying on this online cancellation path.</p></div>
      </div>
    </section>

    <section class="eq-policy-section eq-reveal" id="returns" aria-labelledby="policy-returns-title">
      <div class="eq-policy-section__head"><span>03 / After delivery</span><h2 id="policy-returns-title">Returning an item</h2><p>Returns are item-based, so you may ask about a particular item and quantity without treating the entire order as returned.</p></div>
      <div class="eq-policy-steps">
        <div class="eq-policy-step"><h3>Check eligibility</h3><p>Look for the item's returnable status and return window on the product and saved order details. The window starts from recorded delivery, not order placement.</p></div>
        <div class="eq-policy-step"><h3>Send a request</h3><p>For an eligible delivered order, request a return from its order page with the item quantity and reason. Do not send the item yet.</p></div>
        <div class="eq-policy-step"><h3>Wait for a decision</h3><p>Rthquick reviews the reason, authorizes or rejects the return, and confirms who pays the return courier. Authorization is not a refund.</p></div>
        <div class="eq-policy-step"><h3>Receipt and inspection</h3><p>After an authorized item arrives, Rthquick records receipt and inspects it. An accepted inspection is needed before a paid COD refund can be approved.</p></div>
      </div>
      <div class="eq-policy-grid" style="margin-top:1rem">
        <div class="eq-policy-card eq-policy-card--tint"><h3>Wrong or damaged item</h3><p>When Rthquick verifies that the wrong item was sent or the item is damaged/defective, Rthquick is responsible for return courier postage. If you paid the courier, keep the receipt: a verified amount can be added to the refund. A pickup is not automatic.</p></div>
        <div class="eq-policy-card"><h3>Fit, change of mind or other reasons</h3><p>The customer pays the return courier when Rthquick verifies one of these reasons. Your reported reason is reviewed; the confirmed reason and payer are shown after authorization.</p></div>
      </div>
      <div class="eq-policy-callout"><strong>Cannot request online?</strong> If the item is marked final sale, the delivery record or return window is unclear, or the online option is unavailable, contact support with your order number. These cases need manual review; a return is not automatically guaranteed. There is no separate automatic exchange flow—ask support about an exchange.</div>
    </section>

    <section class="eq-policy-section eq-reveal" id="refunds" aria-labelledby="policy-refunds-title">
      <div class="eq-policy-section__head"><span>04 / Money back</span><h2 id="policy-refunds-title">How COD refunds work</h2><p>A refund can be considered after Rthquick receives and accepts an eligible return on a delivered, paid COD order.</p></div>
      <div class="eq-policy-grid">
        <div class="eq-policy-card"><h3>Refund amount</h3><p>The returned item's original paid price is used, less its proportional share of any order-wide coupon discount. A partial return does not refund the original delivery charge. If all items are returned, Rthquick may include that charge once after review.</p></div>
        <div class="eq-policy-card"><h3>Return courier is separate</h3><p>Original delivery and return courier postage are different charges. When Rthquick is responsible for the return courier and you paid it, a verified receipt may support reimbursement. We do not reimburse the same receipt twice.</p></div>
        <div class="eq-policy-card eq-policy-card--warm"><h3>Recipient checked first</h3><p>Before recording an external refund payment, Rthquick verifies the intended recipient through the original order contact or in person. If an alternate recipient is involved, the reason is recorded.</p></div>
        <div class="eq-policy-card eq-policy-card--tint"><h3>Paid only after transfer</h3><p>Refund approval records the amount but does not send money. Rthquick sends the COD refund outside the website, then records the actual payment method and reference. Your order page distinguishes approved/pending payment from completed.</p></div>
      </div>
      <div class="eq-policy-example"><strong>Example:</strong> An order has ৳3,000 of products and a ৳300 order-wide discount. If an eligible ৳1,000 item is returned, its discount share is ৳100, so its product refund is ৳900. A partial return does not add the original delivery charge. Any eligible, receipt-backed return-courier reimbursement is considered separately.</div>
    </section>

    <section class="eq-policy-cta eq-reveal" id="need-help" aria-labelledby="policy-help-title">
      <div><h2 id="policy-help-title">Need help with a specific order?</h2><p>Tell us the order number and what happened. Our team can review delivery, cancellation, return eligibility or refund status.</p></div>
      <a href="{{ route('about') }}#contact-support">Contact Rthquick</a>
    </section>
  </div>
</main>
@endsection

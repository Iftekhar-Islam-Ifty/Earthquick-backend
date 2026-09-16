@extends('layouts.app')

@section('title', 'Secure Checkout — Earthquick by Nous Telos')
@section('body_class', 'eq-checkout-page')

@section('content')
<!-- Breadcrumb Bar -->
<nav class="eq-breadcrumb" aria-label="Breadcrumb" style="padding: 0.9rem 0; border-bottom: 1px solid var(--eq-line); background: var(--eq-white);">
  <div class="eq-container">
    <ol class="eq-breadcrumb__list" style="display: flex; align-items: center; gap: 0.5rem; list-style: none; margin: 0; padding: 0; font-size: 0.84rem; color: var(--eq-charcoal-soft);">
      <li><a href="{{ route('home') }}" style="color: inherit; text-decoration: none;">Home</a></li>
      <li style="color: var(--eq-line-dark);">&rsaquo;</li>
      <li><a href="{{ route('category.show', 'women') }}" style="color: inherit; text-decoration: none;">Shop</a></li>
      <li style="color: var(--eq-line-dark);">&rsaquo;</li>
      <li style="color: var(--eq-charcoal); font-weight: 500;" aria-current="page">Checkout</li>
    </ol>
  </div>
</nav>

<!-- Checkout Hero Header -->
<section class="eq-checkout-hero">
  <div class="eq-container">
    <h1 class="eq-checkout-hero__title" id="checkout-main-title">Secure Checkout</h1>
    <p class="eq-checkout-hero__desc">Please provide your delivery information to finalize your Nous Telos order.</p>
  </div>
</section>

<!-- Main Checkout Section -->
<main class="eq-container" id="main-content">

  @if(count($cart) === 0)
    <!-- STATE 1: EMPTY CART VIEW -->
    <div class="eq-checkout-empty-view" id="checkout-empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <path d="M16 10a4 4 0 0 1-8 0"></path>
      </svg>
      <h2>Your shopping bag is empty</h2>
      <p>You haven't added any products to your bag yet. Discover our artisanal sarees, tailored three-piece sets, and curated bags.</p>
      <a href="{{ route('category.show', 'women') }}" class="eq-btn eq-btn--primary" style="padding: 0.85rem 2rem; display: inline-block; text-decoration: none;">
        Explore Collections &rarr;
      </a>
    </div>
  @else
    <!-- Validation Errors Alert -->
    @if ($errors->any())
      <div style="background: #fdf2f2; color: #9b1c1c; padding: 1.25rem; border-radius: 6px; margin: 1.5rem 0; border: 1px solid #f8b4b4;">
        <strong style="display: block; font-size: 0.95rem; margin-bottom: 0.5rem;">Please review and correct the following details:</strong>
        <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.88rem; line-height: 1.5;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- STATE 2: ACTIVE CHECKOUT FORM -->
    <div class="eq-checkout-layout" id="checkout-active-view">
      
      <!-- LEFT COLUMN: CUSTOMER DETAILS, DELIVERY & PAYMENT -->
      <form class="eq-checkout-form" id="checkout-order-form" method="POST" action="{{ route('checkout.store') }}" novalidate>
        @csrf
        
        <!-- CARD 1: CONTACT & CUSTOMER INFORMATION -->
        <div class="eq-checkout-card">
          <div class="eq-checkout-card__header">
            <span class="eq-checkout-card__step-num">1</span>
            <h2 class="eq-checkout-card__title">Customer Contact Details</h2>
          </div>
          
          <div class="eq-form-grid">
            <div class="eq-form-group eq-form-group--full">
              <label for="cust-name" class="eq-form-label">Full Name <span class="req">*</span></label>
              <input type="text" id="cust-name" name="customer_name" class="eq-form-input" value="{{ old('customer_name') }}" placeholder="e.g. Iftekhar Islam Ifty" autocomplete="name" required />
            </div>

            <div class="eq-form-group">
              <label for="cust-phone" class="eq-form-label">Mobile Phone Number <span class="req">*</span></label>
              <input type="tel" id="cust-phone" name="customer_phone" class="eq-form-input" value="{{ old('customer_phone') }}" placeholder="e.g. 01793123456" autocomplete="tel" required />
              <small style="font-size: 0.76rem; color: var(--eq-charcoal-soft); margin-top: 2px;">We'll call this number to confirm your order.</small>
            </div>

            <div class="eq-form-group">
              <label for="cust-email" class="eq-form-label">Email Address <span style="color: var(--eq-charcoal-soft); font-weight: normal;">(Optional)</span></label>
              <input type="email" id="cust-email" name="customer_email" class="eq-form-input" value="{{ old('customer_email') }}" placeholder="e.g. hello@earthquick.com" autocomplete="email" />
              <small style="font-size: 0.76rem; color: var(--eq-charcoal-soft); margin-top: 2px;">Order invoice &amp; tracking link will be sent here.</small>
            </div>
          </div>
        </div>

        <!-- CARD 2: DELIVERY DESTINATION & SHIPPING -->
        <div class="eq-checkout-card">
          <div class="eq-checkout-card__header">
            <span class="eq-checkout-card__step-num">2</span>
            <h2 class="eq-checkout-card__title">Delivery Address &amp; Shipping</h2>
          </div>

          <!-- Delivery Zone Selection -->
          <div class="eq-form-group" style="margin-bottom: 1.25rem;">
            <label class="eq-form-label" style="margin-bottom: 0.5rem;">Select Delivery Zone <span class="req">*</span></label>
            <div class="eq-delivery-methods">
              
              <!-- Option: Inside Chittagong -->
              <label class="eq-method-card {{ old('delivery_zone', 'inside_ctg') === 'inside_ctg' ? 'is-selected' : '' }}" id="card-shipping-inside">
                <input type="radio" name="delivery_zone" value="inside_ctg" {{ old('delivery_zone', 'inside_ctg') === 'inside_ctg' ? 'checked' : '' }} onchange="selectShippingZone('inside_ctg', 80)" />
                <div class="eq-method-card__content">
                  <div class="eq-method-card__name">
                    <span>Inside Chattogram (Ctg)</span>
                    <span class="eq-method-card__price" id="shipping-rate-inside">৳80</span>
                  </div>
                  <div class="eq-method-card__desc">Same / Next day delivery within Chattogram City (Within 48 hours)</div>
                </div>
              </label>

              <!-- Option: Outside Chittagong -->
              <label class="eq-method-card {{ old('delivery_zone') === 'outside_ctg' ? 'is-selected' : '' }}" id="card-shipping-outside">
                <input type="radio" name="delivery_zone" value="outside_ctg" {{ old('delivery_zone') === 'outside_ctg' ? 'checked' : '' }} onchange="selectShippingZone('outside_ctg', 150)" />
                <div class="eq-method-card__content">
                  <div class="eq-method-card__name">
                    <span>Outside Chattogram (Nationwide)</span>
                    <span class="eq-method-card__price" id="shipping-rate-outside">৳150</span>
                  </div>
                  <div class="eq-method-card__desc">Dhaka &amp; All 64 Districts via Courier in 2–4 days</div>
                </div>
              </label>

            </div>
          </div>

          <!-- Address Fields -->
          <div class="eq-form-grid">
            <div class="eq-form-group">
              <label for="cust-city" class="eq-form-label">District / City <span class="req">*</span></label>
              <select id="cust-city" name="district" class="eq-form-select" autocomplete="address-level1" required>
                <option value="Chattogram" {{ old('district', 'Chattogram') === 'Chattogram' ? 'selected' : '' }}>Chattogram (Chittagong)</option>
                <option value="Dhaka" {{ old('district') === 'Dhaka' ? 'selected' : '' }}>Dhaka</option>
                <option value="Sylhet" {{ old('district') === 'Sylhet' ? 'selected' : '' }}>Sylhet</option>
                <option value="Rajshahi" {{ old('district') === 'Rajshahi' ? 'selected' : '' }}>Rajshahi</option>
                <option value="Khulna" {{ old('district') === 'Khulna' ? 'selected' : '' }}>Khulna</option>
                <option value="Barishal" {{ old('district') === 'Barishal' ? 'selected' : '' }}>Barishal</option>
                <option value="Rangpur" {{ old('district') === 'Rangpur' ? 'selected' : '' }}>Rangpur</option>
                <option value="Mymensingh" {{ old('district') === 'Mymensingh' ? 'selected' : '' }}>Mymensingh</option>
                <option value="Comilla" {{ old('district') === 'Comilla' ? 'selected' : '' }}>Comilla</option>
                <option value="Cox's Bazar" {{ old('district') === 'Cox\'s Bazar' ? 'selected' : '' }}>Cox's Bazar</option>
                <option value="Gazipur" {{ old('district') === 'Gazipur' ? 'selected' : '' }}>Gazipur</option>
                <option value="Narayanganj" {{ old('district') === 'Narayanganj' ? 'selected' : '' }}>Narayanganj</option>
                <option value="Bogra" {{ old('district') === 'Bogra' ? 'selected' : '' }}>Bogra</option>
                <option value="Other" {{ old('district') === 'Other' ? 'selected' : '' }}>Other District</option>
              </select>
            </div>

            <div class="eq-form-group">
              <label for="cust-area" class="eq-form-label">Area / Thana <span class="req">*</span></label>
              <input type="text" id="cust-area" name="area" class="eq-form-input" value="{{ old('area') }}" placeholder="e.g. GEC, Nasirabad, Agrabad, Panchlaish" autocomplete="address-level2" required />
            </div>

            <div class="eq-form-group eq-form-group--full">
              <label for="cust-address" class="eq-form-label">Full Street Address / House / Road <span class="req">*</span></label>
              <textarea id="cust-address" name="address" class="eq-form-textarea" placeholder="e.g. Holding 45, Road 2, O.R. Nizam Road, Flat 4A" autocomplete="street-address" required>{{ old('address') }}</textarea>
            </div>

            <div class="eq-form-group eq-form-group--full">
              <label for="cust-notes" class="eq-form-label">Special Delivery Instructions <span style="color: var(--eq-charcoal-soft); font-weight: normal;">(Optional)</span></label>
              <input type="text" id="cust-notes" name="order_notes" class="eq-form-input" value="{{ old('order_notes') }}" placeholder="e.g. Please call before arrival or leave at reception" />
            </div>
          </div>

        </div>

        <!-- CARD 3: PAYMENT METHOD -->
        <div class="eq-checkout-card">
          <div class="eq-checkout-card__header">
            <span class="eq-checkout-card__step-num">3</span>
            <h2 class="eq-checkout-card__title">Payment Method</h2>
          </div>

          <div class="eq-payment-methods">

            <!-- COD (Cash on Delivery) -->
            <div class="eq-payment-card {{ old('payment_method', 'cod') === 'cod' ? 'is-selected' : '' }}" id="payment-card-cod">
              <label class="eq-payment-card__header" for="pay-cod">
                <input type="radio" id="pay-cod" name="payment_method" value="cod" {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }} onchange="selectPaymentMethod('cod')" />
                <span class="eq-payment-card__title">Cash on Delivery (COD)</span>
                <span class="eq-payment-card__badge">Most Popular</span>
              </label>
              <div class="eq-payment-card__body">
                Pay safely in cash to our courier partner when you inspect and receive your parcel at your doorstep.
              </div>
            </div>

            <!-- bKash / Mobile Banking -->
            <div class="eq-payment-card {{ old('payment_method') === 'bkash' ? 'is-selected' : '' }}" id="payment-card-bkash">
              <label class="eq-payment-card__header" for="pay-bkash">
                <input type="radio" id="pay-bkash" name="payment_method" value="bkash" {{ old('payment_method') === 'bkash' ? 'checked' : '' }} onchange="selectPaymentMethod('bkash')" />
                <span class="eq-payment-card__title">bKash / Nagad / Rocket (Mobile Banking)</span>
                <span class="eq-payment-card__badge" style="background-color: rgba(201, 150, 47, 0.15); color: var(--eq-gold-dark);">Direct</span>
              </label>
              <div class="eq-payment-card__body" id="bkash-details-box" style="{{ old('payment_method') === 'bkash' ? 'display: block;' : 'display: none;' }}">
                <p style="margin-top: 0; margin-bottom: 0.5rem;">
                  Please send the order amount to our Merchant bKash Number: <strong>01876-543210</strong>. Our support desk will confirm the payment over the phone.
                </p>
              </div>
            </div>

          </div>

          <!-- Terms acceptance -->
          <div style="margin-top: 1.5rem; display: flex; align-items: flex-start; gap: 0.65rem; font-size: 0.84rem; color: var(--eq-charcoal-soft);">
            <input type="checkbox" id="accept-terms" checked required style="accent-color: var(--eq-gold); margin-top: 0.2rem;" />
            <label for="accept-terms">
              I agree to the <a href="{{ route('about') }}" style="color: var(--eq-charcoal); text-decoration: underline;">Terms &amp; Conditions</a> and <a href="{{ route('about') }}" style="color: var(--eq-charcoal); text-decoration: underline;">Privacy Policy</a> of Earthquick / Nous Telos.
            </label>
          </div>

        </div>

        <!-- Mobile Submit Button -->
        <div class="d-block d-lg-none" style="margin-bottom: 2rem;">
          <button type="submit" class="eq-btn-place-order" id="btn-submit-order-mobile">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <span>Confirm &amp; Place Order &bull; <span class="order-total-display">৳{{ number_format(max(0, $subtotal - ($discount ?? 0)) + 80) }}</span></span>
          </button>
        </div>

      </form>

      <!-- RIGHT COLUMN: STICKY ORDER SUMMARY -->
      <aside class="eq-order-summary-panel" aria-label="Order Summary">
        <div class="eq-order-summary__title">
          <span>Order Summary</span>
          <span style="font-size: 0.86rem; font-weight: normal; color: var(--eq-charcoal-soft);" id="summary-items-count">{{ $count }} {{ Str::plural('item', $count) }}</span>
        </div>

        <!-- Items Listing -->
        <div class="eq-order-summary__items" id="checkout-items-list">
          @foreach($cart as $item)
            <div class="eq-summary-item">
              <img class="eq-summary-item__img" src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" />
              <div class="eq-summary-item__details">
                <div class="eq-summary-item__name">{{ $item['name'] }}</div>
                <div class="eq-summary-item__meta">{{ $item['size'] }} &bull; Qty: {{ $item['quantity'] }}</div>
              </div>
              <div class="eq-summary-item__price">৳{{ number_format($item['price'] * $item['quantity']) }}</div>
            </div>
          @endforeach
        </div>

        <!-- Promo / Coupon Code Entry -->
        <div class="eq-checkout-coupon-box" style="margin-top: 1.25rem; margin-bottom: 1.25rem; padding: 0.85rem 1rem; background: #ffffff; border: 1px solid var(--eq-line); border-radius: 8px;">
          @if(!empty($coupon) && ($discount ?? 0) > 0)
            <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(40, 167, 69, 0.08); border: 1px dashed #28a745; border-radius: 6px; padding: 0.55rem 0.8rem;">
              <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="background: #28a745; color: #fff; font-size: 0.74rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; letter-spacing: 0.5px;">{{ $coupon['code'] }}</span>
                <span style="font-size: 0.82rem; color: #1e7e34; font-weight: 600;">-৳{{ number_format($discount) }} Applied</span>
              </div>
              <form action="{{ route('cart.coupon.remove') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #dc3545; font-size: 0.78rem; font-weight: 600; cursor: pointer; text-decoration: underline; padding: 0;">Remove</button>
              </form>
            </div>
          @else
            <form action="{{ route('cart.coupon.apply') }}" method="POST" style="display: flex; gap: 0.45rem; margin: 0;">
              @csrf
              <input type="text" name="code" placeholder="Promo code (e.g. EID2026)" value="{{ old('code') }}" style="flex: 1; min-height: 38px; padding: 0.4rem 0.75rem; font-size: 0.82rem; text-transform: uppercase; border: 1px solid #d1d5db; border-radius: 6px; font-family: monospace;" required />
              <button type="submit" style="min-height: 38px; padding: 0 1.1rem; background: var(--eq-charcoal, #1a1a1a); color: #ffffff; font-size: 0.82rem; font-weight: 600; border: none; border-radius: 6px; cursor: pointer; transition: background 0.2s ease;">Apply</button>
            </form>
            @if(session('error'))
              <p style="color: #dc3545; font-size: 0.78rem; margin: 0.4rem 0 0 0;">{{ session('error') }}</p>
            @endif
            @if(session('success'))
              <p style="color: #28a745; font-size: 0.78rem; margin: 0.4rem 0 0 0;">{{ session('success') }}</p>
            @endif
          @endif
        </div>

        <!-- Price Breakdown -->
        <div class="eq-price-breakdown">
          <div class="eq-price-row">
            <span>Subtotal</span>
            <span id="price-subtotal">৳{{ number_format($subtotal) }}</span>
          </div>
          @if(!empty($coupon) && ($discount ?? 0) > 0)
            <div class="eq-price-row" id="price-discount-row" style="color: #28a745; font-weight: 600;">
              <span>Promo Discount ({{ $coupon['code'] }})</span>
              <span id="price-discount">-৳{{ number_format($discount) }}</span>
            </div>
          @endif
          <div class="eq-price-row">
            <span>Delivery Fee</span>
            <span id="price-shipping">৳80</span>
          </div>
          <div class="eq-price-row eq-price-row--total">
            <span>Grand Total</span>
            <span class="eq-total-amount" id="price-grand-total">৳{{ number_format(max(0, $subtotal - ($discount ?? 0)) + 80) }}</span>
          </div>
        </div>

        <!-- Place Order Button (Desktop) -->
        <button type="button" class="eq-btn-place-order d-none d-lg-flex" id="btn-submit-order-desktop" onclick="document.getElementById('checkout-order-form').submit();">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
          <span>Confirm &amp; Place Order</span>
        </button>

        <div class="eq-checkout-trust-badge" style="margin-top: 1.25rem;">
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
          </svg>
          <span>100% Authentic Handcrafted Heritage &bull; Express Delivery</span>
        </div>

      </aside>

    </div>
  @endif

</main>
@endsection

@push('scripts')
<script>
  const checkoutSubtotal = {{ (float) $subtotal }};
  const checkoutDiscount = {{ (float) ($discount ?? 0) }};

  function selectShippingZone(zone, fee) {
    const cardInside = document.getElementById('card-shipping-inside');
    const cardOutside = document.getElementById('card-shipping-outside');
    const shippingEl = document.getElementById('price-shipping');
    const grandTotalEl = document.getElementById('price-grand-total');
    const totalDisplays = document.querySelectorAll('.order-total-display');

    if (zone === 'inside_ctg') {
      if (cardInside) cardInside.classList.add('is-selected');
      if (cardOutside) cardOutside.classList.remove('is-selected');
    } else {
      if (cardOutside) cardOutside.classList.add('is-selected');
      if (cardInside) cardInside.classList.remove('is-selected');
    }

    const total = Math.max(0, checkoutSubtotal - checkoutDiscount) + fee;
    const formattedTotal = '৳' + total.toLocaleString('en-IN');

    if (shippingEl) shippingEl.textContent = '৳' + fee;
    if (grandTotalEl) grandTotalEl.textContent = formattedTotal;
    totalDisplays.forEach(el => el.textContent = formattedTotal);
  }

  function selectPaymentMethod(method) {
    const cardCod = document.getElementById('payment-card-cod');
    const cardBkash = document.getElementById('payment-card-bkash');
    const bkashBox = document.getElementById('bkash-details-box');

    if (method === 'cod') {
      if (cardCod) cardCod.classList.add('is-selected');
      if (cardBkash) cardBkash.classList.remove('is-selected');
      if (bkashBox) bkashBox.style.display = 'none';
    } else if (method === 'bkash') {
      if (cardBkash) cardBkash.classList.add('is-selected');
      if (cardCod) cardCod.classList.remove('is-selected');
      if (bkashBox) bkashBox.style.display = 'block';
    }
  }

  // Handle District Auto-Switch
  const citySelect = document.getElementById('cust-city');
  if (citySelect) {
    citySelect.addEventListener('change', function() {
      const selected = this.value;
      const radioInside = document.querySelector('input[name="delivery_zone"][value="inside_ctg"]');
      const radioOutside = document.querySelector('input[name="delivery_zone"][value="outside_ctg"]');
      if (selected === 'Chattogram') {
        if (radioInside) {
          radioInside.checked = true;
          selectShippingZone('inside_ctg', 80);
        }
      } else {
        if (radioOutside) {
          radioOutside.checked = true;
          selectShippingZone('outside_ctg', 150);
        }
      }
    });
  }
</script>
@endpush

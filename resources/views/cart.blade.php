@extends('layouts.app')

@section('title', 'Shopping Bag — Rthquick')
@section('body_class', 'eq-cart-page')

@section('content')
<main id="main-content" class="eq-cart-page__main">
  <div class="eq-container">
    <div class="eq-cart-page__content">
      <h1 class="eq-cart-page__title">Your Shopping Bag</h1>

      @if(count($cart) > 0)
        <div class="eq-cart-page__layout">
          <!-- Cart Items List -->
          <div>
            @foreach($cart as $key => $item)
              <div class="eq-cart-page__item">
                <img class="eq-cart-page__image" src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" />
                <div class="eq-cart-page__item-info">
                  <h3 style="font-size: 1.05rem; margin-bottom: 0.35rem;">
                    <a href="{{ route('product.show', $item['slug']) }}" style="color: var(--eq-charcoal); text-decoration: none;">
                      {{ $item['name'] }}
                    </a>
                  </h3>
                  @if(!empty($item['brand_name']))<p style="font-size:.82rem;color:var(--eq-charcoal-muted);margin:.15rem 0;">Brand: {{ $item['brand_name'] }}</p>@endif
                  @if(!empty($item['vendor_name']))<p style="font-size:.82rem;color:var(--eq-charcoal-muted);margin:.15rem 0;">Sold by: {{ $item['vendor_name'] }}</p>@endif
                  @if(!empty($item['warranty_provider_name']))<p style="font-size:.82rem;color:var(--eq-charcoal-muted);margin:.15rem 0;">Warranty provider: {{ $item['warranty_provider_name'] }}</p>@endif
                  <p style="color: var(--eq-charcoal-muted); font-size: 0.85rem; margin-bottom: 0.45rem;">
                    @if(($item['size'] ?? 'Standard') !== 'Standard') {{ $item['size'] }} &bull; @endif Qty: {{ $item['quantity'] }}
                  </p>
                  <p style="font-weight: 600; color: var(--eq-charcoal);">
                    ৳{{ number_format($item['price']) }} &times; {{ $item['quantity'] }} = ৳{{ number_format($item['price'] * $item['quantity']) }}
                  </p>
                </div>
              </div>
            @endforeach
          </div>

          <!-- Order Summary Card -->
          <div class="eq-cart-page__summary">
            <h2 style="font-size: 1.3rem; margin-bottom: 1.25rem;">Order Summary</h2>

            <!-- Promo / Coupon Box -->
            <div style="margin-bottom: 1.25rem;">
              @if(!empty($coupon) && ($discount ?? 0) > 0)
                <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(40, 167, 69, 0.08); border: 1px dashed #28a745; border-radius: 6px; padding: 0.55rem 0.8rem;">
                  <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="background: #28a745; color: #fff; font-size: 0.74rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; letter-spacing: 0.5px;">{{ $coupon['code'] }}</span>
                    <span style="font-size: 0.82rem; color: #1e7e34; font-weight: 600;">-৳{{ number_format($discount) }}</span>
                  </div>
                  <form action="{{ route('cart.coupon.remove') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #dc3545; font-size: 0.78rem; font-weight: 600; cursor: pointer; text-decoration: underline; padding: 0;">Remove</button>
                  </form>
                </div>
              @else
                <form class="eq-cart-page__coupon-form" action="{{ route('cart.coupon.apply') }}" method="POST">
                  @csrf
                  <input type="text" name="code" placeholder="Promo code" value="{{ old('code') }}" style="flex: 1; min-height: 38px; padding: 0.4rem 0.75rem; font-size: 0.82rem; text-transform: uppercase; border: 1px solid #d1d5db; border-radius: 6px; font-family: monospace;" required />
                  <button type="submit" style="min-height: 38px; padding: 0 1rem; background: var(--eq-charcoal, #1a1a1a); color: #ffffff; font-size: 0.82rem; font-weight: 600; border: none; border-radius: 6px; cursor: pointer;">Apply</button>
                </form>
                @if(session('error'))
                  <p style="color: #dc3545; font-size: 0.78rem; margin: 0.4rem 0 0 0;">{{ session('error') }}</p>
                @endif
                @if(session('success'))
                  <p style="color: #28a745; font-size: 0.78rem; margin: 0.4rem 0 0 0;">{{ session('success') }}</p>
                @endif
              @endif
            </div>

            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.95rem;">
              <span>Subtotal</span>
              <strong>৳{{ number_format($subtotal) }}</strong>
            </div>

            @if(!empty($coupon) && ($discount ?? 0) > 0)
              <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.95rem; color: #28a745; font-weight: 600;">
                <span>Promo Discount ({{ $coupon['code'] }})</span>
                <span>-৳{{ number_format($discount) }}</span>
              </div>
            @endif

            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.95rem;">
              <span>Estimated Total</span>
              <strong>৳{{ number_format($total ?? max(0, $subtotal - ($discount ?? 0))) }}</strong>
            </div>

            <div style="display: flex; justify-content: space-between; margin-bottom: 1.25rem; font-size: 0.88rem; color: var(--eq-charcoal-soft);">
              <span>Delivery Charges</span>
              <span>Calculated at checkout</span>
            </div>
            <hr style="border: 0; border-top: 1px solid var(--eq-line); margin-bottom: 1.25rem;" />
            <a href="{{ route('checkout.index') }}" class="eq-btn eq-btn--primary eq-btn--pill" style="width: 100%; justify-content: center; text-decoration: none;">
              Proceed to Checkout &rarr;
            </a>
            <p style="font-size: 0.78rem; text-align: center; color: var(--eq-charcoal-muted); margin-top: 1rem;">
              Secure Cash on Delivery &amp; doorstep delivery across Bangladesh.
            </p>
          </div>
        </div>
      @else
        <div style="text-align: center; padding: 4rem 1rem; background: var(--eq-cream-deep); border-radius: var(--radius-md);">
          <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem; color: var(--eq-charcoal-muted);">
            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
            <path d="M3 6h18"></path>
            <path d="M16 10a4 4 0 0 1-8 0"></path>
          </svg>
          <h2 style="font-family: var(--font-display); font-size: 1.5rem; margin-bottom: 0.5rem;">Your bag is empty</h2>
          <p style="color: var(--eq-charcoal-muted); margin-bottom: 1.5rem;">Browse products from independent stores across the Rthquick marketplace.</p>
          <a href="{{ route('stores.index') }}" class="eq-btn eq-btn--primary eq-btn--pill">
            Explore Stores
          </a>
        </div>
      @endif
    </div>
  </div>
</main>
@endsection

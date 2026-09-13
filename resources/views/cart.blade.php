@extends('layouts.app')

@section('title', 'Shopping Bag — Earthquick / Nous Telos')
@section('body_class', 'eq-cart-page')

@section('content')
<main id="main-content" style="padding: 3rem 0;">
  <div class="eq-container">
    <div style="max-width: 900px; margin: 0 auto;">
      <h1 style="font-family: var(--font-display); font-size: 2.2rem; margin-bottom: 2rem;">Your Shopping Bag</h1>

      @if(count($cart) > 0)
        <div style="display: grid; grid-template-columns: 1fr 340px; gap: 2.5rem; align-items: start;">
          <!-- Cart Items List -->
          <div>
            @foreach($cart as $key => $item)
              <div style="display: flex; gap: 1.5rem; padding: 1.5rem 0; border-bottom: 1px solid var(--eq-line); align-items: center;">
                <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" style="width: 90px; height: 110px; object-fit: cover; border-radius: 4px;" />
                <div style="flex: 1;">
                  <h3 style="font-size: 1.05rem; margin-bottom: 0.35rem;">
                    <a href="{{ route('product.show', $item['slug']) }}" style="color: var(--eq-charcoal); text-decoration: none;">
                      {{ $item['name'] }}
                    </a>
                  </h3>
                  <p style="color: var(--eq-charcoal-muted); font-size: 0.85rem; margin-bottom: 0.45rem;">
                    Size: {{ $item['size'] }} &bull; Qty: {{ $item['quantity'] }}
                  </p>
                  <p style="font-weight: 600; color: var(--eq-charcoal);">
                    ৳{{ number_format($item['price']) }} &times; {{ $item['quantity'] }} = ৳{{ number_format($item['price'] * $item['quantity']) }}
                  </p>
                </div>
              </div>
            @endforeach
          </div>

          <!-- Order Summary Card -->
          <div style="background: var(--eq-cream); padding: 2rem; border-radius: var(--radius-sm); border: 1px solid var(--eq-line);">
            <h2 style="font-size: 1.3rem; margin-bottom: 1.25rem;">Order Summary</h2>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.95rem;">
              <span>Subtotal</span>
              <strong>৳{{ number_format($total) }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 1.25rem; font-size: 0.88rem; color: var(--eq-charcoal-soft);">
              <span>Delivery Charges</span>
              <span>Calculated at checkout<br>(৳80 CTG / ৳150 Outside)</span>
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
          <p style="color: var(--eq-charcoal-muted); margin-bottom: 1.5rem;">Discover our authentic handwoven sarees and tailored artisanal sets.</p>
          <a href="{{ route('category.show', 'women') }}" class="eq-btn eq-btn--primary eq-btn--pill">
            Explore Collections
          </a>
        </div>
      @endif
    </div>
  </div>
</main>
@endsection


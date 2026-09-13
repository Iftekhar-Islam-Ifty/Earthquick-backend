@extends('admin.layout')

@section('title', 'Order #' . $order->order_number . ' — Earthquick Admin')
@section('page_title', 'Order Details #' . $order->order_number)

@section('content')

  <!-- Back Link & Header Bar -->
  <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
    <a href="{{ route('admin.orders') }}" class="eq-admin-btn eq-admin-btn--outline" style="gap: 0.5rem;">
      &larr; Back to All Orders
    </a>

    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <span style="font-size: 0.85rem; color: var(--eq-charcoal-soft);">
        Current Lifecycle Status:
      </span>
      <span class="eq-status-badge eq-status-badge--{{ $order->status }}" style="font-size: 0.82rem; padding: 0.35rem 0.85rem;">
        {{ str_replace('_', ' ', $order->status) }}
      </span>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.75rem;">
    
    <!-- LEFT COLUMN: ITEMS & FINANCIAL SUMMARY -->
    <div>
      <!-- Order Items Card -->
      <section class="eq-admin-card">
        <div class="eq-admin-card__header">
          <h2 class="eq-admin-card__title">Purchased Creations ({{ $order->items->count() }} items)</h2>
        </div>

        <div class="eq-admin-table-wrap">
          <table class="eq-admin-table">
            <thead>
              <tr>
                <th>Piece</th>
                <th>Unit Price</th>
                <th style="text-align: center;">Quantity</th>
                <th style="text-align: right;">Line Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach($order->items as $item)
                <tr>
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                      @if($item->product_image)
                        <img src="{{ asset($item->product_image) }}" alt="{{ $item->product_name }}" style="width: 44px; height: 56px; object-fit: cover; border-radius: 4px; border: 1px solid var(--eq-line);" />
                      @endif
                      <div>
                        <div style="font-weight: 500; color: var(--eq-navy);">{{ $item->product_name }}</div>
                        @if($item->size)
                          <div style="font-size: 0.76rem; color: var(--eq-charcoal-soft);">Size: {{ $item->size }}</div>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td style="font-size: 0.88rem;">৳{{ number_format($item->unit_price) }}</td>
                  <td style="text-align: center; font-weight: 600;">&times; {{ $item->quantity }}</td>
                  <td style="text-align: right; font-weight: 600; color: var(--eq-charcoal);">
                    ৳{{ number_format($item->total_price) }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- Financial Summary Breakdown -->
        <div style="border-top: 1px solid var(--eq-line); padding-top: 1.25rem; margin-top: 1.25rem;">
          <div style="max-width: 320px; margin-left: auto; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.88rem;">
            <div style="display: flex; justify-content: space-between; color: var(--eq-charcoal-soft);">
              <span>Subtotal:</span>
              <span>৳{{ number_format($order->subtotal) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; color: var(--eq-charcoal-soft);">
              <span>Delivery Charge ({{ $order->delivery_zone === 'inside_ctg' ? 'Inside Chattogram' : 'Outside Chattogram' }}):</span>
              <span>৳{{ number_format($order->delivery_fee) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 600; color: var(--eq-navy); border-top: 2px solid var(--eq-navy); padding-top: 0.65rem; margin-top: 0.35rem;">
              <span>Total Payable:</span>
              <span style="color: var(--eq-gold-dark);">৳{{ number_format($order->total) }}</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Special Order Notes -->
      @if($order->order_notes)
        <section class="eq-admin-card">
          <div class="eq-admin-card__header">
            <h3 class="eq-admin-card__title" style="font-size: 1rem;">Customer Delivery Instructions / Notes</h3>
          </div>
          <p style="font-size: 0.88rem; color: var(--eq-charcoal); font-style: italic; background: var(--eq-cream); padding: 0.85rem 1rem; border-radius: 4px; border-left: 3px solid var(--eq-gold);">
            &ldquo;{{ $order->order_notes }}&rdquo;
          </p>
        </section>
      @endif
    </div>

    <!-- RIGHT COLUMN: STATUS UPDATE & CLIENT INFO -->
    <div>
      
      <!-- Lifecycle Status Form Card -->
      <section class="eq-admin-card" style="border: 1px solid var(--eq-gold);">
        <div class="eq-admin-card__header">
          <h3 class="eq-admin-card__title" style="font-size: 1rem; color: var(--eq-gold-dark);">
            Update Delivery Status
          </h3>
        </div>

        <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}">
          @csrf
          <div style="margin-bottom: 1.25rem;">
            <label for="order-status-select" style="display: block; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.45rem; color: var(--eq-charcoal);">
              Select Lifecycle Stage:
            </label>
            <select name="status" id="order-status-select" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-family: var(--font-body); font-size: 0.9rem; background: var(--eq-white); color: var(--eq-charcoal); outline: none;">
              <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending (Received)</option>
              <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed (Artisan Verified)</option>
              <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing (Packaging in Atelier)</option>
              <option value="in_transit" {{ $order->status === 'in_transit' ? 'selected' : '' }}>In Transit (Dispatched with Courier)</option>
              <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered (Complete)</option>
              <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
          </div>

          <p style="font-size: 0.78rem; color: var(--eq-charcoal-soft); margin-bottom: 1.25rem; line-height: 1.45;">
            &bull; Updating status immediately refreshes the parcel tracking progress bar on the customer's personal dashboard (`/account`).
          </p>

          <button type="submit" class="eq-admin-btn eq-admin-btn--gold" style="width: 100%; justify-content: center; padding: 0.65rem;">
            Update Order Status
          </button>
        </form>
      </section>

      <!-- Customer Details Card -->
      <section class="eq-admin-card">
        <div class="eq-admin-card__header">
          <h3 class="eq-admin-card__title" style="font-size: 1rem;">Customer &amp; Shipping Destination</h3>
        </div>

        <div style="font-size: 0.86rem; display: flex; flex-direction: column; gap: 0.85rem;">
          <div>
            <span style="display: block; font-size: 0.72rem; text-transform: uppercase; color: var(--eq-charcoal-soft); letter-spacing: 0.05em;">Customer Name</span>
            <strong style="color: var(--eq-navy); font-size: 0.95rem;">{{ $order->customer_name }}</strong>
            @if($order->user_id)
              <span style="display: inline-block; font-size: 0.68rem; background: #e0f2fe; color: #0369a1; padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.35rem;">
                Registered Account #{{ $order->user_id }}
              </span>
            @else
              <span style="display: inline-block; font-size: 0.68rem; background: var(--eq-cream-deep); color: var(--eq-charcoal-soft); padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.35rem;">
                Guest Checkout
              </span>
            @endif
          </div>

          <div>
            <span style="display: block; font-size: 0.72rem; text-transform: uppercase; color: var(--eq-charcoal-soft); letter-spacing: 0.05em;">Phone Number</span>
            <a href="tel:{{ $order->customer_phone }}" style="color: var(--eq-navy); font-weight: 500; text-decoration: none;">
              {{ $order->customer_phone }}
            </a>
          </div>

          @if($order->customer_email)
            <div>
              <span style="display: block; font-size: 0.72rem; text-transform: uppercase; color: var(--eq-charcoal-soft); letter-spacing: 0.05em;">Email Address</span>
              <a href="mailto:{{ $order->customer_email }}" style="color: var(--eq-navy); text-decoration: none;">
                {{ $order->customer_email }}
              </a>
            </div>
          @endif

          <div style="border-top: 1px solid var(--eq-line); padding-top: 0.75rem;">
            <span style="display: block; font-size: 0.72rem; text-transform: uppercase; color: var(--eq-charcoal-soft); letter-spacing: 0.05em;">Delivery Address</span>
            <p style="color: var(--eq-charcoal); line-height: 1.45; margin-top: 0.2rem;">
              {{ $order->address }}<br />
              <strong>{{ $order->area }}, {{ $order->district }}</strong><br />
              <span style="font-size: 0.78rem; color: var(--eq-charcoal-soft);">
                Zone: {{ $order->delivery_zone === 'inside_ctg' ? 'Chattogram Metropolitan' : 'Outside Chattogram (Nationwide)' }}
              </span>
            </p>
          </div>

          <div style="border-top: 1px solid var(--eq-line); padding-top: 0.75rem;">
            <span style="display: block; font-size: 0.72rem; text-transform: uppercase; color: var(--eq-charcoal-soft); letter-spacing: 0.05em;">Payment Arrangement</span>
            <strong style="text-transform: uppercase;">
              {{ $order->payment_method === 'cod' ? 'Cash on Delivery (COD)' : $order->payment_method }}
            </strong>
          </div>
        </div>
      </section>

    </div>

  </div>

@endsection


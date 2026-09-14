@extends('admin.layout')

@section('title', 'Order #' . $order->order_number . ' — Earthquick Admin')
@section('page_title', 'Order Details #' . $order->order_number)

@section('content')

  <!-- Top Action & Navigation Bar Card -->
  <div style="background: var(--eq-white); border: 1px solid var(--eq-line); border-radius: 8px; padding: 1rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);">
    <div style="display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;">
      <a href="{{ route('admin.orders') }}" class="eq-admin-btn eq-admin-btn--outline" style="gap: 0.5rem; padding: 0.45rem 1rem; font-size: 0.85rem;">
        &larr; Back to All Orders
      </a>
      <div style="font-size: 0.88rem; color: var(--eq-charcoal-soft);">
        Placed on: <strong style="color: var(--eq-navy);">{{ $order->created_at->format('d M Y, h:i A') }}</strong>
      </div>
    </div>

    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
      <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="eq-admin-btn eq-admin-btn--primary" style="gap: 0.45rem; padding: 0.45rem 1.1rem; font-size: 0.85rem;">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="6 9 6 2 18 2 18 9"></polyline>
          <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
          <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        Print Invoice / Packing Slip
      </a>

      <div style="display: flex; align-items: center; gap: 0.5rem; background: var(--eq-cream); padding: 0.35rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line);">
        <span style="font-size: 0.78rem; color: var(--eq-charcoal-soft); text-transform: uppercase; letter-spacing: 0.04em;">Status:</span>
        <span class="eq-status-badge eq-status-badge--{{ $order->status }}" style="font-size: 0.8rem; padding: 0.25rem 0.75rem;">
          {{ str_replace('_', ' ', $order->status) }}
        </span>
      </div>
    </div>
  </div>

  <!-- Two-Column Order Work Area -->
  <div style="display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 2rem; align-items: start;">
    
    <!-- LEFT COLUMN: ITEMS & FINANCIAL SUMMARY -->
    <div>
      <!-- Order Items Card -->
      <section class="eq-admin-card">
        <div class="eq-admin-card__header">
          <h2 class="eq-admin-card__title">Purchased Creations ({{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }})</h2>
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
              <span>Delivery Charge ({{ $order->delivery_zone === 'inside_ctg' ? 'Chattogram' : 'Nationwide' }}):</span>
              <span>৳{{ number_format($order->delivery_fee) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 600; color: var(--eq-navy); border-top: 2px solid var(--eq-navy); padding-top: 0.65rem; margin-top: 0.35rem;">
              <span>Total Payable (COD):</span>
              <span style="color: var(--eq-gold-dark);">৳{{ number_format($order->total) }}</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Customer Delivery Notes -->
      @if($order->order_notes)
        <section class="eq-admin-card" style="border-left: 3px solid var(--eq-gold);">
          <div class="eq-admin-card__header">
            <h3 class="eq-admin-card__title" style="font-size: 1rem;">Customer Delivery Instructions</h3>
          </div>
          <p style="font-size: 0.88rem; color: var(--eq-charcoal); font-style: italic; background: var(--eq-cream); padding: 0.85rem 1rem; border-radius: 4px;">
            &ldquo;{{ $order->order_notes }}&rdquo;
          </p>
        </section>
      @endif

      <!-- Internal Admin Remarks -->
      @if($order->admin_notes)
        <section class="eq-admin-card" style="border-left: 3px solid var(--eq-navy);">
          <div class="eq-admin-card__header">
            <h3 class="eq-admin-card__title" style="font-size: 1rem;">Internal Admin &amp; Warehouse Remarks</h3>
          </div>
          <p style="font-size: 0.88rem; color: var(--eq-charcoal); background: var(--eq-cream-deep); padding: 0.85rem 1rem; border-radius: 4px; white-space: pre-wrap;">{{ $order->admin_notes }}</p>
        </section>
      @endif
    </div>

    <!-- RIGHT COLUMN: STATUS UPDATE & CLIENT INFO -->
    <div>
      
      <!-- Lifecycle & Logistics Form Card -->
      <section class="eq-admin-card" style="border: 1px solid var(--eq-gold);">
        <div class="eq-admin-card__header">
          <h3 class="eq-admin-card__title" style="font-size: 1rem; color: var(--eq-gold-dark);">
            Fulfillment &amp; Courier Logistics
          </h3>
        </div>

        <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}">
          @csrf
          <div style="margin-bottom: 1.15rem;">
            <label for="order-status-select" style="display: block; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.45rem; color: var(--eq-charcoal);">
              Lifecycle Stage:
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

          <div style="margin-bottom: 1.15rem;">
            <label for="order-courier-input" style="display: block; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.45rem; color: var(--eq-charcoal);">
              Courier Partner:
            </label>
            <input type="text" name="courier_name" id="order-courier-input" list="courier-list" value="{{ old('courier_name', $order->courier_name) }}" placeholder="e.g. Steadfast Courier, Sundarban, Pathao" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-family: var(--font-body); font-size: 0.9rem; background: var(--eq-white); color: var(--eq-charcoal); outline: none;">
            <datalist id="courier-list">
              <option value="Steadfast Courier">
              <option value="Sundarban Courier Service">
              <option value="Pathao Courier">
              <option value="RedX Logistics">
              <option value="Paperfly">
              <option value="eCourier">
              <option value="In-House Atelier Courier">
            </datalist>
          </div>

          <div style="margin-bottom: 1.15rem;">
            <label for="order-tracking-input" style="display: block; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.45rem; color: var(--eq-charcoal);">
              Tracking ID / Consignment No:
            </label>
            <input type="text" name="tracking_number" id="order-tracking-input" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="e.g. ST-7894210" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-family: var(--font-body); font-size: 0.9rem; background: var(--eq-white); color: var(--eq-charcoal); outline: none;">
          </div>

          <div style="margin-bottom: 1.15rem;">
            <label for="order-admin-notes" style="display: block; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.45rem; color: var(--eq-charcoal);">
              Internal Warehouse Notes:
            </label>
            <textarea name="admin_notes" id="order-admin-notes" rows="3" placeholder="Remarks visible to shop admin only..." style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-family: var(--font-body); font-size: 0.86rem; background: var(--eq-white); color: var(--eq-charcoal); outline: none; resize: vertical;">{{ old('admin_notes', $order->admin_notes) }}</textarea>
          </div>

          <p style="font-size: 0.76rem; color: var(--eq-charcoal-soft); margin-bottom: 1.15rem; line-height: 1.45;">
            &bull; Saving updates synchronizes courier &amp; tracking code with customer tracking at <code>/account</code>.
          </p>

          <button type="submit" class="eq-admin-btn eq-admin-btn--gold" style="width: 100%; justify-content: center; padding: 0.65rem;">
            Update Fulfillment &amp; Status
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
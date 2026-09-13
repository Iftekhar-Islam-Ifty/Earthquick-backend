@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' Details — Earthquick by Nous Telos')
@section('body_class', 'eq-checkout-page')

@section('content')
<!-- Breadcrumb Bar -->
<nav class="eq-breadcrumb" aria-label="Breadcrumb" style="padding: 0.9rem 0; border-bottom: 1px solid var(--eq-line); background: var(--eq-white);">
  <div class="eq-container">
    <ol class="eq-breadcrumb__list" style="display: flex; align-items: center; gap: 0.5rem; list-style: none; margin: 0; padding: 0; font-size: 0.84rem; color: var(--eq-charcoal-soft);">
      <li><a href="{{ route('home') }}" style="color: inherit; text-decoration: none;">Home</a></li>
      <li style="color: var(--eq-line-dark);">&rsaquo;</li>
      <li><a href="{{ route('account.dashboard') }}" style="color: inherit; text-decoration: none;">My Account</a></li>
      <li style="color: var(--eq-line-dark);">&rsaquo;</li>
      <li style="color: var(--eq-charcoal); font-weight: 500;" aria-current="page">Order #{{ $order->order_number }}</li>
    </ol>
  </div>
</nav>

<main class="eq-container" id="main-content" style="padding: 3rem 0 5rem;">
  <div style="max-width: 780px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <a href="{{ route('account.dashboard') }}" style="font-size: 0.86rem; color: var(--eq-teal-dark); text-decoration: none; font-weight: 500;">
          &larr; Back to Order History
        </a>
        <h1 style="font-family: var(--font-display); font-size: 1.75rem; color: var(--eq-charcoal); margin: 0.25rem 0 0;">
          Invoice #{{ $order->order_number }}
        </h1>
      </div>

      <div style="display: flex; gap: 0.75rem;">
        <button type="button" class="eq-btn eq-btn--outline" onclick="window.print()" style="padding: 0.55rem 1.25rem; font-size: 0.84rem; cursor: pointer;">
          Print Invoice
        </button>
      </div>
    </div>

    @php
      $status = strtolower($order->status);
      $isConfirmed = true;
      $isProcessing = in_array($status, ['processing', 'shipped', 'delivered']);
      $isInTransit = in_array($status, ['shipped', 'delivered']);
      $isDelivered = ($status === 'delivered');

      $statusColor = $isDelivered ? '#27ae60' : ($status === 'cancelled' ? '#e74c3c' : 'var(--eq-gold-dark)');
      $statusBg = $isDelivered ? 'rgba(39, 174, 96, 0.12)' : ($status === 'cancelled' ? 'rgba(231, 76, 60, 0.12)' : 'rgba(201, 150, 47, 0.12)');
    @endphp

    <!-- Receipt Card -->
    <div class="eq-order-receipt-card">
      <div class="eq-receipt-header">
        <div>
          <div class="eq-receipt-id" style="font-size: 1.15rem; font-weight: 600; color: var(--eq-charcoal);">Order #{{ $order->order_number }}</div>
          <div style="font-size: 0.82rem; color: var(--eq-charcoal-soft); margin-top: 2px;">
            Placed on: {{ $order->created_at->format('d M Y, h:i A') }}
          </div>
        </div>
        <span class="eq-receipt-badge" style="background-color: {{ $statusBg }}; color: {{ $statusColor }}; font-size: 0.82rem; padding: 0.3rem 0.85rem; border-radius: 999px; font-weight: 600; text-transform: capitalize;">
          {{ $order->status }}
        </span>
      </div>

      <!-- Live Parcel Tracking Stepper -->
      <div class="eq-order-tracking-box" style="margin-bottom: 1.5rem;">
        <div class="eq-tracking-header">
          <div class="eq-tracking-title">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <rect x="1" y="3" width="15" height="13"></rect>
              <polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon>
              <circle cx="5.5" cy="18.5" r="2.5"></circle>
              <circle cx="18.5" cy="18.5" r="2.5"></circle>
            </svg>
            <span>Live Parcel Tracking Timeline</span>
          </div>
          <span class="eq-tracking-courier">
            Courier: <strong>Steadfast / Sundarban Express</strong>
          </span>
        </div>
        
        <div class="eq-tracking-stepper">
          <div class="eq-tracking-step {{ $isConfirmed ? 'is-complete' : '' }}">
            <div class="eq-step-dot"></div>
            <div class="eq-step-label">Confirmed</div>
          </div>
          <div class="eq-tracking-line {{ $isProcessing ? 'is-complete' : '' }}"></div>
          <div class="eq-tracking-step {{ $isProcessing ? ($isInTransit ? 'is-complete' : 'is-active') : '' }}">
            <div class="eq-step-dot"></div>
            <div class="eq-step-label">Processing</div>
          </div>
          <div class="eq-tracking-line {{ $isInTransit ? 'is-complete' : '' }}"></div>
          <div class="eq-tracking-step {{ $isInTransit ? ($isDelivered ? 'is-complete' : 'is-active') : '' }}">
            <div class="eq-step-dot"></div>
            <div class="eq-step-label">In Transit</div>
          </div>
          <div class="eq-tracking-line {{ $isDelivered ? 'is-complete' : '' }}"></div>
          <div class="eq-tracking-step {{ $isDelivered ? 'is-complete' : '' }}">
            <div class="eq-step-dot"></div>
            <div class="eq-step-label">Delivered</div>
          </div>
        </div>
      </div>

      <!-- Customer Details Grid -->
      <div class="eq-receipt-grid">
        <div>
          <div class="eq-receipt-group-label">Recipient Name</div>
          <div class="eq-receipt-group-val">{{ $order->customer_name }}</div>
        </div>
        <div>
          <div class="eq-receipt-group-label">Contact Phone</div>
          <div class="eq-receipt-group-val">{{ $order->customer_phone }}</div>
        </div>
        <div>
          <div class="eq-receipt-group-label">Delivery Address</div>
          <div class="eq-receipt-group-val">
            {{ $order->address }}, {{ $order->area }}, {{ $order->district }}
            <br />
            <small style="color: var(--eq-teal-dark); font-weight: normal;">
              ({{ $order->delivery_zone === 'inside_ctg' ? 'Inside Chattogram City (৳80)' : 'Outside Chattogram (৳150)' }})
            </small>
          </div>
        </div>
        <div>
          <div class="eq-receipt-group-label">Payment Mode</div>
          <div class="eq-receipt-group-val">{{ strtoupper($order->payment_method) }}</div>
        </div>
      </div>

      @if($order->order_notes)
        <div style="margin-bottom: 1.25rem; padding: 0.75rem 1rem; background: rgba(0,0,0,0.02); border-radius: 4px; font-size: 0.84rem;">
          <strong style="color: var(--eq-charcoal);">Special Instructions:</strong> {{ $order->order_notes }}
        </div>
      @endif

      <!-- Items Table -->
      <table class="eq-receipt-items-table" aria-label="Purchased Items">
        <thead>
          <tr>
            <th>Purchased Piece</th>
            <th style="text-align: center;">Qty</th>
            <th style="text-align: right;">Total</th>
          </tr>
        </thead>
        <tbody id="receipt-items-body">
          @foreach($order->items as $item)
            <tr>
              <td>
                <div style="display: flex; align-items: center; gap: 0.85rem;">
                  @if($item->product_image)
                    <img src="{{ asset($item->product_image) }}" alt="{{ $item->product_name }}" style="width: 46px; height: 56px; object-fit: cover; border-radius: 4px; background: var(--eq-cream-deep);" />
                  @endif
                  <div>
                    <div style="font-weight: 500; color: var(--eq-charcoal);">{{ $item->product_name }}</div>
                    <div style="font-size: 0.78rem; color: var(--eq-charcoal-soft);">৳{{ number_format($item->unit_price) }} each</div>
                  </div>
                </div>
              </td>
              <td style="text-align: center; font-weight: 500;">{{ $item->quantity }}</td>
              <td style="text-align: right; font-weight: 600; color: var(--eq-charcoal);">৳{{ number_format($item->total_price) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2" style="padding-top: 1rem; font-weight: 500;">Subtotal:</td>
            <td style="text-align: right; padding-top: 1rem; font-weight: 500;">৳{{ number_format($order->subtotal) }}</td>
          </tr>
          <tr>
            <td colspan="2" style="font-size: 0.85rem; color: var(--eq-charcoal-soft);">
              Delivery Charge ({{ $order->delivery_zone === 'inside_ctg' ? 'Inside Chattogram' : 'Outside Chattogram' }}):
            </td>
            <td style="text-align: right; font-size: 0.85rem;">৳{{ number_format($order->delivery_fee) }}</td>
          </tr>
          <tr style="border-top: 1.5px solid var(--eq-line);">
            <td colspan="2" style="padding-top: 0.75rem; font-weight: 600; font-size: 1.05rem; color: var(--eq-charcoal);">Total Payable:</td>
            <td style="text-align: right; padding-top: 0.75rem; font-weight: 600; font-size: 1.15rem; color: var(--eq-gold-dark);">৳{{ number_format($order->total) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>

  </div>
</main>
@endsection


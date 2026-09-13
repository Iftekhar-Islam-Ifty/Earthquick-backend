@extends('layouts.app')

@section('title', 'Order Confirmed — Earthquick by Nous Telos')
@section('body_class', 'eq-checkout-page')

@section('content')
<main class="eq-container" id="main-content" style="padding: 3.5rem 0 5rem;">

  <div class="eq-order-success-view" id="checkout-success-view" style="display: block; max-width: 720px; margin: 0 auto;">
    <div class="eq-order-success__icon">
      <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
    </div>

    <h1 class="eq-order-success__title">Thank you for your order!</h1>
    <p class="eq-order-success__subtitle">
      আপনার অর্ডারটি সফলভাবে গ্রহণ করা হয়েছে (Order placed successfully). আমাদের প্রতিনিধি শীঘ্রই আপনার মোবাইল নম্বরে কল করে অর্ডার কনফার্ম করবেন।
    </p>

    <!-- Receipt Card -->
    <div class="eq-order-receipt-card">
      <div class="eq-receipt-header">
        <div>
          <div class="eq-receipt-id" id="receipt-order-id">Order #{{ $order->order_number }}</div>
          <div style="font-size: 0.8rem; color: var(--eq-charcoal-soft);" id="receipt-order-date">Date: {{ $order->created_at->format('d M Y, h:i A') }}</div>
        </div>
        <span class="eq-receipt-badge" id="receipt-status-badge">
          @if($order->status === 'pending')
            Received &bull; Pending Confirmation
          @else
            {{ ucfirst($order->status) }}
          @endif
        </span>
      </div>

      <div class="eq-receipt-grid">
        <div>
          <div class="eq-receipt-group-label">Customer Name</div>
          <div class="eq-receipt-group-val" id="receipt-cust-name">{{ $order->customer_name }}</div>
        </div>
        <div>
          <div class="eq-receipt-group-label">Phone Number</div>
          <div class="eq-receipt-group-val" id="receipt-cust-phone">{{ $order->customer_phone }}</div>
        </div>
        <div>
          <div class="eq-receipt-group-label">Delivery Destination</div>
          <div class="eq-receipt-group-val" id="receipt-cust-address">
            {{ $order->address }}, {{ $order->area }}, {{ $order->district }}
            <br />
            <small style="color: var(--eq-teal-dark); font-weight: normal;">
              ({{ $order->delivery_zone === 'inside_ctg' ? 'Inside Chattogram City — Within 48 Hours' : 'Outside Chattogram (Nationwide) — 2 to 4 Days' }})
            </small>
          </div>
        </div>
        <div>
          <div class="eq-receipt-group-label">Payment Method</div>
          <div class="eq-receipt-group-val" id="receipt-pay-method">
            @if($order->payment_method === 'cod')
              Cash on Delivery (COD)
            @elseif($order->payment_method === 'bkash')
              bKash / Mobile Banking
            @else
              {{ strtoupper($order->payment_method) }}
            @endif
          </div>
        </div>
      </div>

      @if($order->order_notes)
        <div style="margin-bottom: 1.25rem; padding: 0.75rem 1rem; background: rgba(0,0,0,0.02); border-radius: 4px; font-size: 0.84rem;">
          <strong style="color: var(--eq-charcoal);">Special Instructions:</strong> {{ $order->order_notes }}
        </div>
      @endif

      <table class="eq-receipt-items-table" aria-label="Purchased Items">
        <thead>
          <tr>
            <th>Item</th>
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
            <td style="text-align: right; padding-top: 1rem; font-weight: 500;" id="receipt-subtotal">৳{{ number_format($order->subtotal) }}</td>
          </tr>
          <tr>
            <td colspan="2" style="font-size: 0.85rem; color: var(--eq-charcoal-soft);">
              Delivery Charge ({{ $order->delivery_zone === 'inside_ctg' ? 'Inside Chattogram' : 'Outside Chattogram' }}):
            </td>
            <td style="text-align: right; font-size: 0.85rem;" id="receipt-shipping">৳{{ number_format($order->delivery_fee) }}</td>
          </tr>
          <tr style="border-top: 1.5px solid var(--eq-line);">
            <td colspan="2" style="padding-top: 0.75rem; font-weight: 600; font-size: 1.05rem; color: var(--eq-charcoal);">Total Payable:</td>
            <td style="text-align: right; padding-top: 0.75rem; font-weight: 600; font-size: 1.15rem; color: var(--eq-gold-dark);" id="receipt-total">৳{{ number_format($order->total) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Action Buttons -->
    <div class="eq-order-success__actions" style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
      <button type="button" class="eq-btn eq-btn--outline" onclick="window.print()" style="padding: 0.8rem 1.75rem; cursor: pointer;">
        Print Invoice Receipt
      </button>
      <a href="{{ route('home') }}" class="eq-btn eq-btn--primary" style="padding: 0.8rem 2rem; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
        Return to Homepage &rarr;
      </a>
    </div>

  </div>

</main>
@endsection


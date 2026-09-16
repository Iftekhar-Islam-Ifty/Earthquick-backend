<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice #{{ $order->order_number }} — Earthquick Store</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    :root {
      --eq-navy: #0e1e24;
      --eq-gold: #c9962f;
      --eq-gold-dark: #a87920;
      --eq-charcoal: #1c2427;
      --eq-charcoal-soft: #536066;
      --eq-cream: #faf7f2;
      --eq-cream-deep: #f0eae1;
      --eq-line: #e2dacd;
      --font-display: 'Cinzel', serif;
      --font-body: 'Jost', sans-serif;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: var(--font-body);
      color: var(--eq-charcoal);
      background-color: #f5f2eb;
      line-height: 1.5;
      font-size: 14px;
      -webkit-font-smoothing: antialiased;
    }

    /* Floating Action Bar (Hidden on Print) */
    .eq-invoice-actions {
      position: sticky;
      top: 0;
      z-index: 100;
      background: rgba(14, 30, 36, 0.95);
      backdrop-filter: blur(8px);
      color: #fff;
      padding: 0.85rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    .eq-invoice-actions__btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.55rem 1.25rem;
      font-family: var(--font-body);
      font-size: 0.86rem;
      font-weight: 500;
      border-radius: 6px;
      text-decoration: none;
      cursor: pointer;
      border: 1px solid transparent;
      transition: all 0.2s ease;
    }

    .eq-invoice-actions__btn--back {
      background: transparent;
      color: #fff;
      border-color: rgba(255, 255, 255, 0.25);
    }

    .eq-invoice-actions__btn--back:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: #fff;
    }

    .eq-invoice-actions__btn--print {
      background: var(--eq-gold);
      color: var(--eq-navy);
      font-weight: 600;
    }

    .eq-invoice-actions__btn--print:hover {
      background: #dfab3e;
      box-shadow: 0 2px 10px rgba(201, 150, 47, 0.4);
    }

    /* Main Invoice Sheet */
    .eq-invoice-sheet {
      max-width: 820px;
      margin: 2rem auto;
      background: #ffffff;
      padding: 3rem;
      border-radius: 8px;
      box-shadow: 0 5px 25px rgba(0, 0, 0, 0.06);
      border: 1px solid var(--eq-line);
    }

    /* Header Section */
    .eq-invoice-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 2px solid var(--eq-navy);
      padding-bottom: 1.5rem;
      margin-bottom: 2rem;
    }

    .eq-brand-title {
      font-family: var(--font-display);
      font-size: 1.75rem;
      letter-spacing: 0.08em;
      color: var(--eq-navy);
      font-weight: 700;
    }

    .eq-brand-sub {
      font-size: 0.76rem;
      text-transform: uppercase;
      letter-spacing: 0.18em;
      color: var(--eq-gold-dark);
      font-weight: 600;
      margin-top: 0.2rem;
    }

    .eq-brand-address {
      font-size: 0.8rem;
      color: var(--eq-charcoal-soft);
      margin-top: 0.6rem;
      line-height: 1.4;
    }

    .eq-invoice-meta {
      text-align: right;
    }

    .eq-invoice-badge {
      display: inline-block;
      font-family: var(--font-display);
      font-size: 1.15rem;
      font-weight: 600;
      letter-spacing: 0.05em;
      color: var(--eq-navy);
      margin-bottom: 0.4rem;
    }

    .eq-meta-row {
      font-size: 0.82rem;
      color: var(--eq-charcoal-soft);
      margin-top: 0.2rem;
    }

    .eq-meta-row strong {
      color: var(--eq-charcoal);
    }

    /* Grid Details */
    .eq-invoice-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      margin-bottom: 2rem;
      padding: 1.25rem 1.5rem;
      background: var(--eq-cream);
      border-radius: 6px;
      border: 1px solid var(--eq-line);
    }

    .eq-grid-title {
      font-size: 0.72rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--eq-charcoal-soft);
      margin-bottom: 0.35rem;
      font-weight: 600;
    }

    .eq-grid-val {
      font-size: 0.92rem;
      color: var(--eq-navy);
      line-height: 1.45;
    }

    .eq-grid-val strong {
      font-weight: 600;
    }

    .eq-courier-box {
      margin-top: 0.75rem;
      padding-top: 0.75rem;
      border-top: 1px dashed var(--eq-line);
      font-size: 0.84rem;
    }

    /* Table Section */
    .eq-invoice-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 2rem;
    }

    .eq-invoice-table th {
      background: var(--eq-cream-deep);
      color: var(--eq-navy);
      font-size: 0.76rem;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      font-weight: 600;
      padding: 0.75rem 1rem;
      text-align: left;
      border-bottom: 1px solid var(--eq-line);
    }

    .eq-invoice-table td {
      padding: 1rem;
      border-bottom: 1px solid var(--eq-line);
      vertical-align: middle;
      font-size: 0.88rem;
    }

    .eq-invoice-item-thumb {
      width: 44px;
      height: 56px;
      object-fit: cover;
      border-radius: 4px;
      border: 1px solid var(--eq-line);
    }

    /* Totals Summary */
    .eq-totals-wrap {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 2.5rem;
    }

    .eq-totals-table {
      width: 320px;
      border-collapse: collapse;
    }

    .eq-totals-table td {
      padding: 0.4rem 0.5rem;
      font-size: 0.88rem;
    }

    .eq-totals-table td:last-child {
      text-align: right;
    }

    .eq-totals-total-row td {
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--eq-navy);
      border-top: 2px solid var(--eq-navy);
      padding-top: 0.65rem;
    }

    /* Notes & Signoff */
    .eq-notes-section {
      border-top: 1px solid var(--eq-line);
      padding-top: 1.5rem;
      margin-bottom: 3rem;
      display: grid;
      grid-template-columns: 1.5fr 1fr;
      gap: 2rem;
    }

    .eq-policy-note {
      font-size: 0.78rem;
      color: var(--eq-charcoal-soft);
      line-height: 1.5;
    }

    .eq-signature-box {
      text-align: right;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
    }

    .eq-signature-line {
      width: 180px;
      margin-left: auto;
      border-top: 1px solid var(--eq-charcoal);
      margin-bottom: 0.35rem;
    }

    .eq-signature-label {
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--eq-charcoal-soft);
    }

    /* Print Specific Media Queries */
    @media print {
      .no-print, .eq-invoice-actions {
        display: none !important;
      }

      body {
        background: #ffffff !important;
        color: #000000 !important;
        font-size: 12px !important;
      }

      .eq-invoice-sheet {
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
        border: none !important;
        box-shadow: none !important;
      }

      .eq-brand-title {
        color: #000000 !important;
      }

      .eq-invoice-grid {
        background: #fafafa !important;
        border: 1px solid #ddd !important;
      }

      .eq-invoice-table th {
        background: #f0f0f0 !important;
        color: #000 !important;
      }

      .eq-totals-total-row td {
        color: #000 !important;
      }
    }
  </style>
</head>
<body>

  <!-- Top Action Navigation Bar (Hidden during printing) -->
  <aside class="eq-invoice-actions no-print" aria-label="Invoice Controls">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <a href="{{ route('admin.orders.show', $order->id) }}" class="eq-invoice-actions__btn eq-invoice-actions__btn--back">
        &larr; Return to Order #{{ $order->order_number }}
      </a>
      <span style="font-size: 0.85rem; color: #a5b4bc;">
        Status: <strong style="color: #fff; text-transform: uppercase;">{{ str_replace('_', ' ', $order->status) }}</strong>
      </span>
    </div>

    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <button type="button" onclick="window.print()" class="eq-invoice-actions__btn eq-invoice-actions__btn--print">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="6 9 6 2 18 2 18 9"></polyline>
          <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
          <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        Print Invoice / Packing Slip
      </button>
    </div>
  </aside>

  <!-- Printable Invoice Document -->
  <main class="eq-invoice-sheet" id="invoice-printable">
    
    <!-- Atelier Header -->
    <header class="eq-invoice-header">
      <div>
        <div class="eq-brand-title">EARTHQUICK</div>
        <div class="eq-brand-sub">NOUS TELOS &bull; DESIGN STUDIO</div>
        <div class="eq-brand-address">
          Chattogram Metropolitan, Bangladesh<br>
          Direct line: +880 1812-345678 &bull; concierge@earthquick.com<br>
          Web: www.earthquick.com
        </div>
      </div>

      <div class="eq-invoice-meta">
        <div class="eq-invoice-badge">PACKING SLIP &amp; INVOICE</div>
        <div class="eq-meta-row">Order No: <strong>#{{ $order->order_number }}</strong></div>
        <div class="eq-meta-row">Date: <strong>{{ $order->created_at->format('d M Y, h:i A') }}</strong></div>
        <div class="eq-meta-row">Payment: <strong>{{ strtoupper($order->payment_method ?? 'COD') }} (Cash on Delivery)</strong></div>
      </div>
    </header>

    <!-- Logistics & Consignee Grid -->
    <section class="eq-invoice-grid">
      <!-- Shipping Destination -->
      <div>
        <div class="eq-grid-title">Shipped To / Consignee</div>
        <div class="eq-grid-val">
          <strong>{{ $order->customer_name }}</strong><br>
          Phone: {{ $order->customer_phone }}<br>
          @if($order->customer_email)
            Email: {{ $order->customer_email }}<br>
          @endif
          Address: {{ $order->address }}<br>
          <strong>{{ $order->area }}, {{ $order->district }}</strong><br>
          <span style="font-size: 0.8rem; color: var(--eq-charcoal-soft);">
            Zone: {{ $order->delivery_zone === 'inside_ctg' ? 'Chattogram Metro' : 'Outside Chattogram (Nationwide)' }}
          </span>
        </div>
      </div>

      <!-- Courier Logistics Information -->
      <div>
        <div class="eq-grid-title">Logistics &amp; Fulfillment</div>
        <div class="eq-grid-val">
          Fulfillment Stage: <strong>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</strong><br>
          Courier Partner: <strong>{{ $order->courier_name ?: 'Standard Store Dispatch' }}</strong><br>
          Consignment Tracking ID: 
          @if($order->tracking_number)
            <strong style="color: var(--eq-gold-dark); letter-spacing: 0.04em;">{{ $order->tracking_number }}</strong>
          @else
            <span style="color: var(--eq-charcoal-soft); font-style: italic;">Pending Generation</span>
          @endif
        </div>

        @if($order->order_notes)
          <div class="eq-courier-box">
            <span class="eq-grid-title" style="display: block; margin-bottom: 0.2rem;">Customer Delivery Note:</span>
            <span style="font-style: italic; color: var(--eq-charcoal);">&ldquo;{{ $order->order_notes }}&rdquo;</span>
          </div>
        @endif
      </section>

    <!-- Itemized Breakdown Table -->
    <table class="eq-invoice-table">
      <thead>
        <tr>
          <th style="width: 5%;">#</th>
          <th style="width: 50%;">Creations &amp; Specification</th>
          <th style="width: 15%;">Unit Price</th>
          <th style="width: 10%; text-align: center;">Qty</th>
          <th style="width: 20%; text-align: right;">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $index => $item)
          <tr>
            <td style="color: var(--eq-charcoal-soft);">{{ $index + 1 }}</td>
            <td>
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                @if($item->product_image)
                  <img src="{{ asset($item->product_image) }}" alt="{{ $item->product_name }}" class="eq-invoice-item-thumb">
                @endif
                <div>
                  <div style="font-weight: 600; color: var(--eq-navy);">{{ $item->product_name }}</div>
                  @if($item->size)
                    <div style="font-size: 0.78rem; color: var(--eq-charcoal-soft);">Size: {{ $item->size }}</div>
                  @endif
                </div>
              </div>
            </td>
            <td>৳{{ number_format($item->unit_price) }}</td>
            <td style="text-align: center; font-weight: 600;">{{ $item->quantity }}</td>
            <td style="text-align: right; font-weight: 600; color: var(--eq-navy);">৳{{ number_format($item->total_price) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <!-- Financial Breakdown -->
    <div class="eq-totals-wrap">
      <table class="eq-totals-table">
        <tr>
          <td style="color: var(--eq-charcoal-soft);">Subtotal:</td>
          <td>৳{{ number_format($order->subtotal) }}</td>
        </tr>
        @if($order->discount_amount > 0)
          <tr style="color: #28a745; font-weight: 600;">
            <td>Promo Discount ({{ $order->coupon_code }}):</td>
            <td>-৳{{ number_format($order->discount_amount) }}</td>
          </tr>
        @endif
        <tr>
          <td style="color: var(--eq-charcoal-soft);">Delivery Charge:</td>
          <td>৳{{ number_format($order->delivery_fee) }}</td>
        </tr>
        <tr class="eq-totals-total-row">
          <td>Total Payable (COD):</td>
          <td style="color: var(--eq-gold-dark);">৳{{ number_format($order->total) }}</td>
        </tr>
      </table>
    </div>

    <!-- Notes & Sign-off Block -->
    <footer class="eq-notes-section">
      <div class="eq-policy-note">
        <strong>Quality Assurance &amp; Exchange Terms:</strong><br>
        Each garment is crafted with meticulous care. In the event of sizing discrepancies or defects, exchange requests are honored within 7 days of parcel delivery provided original tags, packaging, and this packing slip are intact.
      </div>

      <div class="eq-signature-box">
        <div class="eq-signature-line"></div>
        <div class="eq-signature-label">Authorized Dispatch</div>
      </div>
    </footer>

  </main>

</body>
</html>


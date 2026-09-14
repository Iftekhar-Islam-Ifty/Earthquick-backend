@extends('admin.layout')

@section('title', 'Customer Orders Portfolio — Earthquick Admin')
@section('page_title', 'Customer Orders Management')

@section('content')

  <!-- Search and Quick Filter Controls Card -->
  <div style="background: var(--eq-white); border: 1px solid var(--eq-line); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
      
      <!-- Status Filter Navigation Pills -->
      <div style="display: flex; gap: 0.45rem; flex-wrap: wrap;">
        <a href="{{ route('admin.orders', array_filter(['search' => $search])) }}" 
           class="eq-admin-btn {{ empty($status) ? 'eq-admin-btn--primary' : 'eq-admin-btn--outline' }}" 
           style="border-radius: 999px; padding: 0.35rem 0.9rem; font-size: 0.84rem;">
          All ({{ $statusCounts['all'] }})
        </a>
        <a href="{{ route('admin.orders', array_filter(['status' => 'pending', 'search' => $search])) }}" 
           class="eq-admin-btn {{ $status === 'pending' ? 'eq-admin-btn--gold' : 'eq-admin-btn--outline' }}" 
           style="border-radius: 999px; padding: 0.35rem 0.9rem; font-size: 0.84rem;">
          Pending ({{ $statusCounts['pending'] }})
        </a>
        <a href="{{ route('admin.orders', array_filter(['status' => 'confirmed', 'search' => $search])) }}" 
           class="eq-admin-btn {{ $status === 'confirmed' ? 'eq-admin-btn--primary' : 'eq-admin-btn--outline' }}" 
           style="border-radius: 999px; padding: 0.35rem 0.9rem; font-size: 0.84rem;">
          Confirmed ({{ $statusCounts['confirmed'] }})
        </a>
        <a href="{{ route('admin.orders', array_filter(['status' => 'processing', 'search' => $search])) }}" 
           class="eq-admin-btn {{ $status === 'processing' ? 'eq-admin-btn--primary' : 'eq-admin-btn--outline' }}" 
           style="border-radius: 999px; padding: 0.35rem 0.9rem; font-size: 0.84rem;">
          Processing ({{ $statusCounts['processing'] }})
        </a>
        <a href="{{ route('admin.orders', array_filter(['status' => 'in_transit', 'search' => $search])) }}" 
           class="eq-admin-btn {{ $status === 'in_transit' ? 'eq-admin-btn--primary' : 'eq-admin-btn--outline' }}" 
           style="border-radius: 999px; padding: 0.35rem 0.9rem; font-size: 0.84rem;">
          In Transit ({{ $statusCounts['in_transit'] }})
        </a>
        <a href="{{ route('admin.orders', array_filter(['status' => 'delivered', 'search' => $search])) }}" 
           class="eq-admin-btn {{ $status === 'delivered' ? 'eq-admin-btn--success' : 'eq-admin-btn--outline' }}" 
           style="border-radius: 999px; padding: 0.35rem 0.9rem; font-size: 0.84rem;">
          Delivered ({{ $statusCounts['delivered'] }})
        </a>
        <a href="{{ route('admin.orders', array_filter(['status' => 'cancelled', 'search' => $search])) }}" 
           class="eq-admin-btn {{ $status === 'cancelled' ? 'eq-admin-btn--danger' : 'eq-admin-btn--outline' }}" 
           style="border-radius: 999px; padding: 0.35rem 0.9rem; font-size: 0.84rem;">
          Cancelled ({{ $statusCounts['cancelled'] }})
        </a>
      </div>

      <!-- Live Order Search Input Box -->
      <form method="GET" action="{{ route('admin.orders') }}" style="display: flex; gap: 0.5rem; align-items: center;">
        @if(!empty($status))
          <input type="hidden" name="status" value="{{ $status }}">
        @endif
        
        <div style="position: relative;">
          <input type="text" 
                 name="search" 
                 value="{{ $search ?? '' }}" 
                 placeholder="Search Order #, Phone, Name..." 
                 style="padding: 0.45rem 0.85rem 0.45rem 2.2rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.86rem; width: 260px; outline: none; font-family: var(--font-body); background: var(--eq-white); color: var(--eq-charcoal);" />
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--eq-charcoal-soft); pointer-events: none;" aria-hidden="true">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </div>

        <button type="submit" class="eq-admin-btn eq-admin-btn--primary" style="padding: 0.45rem 0.85rem; font-size: 0.84rem;">
          Search
        </button>

        @if(!empty($search))
          <a href="{{ route('admin.orders', array_filter(['status' => $status])) }}" class="eq-admin-btn eq-admin-btn--outline" style="padding: 0.45rem 0.75rem; font-size: 0.84rem; color: #e74c3c; border-color: #fca5a5;">
            Clear
          </a>
        @endif
      </form>

    </div>
  </div>

  <!-- Orders Data Table Card -->
  <section class="eq-admin-card">
    <div class="eq-admin-card__header">
      <h2 class="eq-admin-card__title">
        {{ $status ? ucfirst(str_replace('_', ' ', $status)) . ' Orders' : 'All Customer Orders' }}
        @if(!empty($search))
          <span style="font-size: 0.85rem; font-weight: normal; color: var(--eq-gold-dark);">
            matching &ldquo;{{ $search }}&rdquo;
          </span>
        @endif
        <span style="font-size: 0.85rem; font-weight: normal; color: var(--eq-charcoal-soft);">
          ({{ $orders->total() }} total)
        </span>
      </h2>

      <a href="{{ route('admin.orders.export', array_filter(['status' => $status])) }}" class="eq-admin-btn eq-admin-btn--outline" style="padding: 0.4rem 0.85rem; font-size: 0.82rem; gap: 0.4rem;" title="Export orders ledger to CSV">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
          <polyline points="7 10 12 15 17 10"></polyline>
          <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        Export CSV
      </a>
    </div>

    <div class="eq-admin-table-wrap">
      <table class="eq-admin-table">
        <thead>
          <tr>
            <th>Invoice ID</th>
            <th>Client Details</th>
            <th>Destination Address</th>
            <th>Items Qty</th>
            <th>Total (৳)</th>
            <th>Payment</th>
            <th>Status &amp; Courier</th>
            <th>Order Date</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $order)
            <tr>
              <td>
                <a href="{{ route('admin.orders.show', $order->id) }}" style="font-weight: 600; color: var(--eq-navy); text-decoration: none;">
                  {{ $order->order_number }}
                </a>
              </td>
              <td>
                <div style="font-weight: 500;">{{ $order->customer_name }}</div>
                <div style="font-size: 0.78rem; color: var(--eq-charcoal-soft);">{{ $order->customer_phone }}</div>
                @if($order->customer_email)
                  <div style="font-size: 0.74rem; color: var(--eq-charcoal-muted);">{{ $order->customer_email }}</div>
                @endif
              </td>
              <td>
                <div style="font-size: 0.85rem; font-weight: 500;">{{ $order->district }} &bull; {{ $order->area }}</div>
                <div style="font-size: 0.76rem; color: var(--eq-charcoal-soft); max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $order->address }}">
                  {{ $order->address }}
                </div>
              </td>
              <td style="text-align: center;">
                <span style="background: var(--eq-cream-deep); padding: 0.15rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.8rem;">
                  {{ $order->items->sum('quantity') }}
                </span>
              </td>
              <td style="font-weight: 600; color: var(--eq-gold-dark);">
                ৳{{ number_format($order->total) }}
              </td>
              <td>
                <span style="font-size: 0.76rem; text-transform: uppercase; background: var(--eq-cream); padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 500;">
                  {{ $order->payment_method ?? 'COD' }}
                </span>
              </td>
              <td>
                <span class="eq-status-badge eq-status-badge--{{ $order->status }}">
                  {{ str_replace('_', ' ', $order->status) }}
                </span>
                @if($order->courier_name)
                  <div style="font-size: 0.74rem; color: var(--eq-navy); margin-top: 3px; font-weight: 500;">
                    {{ $order->courier_name }}
                  </div>
                  @if($order->tracking_number)
                    <div style="font-size: 0.7rem; color: var(--eq-gold-dark); letter-spacing: 0.03em;">
                      {{ $order->tracking_number }}
                    </div>
                  @endif
                @endif
              </td>
              <td style="font-size: 0.82rem; color: var(--eq-charcoal-soft); white-space: nowrap;">
                {{ $order->created_at->format('d M Y, h:i A') }}
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <div style="display: inline-flex; gap: 0.4rem;">
                  <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="eq-admin-btn eq-admin-btn--outline" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;" title="Print Packing Slip">
                    Invoice
                  </a>
                  <a href="{{ route('admin.orders.show', $order->id) }}" class="eq-admin-btn eq-admin-btn--primary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                    Manage &rarr;
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" style="text-align: center; padding: 3.5rem 1rem; color: var(--eq-charcoal-soft);">
                @if(!empty($search))
                  No orders found matching &ldquo;<strong>{{ $search }}</strong>&rdquo;. 
                  <a href="{{ route('admin.orders') }}" style="color: var(--eq-gold-dark); text-decoration: underline; margin-left: 0.5rem;">Reset Filter</a>
                @else
                  No customer orders found under this status.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($orders->hasPages())
      <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--eq-line); display: flex; justify-content: center;">
        {{ $orders->links() }}
      </div>
    @endif

  </section>

@endsection
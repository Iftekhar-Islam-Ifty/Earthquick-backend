@extends('admin.layout')

@section('title', 'Customer Orders Portfolio — Earthquick Admin')
@section('page_title', 'Customer Orders Management')

@section('content')

  <!-- Status Filter Navigation Pills -->
  <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
    <a href="{{ route('admin.orders') }}" 
       class="eq-admin-btn {{ empty($status) ? 'eq-admin-btn--primary' : 'eq-admin-btn--outline' }}" 
       style="border-radius: 999px; padding: 0.4rem 1rem;">
      All ({{ $statusCounts['all'] }})
    </a>
    <a href="{{ route('admin.orders', ['status' => 'pending']) }}" 
       class="eq-admin-btn {{ $status === 'pending' ? 'eq-admin-btn--gold' : 'eq-admin-btn--outline' }}" 
       style="border-radius: 999px; padding: 0.4rem 1rem;">
      Pending ({{ $statusCounts['pending'] }})
    </a>
    <a href="{{ route('admin.orders', ['status' => 'confirmed']) }}" 
       class="eq-admin-btn {{ $status === 'confirmed' ? 'eq-admin-btn--primary' : 'eq-admin-btn--outline' }}" 
       style="border-radius: 999px; padding: 0.4rem 1rem;">
      Confirmed ({{ $statusCounts['confirmed'] }})
    </a>
    <a href="{{ route('admin.orders', ['status' => 'processing']) }}" 
       class="eq-admin-btn {{ $status === 'processing' ? 'eq-admin-btn--primary' : 'eq-admin-btn--outline' }}" 
       style="border-radius: 999px; padding: 0.4rem 1rem;">
      Processing ({{ $statusCounts['processing'] }})
    </a>
    <a href="{{ route('admin.orders', ['status' => 'in_transit']) }}" 
       class="eq-admin-btn {{ $status === 'in_transit' ? 'eq-admin-btn--primary' : 'eq-admin-btn--outline' }}" 
       style="border-radius: 999px; padding: 0.4rem 1rem;">
      In Transit ({{ $statusCounts['in_transit'] }})
    </a>
    <a href="{{ route('admin.orders', ['status' => 'delivered']) }}" 
       class="eq-admin-btn {{ $status === 'delivered' ? 'eq-admin-btn--success' : 'eq-admin-btn--outline' }}" 
       style="border-radius: 999px; padding: 0.4rem 1rem;">
      Delivered ({{ $statusCounts['delivered'] }})
    </a>
    <a href="{{ route('admin.orders', ['status' => 'cancelled']) }}" 
       class="eq-admin-btn {{ $status === 'cancelled' ? 'eq-admin-btn--danger' : 'eq-admin-btn--outline' }}" 
       style="border-radius: 999px; padding: 0.4rem 1rem;">
      Cancelled ({{ $statusCounts['cancelled'] }})
    </a>
  </div>

  <!-- Orders Data Table Card -->
  <section class="eq-admin-card">
    <div class="eq-admin-card__header">
      <h2 class="eq-admin-card__title">
        {{ $status ? ucfirst(str_replace('_', ' ', $status)) . ' Orders' : 'All Customer Orders' }}
        <span style="font-size: 0.85rem; font-weight: normal; color: var(--eq-charcoal-soft);">
          ({{ $orders->total() }} total)
        </span>
      </h2>
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
            <th>Status</th>
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
              </td>
              <td style="font-size: 0.82rem; color: var(--eq-charcoal-soft); white-space: nowrap;">
                {{ $order->created_at->format('d M Y, h:i A') }}
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <a href="{{ route('admin.orders.show', $order->id) }}" class="eq-admin-btn eq-admin-btn--outline" style="padding: 0.35rem 0.75rem;">
                  Manage &rarr;
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" style="text-align: center; padding: 3rem; color: var(--eq-charcoal-soft);">
                No customer orders found under this status.
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


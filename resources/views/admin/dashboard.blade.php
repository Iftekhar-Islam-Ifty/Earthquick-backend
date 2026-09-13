@extends('admin.layout')

@section('title', 'Atelier Dashboard — Earthquick Admin')
@section('page_title', 'Atelier Executive Dashboard')

@section('content')

  <!-- Stat Metric Cards -->
  <section class="eq-admin-metrics">
    <!-- Card 1: Total Orders -->
    <div class="eq-metric-box">
      <span class="eq-metric-box__title">Total Orders</span>
      <div class="eq-metric-box__value">{{ number_format($totalOrders) }}</div>
      <span class="eq-metric-box__sub">Lifetime volume across all regions</span>
    </div>

    <!-- Card 2: Total Revenue -->
    <div class="eq-metric-box">
      <span class="eq-metric-box__title">Net Sales Revenue</span>
      <div class="eq-metric-box__value" style="color: var(--eq-gold-dark);">৳{{ number_format($totalRevenue) }}</div>
      <span class="eq-metric-box__sub">Excludes cancelled transactions</span>
    </div>

    <!-- Card 3: Pending Orders -->
    <div class="eq-metric-box" style="border-left: 3px solid var(--eq-gold);">
      <span class="eq-metric-box__title">Pending Orders</span>
      <div class="eq-metric-box__value" style="color: #b45309;">{{ number_format($pendingOrdersCount) }}</div>
      <span class="eq-metric-box__sub">Requires packaging &amp; dispatch</span>
    </div>

    <!-- Card 4: Catalog Products -->
    <div class="eq-metric-box">
      <span class="eq-metric-box__title">Catalog Portfolio</span>
      <div class="eq-metric-box__value">{{ number_format($totalProducts) }}</div>
      <span class="eq-metric-box__sub">Active artisanal creations</span>
    </div>
  </section>

  <!-- Recent Orders Section -->
  <section class="eq-admin-card">
    <div class="eq-admin-card__header">
      <h2 class="eq-admin-card__title">Recent Customer Orders (Latest 10)</h2>
      <a href="{{ route('admin.orders') }}" class="eq-admin-btn eq-admin-btn--outline">
        View All Orders &rarr;
      </a>
    </div>

    <div class="eq-admin-table-wrap">
      <table class="eq-admin-table">
        <thead>
          <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Location</th>
            <th>Total Amount</th>
            <th>Payment</th>
            <th>Lifecycle Status</th>
            <th>Date Placed</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentOrders as $order)
            <tr>
              <td>
                <a href="{{ route('admin.orders.show', $order->id) }}" style="font-weight: 600; color: var(--eq-navy); text-decoration: none;">
                  {{ $order->order_number }}
                </a>
              </td>
              <td>
                <div style="font-weight: 500;">{{ $order->customer_name }}</div>
                <div style="font-size: 0.76rem; color: var(--eq-charcoal-soft);">{{ $order->customer_phone }}</div>
              </td>
              <td>
                <div>{{ $order->district }}</div>
                <div style="font-size: 0.74rem; color: var(--eq-charcoal-soft);">{{ $order->area }}</div>
              </td>
              <td style="font-weight: 600; color: var(--eq-gold-dark);">
                ৳{{ number_format($order->total) }}
              </td>
              <td>
                <span style="font-size: 0.78rem; text-transform: uppercase; background: var(--eq-cream); padding: 0.2rem 0.5rem; border-radius: 4px;">
                  {{ $order->payment_method ?? 'COD' }}
                </span>
              </td>
              <td>
                <span class="eq-status-badge eq-status-badge--{{ $order->status }}">
                  {{ str_replace('_', ' ', $order->status) }}
                </span>
              </td>
              <td style="font-size: 0.82rem; color: var(--eq-charcoal-soft);">
                {{ $order->created_at->format('d M Y, h:i A') }}
              </td>
              <td style="text-align: right;">
                <a href="{{ route('admin.orders.show', $order->id) }}" class="eq-admin-btn eq-admin-btn--outline" style="padding: 0.3rem 0.65rem; font-size: 0.78rem;">
                  Inspect &rarr;
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--eq-charcoal-soft);">
                No customer orders recorded yet.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

@endsection


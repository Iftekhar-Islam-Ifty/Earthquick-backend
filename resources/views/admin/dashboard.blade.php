@extends('admin.layout')

@section('title', 'Atelier Dashboard & Analytics — Earthquick Admin')
@section('page_title', 'Atelier Executive Dashboard')

@section('content')

  <style>
    .eq-dashboard-charts-grid {
      display: grid;
      grid-template-columns: 1.65fr 1fr;
      gap: 1.5rem;
      margin-bottom: 2rem;
      min-width: 0;
      width: 100%;
      align-items: stretch;
    }
    .eq-dashboard-widgets-grid {
      display: grid;
      grid-template-columns: 1.25fr 0.75fr;
      gap: 1.5rem;
      margin-bottom: 2rem;
      min-width: 0;
      width: 100%;
      align-items: start;
    }
    @media (max-width: 1100px) {
      .eq-dashboard-charts-grid {
        grid-template-columns: 1fr;
      }
      .eq-dashboard-widgets-grid {
        grid-template-columns: 1fr;
      }
    }
    .eq-chart-wrapper {
      position: relative;
      width: 100%;
      min-width: 0;
      height: 280px;
      max-height: 280px;
      overflow: hidden;
    }
    .eq-chart-wrapper canvas {
      max-width: 100% !important;
      max-height: 100% !important;
    }
  </style>

  <!-- Page Header & Global Quick Actions -->
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem; min-width: 0;">
    <div>
      <h2 style="font-family: var(--font-display); font-size: 1.45rem; color: var(--eq-navy); margin-bottom: 0.2rem;">
        Executive Performance &amp; Analytics
      </h2>
      <p style="font-size: 0.84rem; color: var(--eq-charcoal-soft);">
        Live overview of commercial health, revenue trajectories, category metrics, and ledger reports.
      </p>
    </div>
    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
      <a href="{{ route('admin.orders.export') }}" class="eq-admin-btn eq-admin-btn--outline" style="gap: 0.45rem; padding: 0.55rem 1rem; font-size: 0.84rem;" title="Download complete orders ledger as Excel CSV">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
          <polyline points="7 10 12 15 17 10"></polyline>
          <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        Export Orders (CSV)
      </a>
      <a href="{{ route('admin.orders') }}" class="eq-admin-btn eq-admin-btn--primary" style="gap: 0.45rem; padding: 0.55rem 1rem; font-size: 0.84rem;">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 11l3 3L22 4"></path>
          <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
        </svg>
        Manage Orders
      </a>
    </div>
  </div>

  <!-- Stat Metric Cards (4-Column Grid) -->
  <section class="eq-admin-metrics" style="margin-bottom: 2rem;">
    
    <!-- Card 1: Total Orders -->
    <div class="eq-metric-box" style="border-top: 3px solid var(--eq-navy); min-width: 0;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.4rem;">
        <span class="eq-metric-box__title">Total Orders</span>
        <span style="font-size: 0.7rem; font-weight: 600; background: #e0f2fe; color: #0369a1; padding: 0.15rem 0.5rem; border-radius: 999px;">
          Lifetime
        </span>
      </div>
      <div class="eq-metric-box__value" style="color: var(--eq-navy);">
        {{ number_format($totalOrders) }}
      </div>
      <span class="eq-metric-box__sub">
        Today: <strong>{{ $todayOrders }}</strong> &bull; This Month: <strong>{{ $monthOrders }}</strong>
      </span>
    </div>

    <!-- Card 2: Net Sales Revenue -->
    <div class="eq-metric-box" style="border-top: 3px solid var(--eq-gold); min-width: 0;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.4rem;">
        <span class="eq-metric-box__title">Net Sales Revenue</span>
        <span style="font-size: 0.7rem; font-weight: 600; background: #fef3c7; color: #92400e; padding: 0.15rem 0.5rem; border-radius: 999px;">
          Excl. Cancelled
        </span>
      </div>
      <div class="eq-metric-box__value" style="color: var(--eq-gold-dark);">
        ৳{{ number_format($totalRevenue) }}
      </div>
      <span class="eq-metric-box__sub">
        Today: ৳{{ number_format($todayRevenue) }} &bull; Month: ৳{{ number_format($monthRevenue) }}
      </span>
    </div>

    <!-- Card 3: Average Order Value (AOV) -->
    <div class="eq-metric-box" style="border-top: 3px solid #166534; min-width: 0;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.4rem;">
        <span class="eq-metric-box__title">Average Order Value</span>
        <span style="font-size: 0.7rem; font-weight: 600; background: #dcfce7; color: #166534; padding: 0.15rem 0.5rem; border-radius: 999px;">
          AOV
        </span>
      </div>
      <div class="eq-metric-box__value" style="color: #166534;">
        ৳{{ number_format($averageOrderValue) }}
      </div>
      <span class="eq-metric-box__sub">
        Per fulfilled acquisition transaction
      </span>
    </div>

    <!-- Card 4: Catalog & Atelier Stock -->
    <div class="eq-metric-box" style="border-top: 3px solid var(--eq-charcoal); min-width: 0;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.4rem;">
        <span class="eq-metric-box__title">Catalog Portfolio</span>
        <span style="font-size: 0.7rem; font-weight: 600; background: var(--eq-cream-deep); color: var(--eq-charcoal); padding: 0.15rem 0.5rem; border-radius: 999px;">
          Stock
        </span>
      </div>
      <div class="eq-metric-box__value">
        {{ number_format($totalProducts) }} <span style="font-size: 0.88rem; font-weight: 400; color: var(--eq-charcoal-soft);">items</span>
      </div>
      <span class="eq-metric-box__sub">
        <strong>{{ $inStockProducts }}</strong> in-stock &bull; <strong style="color: #b45309;">{{ $pendingOrdersCount }}</strong> pending
      </span>
    </div>

  </section>

  <!-- Interactive Infographic Charts Section (2 Columns, Responsive) -->
  <div class="eq-dashboard-charts-grid">
    
    <!-- Left Chart: Revenue & Order Trajectory -->
    <section class="eq-admin-card" style="margin-bottom: 0; min-width: 0; overflow: hidden; display: flex; flex-direction: column;">
      <div class="eq-admin-card__header" style="margin-bottom: 1rem;">
        <div>
          <h3 class="eq-admin-card__title" style="font-size: 1.05rem;">
            Sales Revenue &amp; Order Volume Trend
          </h3>
          <span style="font-size: 0.78rem; color: var(--eq-charcoal-soft);">
            Daily commercial performance over the past 14 days
          </span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.75rem;">
          <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
            <span style="width: 10px; height: 10px; background: var(--eq-gold); border-radius: 2px;"></span>
            Revenue (৳)
          </span>
          <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
            <span style="width: 10px; height: 10px; background: var(--eq-navy); border-radius: 2px;"></span>
            Orders
          </span>
        </div>
      </div>
      <div class="eq-chart-wrapper">
        <canvas id="revenueTrendChart"></canvas>
      </div>
    </section>

    <!-- Right Chart: Category Distribution -->
    <section class="eq-admin-card" style="margin-bottom: 0; min-width: 0; overflow: hidden; display: flex; flex-direction: column;">
      <div class="eq-admin-card__header" style="margin-bottom: 1rem;">
        <div>
          <h3 class="eq-admin-card__title" style="font-size: 1.05rem;">
            Category Sales Share
          </h3>
          <span style="font-size: 0.78rem; color: var(--eq-charcoal-soft);">
            Revenue distribution by parent category
          </span>
        </div>
      </div>
      <div class="eq-chart-wrapper" style="display: flex; align-items: center; justify-content: center;">
        <canvas id="categorySalesChart"></canvas>
      </div>
    </section>

  </div>

  <!-- Secondary Insights Grid (Top Creations & Export) -->
  <div class="eq-dashboard-widgets-grid">
    
    <!-- Top 5 Best-Selling Creations -->
    <section class="eq-admin-card" style="margin-bottom: 0; min-width: 0; overflow: hidden;">
      <div class="eq-admin-card__header">
        <div>
          <h3 class="eq-admin-card__title" style="font-size: 1.05rem;">
            Top Performing Creations
          </h3>
          <span style="font-size: 0.78rem; color: var(--eq-charcoal-soft);">
            Ranked by units sold across completed orders
          </span>
        </div>
      </div>

      <div class="eq-admin-table-wrap" style="width: 100%; min-width: 0; overflow-x: auto;">
        <table class="eq-admin-table" style="font-size: 0.84rem; min-width: 360px;">
          <thead>
            <tr>
              <th>Piece</th>
              <th>Category</th>
              <th style="text-align: center;">Sold</th>
              <th style="text-align: right;">Revenue</th>
            </tr>
          </thead>
          <tbody>
            @forelse($topProducts as $top)
              <tr>
                <td>
                  <div style="display: flex; align-items: center; gap: 0.75rem;">
                    @if($top->product_image)
                      <img src="{{ asset($top->product_image) }}" alt="{{ $top->product_name }}" style="width: 36px; height: 46px; object-fit: cover; border-radius: 4px; border: 1px solid var(--eq-line); flex-shrink: 0;" />
                    @endif
                    <div style="min-width: 0;">
                      @if($top->product_slug)
                        <a href="{{ route('product.show', $top->product_slug) }}" target="_blank" style="font-weight: 500; color: var(--eq-navy); text-decoration: none; word-break: break-word;">
                          {{ $top->product_name }}
                        </a>
                      @else
                        <span style="font-weight: 500; color: var(--eq-navy); word-break: break-word;">{{ $top->product_name }}</span>
                      @endif
                    </div>
                  </div>
                </td>
                <td style="color: var(--eq-charcoal-soft);">
                  {{ $top->category_name ?? 'Atelier' }}
                </td>
                <td style="text-align: center; font-weight: 600;">
                  {{ $top->units_sold }}
                </td>
                <td style="text-align: right; font-weight: 600; color: var(--eq-gold-dark); white-space: nowrap;">
                  ৳{{ number_format($top->total_revenue) }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" style="text-align: center; padding: 2rem; color: var(--eq-charcoal-soft);">
                  No order item records yet to aggregate top products.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    <!-- Quick Ledger & Order Export Card -->
    <section class="eq-admin-card" style="margin-bottom: 0; min-width: 0; overflow: hidden;">
      <div class="eq-admin-card__header">
        <div>
          <h3 class="eq-admin-card__title" style="font-size: 1.05rem;">
            Report &amp; Ledger Export
          </h3>
          <span style="font-size: 0.78rem; color: var(--eq-charcoal-soft);">
            Filtered downloads for Excel spreadsheets
          </span>
        </div>
      </div>

      <div style="font-size: 0.86rem; color: var(--eq-charcoal); line-height: 1.6; margin-bottom: 1.25rem;">
        <p style="margin-bottom: 0.75rem;">
          Download a complete chronological transaction ledger formatted for Microsoft Excel with UTF-8 encoding.
        </p>
        <div style="display: flex; flex-direction: column; gap: 0.5rem; min-width: 0;">
          <a href="{{ route('admin.orders.export') }}" class="eq-admin-btn eq-admin-btn--primary" style="justify-content: center; padding: 0.6rem; text-align: center; white-space: normal; line-height: 1.35;">
            &darr; Export Complete Orders Ledger
          </a>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(90px, 1fr)); gap: 0.5rem; margin-top: 0.25rem; min-width: 0;">
            <a href="{{ route('admin.orders.export', ['status' => 'pending']) }}" class="eq-admin-btn eq-admin-btn--outline" style="justify-content: center; font-size: 0.78rem; padding: 0.42rem; text-align: center;">
              Pending
            </a>
            <a href="{{ route('admin.orders.export', ['status' => 'processing']) }}" class="eq-admin-btn eq-admin-btn--outline" style="justify-content: center; font-size: 0.78rem; padding: 0.42rem; text-align: center;">
              Processing
            </a>
            <a href="{{ route('admin.orders.export', ['status' => 'delivered']) }}" class="eq-admin-btn eq-admin-btn--outline" style="justify-content: center; font-size: 0.78rem; padding: 0.42rem; text-align: center;">
              Delivered
            </a>
          </div>
        </div>
      </div>

      <div style="background: var(--eq-cream); padding: 0.85rem 1rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.78rem; color: var(--eq-charcoal-soft); word-break: break-word;">
        &bull; Includes Order #, Contacts, Address, Courier Tracking, Line Totals, and Fulfillment Status.
      </div>
    </section>

  </div>

  <!-- Recent Orders Table Section -->
  <section class="eq-admin-card" style="min-width: 0; overflow: hidden;">
    <div class="eq-admin-card__header">
      <div>
        <h2 class="eq-admin-card__title">Recent Customer Orders (Latest 10)</h2>
        <span style="font-size: 0.78rem; color: var(--eq-charcoal-soft);">
          Latest incoming transaction stream
        </span>
      </div>
      <a href="{{ route('admin.orders') }}" class="eq-admin-btn eq-admin-btn--outline">
        View All Orders &rarr;
      </a>
    </div>

    <div class="eq-admin-table-wrap" style="width: 100%; min-width: 0; overflow-x: auto;">
      <table class="eq-admin-table" style="min-width: 720px;">
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
              <td style="font-weight: 600; color: var(--eq-gold-dark); white-space: nowrap;">
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
              <td style="font-size: 0.82rem; color: var(--eq-charcoal-soft); white-space: nowrap;">
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

  <!-- Chart.js Engine Integration -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      
      // 1. Setup Revenue & Order Volume Trend Chart
      const trendLabels = {!! $chartLabelsJson !!};
      const trendRevenues = {!! $chartRevenueJson !!};
      const trendOrders = {!! $chartOrdersJson !!};

      const trendCtx = document.getElementById('revenueTrendChart');
      if (trendCtx) {
        new Chart(trendCtx, {
          type: 'bar',
          data: {
            labels: trendLabels,
            datasets: [
              {
                type: 'line',
                label: 'Revenue (৳)',
                data: trendRevenues,
                borderColor: '#c9962f',
                backgroundColor: 'rgba(201, 150, 47, 0.12)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#c9962f',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 1.5,
                pointRadius: 4,
                yAxisID: 'yRevenue'
              },
              {
                type: 'bar',
                label: 'Orders Count',
                data: trendOrders,
                backgroundColor: 'rgba(27, 58, 75, 0.78)',
                hoverBackgroundColor: '#1b3a4b',
                borderRadius: 4,
                barThickness: 12,
                yAxisID: 'yOrders'
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
              mode: 'index',
              intersect: false
            },
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                backgroundColor: '#1b3a4b',
                titleColor: '#ffffff',
                bodyColor: '#f7f2e9',
                padding: 10,
                boxPadding: 4,
                usePointStyle: true,
                callbacks: {
                  label: function (context) {
                    let label = context.dataset.label || '';
                    if (label) {
                      label += ': ';
                    }
                    if (context.dataset.yAxisID === 'yRevenue') {
                      label += '৳' + Number(context.parsed.y).toLocaleString();
                    } else {
                      label += context.parsed.y + ' orders';
                    }
                    return label;
                  }
                }
              }
            },
            scales: {
              x: {
                grid: {
                  display: false
                },
                ticks: {
                  font: {
                    family: 'Jost, sans-serif',
                    size: 11
                  },
                  color: '#8c857b'
                }
              },
              yRevenue: {
                type: 'linear',
                position: 'left',
                beginAtZero: true,
                grid: {
                  color: 'rgba(42, 38, 34, 0.06)'
                },
                ticks: {
                  font: {
                    family: 'Jost, sans-serif',
                    size: 11
                  },
                  color: '#8c857b',
                  callback: function (value) {
                    return '৳' + (value >= 1000 ? (value / 1000) + 'k' : value);
                  }
                }
              },
              yOrders: {
                type: 'linear',
                position: 'right',
                beginAtZero: true,
                grid: {
                  drawOnChartArea: false
                },
                ticks: {
                  stepSize: 1,
                  font: {
                    family: 'Jost, sans-serif',
                    size: 11
                  },
                  color: '#8c857b'
                }
              }
            }
          }
        });
      }

      // 2. Setup Category Sales Doughnut Chart
      const categoryLabels = {!! $categoryLabelsJson !!};
      const categoryData = {!! $categoryDataJson !!};
      const categoryCtx = document.getElementById('categorySalesChart');

      if (categoryCtx) {
        if (categoryLabels.length === 0 || categoryData.every(v => v === 0)) {
          new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
              labels: ['No Sales Recorded Yet'],
              datasets: [{
                data: [1],
                backgroundColor: ['#e2e8f0'],
                borderWidth: 0
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'bottom',
                  labels: {
                    font: { family: 'Jost, sans-serif', size: 12 },
                    color: '#8c857b'
                  }
                },
                tooltip: { enabled: false }
              },
              cutout: '70%'
            }
          });
        } else {
          new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
              labels: categoryLabels,
              datasets: [{
                data: categoryData,
                backgroundColor: [
                  '#1b3a4b', // Navy
                  '#c9962f', // Gold
                  '#2c7a78', // Teal
                  '#b45309', // Amber
                  '#991b1b', // Burgundy
                  '#64748b'  // Slate
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'bottom',
                  labels: {
                    boxWidth: 12,
                    padding: 12,
                    font: {
                      family: 'Jost, sans-serif',
                      size: 11
                    },
                    color: '#5c564d'
                  }
                },
                tooltip: {
                  backgroundColor: '#1b3a4b',
                  titleColor: '#ffffff',
                  bodyColor: '#f7f2e9',
                  callbacks: {
                    label: function (context) {
                      const value = context.parsed;
                      return ' ' + context.label + ': ৳' + Number(value).toLocaleString();
                    }
                  }
                }
              },
              cutout: '68%'
            }
          });
        }
      }

    });
  </script>

@endsection
@extends('admin.layout')

@section('title', 'Coupons & Campaign Discounts — Earthquick Admin')
@section('header_title', 'Coupons & Offers Management')

@push('styles')
<style>
  /* Scoped Styles for Coupons Dashboard */
  .eq-coupon-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.25rem;
    background: #ffffff;
    border: 1px solid var(--eq-line);
    border-radius: 10px;
    padding: 1.15rem 1.35rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
  }

  .eq-coupon-hero__title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--eq-navy);
    margin: 0 0 0.25rem 0;
    font-family: var(--font-display, inherit);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .eq-coupon-hero__desc {
    font-size: 0.83rem;
    color: var(--eq-charcoal-soft);
    margin: 0;
    line-height: 1.4;
  }

  /* 4-Card Responsive KPI Strip */
  .eq-coupon-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
    width: 100%;
  }

  .eq-coupon-card {
    background: #ffffff;
    border: 1px solid var(--eq-line);
    border-radius: 10px;
    padding: 1rem 1.15rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 2px 6px rgba(27, 58, 75, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    position: relative;
    overflow: hidden;
  }

  .eq-coupon-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(27, 58, 75, 0.08);
    border-color: var(--eq-gold);
  }

  .eq-coupon-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
  }

  .eq-coupon-card--navy::before { background: var(--eq-navy); }
  .eq-coupon-card--emerald::before { background: #057a55; }
  .eq-coupon-card--gold::before { background: var(--eq-gold); }
  .eq-coupon-card--terracotta::before { background: #92400e; }

  .eq-coupon-card__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.65rem;
  }

  .eq-coupon-card__icon-wrap {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .eq-coupon-card--navy .eq-coupon-card__icon-wrap { background: rgba(27, 58, 75, 0.08); color: var(--eq-navy); }
  .eq-coupon-card--emerald .eq-coupon-card__icon-wrap { background: rgba(5, 122, 85, 0.1); color: #057a55; }
  .eq-coupon-card--gold .eq-coupon-card__icon-wrap { background: rgba(201, 150, 47, 0.12); color: var(--eq-gold-dark); }
  .eq-coupon-card--terracotta .eq-coupon-card__icon-wrap { background: rgba(146, 64, 14, 0.1); color: #92400e; }

  .eq-coupon-card__badge {
    font-size: 0.68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.15rem 0.5rem;
    border-radius: 999px;
  }

  .eq-coupon-card--navy .eq-coupon-card__badge { background: #e0f2fe; color: #0369a1; }
  .eq-coupon-card--emerald .eq-coupon-card__badge { background: #dcfce7; color: #15803d; }
  .eq-coupon-card--gold .eq-coupon-card__badge { background: #fef3c7; color: #92400e; }
  .eq-coupon-card--terracotta .eq-coupon-card__badge { background: #fee2e2; color: #991b1b; }

  .eq-coupon-card__value {
    font-family: var(--font-display, inherit);
    font-size: 1.65rem;
    font-weight: 700;
    line-height: 1.1;
    margin-bottom: 0.25rem;
    letter-spacing: -0.02em;
  }

  .eq-coupon-card--navy .eq-coupon-card__value { color: var(--eq-navy); }
  .eq-coupon-card--emerald .eq-coupon-card__value { color: #057a55; }
  .eq-coupon-card--gold .eq-coupon-card__value { color: var(--eq-gold-dark); }
  .eq-coupon-card--terracotta .eq-coupon-card__value { color: #92400e; }

  .eq-coupon-card__label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--eq-charcoal);
    margin-bottom: 0.15rem;
  }

  .eq-coupon-card__sub {
    font-size: 0.73rem;
    color: var(--eq-charcoal-soft);
  }

  /* Filter & Control Toolbar */
  .eq-coupon-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
    padding: 0.85rem 1.25rem;
    background: #fbf9f6;
    border-bottom: 1px solid var(--eq-line);
  }

  .eq-coupon-search-box {
    position: relative;
    min-width: 240px;
    flex: 1;
    max-width: 380px;
  }

  .eq-coupon-search-box svg {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--eq-charcoal-soft);
    pointer-events: none;
  }

  .eq-coupon-search-box input {
    width: 100%;
    padding: 0.45rem 0.85rem 0.45rem 2.25rem;
    font-size: 0.82rem;
    border: 1px solid var(--eq-line);
    border-radius: 6px;
    background: #ffffff;
    color: var(--eq-charcoal);
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }

  .eq-coupon-search-box input:focus {
    border-color: var(--eq-gold);
    box-shadow: 0 0 0 3px rgba(201, 150, 47, 0.15);
  }

  .eq-coupon-filter-chips {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    flex-wrap: wrap;
  }

  .eq-coupon-chip {
    padding: 0.35rem 0.75rem;
    font-size: 0.76rem;
    font-weight: 600;
    border-radius: 999px;
    border: 1px solid var(--eq-line);
    background: #ffffff;
    color: var(--eq-charcoal-soft);
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
  }

  .eq-coupon-chip.is-active,
  .eq-coupon-chip:hover {
    background: var(--eq-navy);
    color: #ffffff;
    border-color: var(--eq-navy);
  }

  /* Usage Mini Progress Bar */
  .eq-usage-bar {
    width: 100%;
    height: 5px;
    background: #e5e7eb;
    border-radius: 999px;
    overflow: hidden;
    margin-top: 0.3rem;
  }

  .eq-usage-fill {
    height: 100%;
    background: var(--eq-gold);
    border-radius: 999px;
  }

  /* Responsive Media Queries */
  @media (max-width: 1024px) {
    .eq-coupon-kpi-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 0.85rem;
    }
  }

  @media (max-width: 640px) {
    .eq-coupon-hero {
      padding: 0.9rem 1rem;
      margin-bottom: 0.85rem;
    }

    .eq-coupon-hero__title {
      font-size: 1.1rem;
    }

    .eq-coupon-hero a.eq-admin-btn {
      width: 100%;
      justify-content: center;
      padding: 0.55rem 1rem;
    }

    .eq-coupon-kpi-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 0.55rem;
      margin-bottom: 1rem;
    }

    .eq-coupon-card {
      padding: 0.75rem 0.85rem;
      border-radius: 8px;
    }

    .eq-coupon-card__icon-wrap {
      width: 30px;
      height: 30px;
    }

    .eq-coupon-card__icon-wrap svg {
      width: 15px;
      height: 15px;
    }

    .eq-coupon-card__badge {
      font-size: 0.6rem;
      padding: 0.1rem 0.35rem;
    }

    .eq-coupon-card__value {
      font-size: 1.28rem;
    }

    .eq-coupon-card__label {
      font-size: 0.74rem;
    }

    .eq-coupon-card__sub {
      font-size: 0.68rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .eq-coupon-toolbar {
      padding: 0.65rem 0.85rem;
    }

    .eq-coupon-search-box {
      max-width: 100%;
      min-width: 100%;
    }
  }
</style>
@endpush

@section('content')
<!-- Header & Action Ribbon -->
<div class="eq-coupon-hero">
  <div>
    <div style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--eq-gold-dark); margin-bottom: 0.2rem;">
      Marketing &bull; Campaign Engine
    </div>
    <h2 class="eq-coupon-hero__title">
      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="var(--eq-gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
        <line x1="7" y1="7" x2="7.01" y2="7"></line>
      </svg>
      <span>Coupons &amp; Promotional Discounts</span>
    </h2>
    <p class="eq-coupon-hero__desc">
      Manage seasonal vouchers, percentage deductions, and customer loyalty rewards directly redeemable at checkout.
    </p>
  </div>
  <div>
    <a href="{{ route('admin.coupons.create') }}" class="eq-admin-btn eq-admin-btn--primary" style="display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.6rem 1.25rem; font-size: 0.85rem; font-weight: 600; text-decoration: none; border-radius: 6px; box-shadow: 0 2px 8px rgba(201, 150, 47, 0.25);">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Create Campaign Coupon</span>
    </a>
  </div>
</div>

<!-- 4 Elegantly Designed KPI Stat Cards (Desktop 4-col, Mobile 2x2 grid) -->
<section class="eq-coupon-kpi-grid">
  <!-- Card 1: Total Campaigns -->
  <div class="eq-coupon-card eq-coupon-card--navy">
    <div class="eq-coupon-card__top">
      <div class="eq-coupon-card__icon-wrap">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
          <line x1="7" y1="7" x2="7.01" y2="7"></line>
        </svg>
      </div>
      <span class="eq-coupon-card__badge">All Promos</span>
    </div>
    <div class="eq-coupon-card__value">{{ number_format($totalCampaigns) }}</div>
    <div>
      <div class="eq-coupon-card__label">Total Campaigns</div>
      <div class="eq-coupon-card__sub">All registered promos</div>
    </div>
  </div>

  <!-- Card 2: Active Offers -->
  <div class="eq-coupon-card eq-coupon-card--emerald">
    <div class="eq-coupon-card__top">
      <div class="eq-coupon-card__icon-wrap">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
      </div>
      <span class="eq-coupon-card__badge">Live Now</span>
    </div>
    <div class="eq-coupon-card__value">{{ number_format($activeCampaigns) }}</div>
    <div>
      <div class="eq-coupon-card__label">Active Offers</div>
      <div class="eq-coupon-card__sub">Currently redeemable</div>
    </div>
  </div>

  <!-- Card 3: Total Redemptions -->
  <div class="eq-coupon-card eq-coupon-card--gold">
    <div class="eq-coupon-card__top">
      <div class="eq-coupon-card__icon-wrap">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
      </div>
      <span class="eq-coupon-card__badge">Usage Count</span>
    </div>
    <div class="eq-coupon-card__value">{{ number_format($totalRedemptions) }}</div>
    <div>
      <div class="eq-coupon-card__label">Total Redemptions</div>
      <div class="eq-coupon-card__sub">Times applied by clients</div>
    </div>
  </div>

  <!-- Card 4: Total Discount Disbursed -->
  <div class="eq-coupon-card eq-coupon-card--terracotta">
    <div class="eq-coupon-card__top">
      <div class="eq-coupon-card__icon-wrap">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="1" x2="12" y2="23"></line>
          <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
        </svg>
      </div>
      <span class="eq-coupon-card__badge">Savings</span>
    </div>
    <div class="eq-coupon-card__value">৳{{ number_format($totalDiscountGiven) }}</div>
    <div>
      <div class="eq-coupon-card__label">Total Disbursed</div>
      <div class="eq-coupon-card__sub">Customer savings to date</div>
    </div>
  </div>
</section>

<!-- Coupons Main Container -->
<div class="eq-admin-card" style="padding: 0; overflow: hidden; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);">
  <!-- Toolbar: Search & Filter -->
  <div class="eq-coupon-toolbar">
    <div class="eq-coupon-search-box">
      <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="text" id="coupon-table-search" placeholder="Search coupon code or campaign..." onkeyup="filterCoupons(this.value)" />
    </div>

    <div class="eq-coupon-filter-chips">
      <span style="font-size: 0.78rem; color: var(--eq-charcoal-soft); margin-right: 0.35rem;">
        Total: <strong>{{ $coupons->total() }}</strong> {{ Str::plural('voucher', $coupons->total()) }}
      </span>
      <button type="button" class="eq-coupon-chip is-active" onclick="filterByStatus('all', this)">All</button>
      <button type="button" class="eq-coupon-chip" onclick="filterByStatus('active', this)">Active</button>
      <button type="button" class="eq-coupon-chip" onclick="filterByStatus('disabled', this)">Disabled</button>
    </div>
  </div>

  @if($coupons->isEmpty())
    <div style="text-align: center; padding: 4rem 1.5rem;">
      <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(201, 150, 47, 0.1); color: var(--eq-gold-dark); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
        <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
          <line x1="7" y1="7" x2="7.01" y2="7"></line>
        </svg>
      </div>
      <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--eq-navy); margin-bottom: 0.35rem;">No Campaign Coupons Found</h3>
      <p style="font-size: 0.85rem; color: var(--eq-charcoal-soft); max-width: 440px; margin: 0 auto 1.5rem; line-height: 1.5;">
        You haven't published any seasonal coupons yet. Create promo codes for festivals (Eid, Puja) or personal VIP discounts.
      </p>
      <a href="{{ route('admin.coupons.create') }}" class="eq-admin-btn eq-admin-btn--primary" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1.25rem; font-size: 0.84rem; text-decoration: none; border-radius: 6px;">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Create First Coupon</span>
      </a>
    </div>
  @else
    <!-- DESKTOP TABLE VIEW (>= 861px) -->
    <div class="eq-desktop-only">
      <table class="eq-admin-table" style="margin: 0;" id="admin-coupons-table">
        <thead>
          <tr style="background: #faf8f5;">
            <th style="padding: 0.85rem 1rem;">Code</th>
            <th>Campaign Label</th>
            <th>Discount Value</th>
            <th>Min Spend</th>
            <th>Usage Progress</th>
            <th>Validity</th>
            <th>Status</th>
            <th style="text-align: right; padding-right: 1.25rem;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($coupons as $coupon)
            @php
              $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
              $isLimitReached = $coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit;
              $usagePct = $coupon->usage_limit ? min(100, round(($coupon->used_count / $coupon->usage_limit) * 100)) : 0;
            @endphp
            <tr class="coupon-row" data-code="{{ strtolower($coupon->code) }}" data-name="{{ strtolower($coupon->name) }}" data-status="{{ !$coupon->is_active ? 'disabled' : ($isExpired ? 'expired' : 'active') }}">
              <td style="padding: 0.85rem 1rem;">
                <div style="display: inline-flex; align-items: center; gap: 0.4rem;">
                  <span style="font-family: monospace; font-size: 0.88rem; font-weight: 700; background: rgba(27, 58, 75, 0.08); color: var(--eq-navy); padding: 0.25rem 0.55rem; border-radius: 4px; border: 1px dashed rgba(27, 58, 75, 0.35); letter-spacing: 0.5px;">
                    {{ $coupon->code }}
                  </span>
                  <button type="button" onclick="navigator.clipboard.writeText('{{ $coupon->code }}'); alert('Coupon code {{ $coupon->code }} copied!');" style="background: none; border: none; padding: 2px; color: var(--eq-charcoal-soft); cursor: pointer;" title="Copy Code">
                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                      <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                  </button>
                </div>
              </td>
              <td>
                <div style="font-weight: 600; color: var(--eq-navy); font-size: 0.86rem;">
                  {{ $coupon->name ?: 'Standard Store Promo' }}
                </div>
                <div style="font-size: 0.72rem; color: var(--eq-charcoal-soft);">
                  Created {{ $coupon->created_at->format('M d, Y') }}
                </div>
              </td>
              <td>
                @if($coupon->type === 'percent')
                  <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 700; color: #046c4e; background: #def7ec; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.8rem;">
                    {{ number_format($coupon->value, 0) }}% OFF
                  </span>
                  @if($coupon->max_discount)
                    <div style="font-size: 0.71rem; color: var(--eq-charcoal-soft); margin-top: 2px;">
                      Max: ৳{{ number_format($coupon->max_discount) }}
                    </div>
                  @endif
                @else
                  <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 700; color: #1e40af; background: #dbeafe; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.8rem;">
                    ৳{{ number_format($coupon->value) }} FLAT
                  </span>
                @endif
              </td>
              <td>
                @if($coupon->min_order_amount)
                  <span style="font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal);">
                    ৳{{ number_format($coupon->min_order_amount) }}
                  </span>
                @else
                  <span style="font-size: 0.8rem; color: var(--eq-charcoal-soft); font-style: italic;">
                    None
                  </span>
                @endif
              </td>
              <td style="min-width: 130px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.78rem; font-weight: 600; color: var(--eq-charcoal);">
                  <span>{{ $coupon->used_count }} redeemed</span>
                  <span style="color: var(--eq-charcoal-soft);">{{ $coupon->usage_limit ? number_format($coupon->usage_limit) : '∞' }}</span>
                </div>
                @if($coupon->usage_limit)
                  <div class="eq-usage-bar">
                    <div class="eq-usage-fill" style="width: {{ $usagePct }}%; {{ $usagePct >= 100 ? 'background: #dc2626;' : '' }}"></div>
                  </div>
                @endif
              </td>
              <td>
                @if($coupon->expires_at)
                  <div style="font-size: 0.8rem; font-weight: 500; color: {{ $isExpired ? '#991b1b' : 'var(--eq-charcoal)' }};">
                    {{ $coupon->expires_at->format('M d, Y') }}
                  </div>
                  <div style="font-size: 0.71rem; color: {{ $isExpired ? '#991b1b' : 'var(--eq-charcoal-soft)' }};">
                    {{ $coupon->expires_at->format('h:i A') }} {{ $isExpired ? '(Expired)' : '' }}
                  </div>
                @else
                  <span style="font-size: 0.8rem; color: #046c4e; font-weight: 500; display: inline-flex; align-items: center; gap: 0.25rem;">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                    Evergreen
                  </span>
                @endif
              </td>
              <td>
                @if(!$coupon->is_active)
                  <span class="eq-status-pill eq-status-pill--cancelled" style="background: #f3f4f6; color: #4b5563; font-size: 0.72rem;">
                    Disabled
                  </span>
                @elseif($isExpired)
                  <span class="eq-status-pill eq-status-pill--cancelled" style="background: #fef2f2; color: #991b1b; font-size: 0.72rem;">
                    Expired
                  </span>
                @elseif($isLimitReached)
                  <span class="eq-status-pill eq-status-pill--processing" style="background: #fef3c7; color: #92400e; font-size: 0.72rem;">
                    Exhausted
                  </span>
                @else
                  <span class="eq-status-pill eq-status-pill--delivered" style="background: #def7ec; color: #03543f; font-size: 0.72rem;">
                    Active
                  </span>
                @endif
              </td>
              <td style="text-align: right; padding-right: 1.25rem;">
                <div style="display: inline-flex; align-items: center; gap: 0.4rem;">
                  <!-- Toggle Status Button -->
                  <form method="POST" action="{{ route('admin.coupons.toggle', $coupon->id) }}" style="display: inline; margin: 0;">
                    @csrf
                    <button type="submit" class="eq-admin-btn {{ $coupon->is_active ? 'eq-admin-btn--secondary' : 'eq-admin-btn--primary' }}" style="padding: 0.3rem 0.7rem; font-size: 0.75rem; border-radius: 5px;" title="{{ $coupon->is_active ? 'Disable this offer' : 'Enable this offer' }}">
                      {{ $coupon->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                  </form>

                  <!-- Delete Button -->
                  <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" style="display: inline; margin: 0;" onsubmit="return confirm('Are you sure you want to permanently delete coupon \'{{ $coupon->code }}\'?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: #fdf2f2; border: 1px solid #fecaca; padding: 0.35rem; color: #dc2626; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; border-radius: 5px; transition: all 0.2s ease;" title="Delete Coupon">
                      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                      </svg>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- MOBILE & TABLET ADAPTIVE CARDS VIEW (<= 860px) -->
    <div class="eq-mobile-only" style="padding: 0.75rem;">
      @foreach($coupons as $coupon)
        @php
          $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
          $isLimitReached = $coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit;
          $usagePct = $coupon->usage_limit ? min(100, round(($coupon->used_count / $coupon->usage_limit) * 100)) : 0;
        @endphp
        <div class="coupon-row eq-card-item" data-code="{{ strtolower($coupon->code) }}" data-name="{{ strtolower($coupon->name) }}" data-status="{{ !$coupon->is_active ? 'disabled' : ($isExpired ? 'expired' : 'active') }}" style="border-radius: 10px; border: 1px solid var(--eq-line); padding: 0.95rem; margin-bottom: 0.75rem; background: var(--eq-white); box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);">
          <!-- Top Row: Code Badge & Status Pill -->
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; gap: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.4rem;">
              <span style="font-family: monospace; font-size: 0.96rem; font-weight: 700; color: var(--eq-navy); background: rgba(27, 58, 75, 0.08); padding: 0.25rem 0.6rem; border-radius: 5px; border: 1px dashed rgba(27, 58, 75, 0.35); letter-spacing: 0.5px;">
                {{ $coupon->code }}
              </span>
              <button type="button" onclick="navigator.clipboard.writeText('{{ $coupon->code }}'); alert('Code copied!');" style="background: none; border: none; padding: 2px; color: var(--eq-charcoal-soft); cursor: pointer;" title="Copy">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
              </button>
            </div>

            @if(!$coupon->is_active)
              <span class="eq-status-pill" style="background: #f3f4f6; color: #4b5563; font-size: 0.7rem; padding: 0.2rem 0.55rem;">Disabled</span>
            @elseif($isExpired)
              <span class="eq-status-pill" style="background: #fef2f2; color: #991b1b; font-size: 0.7rem; padding: 0.2rem 0.55rem;">Expired</span>
            @elseif($isLimitReached)
              <span class="eq-status-pill" style="background: #fef3c7; color: #92400e; font-size: 0.7rem; padding: 0.2rem 0.55rem;">Exhausted</span>
            @else
              <span class="eq-status-pill" style="background: #def7ec; color: #03543f; font-size: 0.7rem; padding: 0.2rem 0.55rem;">Active</span>
            @endif
          </div>

          <!-- Campaign Label -->
          <div style="font-size: 0.9rem; font-weight: 700; color: var(--eq-navy); margin-bottom: 0.65rem;">
            {{ $coupon->name ?: 'Standard Promotion Campaign' }}
          </div>

          <!-- 2x2 Metric Grid -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; background: #faf8f5; padding: 0.65rem 0.85rem; border-radius: 8px; font-size: 0.8rem; margin-bottom: 0.75rem; border: 1px solid rgba(27, 58, 75, 0.05);">
            <div>
              <span style="color: var(--eq-charcoal-soft); display: block; font-size: 0.72rem;">Discount Value:</span>
              <strong style="color: #046c4e;">
                @if($coupon->type === 'percent')
                  {{ number_format($coupon->value, 0) }}% Off {{ $coupon->max_discount ? '(Cap: ৳' . number_format($coupon->max_discount) . ')' : '' }}
                @else
                  ৳{{ number_format($coupon->value) }} Flat
                @endif
              </strong>
            </div>

            <div>
              <span style="color: var(--eq-charcoal-soft); display: block; font-size: 0.72rem;">Min Spend:</span>
              <strong style="color: var(--eq-charcoal);">
                {{ $coupon->min_order_amount ? '৳' . number_format($coupon->min_order_amount) : 'None' }}
              </strong>
            </div>

            <div>
              <span style="color: var(--eq-charcoal-soft); display: block; font-size: 0.72rem;">Redemptions:</span>
              <strong>{{ $coupon->used_count }} / {{ $coupon->usage_limit ? number_format($coupon->usage_limit) : '∞' }}</strong>
            </div>

            <div>
              <span style="color: var(--eq-charcoal-soft); display: block; font-size: 0.72rem;">Validity:</span>
              <strong style="{{ $isExpired ? 'color:#991b1b;' : 'color:var(--eq-charcoal);' }}">
                {{ $coupon->expires_at ? $coupon->expires_at->format('d M Y') : 'Evergreen' }}
              </strong>
            </div>
          </div>

          <!-- Action Buttons Bar -->
          <div style="display: grid; grid-template-columns: 1fr auto; gap: 0.5rem; align-items: center;">
            <form method="POST" action="{{ route('admin.coupons.toggle', $coupon->id) }}" style="margin: 0;">
              @csrf
              <button type="submit" class="eq-admin-btn {{ $coupon->is_active ? 'eq-admin-btn--secondary' : 'eq-admin-btn--primary' }}" style="width: 100%; justify-content: center; min-height: 40px; padding: 0.45rem 0.85rem; font-size: 0.8rem; font-weight: 600; border-radius: 6px;">
                {{ $coupon->is_active ? 'Deactivate Offer' : 'Activate Offer' }}
              </button>
            </form>

            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" style="margin: 0;" onsubmit="return confirm('Delete coupon \'{{ $coupon->code }}\'?');">
              @csrf
              @method('DELETE')
              <button type="submit" style="min-height: 40px; min-width: 44px; background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; border-radius: 6px; padding: 0.45rem; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Delete Coupon">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="3 6 5 6 21 6"></polyline>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
              </button>
            </form>
          </div>
        </div>
      @endforeach
    </div>

    @include('partials.pagination-polished', ['paginator' => $coupons])
  @endif
</div>
@endsection

@push('scripts')
<script>
  function filterCoupons(query) {
    const term = query.toLowerCase().trim();
    const rows = document.querySelectorAll('.coupon-row');
    rows.forEach(row => {
      const code = row.getAttribute('data-code') || '';
      const name = row.getAttribute('data-name') || '';
      if (code.includes(term) || name.includes(term)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  function filterByStatus(status, btn) {
    document.querySelectorAll('.eq-coupon-chip').forEach(el => el.classList.remove('is-active'));
    if (btn) btn.classList.add('is-active');

    const rows = document.querySelectorAll('.coupon-row');
    rows.forEach(row => {
      const rowStatus = row.getAttribute('data-status') || '';
      if (status === 'all') {
        row.style.display = '';
      } else if (status === 'active' && rowStatus === 'active') {
        row.style.display = '';
      } else if (status === 'disabled' && (rowStatus === 'disabled' || rowStatus === 'expired')) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }
</script>
@endpush

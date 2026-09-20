@extends('layouts.app')

@section('title', 'My Account & Orders — Earthquick by Nous Telos')
@section('body_class', 'eq-checkout-page')

@section('content')
<!-- Breadcrumb Bar -->
<nav class="eq-breadcrumb" aria-label="Breadcrumb" style="padding: 0.9rem 0; border-bottom: 1px solid var(--eq-line); background: var(--eq-white);">
  <div class="eq-container">
    <ol class="eq-breadcrumb__list" style="display: flex; align-items: center; gap: 0.5rem; list-style: none; margin: 0; padding: 0; font-size: 0.84rem; color: var(--eq-charcoal-soft);">
      <li><a href="{{ route('home') }}" style="color: inherit; text-decoration: none;">Home</a></li>
      <li style="color: var(--eq-line-dark);">&rsaquo;</li>
      <li style="color: var(--eq-charcoal); font-weight: 500;" aria-current="page">My Account</li>
    </ol>
  </div>
</nav>

<!-- Hero Section -->
<section class="eq-checkout-hero">
  <div class="eq-container">
    <h1 class="eq-checkout-hero__title">My Account &amp; Orders</h1>
    <p class="eq-checkout-hero__desc">Manage your orders, shipment status, and personal preferences.</p>
  </div>
</section>

<!-- Main Dashboard Grid -->
<main class="eq-container" id="main-content" style="padding-bottom: 5rem;">
  
  @if(session('success'))
    <div style="background: rgba(39, 174, 96, 0.12); color: #27ae60; padding: 0.9rem 1.25rem; border-radius: 6px; font-size: 0.9rem; margin-bottom: 1.5rem; border: 1px solid rgba(39, 174, 96, 0.3);">
      {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div style="background: #fdf2f2; color: #9b1c1c; padding: 0.9rem 1.25rem; border-radius: 6px; font-size: 0.9rem; margin-bottom: 1.5rem; border: 1px solid #f8b4b4;">
      <ul style="margin: 0; padding-left: 1.2rem;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="eq-account-layout">
    
    <!-- SIDEBAR NAVIGATION -->
    <aside class="eq-account-sidebar">
      <!-- Box 1: User Profile Header Info Box -->
      <div class="eq-account-user-badge">
        <div class="eq-account-avatar" id="sidebar-user-avatar">
          {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div style="min-width: 0; flex: 1;">
          <div style="font-weight: 600; color: var(--eq-charcoal); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" id="sidebar-user-name">
            {{ $user->name }}
          </div>
          <div style="font-size: 0.78rem; color: var(--eq-charcoal-soft); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" id="sidebar-user-phone">
            {{ $user->phone ?? $user->email }}
          </div>
        </div>
        <div class="eq-account-badge-actions">
          <span class="eq-account-status-pill">Active</span>
          <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="eq-account-mobile-logout-btn" id="btn-account-logout-mobile" title="Sign Out">
              <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
              <span>Logout</span>
            </button>
          </form>
        </div>
      </div>

      <!-- Box 2: Navigation Tabs Box -->
      <div class="eq-account-nav-box">
        <ul class="eq-account-nav">
          <li class="eq-account-nav-item">
            <button type="button" class="eq-account-nav-btn is-active" data-tab="orders" id="tab-btn-orders">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
              </svg>
              <span class="eq-nav-label-desktop">My Orders ({{ $orders->count() }})</span>
              <span class="eq-nav-label-mobile">Orders ({{ $orders->count() }})</span>
            </button>
          </li>
          <li class="eq-account-nav-item">
            <button type="button" class="eq-account-nav-btn" data-tab="profile" id="tab-btn-profile">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <span class="eq-nav-label-desktop">Profile Information</span>
              <span class="eq-nav-label-mobile">Profile</span>
            </button>
          </li>
          <li class="eq-account-nav-item">
            <button type="button" class="eq-account-nav-btn" data-tab="address" id="tab-btn-address">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
              <span class="eq-nav-label-desktop">Delivery Address</span>
              <span class="eq-nav-label-mobile">Address</span>
            </button>
          </li>
          <li class="eq-account-nav-item eq-account-nav-item--logout-desktop">
            <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
              @csrf
              <button type="submit" class="eq-account-nav-btn eq-account-nav-btn--logout" id="btn-account-logout" style="width: 100%; border: none; background: none; text-align: left; cursor: pointer;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                  <polyline points="16 17 21 12 16 7"></polyline>
                  <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Sign Out</span>
              </button>
            </form>
          </li>
        </ul>
      </div>
    </aside>

    <!-- CONTENT PANELS -->
    <section class="eq-account-content">
      
      <!-- PANEL 1: ORDERS -->
      <div class="eq-account-panel is-active" id="panel-orders">
        <div class="eq-checkout-card">
          <div class="eq-checkout-card__header">
            <h2 class="eq-checkout-card__title">Order History &amp; Parcel Tracking</h2>
          </div>
          
          <div id="orders-list-container">
            @forelse($orders as $ord)
              @php
                $status = strtolower($ord->status);
                $isConfirmed = true;
                $isProcessing = in_array($status, ['processing', 'shipped', 'delivered']);
                $isInTransit = in_array($status, ['shipped', 'delivered']);
                $isDelivered = ($status === 'delivered');

                $statusColor = $isDelivered ? '#27ae60' : ($status === 'cancelled' ? '#e74c3c' : 'var(--eq-gold-dark)');
                $statusBg = $isDelivered ? 'rgba(39, 174, 96, 0.12)' : ($status === 'cancelled' ? 'rgba(231, 76, 60, 0.12)' : 'rgba(201, 150, 47, 0.12)');
              @endphp

              <div class="eq-order-receipt-card" style="margin-bottom: 1.5rem;">
                <div class="eq-receipt-header">
                  <div>
                    <div class="eq-receipt-id" style="font-size: 1.05rem; font-weight: 600; color: var(--eq-charcoal);">
                      Order #{{ $ord->order_number }}
                    </div>
                    <div style="font-size: 0.8rem; color: var(--eq-charcoal-soft); margin-top: 2px;">
                      Placed: {{ $ord->created_at->format('d M Y, h:i A') }} &bull; Destination: {{ $ord->district }}
                    </div>
                  </div>
                  <span class="eq-receipt-badge" style="background-color: {{ $statusBg }}; color: {{ $statusColor }}; font-size: 0.78rem; padding: 0.25rem 0.75rem; border-radius: 999px; font-weight: 600; text-transform: capitalize;">
                    {{ $ord->status }}
                  </span>
                </div>

                <!-- Live Parcel Tracking Progress -->
                <div class="eq-order-tracking-box">
                  <div class="eq-tracking-header">
                    <div class="eq-tracking-title">
                      <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                      </svg>
                      <span>Parcel Tracking Timeline</span>
                    </div>
                    <span class="eq-tracking-courier">
                      Delivery: <strong>{{ $ord->delivery_zone === 'inside_ctg' ? 'Inside Chattogram Express (48h)' : 'Nationwide Courier (2-4d)' }}</strong>
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

                <!-- Order Items List -->
                <div style="margin: 0.75rem 0 1rem; width: 100%; min-width: 0;">
                  @foreach($ord->items as $item)
                    <div class="eq-order-item-row">
                      <div class="eq-order-item-info">
                        <div class="eq-order-item-thumb">
                          @if($item->product_image)
                            <img src="{{ asset($item->product_image) }}" alt="{{ $item->product_name }}" />
                          @else
                            <img src="{{ asset('images/hero/hero-main.jpg') }}" alt="Product" />
                          @endif
                        </div>
                        <div class="eq-order-item-meta">
                          <div class="eq-order-item-name">{{ $item->product_name }}</div>
                          @if($item->variant_label)
                            <div class="eq-order-item-sub">{{ $item->variant_label }}</div>
                          @endif
                          <div class="eq-order-item-sub">Qty: {{ $item->quantity }} &bull; ৳{{ number_format($item->unit_price) }} each</div>
                        </div>
                      </div>
                      <div class="eq-order-item-price">৳{{ number_format($item->total_price) }}</div>
                    </div>
                  @endforeach
                </div>

                <!-- Order Total & Payment Summary -->
                <div class="eq-order-receipt-footer">
                  <div class="eq-order-receipt-pay">
                    Payment: <strong>{{ strtoupper($ord->payment_method) }}</strong> &bull; Delivery: ৳{{ number_format($ord->delivery_fee) }}
                  </div>
                  <div style="display: flex; align-items: center; gap: 1rem;">
                    <div class="eq-order-receipt-total">
                      Total: <span>৳{{ number_format($ord->total) }}</span>
                    </div>
                    <a href="{{ route('account.order', $ord->order_number) }}" class="eq-btn eq-btn--outline" style="padding: 0.35rem 0.85rem; font-size: 0.82rem; text-decoration: none;">
                      View Invoice &rarr;
                    </a>
                  </div>
                </div>
              </div>
            @empty
              <div style="text-align: center; padding: 3rem 1rem;">
                <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--eq-charcoal-muted); margin-bottom: 0.75rem;">
                  <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
                <h3 style="font-family: var(--font-display); font-size: 1.25rem; margin-bottom: 0.35rem;">No orders placed yet</h3>
                <p style="color: var(--eq-charcoal-soft); margin-bottom: 1.25rem; font-size: 0.9rem;">Explore our handwoven sarees, co-ords, and accessories crafted for you.</p>
                <a href="{{ route('category.show', 'women') }}" class="eq-btn eq-btn--primary" style="padding: 0.65rem 1.75rem; text-decoration: none; display: inline-block;">
                  Start Shopping &rarr;
                </a>
              </div>
            @endforelse
          </div>
        </div>
      </div>

      <!-- PANEL 2: PROFILE INFORMATION -->
      <div class="eq-account-panel" id="panel-profile">
        <div class="eq-checkout-card">
          <div class="eq-checkout-card__header">
            <h2 class="eq-checkout-card__title">Personal Profile Details</h2>
          </div>

          <form class="eq-form-grid" id="profile-edit-form" method="POST" action="{{ route('account.profile.update') }}">
            @csrf
            
            <div class="eq-form-group eq-form-group--full">
              <label for="profile-name" class="eq-form-label">Full Name <span class="req">*</span></label>
              <input type="text" id="profile-name" name="name" class="eq-form-input" value="{{ old('name', $user->name) }}" required />
            </div>

            <div class="eq-form-group">
              <label for="profile-phone" class="eq-form-label">Mobile Number <span class="req">*</span></label>
              <input type="tel" id="profile-phone" name="phone" class="eq-form-input" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 01793123456" required />
            </div>

            <div class="eq-form-group">
              <label for="profile-email" class="eq-form-label">Email Address <span class="req">*</span></label>
              <input type="email" id="profile-email" name="email" class="eq-form-input" value="{{ old('email', $user->email) }}" required />
            </div>

            <div class="eq-form-group">
              <label for="profile-city" class="eq-form-label">City / District</label>
              <input type="text" id="profile-city" name="city" class="eq-form-input" value="{{ old('city', $user->city ?? 'Chattogram') }}" />
            </div>

            <div class="eq-form-group">
              <label for="profile-area" class="eq-form-label">Area / Thana</label>
              <input type="text" id="profile-area" name="area" class="eq-form-input" value="{{ old('area', $user->area) }}" placeholder="e.g. GEC Circle, Nasirabad" />
            </div>

            <div class="eq-form-group eq-form-group--full">
              <label for="profile-address" class="eq-form-label">Street Address / House Details</label>
              <textarea id="profile-address" name="address" class="eq-form-textarea" rows="2" placeholder="House #, Road #, Apartment">{{ old('address', $user->address) }}</textarea>
            </div>

            <div class="eq-form-group eq-form-group--full">
              <button type="submit" class="eq-btn eq-btn--primary" style="padding: 0.75rem 2rem; border: none; cursor: pointer; border-radius: 4px;">
                Save Profile Changes
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- PANEL 3: DELIVERY ADDRESS -->
      <div class="eq-account-panel" id="panel-address">
        <div class="eq-checkout-card">
          <div class="eq-checkout-card__header">
            <h2 class="eq-checkout-card__title">Saved Shipping Address</h2>
          </div>

          <div class="eq-form-grid">
            <div class="eq-form-group">
              <label class="eq-form-label">City / Region</label>
              <input type="text" class="eq-form-input" value="{{ $user->city ?? 'Chattogram (Chittagong)' }}" readonly style="background: var(--eq-cream);" />
            </div>
            <div class="eq-form-group">
              <label class="eq-form-label">Area / Thana</label>
              <input type="text" class="eq-form-input" value="{{ $user->area ?? 'Not specified' }}" readonly style="background: var(--eq-cream);" />
            </div>
            <div class="eq-form-group eq-form-group--full">
              <label class="eq-form-label">Full Delivery Address</label>
              <input type="text" class="eq-form-input" value="{{ $user->address ?? 'No address saved yet. Update in profile tab.' }}" readonly style="background: var(--eq-cream);" />
            </div>
          </div>
          <p style="font-size: 0.84rem; color: var(--eq-charcoal-soft); margin-top: 1.25rem;">
            Your saved delivery destination will be automatically pre-filled when you proceed to <a href="{{ route('checkout.index') }}" class="eq-auth-link">Checkout</a>.
          </p>
        </div>
      </div>

    </section>

  </div>
</main>
@endsection

@push('scripts')
<script>
  // Tab Navigation Handling
  document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = document.querySelectorAll('.eq-account-nav-btn[data-tab]');
    const panels = document.querySelectorAll('.eq-account-panel');

    tabButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        tabButtons.forEach(b => b.classList.remove('is-active'));
        panels.forEach(p => p.classList.remove('is-active'));

        btn.classList.add('is-active');
        const target = btn.getAttribute('data-tab');
        const targetPanel = document.querySelector('#panel-' + target);
        if (targetPanel) {
          targetPanel.classList.add('is-active');
        }
      });
    });
  });
</script>
@endpush

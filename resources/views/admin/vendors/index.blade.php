@extends('admin.layout')

@section('title', 'Brand Partners & Stores — Earthquick Admin')
@section('page_title', 'Vendors & Brand Partners')

@section('content')

  <!-- Header Actions Bar -->
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
    <div>
      <h2 style="font-family: var(--font-display); font-size: 1.45rem; color: var(--eq-navy); margin: 0 0 0.25rem 0; font-weight: 600;">
        Brand Partners &amp; Stores
      </h2>
      <p style="font-size: 0.84rem; color: var(--eq-charcoal-soft); margin: 0;">
        Manage multi-vendor storefronts, brand metadata, SKUs prefixes, and active store statuses.
      </p>
    </div>

    <div style="display: flex; gap: 0.75rem; align-items: center;">
      <a href="{{ route('stores.index') }}" target="_blank" class="eq-admin-btn eq-admin-btn--outline" style="font-size: 0.82rem; padding: 0.5rem 0.9rem; text-decoration: none;">
        View Public Directory &nearr;
      </a>
      <a href="{{ route('admin.vendors.create') }}" class="eq-admin-btn eq-admin-btn--primary" style="font-size: 0.82rem; padding: 0.5rem 1rem; text-decoration: none;">
        + Onboard New Vendor
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="eq-admin-alert eq-admin-alert--success" style="background: #eafaf1; color: #1e7e34; border: 1px solid #c3e6cb; padding: 0.85rem 1.15rem; border-radius: 6px; margin-bottom: 1.25rem; font-size: 0.86rem;">
      {{ session('success') }}
    </div>
  @endif

  <!-- Filters & Search Toolbar -->
  <section class="eq-admin-card" style="padding: 0.75rem 1rem; margin-bottom: 1rem;">
    <form method="GET" action="{{ route('admin.vendors.index') }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
      
      <!-- Search Input -->
      <div style="display: flex; align-items: center; gap: 0.5rem; flex: 1; min-width: 200px;">
        <input 
          type="text" 
          name="search" 
          value="{{ $search }}" 
          placeholder="Search by brand name, code (e.g. NT, BRT), or email..." 
          style="width: 100%; padding: 0.4rem 0.75rem; border-radius: 5px; border: 1px solid var(--eq-line); font-size: 0.84rem; background: #ffffff;"
        />
      </div>

      <!-- Status Filter -->
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <label for="filter-status" style="font-size: 0.8rem; font-weight: 500; color: var(--eq-charcoal-soft);">Status:</label>
        <select name="status" id="filter-status" onchange="this.form.submit()" style="padding: 0.4rem 0.65rem; border-radius: 5px; border: 1px solid var(--eq-line); font-size: 0.84rem; background: #ffffff;">
          <option value="">All Statuses</option>
          <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
          <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Suspended Only</option>
        </select>
      </div>

      <button type="submit" class="eq-admin-btn eq-admin-btn--primary" style="padding: 0.4rem 0.85rem; font-size: 0.8rem;">
        Search
      </button>

      @if($search || $status)
        <a href="{{ route('admin.vendors.index') }}" class="eq-admin-btn eq-admin-btn--outline" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; text-decoration: none;">
          Clear
        </a>
      @endif

      <div style="margin-left: auto; font-size: 0.82rem; color: var(--eq-charcoal-soft);">
        Total: <strong>{{ $vendors->total() }}</strong> partner brands
      </div>

    </form>
  </section>

  <!-- Vendors Table Card -->
  <section class="eq-admin-card" style="padding: 1rem;">
    <!-- Desktop Table View -->
    <div class="eq-desktop-only">
      <div class="eq-admin-table-wrap">
        <table class="eq-admin-table">
          <thead>
            <tr>
              <th>Brand Partner</th>
              <th>Code / Prefix</th>
              <th>Contact Info</th>
              <th style="text-align: center;">Catalog Units</th>
              <th style="text-align: center;">Status</th>
              <th style="text-align: right; min-width: 220px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($vendors as $vendor)
              <tr>
                <td>
                  <div style="display: flex; align-items: center; gap: 0.85rem;">
                    <div style="width: 42px; height: 42px; border-radius: 6px; background: var(--eq-cream); border: 1px solid var(--eq-line); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--eq-navy); font-size: 0.95rem; overflow: hidden; flex-shrink: 0;">
                      @if($vendor->logo)
                        <img src="{{ asset($vendor->logo) }}" alt="{{ $vendor->name }}" style="width: 100%; height: 100%; object-fit: cover;" />
                      @else
                        {{ substr($vendor->name, 0, 2) }}
                      @endif
                    </div>
                    <div>
                      <div style="font-weight: 600; color: var(--eq-navy); font-size: 0.92rem;">
                        {{ $vendor->name }}
                      </div>
                      <div style="font-size: 0.74rem; color: var(--eq-charcoal-soft); margin-top: 0.15rem;">
                        {{ $vendor->tagline ?? 'Partner Brand' }}
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <span style="font-family: monospace; font-size: 0.82rem; font-weight: 700; background: var(--eq-cream); color: var(--eq-navy); padding: 0.2rem 0.5rem; border-radius: 4px; border: 1px solid var(--eq-line);">
                    {{ $vendor->vendor_code }}
                  </span>
                </td>
                <td style="font-size: 0.82rem; color: var(--eq-charcoal-soft);">
                  @if($vendor->email)
                    <div>{{ $vendor->email }}</div>
                  @endif
                  @if($vendor->phone)
                    <div style="color: var(--eq-charcoal-muted); margin-top: 2px;">{{ $vendor->phone }}</div>
                  @endif
                  @if(!$vendor->email && !$vendor->phone)
                    <span style="color: var(--eq-charcoal-muted); font-style: italic;">No direct contact saved</span>
                  @endif
                </td>
                <td style="text-align: center; font-size: 0.88rem; color: var(--eq-charcoal);">
                  <strong>{{ $vendor->products_count }}</strong> items
                </td>
                <td style="text-align: center;">
                  @if($vendor->is_active)
                    <span class="eq-status-badge eq-status-badge--delivered">Active</span>
                  @else
                    <span class="eq-status-badge eq-status-badge--cancelled">Suspended</span>
                  @endif
                </td>
                <td style="text-align: right;">
                  <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 0.4rem;">
                    
                    <!-- View Storefront Link -->
                    <a href="{{ route('stores.show', $vendor->slug) }}" target="_blank" class="eq-admin-btn eq-admin-btn--outline" style="font-size: 0.74rem; padding: 0.35rem 0.6rem; text-decoration: none;" title="Visit Public Storefront">
                      Storefront &nearr;
                    </a>

                    <!-- Edit Button -->
                    <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="eq-admin-btn eq-admin-btn--outline" style="font-size: 0.74rem; padding: 0.35rem 0.6rem; text-decoration: none;">
                      Edit
                    </a>

                    <!-- Toggle Status Form -->
                    <form method="POST" action="{{ route('admin.vendors.toggle', $vendor->id) }}" style="margin: 0;">
                      @csrf
                      @if($vendor->is_active)
                        <button type="submit" class="eq-admin-btn eq-admin-btn--outline" style="font-size: 0.74rem; padding: 0.35rem 0.6rem; color: #b45309; border-color: #fde68a; background: #fffbeb;" title="Suspend Vendor">
                          Suspend
                        </button>
                      @else
                        <button type="submit" class="eq-admin-btn eq-admin-btn--success" style="font-size: 0.74rem; padding: 0.35rem 0.6rem;" title="Activate Vendor">
                          Activate
                        </button>
                      @endif
                    </form>

                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align: center; padding: 3rem 1rem; color: var(--eq-charcoal-soft);">
                  No vendors found matching your criteria.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Mobile Adaptive Cards View -->
    <div class="eq-mobile-only">
      <div class="eq-adaptive-cards">
        @forelse($vendors as $vendor)
          <div class="eq-card-item">
            <div class="eq-card-item__top">
              <div style="width: 46px; height: 46px; border-radius: 6px; background: var(--eq-cream); border: 1px solid var(--eq-line); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--eq-navy); font-size: 1rem; overflow: hidden; flex-shrink: 0;">
                @if($vendor->logo)
                  <img src="{{ asset($vendor->logo) }}" alt="{{ $vendor->name }}" style="width: 100%; height: 100%; object-fit: cover;" />
                @else
                  {{ substr($vendor->name, 0, 2) }}
                @endif
              </div>
              <div class="eq-card-item__info">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                  <div class="eq-card-item__title">{{ $vendor->name }}</div>
                  @if($vendor->is_active)
                    <span class="eq-status-badge eq-status-badge--delivered" style="font-size: 0.68rem;">Active</span>
                  @else
                    <span class="eq-status-badge eq-status-badge--cancelled" style="font-size: 0.68rem;">Suspended</span>
                  @endif
                </div>
                <div class="eq-card-item__meta">
                  <span>Code: <strong>{{ $vendor->vendor_code }}</strong></span>
                  <span>&bull; {{ $vendor->products_count }} items</span>
                </div>
              </div>
            </div>

            <div class="eq-card-item__actions" style="margin-top: 0.75rem; border-top: 1px solid var(--eq-line); padding-top: 0.75rem; display: flex; gap: 0.5rem;">
              <a href="{{ route('stores.show', $vendor->slug) }}" target="_blank" class="eq-admin-btn eq-admin-btn--outline" style="flex: 1; text-align: center; font-size: 0.76rem; padding: 0.4rem; text-decoration: none;">
                Storefront
              </a>
              <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="eq-admin-btn eq-admin-btn--primary" style="flex: 1; text-align: center; font-size: 0.76rem; padding: 0.4rem; text-decoration: none;">
                Edit
              </a>
            </div>
          </div>
        @empty
          <div style="text-align: center; padding: 2rem 1rem; color: var(--eq-charcoal-soft);">
            No vendors found.
          </div>
        @endforelse
      </div>
    </div>

    <!-- Pagination -->
    @if($vendors->hasPages())
      <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        {{ $vendors->links() }}
      </div>
    @endif
  </section>

@endsection


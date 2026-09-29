@extends('admin.layout')

@section('title', 'Catalog & Inventory Management — Earthquick Admin')
@section('page_title', 'Stock & Inventory Control')

@section('content')

  <!-- Page Header & New Product CTA -->
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
    <div>
      <h2 style="font-family: var(--font-display); font-size: 1.35rem; color: var(--eq-navy); line-height: 1.2;">
        Products Portfolio & Store Inventory
      </h2>
      <p style="font-size: 0.8rem; color: var(--eq-charcoal-soft); margin-top: 0.15rem;">
        Manage catalog items, pricing, inventory statuses, and creative showcase assets.
      </p>
    </div>
    <div>
      <a href="{{ route('admin.products.create') }}" class="eq-admin-btn eq-admin-btn--gold" style="padding: 0.5rem 1.1rem; font-size: 0.84rem; font-weight: 600; box-shadow: 0 2px 6px rgba(201, 150, 47, 0.25);">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        + Add New Product
      </a>
    </div>
  </div>

  <p class="eq-admin-card" style="margin: 0 0 1rem; padding: 0.75rem 1rem; color: var(--eq-charcoal-soft); font-size: 0.84rem; line-height: 1.55;">
    <strong style="color: var(--eq-navy);">Available units</strong> means pieces left to sell. Checkout reduces the count; Confirm does not change it; cancelling an eligible unpaid order restores it. For size or colour options, this is the total of active variants. Counts refresh automatically while this page is open.
  </p>

  <div style="display:flex;align-items:center;gap:0.65rem;flex-wrap:wrap;margin:-0.35rem 0 1rem;font-size:0.8rem;color:var(--eq-charcoal-soft);">
    <button type="button" id="eq-stock-refresh" class="eq-admin-btn eq-admin-btn--outline" style="padding:0.35rem 0.65rem;font-size:0.78rem;">Refresh stock now</button>
    <span id="eq-stock-sync-status" role="status" aria-live="polite">Checking current units…</span>
  </div>

  <!-- Filters and Search Toolbar -->
  <section class="eq-admin-card" style="padding: 0.75rem 1rem; margin-bottom: 1rem;">
    <form method="GET" action="{{ route('admin.products') }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
      
      <!-- Category Filter -->
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <label for="filter-category" style="font-size: 0.8rem; font-weight: 500; color: var(--eq-charcoal-soft);">Category:</label>
        <select name="category" id="filter-category" onchange="this.form.submit()" style="padding: 0.4rem 0.65rem; border-radius: 5px; border: 1px solid var(--eq-line); font-size: 0.84rem; background: #ffffff;">
          <option value="">All Categories</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->slug }}" {{ $categorySlug === $cat->slug ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Vendor / Brand Filter -->
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <label for="filter-vendor" style="font-size: 0.8rem; font-weight: 500; color: var(--eq-charcoal-soft);">Brand:</label>
        <select name="vendor" id="filter-vendor" onchange="this.form.submit()" style="padding: 0.4rem 0.65rem; border-radius: 5px; border: 1px solid var(--eq-line); font-size: 0.84rem; background: #ffffff;">
          <option value="">All Brands</option>
          @foreach($vendors as $v)
            <option value="{{ $v->slug }}" {{ ($vendorSlug ?? '') === $v->slug ? 'selected' : '' }}>
              {{ $v->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Stock Status Filter -->
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <label for="filter-stock" style="font-size: 0.8rem; font-weight: 500; color: var(--eq-charcoal-soft);">Stock Status:</label>
        <select name="stock" id="filter-stock" onchange="this.form.submit()" style="padding: 0.4rem 0.65rem; border-radius: 5px; border: 1px solid var(--eq-line); font-size: 0.84rem; background: #ffffff;">
          <option value="">All Stock</option>
          <option value="in_stock" {{ $stockFilter === 'in_stock' ? 'selected' : '' }}>In Stock Only</option>
          <option value="out_of_stock" {{ $stockFilter === 'out_of_stock' ? 'selected' : '' }}>Out of Stock Only</option>
        </select>
      </div>

      @if($categorySlug || ($vendorSlug ?? false) || $stockFilter)
        <a href="{{ route('admin.products') }}" class="eq-admin-btn eq-admin-btn--outline" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;">
          Clear Filters
        </a>
      @endif

      <div style="margin-left: auto; font-size: 0.82rem; color: var(--eq-charcoal-soft);">
        Total: <strong>{{ $products->total() }}</strong> catalog items
      </div>

    </form>
  </section>

  <!-- Products Listing (Desktop Table + Mobile Adaptive Cards) -->
  <section class="eq-admin-card" style="padding: 1rem;">
    <!-- Desktop Table View -->
    <div class="eq-desktop-only">
      <div class="eq-admin-table-wrap">
        <table class="eq-admin-table">
          <thead>
            <tr>
              <th>Product Creation</th>
              <th>Brand</th>
              <th>Category</th>
              <th>Fabric</th>
              <th>Unit Price</th>
              <th style="text-align: center;">Available Units</th>
              <th style="text-align: center;">Stock Status</th>
              <th style="text-align: right; min-width: 230px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($products as $product)
              <tr>
                <td>
                  <div style="display: flex; align-items: center; gap: 0.85rem;">
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" style="width: 44px; height: 56px; object-fit: cover; border-radius: 4px; border: 1px solid var(--eq-line);" />
                    <div>
                      <a href="{{ route('product.show', $product->slug) }}" target="_blank" style="font-weight: 600; color: var(--eq-navy); text-decoration: none;">
                        {{ $product->name }}
                      </a>
                      <div style="font-size: 0.72rem; color: var(--eq-charcoal-muted); margin-top: 0.15rem;">
                        SKU: {{ $product->sku ?? ('NT-' . $product->id) }}
                      </div>
                      @if($product->badge)
                        <span style="font-size: 0.68rem; background: var(--eq-cream-deep); color: var(--eq-charcoal); padding: 0.1rem 0.45rem; border-radius: 3px; margin-top: 0.25rem; display: inline-block; font-weight: 500;">
                          {{ $product->badge }}
                        </span>
                      @endif
                    </div>
                  </div>
                </td>
                <td>
                  <span style="font-weight: 600; color: var(--eq-navy); font-size: 0.86rem;">
                    {{ $product->vendor ? $product->vendor->name : 'Nous Telos' }}
                  </span>
                </td>
                <td>
                  <span style="font-weight: 500; color: var(--eq-navy);">{{ $product->category ? $product->category->name : 'N/A' }}</span>
                  @if($product->subcategory)
                    <div style="font-size: 0.74rem; color: var(--eq-charcoal-soft); margin-top: 0.15rem;">&rsaquo; {{ $product->subcategory->name }}</div>
                  @endif
                </td>
                <td style="font-size: 0.84rem; color: var(--eq-charcoal-soft);">
                  {{ $product->fabric ?? 'Handloom' }}
                </td>
                <td style="font-weight: 600; color: var(--eq-gold-dark);">
                  ৳{{ number_format($product->price) }}
                  @if($product->old_price)
                    <div style="font-size: 0.75rem; text-decoration: line-through; color: var(--eq-charcoal-muted); font-weight: 400; margin-top: 0.15rem;">
                      ৳{{ number_format($product->old_price) }}
                    </div>
                  @endif
                </td>
                <td style="text-align: center; font-size: 0.85rem; color: var(--eq-charcoal);">
                  <strong data-stock-units="{{ $product->id }}" data-stock-value="{{ $product->stock_quantity ?? 0 }}" data-stock-state="{{ $product->in_stock ? '1' : '0' }}">{{ $product->stock_quantity ?? 0 }}</strong>
                </td>
                <td style="text-align: center;">
                  @if($product->in_stock)
                    <span class="eq-status-badge eq-status-badge--delivered">In Stock</span>
                  @else
                    <span class="eq-status-badge eq-status-badge--cancelled">Out of Stock</span>
                  @endif
                </td>
                <td style="text-align: right;">
                  <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 0.4rem;">
                    
                    <!-- Toggle Stock Status Button -->
                    <form method="POST" action="{{ route('admin.products.toggle-stock', $product->id) }}" style="margin: 0;">
                      @csrf
                      @if($product->in_stock)
                        <button type="submit" class="eq-admin-btn eq-admin-btn--outline" style="padding: 0.35rem 0.6rem; font-size: 0.76rem; color: #b45309; border-color: #fde68a; background: #fffbeb;" title="Click to mark product Out of Stock">
                          <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                          </svg>
                          Stock Out
                        </button>
                      @else
                        <button type="submit" class="eq-admin-btn eq-admin-btn--success" style="padding: 0.35rem 0.6rem; font-size: 0.76rem;" title="Click to mark product In Stock">
                          <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                          </svg>
                          In Stock
                        </button>
                      @endif
                    </form>

                    <!-- Edit Product Button -->
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="eq-admin-btn eq-admin-btn--outline" style="padding: 0.35rem 0.6rem; font-size: 0.76rem;" title="Edit Product Details">
                      <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                      Edit
                    </a>

                    <!-- Delete Product Button -->
                    <form method="POST" action="{{ route('admin.products.delete', $product->id) }}" style="margin: 0;" onsubmit="return confirm('Are you sure you want to permanently delete \'{{ addslashes($product->name) }}\'? This action cannot be undone.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="eq-admin-btn eq-admin-btn--danger" style="padding: 0.35rem 0.6rem; font-size: 0.76rem;" title="Delete Product">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                          <polyline points="3 6 5 6 21 6"></polyline>
                          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                        Delete
                      </button>
                    </form>

                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align: center; padding: 3rem; color: var(--eq-charcoal-soft);">
                  No products found matching the criteria.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Mobile & Tablet Adaptive Cards (Zero Horizontal Scroll) -->
    <div class="eq-mobile-only">
      <div class="eq-adaptive-cards">
        @forelse($products as $product)
          <div class="eq-card-item">
            <div class="eq-card-item__top">
              <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="eq-card-item__img" />
              <div class="eq-card-item__info">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                  <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="eq-card-item__title">
                    {{ $product->name }}
                  </a>
                  @if($product->in_stock)
                    <span class="eq-status-badge eq-status-badge--delivered" style="flex-shrink: 0; font-size: 0.68rem;">In Stock</span>
                  @else
                    <span class="eq-status-badge eq-status-badge--cancelled" style="flex-shrink: 0; font-size: 0.68rem;">Out of Stock</span>
                  @endif
                </div>

                <div class="eq-card-item__meta">
                  <span>SKU: {{ $product->sku ?? ('NT-' . $product->id) }}</span>
                  <span>&bull; <strong>{{ $product->vendor ? $product->vendor->name : 'Nous Telos' }}</strong></span>
                  @if($product->category)
                    <span>&bull; {{ $product->category->name }}</span>
                  @endif
                  @if($product->fabric)
                    <span>&bull; {{ $product->fabric }}</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="eq-card-item__grid">
              <div class="eq-card-item__kv">
                <span class="eq-card-item__k">Price</span>
                <span class="eq-card-item__v" style="color: var(--eq-gold-dark);">
                  ৳{{ number_format($product->price) }}
                  @if($product->old_price)
                    <small style="text-decoration: line-through; color: var(--eq-charcoal-muted); font-size: 0.72rem; font-weight: 400; margin-left: 2px;">
                      ৳{{ number_format($product->old_price) }}
                    </small>
                  @endif
                </span>
              </div>
              <div class="eq-card-item__kv">
                <span class="eq-card-item__k">Available Units</span>
                <span class="eq-card-item__v"><strong data-stock-units="{{ $product->id }}" data-stock-value="{{ $product->stock_quantity ?? 0 }}" data-stock-state="{{ $product->in_stock ? '1' : '0' }}">{{ $product->stock_quantity ?? 0 }}</strong> units</span>
              </div>
            </div>

            <div class="eq-card-item__actions">
              <form method="POST" action="{{ route('admin.products.toggle-stock', $product->id) }}" style="margin: 0;">
                @csrf
                @if($product->in_stock)
                  <button type="submit" class="eq-admin-btn eq-admin-btn--outline" style="width: 100%; font-size: 0.74rem; padding: 0.4rem 0.35rem; color: #b45309; border-color: #fde68a; background: #fffbeb;">
                    <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="12" cy="12" r="10"></circle>
                      <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                    </svg>
                    Stock Out
                  </button>
                @else
                  <button type="submit" class="eq-admin-btn eq-admin-btn--success" style="width: 100%; font-size: 0.74rem; padding: 0.4rem 0.35rem;">
                    <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    In Stock
                  </button>
                @endif
              </form>

              <a href="{{ route('admin.products.edit', $product->id) }}" class="eq-admin-btn eq-admin-btn--outline" style="font-size: 0.74rem; padding: 0.4rem 0.35rem;">
                <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
              </a>

              <form method="POST" action="{{ route('admin.products.delete', $product->id) }}" style="margin: 0;" onsubmit="return confirm('Are you sure you want to permanently delete \'{{ addslashes($product->name) }}\'? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="eq-admin-btn eq-admin-btn--danger" style="width: 100%; font-size: 0.74rem; padding: 0.4rem 0.35rem;">
                  <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                  </svg>
                  Delete
                </button>
              </form>
            </div>
          </div>
        @empty
          <div style="text-align: center; padding: 2rem 1rem; color: var(--eq-charcoal-soft); background: var(--eq-white); border-radius: 8px; border: 1px dashed var(--eq-line);">
            No products found matching the criteria.
          </div>
        @endforelse
      </div>
    </div>

    @if($products->hasPages())
      @include('partials.pagination-polished', ['paginator' => $products])
    @endif

  </section>

@endsection

@push('scripts')
<script>
(() => {
  const nodes = [...document.querySelectorAll('[data-stock-units]')];
  const status = document.getElementById('eq-stock-sync-status');
  const refresh = document.getElementById('eq-stock-refresh');
  const ids = [...new Set(nodes.map(node => node.dataset.stockUnits))];
  if (!ids.length) {
    if (status) status.textContent = 'No products on this page.';
    return;
  }

  const url = new URL(@json(route('admin.products.stock-levels')));
  ids.forEach(id => url.searchParams.append('ids[]', id));
  let inFlight = false;
  let lastCheck = 0;

  async function syncStock(force = false) {
    if (inFlight || document.hidden || (!force && Date.now() - lastCheck < 1000)) return;
    inFlight = true;
    lastCheck = Date.now();
    try {
      const response = await fetch(url, {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' },
        cache: 'no-store',
      });
      if (!response.ok) throw new Error('Stock check failed');
      const data = await response.json();
      let changed = 0;
      for (const node of nodes) {
        const current = data.products?.[node.dataset.stockUnits];
        if (!current) {
          window.location.reload();
          return;
        }
        if (node.dataset.stockState !== (current.in_stock ? '1' : '0')) {
          window.location.reload();
          return;
        }
        if (node.dataset.stockValue !== String(current.units)) {
          node.dataset.stockValue = String(current.units);
          node.textContent = String(current.units);
          node.style.color = 'var(--eq-teal)';
          window.setTimeout(() => { node.style.color = ''; }, 1800);
          changed++;
        }
      }
      if (status && changed) status.textContent = 'Available units updated just now.';
      else if (status && status.textContent === 'Checking current units…') status.textContent = 'Stock is up to date.';
    } catch (error) {
      if (status) status.textContent = 'Could not check live stock. Use Refresh stock now or reload the page.';
    } finally {
      inFlight = false;
    }
  }

  refresh?.addEventListener('click', () => syncStock(true));
  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) syncStock(true);
  });
  window.addEventListener('focus', () => syncStock(true));
  window.setInterval(() => syncStock(), 5000);
  syncStock(true);
})();
</script>
@endpush

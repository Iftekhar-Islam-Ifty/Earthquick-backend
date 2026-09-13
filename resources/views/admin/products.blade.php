@extends('admin.layout')

@section('title', 'Catalog & Inventory Management — Earthquick Admin')
@section('page_title', 'Stock & Inventory Control')

@section('content')

  <!-- Filters and Search Toolbar -->
  <section class="eq-admin-card" style="padding: 1rem 1.25rem;">
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

      <!-- Stock Status Filter -->
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <label for="filter-stock" style="font-size: 0.8rem; font-weight: 500; color: var(--eq-charcoal-soft);">Stock Status:</label>
        <select name="stock" id="filter-stock" onchange="this.form.submit()" style="padding: 0.4rem 0.65rem; border-radius: 5px; border: 1px solid var(--eq-line); font-size: 0.84rem; background: #ffffff;">
          <option value="">All Stock</option>
          <option value="in_stock" {{ $stockFilter === 'in_stock' ? 'selected' : '' }}>In Stock Only</option>
          <option value="out_of_stock" {{ $stockFilter === 'out_of_stock' ? 'selected' : '' }}>Out of Stock Only</option>
        </select>
      </div>

      @if($categorySlug || $stockFilter)
        <a href="{{ route('admin.products') }}" class="eq-admin-btn eq-admin-btn--outline" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;">
          Clear Filters
        </a>
      @endif

      <div style="margin-left: auto; font-size: 0.82rem; color: var(--eq-charcoal-soft);">
        Total: <strong>{{ $products->total() }}</strong> catalog items
      </div>

    </form>
  </section>

  <!-- Products Table Card -->
  <section class="eq-admin-card">
    <div class="eq-admin-table-wrap">
      <table class="eq-admin-table">
        <thead>
          <tr>
            <th>Product Creation</th>
            <th>Category / Subcategory</th>
            <th>Fabric</th>
            <th>Unit Price (৳)</th>
            <th>Atelier Units</th>
            <th>Stock Status</th>
            <th style="text-align: right;">Inventory Action</th>
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
                    @if($product->badge)
                      <span style="font-size: 0.68rem; background: var(--eq-cream-deep); padding: 0.1rem 0.4rem; border-radius: 3px; margin-left: 0.35rem;">
                        {{ $product->badge }}
                      </span>
                    @endif
                  </div>
                </div>
              </td>
              <td>
                <span style="font-weight: 500;">{{ $product->category ? $product->category->name : 'N/A' }}</span>
                @if($product->subcategory)
                  <div style="font-size: 0.74rem; color: var(--eq-charcoal-soft);">&rsaquo; {{ $product->subcategory->name }}</div>
                @endif
              </td>
              <td style="font-size: 0.84rem; color: var(--eq-charcoal-soft);">
                {{ $product->fabric ?? 'Handloom' }}
              </td>
              <td style="font-weight: 600; color: var(--eq-gold-dark);">
                ৳{{ number_format($product->price) }}
              </td>
              <td style="font-size: 0.84rem;">
                {{ $product->stock_quantity ?? 0 }} units
              </td>
              <td>
                @if($product->in_stock)
                  <span class="eq-status-badge eq-status-badge--delivered">In Stock</span>
                @else
                  <span class="eq-status-badge eq-status-badge--cancelled">Out of Stock</span>
                @endif
              </td>
              <td style="text-align: right;">
                <form method="POST" action="{{ route('admin.products.toggle-stock', $product->id) }}" style="display: inline;">
                  @csrf
                  @if($product->in_stock)
                    <button type="submit" class="eq-admin-btn eq-admin-btn--danger" style="padding: 0.35rem 0.75rem; font-size: 0.76rem;" title="Mark as Out of Stock">
                      Mark Out of Stock
                    </button>
                  @else
                    <button type="submit" class="eq-admin-btn eq-admin-btn--success" style="padding: 0.35rem 0.75rem; font-size: 0.76rem;" title="Mark as In Stock">
                      Mark In Stock
                    </button>
                  @endif
                </form>
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

    @if($products->hasPages())
      <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--eq-line); display: flex; justify-content: center;">
        {{ $products->links() }}
      </div>
    @endif

  </section>

@endsection


@extends('admin.layout')

@section('title', 'Edit Vendor: ' . $vendor->name . ' — Earthquick Admin')
@section('page_title', 'Edit Brand Partner: ' . $vendor->name)

@section('content')

  <!-- Header Breadcrumb & Actions -->
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
    <div>
      <a href="{{ route('admin.vendors.index') }}" style="color: var(--eq-charcoal-soft); text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
        &larr; Back to Vendors Portfolio
      </a>
      <h2 style="font-family: var(--font-display); font-size: 1.5rem; color: var(--eq-navy); margin-top: 0.25rem;">
        Edit Brand Partner: {{ $vendor->name }}
      </h2>
    </div>

    <div>
      <a href="{{ route('stores.show', $vendor->slug) }}" target="_blank" class="eq-admin-btn eq-admin-btn--outline" style="font-size: 0.82rem; padding: 0.5rem 0.9rem; text-decoration: none;">
        View Live Storefront &nearr;
      </a>
    </div>
  </div>

  @if($errors->any())
    <div class="eq-admin-alert eq-admin-alert--error" style="background: #fdf2f2; color: #9b1c1c; border: 1px solid #f8b4b4; padding: 1rem 1.25rem; border-radius: 6px; margin-bottom: 1.5rem;">
      <div>
        <strong style="display: block; margin-bottom: 0.35rem;">Please correct the following errors:</strong>
        <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.84rem;">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.vendors.update', $vendor->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="eq-admin-2col-grid">
      
      <!-- LEFT COLUMN: Brand Identity & Contact -->
      <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        
        <!-- Identity Card -->
        <div class="eq-admin-card" style="margin-bottom: 0;">
          <h3 class="eq-admin-card__title" style="margin-bottom: 1.25rem; font-size: 1.05rem;">Brand Identity</h3>

          <!-- Name -->
          <div style="margin-bottom: 1.25rem;">
            <label for="input-name" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
              Brand / Vendor Name <span style="color: #dc2626;">*</span>
            </label>
            <input 
              type="text" 
              name="name" 
              id="input-name" 
              value="{{ old('name', $vendor->name) }}" 
              required
              style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: inherit; background: #ffffff;"
            />
          </div>

          <!-- Code & Slug Row -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
              <label for="input-code" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
                Vendor Code (SKU Prefix) <span style="color: #dc2626;">*</span>
              </label>
              <input 
                type="text" 
                name="vendor_code" 
                id="input-code" 
                value="{{ old('vendor_code', $vendor->vendor_code) }}" 
                maxlength="10"
                required
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: monospace; text-transform: uppercase; background: #ffffff;"
              />
              <small style="color: var(--eq-charcoal-muted); font-size: 0.72rem; margin-top: 0.25rem; display: block;">
                Used to prefix SKU identifiers (e.g., {{ $vendor->vendor_code }}-ABC-123).
              </small>
            </div>

            <div>
              <label for="input-slug" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
                URL Slug <span style="color: #dc2626;">*</span>
              </label>
              <input 
                type="text" 
                name="slug" 
                id="input-slug" 
                value="{{ old('slug', $vendor->slug) }}" 
                required
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: inherit; background: #ffffff;"
              />
            </div>
          </div>

          <!-- Tagline -->
          <div style="margin-bottom: 1.25rem;">
            <label for="input-tagline" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
              Brand Tagline
            </label>
            <input 
              type="text" 
              name="tagline" 
              id="input-tagline" 
              value="{{ old('tagline', $vendor->tagline) }}" 
              style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: inherit; background: #ffffff;"
            />
          </div>

          <!-- Description / Bio -->
          <div>
            <label for="input-description" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
              Atelier Biography / Storefront Description
            </label>
            <textarea 
              name="description" 
              id="input-description" 
              rows="4" 
              style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.88rem; font-family: inherit; background: #ffffff; resize: vertical;"
            >{{ old('description', $vendor->description) }}</textarea>
          </div>

        </div>

        <!-- Contact & Operations Card -->
        <div class="eq-admin-card" style="margin-bottom: 0;">
          <h3 class="eq-admin-card__title" style="margin-bottom: 1.25rem; font-size: 1.05rem;">Contact &amp; Operations</h3>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
              <label for="input-email" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
                Support / Business Email
              </label>
              <input 
                type="email" 
                name="email" 
                id="input-email" 
                value="{{ old('email', $vendor->email) }}" 
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: inherit; background: #ffffff;"
              />
            </div>

            <div>
              <label for="input-phone" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
                Phone / Hotline
              </label>
              <input 
                type="text" 
                name="phone" 
                id="input-phone" 
                value="{{ old('phone', $vendor->phone) }}" 
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: inherit; background: #ffffff;"
              />
            </div>
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label for="input-address" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
              Workshop / Physical Address
            </label>
            <input 
              type="text" 
              name="address" 
              id="input-address" 
              value="{{ old('address', $vendor->address) }}" 
              style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: inherit; background: #ffffff;"
            />
          </div>

          <div>
            <div>
              <label for="input-sort" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
                Display Priority / Sort Order
              </label>
              <input 
                type="number" 
                name="sort_order" 
                id="input-sort" 
                value="{{ old('sort_order', $vendor->sort_order) }}" 
                min="0"
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: inherit; background: #ffffff;"
              />
            </div>
          </div>

        </div>

      </div>

      <!-- RIGHT COLUMN: Media & Publishing Controls -->
      <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        
        <!-- Status & Visibility Card -->
        <div class="eq-admin-card" style="margin-bottom: 0;">
          <h3 class="eq-admin-card__title" style="margin-bottom: 1.25rem; font-size: 1.05rem;">Publishing Controls</h3>

          <label style="display: flex; align-items: center; gap: 0.65rem; font-size: 0.88rem; color: var(--eq-charcoal); margin-bottom: 1rem; cursor: pointer;">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $vendor->is_active) ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: var(--eq-gold);" />
            <span><strong>Active &amp; Published</strong> (Visible in store directory and products enabled)</span>
          </label>

        </div>

        <!-- Storefront Media Card -->
        <div class="eq-admin-card" style="margin-bottom: 0;">
          <h3 class="eq-admin-card__title" style="margin-bottom: 1.25rem; font-size: 1.05rem;">Storefront Media</h3>

          <!-- Logo Upload -->
          <div style="margin-bottom: 1.5rem;">
            <label for="input-logo" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
              Brand Logo / Monogram Avatar
            </label>
            @if($vendor->logo)
              <div style="margin-bottom: 0.65rem; display: flex; align-items: center; gap: 0.75rem;">
                <img src="{{ asset($vendor->logo) }}" alt="{{ $vendor->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid var(--eq-line);" />
                <span style="font-size: 0.75rem; color: var(--eq-charcoal-muted);">Current logo</span>
              </div>
            @endif
            <input 
              type="file" 
              name="logo" 
              id="input-logo" 
              accept="image/png,image/jpeg,image/webp,image/svg+xml"
              style="width: 100%; font-size: 0.84rem;"
            />
            <small style="color: var(--eq-charcoal-muted); font-size: 0.72rem; margin-top: 0.25rem; display: block;">
              Leave blank to keep existing logo.
            </small>
          </div>

          <!-- Banner Upload -->
          <div>
            <label for="input-banner" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
              Storefront Header Banner
            </label>
            @if($vendor->banner)
              <div style="margin-bottom: 0.65rem;">
                <img src="{{ asset($vendor->banner) }}" alt="{{ $vendor->name }}" style="width: 100%; height: 70px; object-fit: cover; border-radius: 4px; border: 1px solid var(--eq-line);" />
              </div>
            @endif
            <input 
              type="file" 
              name="banner" 
              id="input-banner" 
              accept="image/png,image/jpeg,image/webp"
              style="width: 100%; font-size: 0.84rem;"
            />
            <small style="color: var(--eq-charcoal-muted); font-size: 0.72rem; margin-top: 0.25rem; display: block;">
              Leave blank to keep existing banner.
            </small>
          </div>

        </div>

        <!-- Submit Button -->
        <button type="submit" class="eq-admin-btn eq-admin-btn--primary" style="width: 100%; padding: 0.85rem; font-size: 0.95rem; font-weight: 600;">
          Update Brand Partner Profile
        </button>

      </div>

    </div>
  </form>

@endsection

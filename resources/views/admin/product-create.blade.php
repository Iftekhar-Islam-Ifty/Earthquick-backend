@extends('admin.layout')

@section('title', 'Add New Product — Earthquick Admin')
@section('page_title', 'Add New Product')

@section('content')

  <!-- Header Breadcrumb & Actions -->
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
    <div>
      <a href="{{ route('admin.products') }}" style="color: var(--eq-charcoal-soft); text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
        &larr; Back to Products Portfolio
      </a>
      <h2 style="font-family: var(--font-display); font-size: 1.5rem; color: var(--eq-navy); margin-top: 0.25rem;">
        Create New Product
      </h2>
    </div>
  </div>

  @if($errors->any())
    <div class="eq-admin-alert eq-admin-alert--error" style="background: #fdf2f2; color: #9b1c1c; border: 1px solid #f8b4b4; padding: 1rem 1.25rem; border-radius: 6px; margin-bottom: 1.5rem;">
      <div>
        <strong style="display: block; margin-bottom: 0.35rem;">Please address the following validation errors:</strong>
        <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.84rem;">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="eq-admin-2col-grid">
      
      <!-- LEFT COLUMN: Primary Product Attributes -->
      <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        
        <!-- Basic Information Card -->
        <div class="eq-admin-card" style="margin-bottom: 0;">
          <h3 class="eq-admin-card__title" style="margin-bottom: 1.25rem; font-size: 1.05rem;">Basic Information</h3>

          <!-- Product Name -->
          <div style="margin-bottom: 1.25rem;">
            <label for="input-name" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
              Product Title / Name <span style="color: #dc2626;">*</span>
            </label>
            <input 
              type="text" 
              name="name" 
              id="input-name" 
              value="{{ old('name') }}" 
              placeholder="e.g. Crimson Heirloom Jamdani Saree" 
              required
              style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: inherit; background: #ffffff;"
            />
          </div>

          <!-- Brand / Vendor Partner -->
          <div style="margin-bottom: 1.25rem;">
            <label for="select-vendor" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
              Brand / Vendor Partner <span style="color: #dc2626;">*</span>
            </label>
            <select 
              name="vendor_id" 
              id="select-vendor" 
              required
              style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.88rem; background: #ffffff; cursor: pointer;"
            >
              @foreach($vendors as $v)
                <option value="{{ $v->id }}" {{ old('vendor_id', $v->slug === 'nous-telos' ? $v->id : '') == $v->id ? 'selected' : '' }}>
                  {{ $v->name }} ({{ $v->vendor_code }}){{ $v->tagline ? ' — ' . $v->tagline : '' }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Category & Subcategory Row -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
              <label for="select-category" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
                Primary Category <span style="color: #dc2626;">*</span>
              </label>
              <select 
                name="category_id" 
                id="select-category" 
                required
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.88rem; background: #ffffff; cursor: pointer;"
              >
                <option value="">-- Select Category --</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" data-slug="{{ $cat->slug }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div>
              <label for="select-subcategory" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
                Subcategory <span style="font-weight: 400; color: var(--eq-charcoal-muted);">(Optional)</span>
              </label>
              <select 
                name="subcategory_id" 
                id="select-subcategory"
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.88rem; background: #ffffff; cursor: pointer;"
              >
                <option value="">-- No Subcategory / None --</option>
                @foreach($categories as $cat)
                  @foreach($cat->subcategories as $sub)
                    <option value="{{ $sub->id }}" data-category="{{ $cat->id }}" {{ old('subcategory_id') == $sub->id ? 'selected' : '' }}>
                      {{ $cat->name }} &rsaquo; {{ $sub->name }}
                    </option>
                  @endforeach
                @endforeach
              </select>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
              <label for="select-delivery-class" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">Delivery Class</label>
              <select name="delivery_class" id="select-delivery-class" required style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.88rem; background: #ffffff;">
                @foreach($catalogSchema['delivery_classes'] as $class => $label)
                  <option value="{{ $class }}" {{ old('delivery_class', 'standard') === $class ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <input type="hidden" name="is_returnable" value="0" />
              <label style="display: flex; align-items: center; gap: 0.55rem; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
                <input type="checkbox" name="is_returnable" value="1" {{ old('is_returnable', '1') ? 'checked' : '' }} style="width: 17px; height: 17px; accent-color: var(--eq-navy);" />
                Return Eligible
              </label>
              <input type="number" name="return_window_days" value="{{ old('return_window_days', 7) }}" min="1" max="365" placeholder="Return window in days" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; background: #ffffff;" />
            </div>
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label for="input-return-policy-note" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">Return Policy Note <span style="font-weight: 400; color: var(--eq-charcoal-muted);">(Optional)</span></label>
            <input type="text" name="return_policy_note" id="input-return-policy-note" value="{{ old('return_policy_note') }}" placeholder="e.g. Unused item with original tags and packaging" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; background: #ffffff;" />
          </div>

          <!-- Price & Old Price Row -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div>
              <label for="input-price" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
                Selling Price (BDT ৳) <span style="color: #dc2626;">*</span>
              </label>
              <input 
                type="number" 
                name="price" 
                id="input-price" 
                value="{{ old('price') }}" 
                placeholder="e.g. 8500" 
                step="0.01" 
                min="0" 
                required
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: inherit; background: #ffffff;"
              />
            </div>

            <div>
              <label for="input-old-price" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
                Original / Old Price (৳) <span style="font-weight: 400; color: var(--eq-charcoal-muted);">(Optional strikeout)</span>
              </label>
              <input 
                type="number" 
                name="old_price" 
                id="input-old-price" 
                value="{{ old('old_price') }}" 
                placeholder="e.g. 10500" 
                step="0.01" 
                min="0"
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: inherit; background: #ffffff;"
              />
            </div>
          </div>

        </div>

        <!-- Specifications & Details Card -->
        <div class="eq-admin-card" style="margin-bottom: 0;">
          <h3 class="eq-admin-card__title" style="margin-bottom: 1.25rem; font-size: 1.05rem;">Product Specifications</h3>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
              <label for="select-product-type" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">Product Type</label>
              <select name="product_type" id="select-product-type" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.88rem; background: #ffffff; cursor: pointer;">
                @foreach($catalogSchema['product_types'] as $type => $definition)
                  <option value="{{ $type }}" {{ old('product_type', 'general') === $type ? 'selected' : '' }}>{{ $definition['label'] }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label for="input-warranty-info" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">Warranty / Support</label>
              <input type="text" name="warranty_info" id="input-warranty-info" value="{{ old('warranty_info') }}" placeholder="e.g. 1 year manufacturer warranty" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: inherit; background: #ffffff;" />
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
              <label for="input-fabric" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
                Fabric / Material
              </label>
              <input 
                type="text" 
                name="fabric" 
                id="input-fabric" 
                value="{{ old('fabric') }}" 
                placeholder="e.g. Pure Silk, Mulmul, Cotton, Leather"
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: inherit; background: #ffffff;"
              />
            </div>

            <div>
              <label for="input-stock-quantity" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
                Stock Units Available
              </label>
              <input 
                type="number" 
                name="stock_quantity" 
                id="input-stock-quantity" 
                value="{{ old('stock_quantity', 10) }}" 
                min="0"
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: inherit; background: #ffffff;"
              />
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div>
              <label for="input-badge" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
                Badge / Ribbon Label <span style="font-weight: 400; color: var(--eq-charcoal-muted);">(Optional)</span>
              </label>
              <input 
                type="text" 
                name="badge" 
                id="input-badge" 
                value="{{ old('badge') }}" 
                placeholder="e.g. Masterpiece, New Arrival, Bestseller"
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; font-family: inherit; background: #ffffff;"
              />
            </div>

            <div>
              <label for="select-badge-type" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
                Badge Styling Tint
              </label>
              <select 
                name="badge_type" 
                id="select-badge-type"
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.88rem; background: #ffffff; cursor: pointer;"
              >
                <option value="ready" {{ old('badge_type') == 'ready' ? 'selected' : '' }}>Ready to Ship (Green Tint)</option>
                <option value="exclusive" {{ old('badge_type') == 'exclusive' ? 'selected' : '' }}>Exclusive (Purple Tint)</option>
                <option value="signature" {{ old('badge_type') == 'signature' ? 'selected' : '' }}>Signature (Gold Tint)</option>
                <option value="bestseller" {{ old('badge_type') == 'bestseller' ? 'selected' : '' }}>Bestseller (Dark Navy Tint)</option>
              </select>
            </div>
          </div>

          <div>
            <label for="textarea-specifications" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">Structured Specifications <span style="font-weight: 400; color: var(--eq-charcoal-muted);">(JSON label/value pairs, optional)</span></label>
            <textarea name="specifications" id="textarea-specifications" rows="6" placeholder='{"Brand":"","Model":"","Color":""}' style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.82rem; font-family: monospace; background: #ffffff; resize: vertical;">{{ old('specifications') }}</textarea>
            <p id="specification-template-help" style="margin: 0.4rem 0 0; color: var(--eq-charcoal-muted); font-size: 0.74rem;">Choose a product type to load its recommended specification labels.</p>
          </div>

          <div style="margin-top: 1.25rem;">
            <label for="textarea-variants" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">Product Variants <span style="font-weight: 400; color: var(--eq-charcoal-muted);">(JSON array, optional)</span></label>
            <textarea name="variants" id="textarea-variants" rows="8" placeholder='[{"sku":"NT-SAR-001-RED","label":"Red / Free Size","attributes":{"Color":"Red","Size":"Free Size"},"price":18500,"stock_quantity":3,"is_active":true}]' style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.82rem; font-family: monospace; background: #ffffff; resize: vertical;">{{ old('variants') }}</textarea>
            <p style="margin: 0.4rem 0 0; color: var(--eq-charcoal-muted); font-size: 0.74rem;">Use one object per size, color or storage option. Leave price null to use the main product price. Variant stock becomes the product's total stock.</p>
          </div>

        </div>

        <!-- Copywriting & Story Card -->
        <div class="eq-admin-card" style="margin-bottom: 0;">
          <h3 class="eq-admin-card__title" style="margin-bottom: 1.25rem; font-size: 1.05rem;">Product Story & Copywriting</h3>

          <div style="margin-bottom: 1.25rem;">
            <label for="textarea-short-desc" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
              Short Summary <span style="font-weight: 400; color: var(--eq-charcoal-muted);">(Displayed on cards & quick previews)</span>
            </label>
            <textarea 
              name="short_desc" 
              id="textarea-short-desc" 
              rows="2"
              placeholder="e.g. Masterfully woven 100-count Jamdani saree featuring traditional floral jaal motifs in rich crimson."
              style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.88rem; font-family: inherit; background: #ffffff; resize: vertical;"
            >{{ old('short_desc') }}</textarea>
          </div>

          <div>
            <label for="textarea-description" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
              Full Product Description
            </label>
            <textarea 
              name="description" 
              id="textarea-description" 
              rows="5"
              placeholder="Describe the weave density, artisan community, drape instructions, and styling notes..."
              style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.88rem; font-family: inherit; background: #ffffff; resize: vertical;"
            >{{ old('description') }}</textarea>
          </div>
        </div>

      </div>

      <!-- RIGHT COLUMN: Media & Publish Settings -->
      <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Image Upload Card -->
        <div class="eq-admin-card" style="margin-bottom: 0;">
          <h3 class="eq-admin-card__title" style="margin-bottom: 1rem; font-size: 1.05rem;">Product Imagery</h3>
          
          <!-- Image Preview Area -->
          <div 
            id="image-preview-container" 
            style="width: 100%; height: 260px; border: 2px dashed var(--eq-line); border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: var(--eq-cream); overflow: hidden; margin-bottom: 1rem; position: relative;"
          >
            <img 
              id="image-preview-img" 
              src="#" 
              alt="Uploaded Preview" 
              style="width: 100%; height: 100%; object-fit: cover; display: none;" 
            />
            <div id="image-placeholder" style="text-align: center; padding: 1rem; color: var(--eq-charcoal-muted);">
              <svg viewBox="0 0 24 24" width="38" height="38" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.5rem; opacity: 0.6;">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
              </svg>
              <div style="font-size: 0.82rem; font-weight: 500;">Select an image to preview</div>
              <div style="font-size: 0.72rem; margin-top: 0.25rem;">JPG, PNG, WEBP, or SVG (Max 5MB)</div>
            </div>
          </div>

          <!-- File Input -->
          <div>
            <label for="input-product-image" style="display: block; font-size: 0.82rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">
              Upload Master Shot <span style="color: #dc2626;">*</span>
            </label>
            <input 
              type="file" 
              name="image" 
              id="input-product-image" 
              accept="image/jpeg,image/png,image/webp,image/svg+xml" 
              required
              onchange="previewSelectedImage(this)"
              style="width: 100%; font-size: 0.82rem;"
            />
          </div>

          <div style="margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid var(--eq-line);">
            <label for="input-gallery-images" style="display: block; font-size: 0.82rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.4rem;">Additional Media <span style="font-weight: 400; color: var(--eq-charcoal-muted);">(Up to 8 files)</span></label>
            <input type="file" name="gallery_images[]" id="input-gallery-images" accept="image/jpeg,image/png,image/webp,image/svg+xml" multiple style="width: 100%; font-size: 0.82rem;" />
            <div style="display: grid; grid-template-columns: 1fr; gap: 0.65rem; margin-top: 0.8rem;">
              <select name="gallery_role" aria-label="Additional media role" style="width: 100%; padding: 0.55rem 0.7rem; border-radius: 6px; border: 1px solid var(--eq-line); background: #ffffff; font-size: 0.82rem;">
                @foreach($catalogSchema['media_roles'] as $role => $label)
                  <option value="{{ $role }}" {{ old('gallery_role', 'gallery') === $role ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
              <input type="text" name="gallery_alt_text" value="{{ old('gallery_alt_text') }}" maxlength="255" placeholder="Accessible image description (optional)" style="width: 100%; padding: 0.55rem 0.7rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.82rem;" />
            </div>
          </div>
        </div>

        <!-- Inventory Status & Highlights Card -->
        <div class="eq-admin-card" style="margin-bottom: 0;">
          <h3 class="eq-admin-card__title" style="margin-bottom: 1rem; font-size: 1.05rem;">Visibility & Status</h3>

          <!-- In Stock Checkbox -->
          <div style="margin-bottom: 0.85rem;">
            <label style="display: flex; align-items: center; gap: 0.65rem; font-size: 0.88rem; cursor: pointer;">
              <input type="checkbox" name="in_stock" value="1" {{ old('in_stock', '1') ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--eq-navy);" />
              <span><strong>In Stock</strong> (Ready for ordering)</span>
            </label>
          </div>

          <!-- Catalog Visibility Checkbox -->
          <div style="margin-bottom: 0.85rem;">
            <label style="display: flex; align-items: center; gap: 0.65rem; font-size: 0.88rem; cursor: pointer;">
              <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--eq-navy);" />
              <span><strong>Published</strong> (Visible in the public catalog)</span>
            </label>
          </div>

          <!-- Featured on Homepage Checkbox -->
          <div style="margin-bottom: 0.85rem;">
            <label style="display: flex; align-items: center; gap: 0.65rem; font-size: 0.88rem; cursor: pointer;">
              <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--eq-gold);" />
              <span><strong>Featured Masterpiece</strong> (Homepage grid)</span>
            </label>
          </div>

          <!-- New Arrival Checkbox -->
          <div style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.65rem; font-size: 0.88rem; cursor: pointer;">
              <input type="checkbox" name="is_new_arrival" value="1" {{ old('is_new_arrival', '1') ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--eq-navy);" />
              <span><strong>New Arrival</strong> (Showcased in fresh drop)</span>
            </label>
          </div>

          <!-- Actions -->
          <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <button 
              type="submit" 
              class="eq-admin-btn eq-admin-btn--primary" 
              style="width: 100%; justify-content: center; padding: 0.8rem; font-size: 0.95rem; font-weight: 600;"
            >
              Publish Product
            </button>
            <a 
              href="{{ route('admin.products') }}" 
              class="eq-admin-btn eq-admin-btn--outline" 
              style="width: 100%; justify-content: center; padding: 0.65rem; font-size: 0.86rem;"
            >
              Discard Changes
            </a>
          </div>

        </div>

      </div>

    </div>
  </form>

@endsection

@push('scripts')
<script>
  // Dynamic Subcategory Filtering based on Primary Category Selection
  const categorySelect = document.getElementById('select-category');
  const subcategorySelect = document.getElementById('select-subcategory');
  const productTypeSelect = document.getElementById('select-product-type');
  const specificationsField = document.getElementById('textarea-specifications');
  const specificationHelp = document.getElementById('specification-template-help');
  const specificationTemplates = @json(collect($catalogSchema['product_types'])->map(fn ($definition) => $definition['template']));
  const categoryProductTypes = @json($catalogSchema['category_defaults']);
  let productTypeTouched = false;

  function filterSubcategories() {
    const selectedCatId = categorySelect.value;
    const options = subcategorySelect.querySelectorAll('option');

    options.forEach(opt => {
      if (!opt.value) {
        opt.style.display = 'block'; // Keep 'No Subcategory'
        return;
      }
      const optCatId = opt.getAttribute('data-category');
      if (!selectedCatId || optCatId === selectedCatId) {
        opt.style.display = 'block';
      } else {
        opt.style.display = 'none';
        if (opt.selected) {
          subcategorySelect.value = '';
        }
      }
    });
  }

  function updateSpecificationTemplate() {
    const template = specificationTemplates[productTypeSelect.value] || specificationTemplates.general;
    specificationsField.placeholder = JSON.stringify(template, null, 2);
    specificationHelp.textContent = `Recommended ${productTypeSelect.options[productTypeSelect.selectedIndex].text} labels are shown in the placeholder. Add only the details this product needs.`;
  }

  function inferProductType() {
    if (productTypeTouched) return;
    const selectedCategory = categorySelect.options[categorySelect.selectedIndex];
    const inferredType = categoryProductTypes[selectedCategory?.dataset.slug];
    if (inferredType && productTypeSelect.querySelector(`option[value="${inferredType}"]`)) {
      productTypeSelect.value = inferredType;
    }
    updateSpecificationTemplate();
  }

  categorySelect.addEventListener('change', () => {
    filterSubcategories();
    inferProductType();
  });
  productTypeSelect.addEventListener('change', () => {
    productTypeTouched = true;
    updateSpecificationTemplate();
  });
  document.addEventListener('DOMContentLoaded', () => {
    filterSubcategories();
    inferProductType();
  });

  // Instant Image Preview Handler
  function previewSelectedImage(input) {
    const previewContainer = document.getElementById('image-preview-container');
    const previewImg = document.getElementById('image-preview-img');
    const placeholder = document.getElementById('image-placeholder');

    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        previewImg.src = e.target.result;
        previewImg.style.display = 'block';
        placeholder.style.display = 'none';
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
@endpush

@extends('admin.layout')

@section('title', 'Create Campaign Coupon — Earthquick Admin')
@section('header_title', 'Create Campaign Coupon')

@section('content')
  <!-- Header Breadcrumb & Actions -->
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem;">
    <div>
      <a href="{{ route('admin.coupons') }}" style="color: var(--eq-charcoal-soft); text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
        &larr; Back to Coupons &amp; Offers
      </a>
      <h2 style="font-family: var(--font-display); font-size: 1.4rem; color: var(--eq-navy); margin-top: 0.25rem;">
        Launch Seasonal Voucher or Promo Code
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

  <form method="POST" action="{{ route('admin.coupons.store') }}" id="coupon-form">
    @csrf

    <div class="eq-admin-2col-grid">
      
      <!-- LEFT COLUMN: Promo Code & Discount Logic -->
      <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        
        <!-- Code & Campaign Identity Card -->
        <div class="eq-admin-card" style="margin-bottom: 0;">
          <h3 class="eq-admin-card__title" style="margin-bottom: 1.15rem; font-size: 1.02rem; font-weight: 600; color: var(--eq-navy);">
            1. Campaign Identity &amp; Code
          </h3>

          <!-- Coupon Code Input -->
          <div style="margin-bottom: 1.15rem;">
            <label for="input-code" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.35rem;">
              Promo Voucher Code <span style="color: #dc2626;">*</span>
            </label>
            <div style="position: relative;">
              <input 
                type="text" 
                name="code" 
                id="input-code" 
                value="{{ old('code') }}" 
                placeholder="e.g. EID2026, PUJA25, VIP500" 
                required
                maxlength="50"
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.95rem; font-family: monospace; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; background: #ffffff;"
                oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, ''); updatePreviewBadge(this.value);"
              />
            </div>
            <small style="display: block; font-size: 0.74rem; color: var(--eq-charcoal-soft); margin-top: 0.25rem;">
              Customers type this code in the Cart Drawer or Checkout page. Auto-capitalized.
            </small>
          </div>

          <!-- Campaign Label -->
          <div>
            <label for="input-name" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.35rem;">
              Campaign / Occasion Title <span style="color: var(--eq-charcoal-soft); font-weight: normal;">(Optional)</span>
            </label>
            <input 
              type="text" 
              name="name" 
              id="input-name" 
              value="{{ old('name') }}" 
              placeholder="e.g. Eid-ul-Fitr Grand Festive Offer, Puja Luxury Edit" 
              maxlength="255"
              style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.88rem; background: #ffffff;"
            />
          </div>
        </div>

        <!-- Discount Calculation Card -->
        <div class="eq-admin-card" style="margin-bottom: 0;">
          <h3 class="eq-admin-card__title" style="margin-bottom: 1.15rem; font-size: 1.02rem; font-weight: 600; color: var(--eq-navy);">
            2. Discount Mechanism &amp; Value
          </h3>

          <!-- Discount Type -->
          <div style="margin-bottom: 1.15rem;">
            <label style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.45rem;">
              Discount Type <span style="color: #dc2626;">*</span>
            </label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem;">
              <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.65rem 0.85rem; border: 1px solid var(--eq-line); border-radius: 6px; cursor: pointer; background: #ffffff;" id="label-type-percent">
                <input type="radio" name="type" value="percent" {{ old('type', 'percent') === 'percent' ? 'checked' : '' }} onchange="toggleTypeHelp('percent')" style="accent-color: var(--eq-gold);" />
                <div>
                  <div style="font-size: 0.86rem; font-weight: 600; color: var(--eq-navy);">Percentage (%)</div>
                  <div style="font-size: 0.72rem; color: var(--eq-charcoal-soft);">e.g. 10% or 15% off</div>
                </div>
              </label>

              <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.65rem 0.85rem; border: 1px solid var(--eq-line); border-radius: 6px; cursor: pointer; background: #ffffff;" id="label-type-fixed">
                <input type="radio" name="type" value="fixed" {{ old('type') === 'fixed' ? 'checked' : '' }} onchange="toggleTypeHelp('fixed')" style="accent-color: var(--eq-gold);" />
                <div>
                  <div style="font-size: 0.86rem; font-weight: 600; color: var(--eq-navy);">Fixed Flat (৳)</div>
                  <div style="font-size: 0.72rem; color: var(--eq-charcoal-soft);">e.g. ৳500 flat discount</div>
                </div>
              </label>
            </div>
          </div>

          <!-- Value & Max Discount Grid -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
            <div>
              <label for="input-value" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.35rem;">
                Discount Amount <span style="color: #dc2626;">*</span>
              </label>
              <div style="position: relative;">
                <input 
                  type="number" 
                  step="0.01" 
                  min="0.01" 
                  name="value" 
                  id="input-value" 
                  value="{{ old('value') }}" 
                  placeholder="e.g. 10 or 500" 
                  required
                  style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; background: #ffffff;"
                />
              </div>
              <small id="value-help" style="display: block; font-size: 0.74rem; color: var(--eq-charcoal-soft); margin-top: 0.25rem;">
                Enter percentage number (e.g. 15 for 15%).
              </small>
            </div>

            <div id="max-discount-wrapper">
              <label for="input-max-discount" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.35rem;">
                Max Discount Cap (৳) <span style="color: var(--eq-charcoal-soft); font-weight: normal;">(Optional)</span>
              </label>
              <input 
                type="number" 
                step="0.01" 
                min="0" 
                name="max_discount" 
                id="input-max-discount" 
                value="{{ old('max_discount') }}" 
                placeholder="e.g. 1000" 
                style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; background: #ffffff;"
              />
              <small style="display: block; font-size: 0.74rem; color: var(--eq-charcoal-soft); margin-top: 0.25rem;">
                Limits maximum discount for percentage promos.
              </small>
            </div>
          </div>
        </div>

      </div>

      <!-- RIGHT COLUMN: Rules, Limits & Publishing -->
      <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        
        <!-- Redemption Rules Card -->
        <div class="eq-admin-card" style="margin-bottom: 0;">
          <h3 class="eq-admin-card__title" style="margin-bottom: 1.15rem; font-size: 1.02rem; font-weight: 600; color: var(--eq-navy);">
            3. Eligibility &amp; Redemptions
          </h3>

          <!-- Minimum Spend -->
          <div style="margin-bottom: 1.15rem;">
            <label for="input-min-order" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.35rem;">
              Minimum Cart Spend (৳) <span style="color: var(--eq-charcoal-soft); font-weight: normal;">(Optional)</span>
            </label>
            <input 
              type="number" 
              step="0.01" 
              min="0" 
              name="min_order_amount" 
              id="input-min-order" 
              value="{{ old('min_order_amount') }}" 
              placeholder="e.g. 2500 (Leave blank for no minimum)" 
              style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; background: #ffffff;"
            />
            <small style="display: block; font-size: 0.74rem; color: var(--eq-charcoal-soft); margin-top: 0.25rem;">
              Coupon will only apply if cart subtotal reaches or exceeds this spend.
            </small>
          </div>

          <!-- Total Usage Limit -->
          <div style="margin-bottom: 1.15rem;">
            <label for="input-usage-limit" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.35rem;">
              Total Usage Limit <span style="color: var(--eq-charcoal-soft); font-weight: normal;">(Optional)</span>
            </label>
            <input 
              type="number" 
              step="1" 
              min="1" 
              name="usage_limit" 
              id="input-usage-limit" 
              value="{{ old('usage_limit') }}" 
              placeholder="e.g. 100 (Leave blank for unlimited)" 
              style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; background: #ffffff;"
            />
            <small style="display: block; font-size: 0.74rem; color: var(--eq-charcoal-soft); margin-top: 0.25rem;">
              Maximum number of times this coupon can be redeemed across all customers.
            </small>
          </div>

          <!-- Expiry Date & Time -->
          <div style="margin-bottom: 1.15rem;">
            <label for="input-expires-at" style="display: block; font-size: 0.84rem; font-weight: 600; color: var(--eq-charcoal); margin-bottom: 0.35rem;">
              Expiration Date &amp; Time <span style="color: var(--eq-charcoal-soft); font-weight: normal;">(Optional)</span>
            </label>
            <input 
              type="datetime-local" 
              name="expires_at" 
              id="input-expires-at" 
              value="{{ old('expires_at') }}" 
              style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-size: 0.9rem; background: #ffffff;"
            />
            <small style="display: block; font-size: 0.74rem; color: var(--eq-charcoal-soft); margin-top: 0.25rem;">
              Leave blank for a permanent voucher code without an expiration cutoff.
            </small>
          </div>

          <!-- Active Switch -->
          <div style="padding: 0.75rem; background: #faf8f5; border-radius: 6px; border: 1px solid var(--eq-line); display: flex; align-items: center; justify-content: space-between;">
            <div>
              <div style="font-size: 0.86rem; font-weight: 600; color: var(--eq-navy);">Publish as Active</div>
              <div style="font-size: 0.74rem; color: var(--eq-charcoal-soft);">Coupon will immediately be redeemable by customers.</div>
            </div>
            <label style="position: relative; display: inline-block; width: 44px; height: 24px;">
              <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;" id="toggle-active" />
              <span class="eq-slider" onclick="document.getElementById('toggle-active').checked = !document.getElementById('toggle-active').checked; updateSlider(document.getElementById('toggle-active'));" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--eq-gold); transition: .3s; border-radius: 24px;"></span>
            </label>
          </div>
        </div>

        <!-- Submit & Actions Card -->
        <div class="eq-admin-card" style="margin-bottom: 0; background: #faf8f5; border-color: rgba(201, 150, 47, 0.4);">
          <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <button type="submit" class="eq-admin-btn eq-admin-btn--primary" style="width: 100%; justify-content: center; padding: 0.75rem 1.25rem; font-size: 0.92rem; font-weight: 600; border-radius: 6px; box-shadow: 0 4px 12px rgba(201, 150, 47, 0.25);">
              🚀 Launch Campaign Coupon
            </button>
            <a href="{{ route('admin.coupons') }}" class="eq-admin-btn eq-admin-btn--secondary" style="width: 100%; justify-content: center; padding: 0.6rem 1rem; font-size: 0.84rem; text-decoration: none; border-radius: 6px; text-align: center;">
              Cancel &amp; Return
            </a>
          </div>
        </div>

      </div>

    </div>
  </form>
@endsection

@push('scripts')
<script>
  function toggleTypeHelp(type) {
    const help = document.getElementById('value-help');
    const maxWrapper = document.getElementById('max-discount-wrapper');
    if (type === 'percent') {
      if (help) help.textContent = 'Enter percentage number (e.g. 15 for 15%).';
      if (maxWrapper) maxWrapper.style.opacity = '1';
    } else {
      if (help) help.textContent = 'Enter flat discount in Bangladeshi Taka (e.g. 500 for ৳500).';
      if (maxWrapper) maxWrapper.style.opacity = '0.5';
    }
  }

  function updateSlider(checkbox) {
    const slider = checkbox.nextElementSibling;
    if (slider) {
      slider.style.backgroundColor = checkbox.checked ? 'var(--eq-gold)' : '#ccc';
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const initialType = document.querySelector('input[name="type"]:checked')?.value || 'percent';
    toggleTypeHelp(initialType);
  });
</script>
@endpush


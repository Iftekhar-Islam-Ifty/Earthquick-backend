@extends('admin.layout')

@section('title', 'Order #' . $order->order_number . ' — Rthquick Admin')
@section('page_title', 'Order Details #' . $order->order_number)

@section('content')

  @if($errors->any())
    <div class="eq-admin-alert eq-admin-alert--error" role="alert">{{ $errors->first() }}</div>
  @endif

  <!-- Top Action & Navigation Bar Card -->
  <div style="background: var(--eq-white); border: 1px solid var(--eq-line); border-radius: 8px; padding: 0.85rem 1.15rem; margin-bottom: 1.15rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.02);">
    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
      <a href="{{ route('admin.orders') }}" class="eq-admin-btn eq-admin-btn--outline" style="gap: 0.4rem; padding: 0.4rem 0.85rem; font-size: 0.82rem;">
        &larr; Back to All Orders
      </a>
      <div style="font-size: 0.84rem; color: var(--eq-charcoal-soft);">
        Placed on: <strong style="color: var(--eq-navy);">{{ $order->created_at->format('d M Y, h:i A') }}</strong>
      </div>
    </div>

    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
      <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="eq-admin-btn eq-admin-btn--primary" style="gap: 0.4rem; padding: 0.4rem 0.95rem; font-size: 0.82rem;">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="6 9 6 2 18 2 18 9"></polyline>
          <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
          <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        Print Invoice / Slip
      </a>

      <div style="display: flex; align-items: center; gap: 0.45rem; background: var(--eq-cream); padding: 0.3rem 0.75rem; border-radius: 6px; border: 1px solid var(--eq-line);">
        <span style="font-size: 0.75rem; color: var(--eq-charcoal-soft); text-transform: uppercase; letter-spacing: 0.04em;">Status:</span>
        <span class="eq-status-badge eq-status-badge--{{ $order->status }}" style="font-size: 0.76rem; padding: 0.2rem 0.65rem;">
          {{ str_replace('_', ' ', $order->status) }}
        </span>
      </div>
    </div>
  </div>

  <!-- Two-Column Order Work Area -->
  <div class="eq-admin-2col-grid">
    
    <!-- LEFT COLUMN: ITEMS & FINANCIAL SUMMARY -->
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <!-- Order Items Card -->
      <section class="eq-admin-card">
        <div class="eq-admin-card__header" style="margin-bottom: 0.75rem; padding-bottom: 0.55rem;">
          <h2 class="eq-admin-card__title" style="font-size: 1.05rem;">Purchased Creations ({{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }})</h2>
        </div>
        <p style="margin:0 0 0.8rem;color:var(--eq-charcoal-soft);font-size:0.78rem;line-height:1.5;">Current available units are shown with each item. Confirming an order does not change stock; cancelling an eligible unpaid order restores it.</p>

        <!-- Desktop Items Table -->
        <div class="eq-desktop-only">
          <div class="eq-admin-table-wrap">
            <table class="eq-admin-table">
              <thead>
                <tr>
                  <th>Piece</th>
                  <th>Unit Price</th>
                  <th style="text-align: center;">Quantity</th>
                  <th style="text-align: right;">Line Total</th>
                </tr>
              </thead>
              <tbody>
                @foreach($order->items as $item)
                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 0.75rem;">
                        @if($item->product_image)
                          <img src="{{ asset($item->product_image) }}" alt="{{ $item->product_name }}" style="width: 40px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid var(--eq-line);" />
                        @endif
                        <div>
                          <div style="font-weight: 500; color: var(--eq-navy);">{{ $item->product_name }}</div>
                          @if($item->vendor)
                            <div style="font-size: 0.72rem; color: var(--eq-gold-dark); font-weight: 600;">Brand: {{ $item->vendor->name }}</div>
                          @endif
                          @if($item->variant_label)
                            <div style="font-size: 0.74rem; color: var(--eq-charcoal-soft);">Option: {{ $item->variant_label }}{{ $item->variant_sku ? ' · '.$item->variant_sku : '' }}</div>
                          @endif
                          @if($item->delivery_class)
                            <div style="font-size: 0.72rem; color: var(--eq-charcoal-soft);">{{ config('catalog.delivery_classes.'.$item->delivery_class, ucfirst($item->delivery_class)) }} · {{ $item->is_returnable ? ($item->return_window_days.'-day return') : 'Final sale' }}</div>
                          @endif
                          @if($item->variant_id)
                            <div style="font-size:0.74rem;color:var(--eq-teal);font-weight:600;">{{ $item->variant ? 'Available now for this option: '.$item->variant->stock_quantity.' units' : 'Original option no longer in the catalog' }}</div>
                          @elseif($item->product)
                            <div style="font-size:0.74rem;color:var(--eq-teal);font-weight:600;">Available now: {{ $item->product->stock_quantity }} units</div>
                          @else
                            <div style="font-size:0.74rem;color:var(--eq-charcoal-soft);">Original product no longer in the catalog</div>
                          @endif
                        </div>
                      </div>
                    </td>
                    <td style="font-size: 0.86rem;">৳{{ number_format($item->unit_price) }}</td>
                    <td style="text-align: center; font-weight: 600;">&times; {{ $item->quantity }}</td>
                    <td style="text-align: right; font-weight: 600; color: var(--eq-charcoal);">
                      ৳{{ number_format($item->total_price) }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        <!-- Mobile Items Cards -->
        <div class="eq-mobile-only">
          <div style="display: flex; flex-direction: column; gap: 0.6rem;">
            @foreach($order->items as $item)
              <div style="display: flex; gap: 0.65rem; align-items: center; padding: 0.55rem 0.65rem; background: var(--eq-cream); border-radius: 6px; border: 1px solid var(--eq-line);">
                @if($item->product_image)
                  <img src="{{ asset($item->product_image) }}" alt="{{ $item->product_name }}" style="width: 44px; height: 56px; object-fit: cover; border-radius: 4px; border: 1px solid var(--eq-line); flex-shrink: 0;" />
                @endif
                <div style="flex: 1; min-width: 0;">
                  <div style="font-weight: 600; font-size: 0.86rem; color: var(--eq-navy); line-height: 1.3;">{{ $item->product_name }}</div>
                  @if($item->vendor)
                    <div style="font-size: 0.7rem; color: var(--eq-gold-dark); font-weight: 600;">Brand: {{ $item->vendor->name }}</div>
                  @endif
                  @if($item->variant_label)
                    <div style="font-size: 0.72rem; color: var(--eq-charcoal-soft); margin-top: 1px;">Option: {{ $item->variant_label }}{{ $item->variant_sku ? ' · '.$item->variant_sku : '' }}</div>
                  @endif
                  @if($item->delivery_class)
                    <div style="font-size: 0.7rem; color: var(--eq-charcoal-soft);">{{ config('catalog.delivery_classes.'.$item->delivery_class, ucfirst($item->delivery_class)) }} · {{ $item->is_returnable ? ($item->return_window_days.'-day return') : 'Final sale' }}</div>
                  @endif
                  @if($item->variant_id)
                    <div style="font-size:0.72rem;color:var(--eq-teal);font-weight:600;">{{ $item->variant ? 'Available now for this option: '.$item->variant->stock_quantity.' units' : 'Original option no longer in the catalog' }}</div>
                  @elseif($item->product)
                    <div style="font-size:0.72rem;color:var(--eq-teal);font-weight:600;">Available now: {{ $item->product->stock_quantity }} units</div>
                  @else
                    <div style="font-size:0.72rem;color:var(--eq-charcoal-soft);">Original product no longer in the catalog</div>
                  @endif
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.3rem; font-size: 0.8rem;">
                    <span style="color: var(--eq-charcoal-soft);">৳{{ number_format($item->unit_price) }} &times; {{ $item->quantity }}</span>
                    <strong style="color: var(--eq-gold-dark);">৳{{ number_format($item->total_price) }}</strong>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- Financial Summary Breakdown -->
        <div style="border-top: 1px solid var(--eq-line); padding-top: 1rem; margin-top: 1rem;">
          <div style="max-width: 320px; margin-left: auto; display: flex; flex-direction: column; gap: 0.45rem; font-size: 0.86rem;">
            <div style="display: flex; justify-content: space-between; color: var(--eq-charcoal-soft);">
              <span>Subtotal:</span>
              <span>৳{{ number_format($order->subtotal) }}</span>
            </div>
            @if($order->discount_amount > 0)
              <div style="display: flex; justify-content: space-between; color: #28a745; font-weight: 600;">
                <span>Promo Discount ({{ $order->coupon_code }}):</span>
                <span>-৳{{ number_format($order->discount_amount) }}</span>
              </div>
            @endif
            <div style="display: flex; justify-content: space-between; color: var(--eq-charcoal-soft);">
              <span>Delivery Charge ({{ $order->delivery_zone === 'inside_ctg' ? 'Chattogram' : 'Nationwide' }}):</span>
              <span>৳{{ number_format($order->delivery_fee) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 1.05rem; font-weight: 600; color: var(--eq-navy); border-top: 2px solid var(--eq-navy); padding-top: 0.55rem; margin-top: 0.25rem;">
              <span>{{ $order->payment_method === 'cod' ? match ($order->payment_status) { 'paid' => 'Total Paid (COD):', 'due_on_delivery' => 'Total Due (COD):', default => 'Order Total (COD):' } : 'Order Total:' }}</span>
              <span style="color: var(--eq-gold-dark);">৳{{ number_format($order->total) }}</span>
            </div>
          </div>
        </div>
      </section>

      @if($order->payment_method === 'cod')
      <section class="eq-admin-card">
        <div class="eq-admin-card__header">
          <h3 class="eq-admin-card__title" style="font-size: 1rem;">COD Payment</h3>
        </div>
        <p style="font-size: 0.85rem; margin-bottom: 0.75rem;">
          Status: <strong>{{ match ($order->payment_status) { 'paid' => 'Paid', 'due_on_delivery' => 'Due on delivery', 'not_due' => 'Not due — order cancelled', default => 'Unverified historical payment' } }}</strong>
        </p>
        @if($order->payment_status === 'paid')
          <p style="font-size: 0.82rem; color: var(--eq-charcoal-soft);">
            Recorded {{ $order->paid_at?->format('d M Y, h:i A') }} by {{ $order->paymentRecorder?->name ?? 'former admin' }}.
            Source: {{ $order->cod_collection_channel === 'in_house' ? 'In-house delivery' : 'Courier remittance' }}.
            @if($order->cod_collection_note) Note: {{ $order->cod_collection_note }} @endif
          </p>
        @elseif($order->payment_method === 'cod' && $order->status === 'delivered')
          <form method="POST" action="{{ route('admin.orders.cod-paid', $order->id) }}">
            @csrf
            <label for="cod-collection-channel" style="display: block; font-size: 0.82rem; margin-bottom: 0.3rem;">Confirmed collection source</label>
            <select id="cod-collection-channel" name="cod_collection_channel" required style="width: 100%; padding: 0.6rem; margin-bottom: 0.7rem; border: 1px solid var(--eq-line); border-radius: 6px;">
              <option value="">Select source</option>
              <option value="in_house" @selected(old('cod_collection_channel') === 'in_house')>In-house delivery cash received</option>
              <option value="courier_remittance" @selected(old('cod_collection_channel') === 'courier_remittance')>Courier remittance received</option>
            </select>
            <label for="cod-collection-note" style="display: block; font-size: 0.82rem; margin-bottom: 0.3rem;">Collection note or receipt ID (optional)</label>
            <input id="cod-collection-note" name="cod_collection_note" type="text" maxlength="255" value="{{ old('cod_collection_note') }}" style="width: 100%; padding: 0.6rem; margin-bottom: 0.7rem; border: 1px solid var(--eq-line); border-radius: 6px;" />
            <label style="display: flex; gap: 0.5rem; align-items: flex-start; font-size: 0.82rem; margin-bottom: 0.8rem;">
              <input type="checkbox" name="confirm_collected" value="1" required />
              <span>I confirm Rthquick has actually received the full COD amount of ৳{{ number_format($order->total) }}. Delivery status alone does not confirm payment.</span>
            </label>
            <button type="submit" class="eq-admin-btn eq-admin-btn--gold" style="width: 100%; justify-content: center;">Mark COD Paid</button>
          </form>
        @elseif($order->status === 'cancelled')
          <p style="font-size: 0.82rem; color: var(--eq-charcoal-soft);">No COD payment is due for this cancelled order.</p>
        @else
          <p style="font-size: 0.82rem; color: var(--eq-charcoal-soft);">Mark the order delivered first, then confirm the cash collection or courier remittance separately.</p>
        @endif
      </section>
      @endif

      <!-- Customer Delivery Notes -->
      @if($order->order_notes)
        <section class="eq-admin-card" style="border-left: 3px solid var(--eq-gold);">
          <div class="eq-admin-card__header">
            <h3 class="eq-admin-card__title" style="font-size: 1rem;">Customer Delivery Instructions</h3>
          </div>
          <p style="font-size: 0.88rem; color: var(--eq-charcoal); font-style: italic; background: var(--eq-cream); padding: 0.85rem 1rem; border-radius: 4px;">
            &ldquo;{{ $order->order_notes }}&rdquo;
          </p>
        </section>
      @endif

      <!-- Internal Admin Remarks -->
      @if($order->admin_notes)
        <section class="eq-admin-card" style="border-left: 3px solid var(--eq-navy);">
          <div class="eq-admin-card__header">
            <h3 class="eq-admin-card__title" style="font-size: 1rem;">Internal Admin &amp; Warehouse Remarks</h3>
          </div>
          <p style="font-size: 0.88rem; color: var(--eq-charcoal); background: var(--eq-cream-deep); padding: 0.85rem 1rem; border-radius: 4px; white-space: pre-wrap;">{{ $order->admin_notes }}</p>
        </section>
      @endif

      <section class="eq-admin-card">
        <div class="eq-admin-card__header">
          <h3 class="eq-admin-card__title" style="font-size: 1rem;">Order Status History</h3>
        </div>
        @forelse($order->statusEvents as $event)
          <div style="border-left: 2px solid var(--eq-line); padding: 0.25rem 0 0.8rem 0.85rem; margin-left: 0.25rem; font-size: 0.82rem; overflow-wrap: anywhere;">
            <strong>{{ $event->from_status ? ucfirst(str_replace('_', ' ', $event->from_status)).' → ' : 'Placed → ' }}{{ ucfirst(str_replace('_', ' ', $event->to_status)) }}</strong>
            <div style="color: var(--eq-charcoal-soft); margin-top: 0.15rem;">{{ $event->created_at->format('d M Y, h:i A') }} · {{ $event->source === 'checkout' ? 'Checkout' : ($event->actor?->name ?? 'Former admin') }}</div>
            @if($event->note)
              <div style="white-space: pre-wrap; margin-top: 0.35rem;">{{ $event->note }}</div>
            @endif
          </div>
        @empty
          <p style="font-size: 0.82rem; color: var(--eq-charcoal-soft);">No recorded history for this older order. Its earlier changes cannot be reconstructed.</p>
        @endforelse
      </section>
    </div>

    <!-- RIGHT COLUMN: STATUS UPDATE & CLIENT INFO -->
    <div>

      @if($order->cancellationRequests->isNotEmpty())
        <section class="eq-admin-card">
          <div class="eq-admin-card__header"><h3 class="eq-admin-card__title" style="font-size: 1rem;">Cancellation Requests</h3></div>
          @foreach($order->cancellationRequests as $cancellation)
            <div style="border-top: 1px solid var(--eq-line); padding: 0.8rem 0; font-size: 0.84rem; overflow-wrap: anywhere;">
              <strong>{{ ucfirst($cancellation->status) }}</strong> · {{ $cancellation->source === 'admin' ? 'Admin initiated' : 'Customer requested' }} · {{ $cancellation->created_at->format('d M Y, h:i A') }}
              <p style="white-space: pre-wrap; margin: 0.45rem 0;">{{ $cancellation->reason }}</p>
              @if($cancellation->status === 'pending')
                <form method="POST" action="{{ route('admin.orders.cancellation-decision', [$order->id, $cancellation->id]) }}">
                  @csrf
                  <label for="decision-note-{{ $cancellation->id }}" style="display: block; margin-bottom: 0.3rem;">Decision note (internal)</label>
                  <textarea id="decision-note-{{ $cancellation->id }}" name="decision_note" required minlength="5" maxlength="2000" rows="2" style="width: 100%; box-sizing: border-box; padding: 0.6rem; border: 1px solid var(--eq-line); border-radius: 6px;">{{ old('decision_note') }}</textarea>
                  <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                    @if(in_array($order->status, ['pending', 'confirmed'], true) && $order->payment_method === 'cod' && $order->payment_status === 'due_on_delivery')
                      <button type="submit" name="decision" value="approve" class="eq-admin-btn eq-admin-btn--gold">Approve &amp; Cancel Order</button>
                    @endif
                    <button type="submit" name="decision" value="reject" class="eq-admin-btn eq-admin-btn--outline">Decline Request</button>
                  </div>
                </form>
              @else
                <p style="color: var(--eq-charcoal-soft); font-size: 0.78rem;">Decided {{ $cancellation->decided_at?->format('d M Y, h:i A') }} by {{ $cancellation->decisionMaker?->name ?? 'former admin' }}.
                  @if($cancellation->decision_note) Note: {{ $cancellation->decision_note }} @endif
                </p>
              @endif
            </div>
          @endforeach
        </section>
      @endif

      @if($order->returnRequests->isNotEmpty())
        <section class="eq-admin-card">
          <div class="eq-admin-card__header"><h3 class="eq-admin-card__title" style="font-size: 1rem;">Item Return Requests</h3></div>
          @foreach($order->returnRequests as $return)
            <div style="border-top: 1px solid var(--eq-line); padding: 0.8rem 0; font-size: 0.84rem; overflow-wrap: anywhere;">
              <strong>{{ $return->item?->product_name ?? 'Historical item' }} · {{ $return->quantity }} unit(s)</strong>
              <div>{{ ucfirst($return->status) }} · requested {{ $return->created_at->format('d M Y, h:i A') }}</div>
              <p style="white-space: pre-wrap; margin: 0.4rem 0;">{{ $return->reason }}</p>
              <p>Customer-reported reason: {{ match ($return->reported_issue_type) { 'wrong_item' => 'Wrong item', 'damaged_defective' => 'Damaged/defective', 'other' => 'Fit/change of mind/other', default => 'Historical request - not categorized' } }}</p>
              @if($return->status === 'pending')
                <form method="POST" action="{{ route('admin.orders.return-decision', [$order->id, $return->id]) }}">
                  @csrf
                  <label for="return-verified-issue-{{ $return->id }}" style="display:block;margin-bottom:0.3rem;">Admin-verified reason (required to authorize)</label>
                  <select id="return-verified-issue-{{ $return->id }}" name="verified_issue_type" style="display:block;width:100%;padding:0.55rem;margin-bottom:0.5rem;border:1px solid var(--eq-line);border-radius:6px;">
                    <option value="">Choose verified reason</option><option value="wrong_item">Wrong item - Rthquick pays return courier</option><option value="damaged_defective">Damaged/defective - Rthquick pays return courier</option><option value="other">Fit/change of mind/other - customer pays return courier</option>
                  </select>
                  <label for="return-decision-{{ $return->id }}" style="display: block; margin-bottom: 0.3rem;">Decision note (internal)</label>
                  <textarea id="return-decision-{{ $return->id }}" name="decision_note" required minlength="5" maxlength="2000" rows="2" style="width: 100%; box-sizing: border-box; padding: 0.6rem; border: 1px solid var(--eq-line); border-radius: 6px;"></textarea>
                  <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                    <button type="submit" name="decision" value="authorize" class="eq-admin-btn eq-admin-btn--gold">Authorize Return</button>
                    <button type="submit" name="decision" value="reject" class="eq-admin-btn eq-admin-btn--outline">Decline Request</button>
                  </div>
                </form>
              @elseif($return->status === 'authorized')
                <p style="font-size: 0.78rem; color: var(--eq-charcoal-soft);">Authorized by {{ $return->decisionMaker?->name ?? 'former admin' }}. This does not mean refunded or restocked.</p>
                <p>Verified reason: {{ str_replace('_', ' ', $return->verified_issue_type ?? 'historical/unverified') }}. Return courier payer: {{ $return->return_shipping_payer === 'earthquick' ? 'Rthquick' : ucfirst($return->return_shipping_payer ?? 'manual review required') }}.</p>
                <form method="POST" action="{{ route('admin.orders.return-receive', [$order->id, $return->id]) }}">
                  @csrf
                  <label for="return-receipt-{{ $return->id }}" style="display: block; margin-bottom: 0.3rem;">Receipt and inspection note</label>
                  <textarea id="return-receipt-{{ $return->id }}" name="receipt_note" required minlength="5" maxlength="2000" rows="2" style="width: 100%; box-sizing: border-box; padding: 0.6rem; border: 1px solid var(--eq-line); border-radius: 6px;"></textarea>
                  <label style="display: flex; gap: 0.5rem; align-items: flex-start; margin: 0.5rem 0;"><input type="checkbox" name="confirm_received" value="1" required /> <span>I confirm these returned units were physically received.</span></label>
                  <button type="submit" class="eq-admin-btn eq-admin-btn--gold">Record Item Received</button>
                </form>
              @else
                <p style="font-size: 0.78rem; color: var(--eq-charcoal-soft);">@if($return->status === 'received') Received {{ $return->received_at?->format('d M Y, h:i A') }} by {{ $return->receiver?->name ?? 'former admin' }}. Refund and stock review remain separate. @else Decided by {{ $return->decisionMaker?->name ?? 'former admin' }}. @endif</p>
                @if($return->status === 'received')
                  <p>Verified reason: {{ str_replace('_', ' ', $return->verified_issue_type ?? 'historical/unverified') }}. Return courier payer: {{ $return->return_shipping_payer === 'earthquick' ? 'Rthquick' : ucfirst($return->return_shipping_payer ?? 'manual review required') }}.</p>
                  @if(!$return->inspection_outcome)
                    <form method="POST" action="{{ route('admin.orders.return-inspect', [$order->id, $return->id]) }}">
                      @csrf
                      <label for="inspection-outcome-{{ $return->id }}">Inspection outcome</label>
                      <select id="inspection-outcome-{{ $return->id }}" name="inspection_outcome" required style="display:block;width:100%;padding:0.55rem;margin:0.35rem 0;border:1px solid var(--eq-line);border-radius:6px;">
                        <option value="">Choose outcome</option><option value="resellable">Accepted - resellable</option><option value="not_resellable">Accepted - not resellable</option><option value="rejected">Rejected after inspection</option>
                      </select>
                      <label for="inspection-note-{{ $return->id }}">Inspection note</label>
                      <textarea id="inspection-note-{{ $return->id }}" name="inspection_note" required minlength="5" maxlength="2000" rows="2" style="display:block;width:100%;box-sizing:border-box;padding:0.6rem;margin:0.35rem 0;border:1px solid var(--eq-line);border-radius:6px;"></textarea>
                      <button type="submit" class="eq-admin-btn eq-admin-btn--gold">Record Inspection</button>
                    </form>
                  @else
                    <p>Inspection: {{ str_replace('_', ' ', ucfirst($return->inspection_outcome)) }} by {{ $return->inspector?->name ?? 'former admin' }}. {{ $return->inspection_note }}</p>
                    @if($return->inspection_outcome === 'resellable')
                      @if($return->restocked_at)
                        <p>Restocked {{ $return->restocked_at->format('d M Y, h:i A') }} by {{ $return->restocker?->name ?? 'former admin' }}.</p>
                      @else
                        <form method="POST" action="{{ route('admin.orders.return-restock', [$order->id, $return->id]) }}">
                          @csrf
                          <label style="display:flex;gap:0.5rem;align-items:flex-start;margin:0.5rem 0;"><input type="checkbox" name="confirm_restock" value="1" required> <span>I confirm these inspected units are resellable and should return to stock.</span></label>
                          <button type="submit" class="eq-admin-btn eq-admin-btn--outline">Restock {{ $return->quantity }} Unit(s)</button>
                        </form>
                      @endif
                    @endif
                    @if(in_array($return->inspection_outcome, ['resellable', 'not_resellable']) && $order->payment_method === 'cod' && $order->payment_status === 'paid' && $order->paid_at)
                      @if(!$return->refund)
                        <form method="POST" action="{{ route('admin.orders.refund-approve', [$order->id, $return->id]) }}" style="margin-top:0.8rem;border-top:1px solid var(--eq-line);padding-top:0.8rem;">
                          @csrf
                          <p>Approve COD refund amount. Item discount is allocated proportionally. No money is sent by this action.</p>
                          <label for="refund-note-{{ $return->id }}">Refund decision note</label>
                          <textarea id="refund-note-{{ $return->id }}" name="approval_note" required minlength="5" maxlength="2000" rows="2" style="display:block;width:100%;box-sizing:border-box;padding:0.6rem;margin:0.35rem 0;border:1px solid var(--eq-line);border-radius:6px;"></textarea>
                          <label style="display:flex;gap:0.5rem;align-items:flex-start;margin:0.5rem 0;"><input type="checkbox" name="include_delivery" value="1"> <span>Include delivery fee (full order return only, after admin review)</span></label>
                          @if($return->return_shipping_payer === 'earthquick')
                            <label for="return-postage-{{ $return->id }}">Verified return courier postage to reimburse (0 if Rthquick booked/paid courier directly)</label>
                            <input id="return-postage-{{ $return->id }}" type="number" name="return_shipping_amount" min="0" step="0.01" value="0" style="display:block;width:100%;box-sizing:border-box;padding:0.55rem;margin:0.35rem 0;border:1px solid var(--eq-line);border-radius:6px;">
                            <label for="return-postage-receipt-{{ $return->id }}">Courier receipt/tracking reference (required for reimbursement; use once)</label>
                            <input id="return-postage-receipt-{{ $return->id }}" type="text" name="return_shipping_receipt_reference" maxlength="100" style="display:block;width:100%;box-sizing:border-box;padding:0.55rem;margin:0.35rem 0;border:1px solid var(--eq-line);border-radius:6px;">
                          @else
                            <p>Return courier reimbursement unavailable: customer pays, or this historical request needs manual payer review.</p>
                          @endif
                          <button type="submit" class="eq-admin-btn eq-admin-btn--gold">Approve Refund Amount</button>
                        </form>
                      @else
                        <div style="margin-top:0.8rem;border-top:1px solid var(--eq-line);padding-top:0.8rem;">
                          <strong>Refund: {{ ucwords(str_replace('_', ' ', $return->refund->status)) }} - {{ number_format($return->refund->total_amount, 2) }}</strong>
                          <p>Items {{ number_format($return->refund->item_amount, 2) }} - discount {{ number_format($return->refund->discount_share, 2) }} + original delivery {{ number_format($return->refund->delivery_amount, 2) }} + return courier {{ number_format($return->refund->return_shipping_amount, 2) }}. Approved by {{ $return->refund->approver?->name ?? 'former admin' }}.</p>
                          @if($return->refund->return_shipping_receipt_reference)<p>Return courier receipt: {{ $return->refund->return_shipping_receipt_reference }}</p>@endif
                          @if($return->refund->status === 'approved')
                            @if(!$return->refund->recipient_verified_at)
                            <form method="POST" action="{{ route('admin.orders.refund-verify-recipient', [$order->id, $return->refund->id]) }}">
                              @csrf
                              <p>Verify the recipient <strong>before</strong> sending money. This step does not mark the refund paid.</p>
                              <label for="refund-method-{{ $return->id }}">Intended refund method</label>
                              <select id="refund-method-{{ $return->id }}" name="method" required style="display:block;width:100%;padding:0.55rem;margin:0.35rem 0;border:1px solid var(--eq-line);border-radius:6px;">
                                <option value="">Choose method</option><option value="bank_transfer">Bank transfer</option><option value="mobile_transfer">Mobile transfer</option><option value="cash">Cash</option>
                              </select>
                              <p>Verify the recipient through the order contact or in person before sending. Store only the destination's last four digits, not the full account number.</p>
                              <label for="refund-recipient-{{ $return->id }}">Verified recipient name</label>
                              <input id="refund-recipient-{{ $return->id }}" type="text" name="recipient_name" required minlength="2" maxlength="150" style="display:block;width:100%;box-sizing:border-box;padding:0.55rem;margin:0.35rem 0;border:1px solid var(--eq-line);border-radius:6px;">
                              <label for="refund-last4-{{ $return->id }}">Destination account/mobile last 4 digits (required for transfers)</label>
                              <input id="refund-last4-{{ $return->id }}" type="text" name="recipient_account_last4" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" style="display:block;width:100%;box-sizing:border-box;padding:0.55rem;margin:0.35rem 0;border:1px solid var(--eq-line);border-radius:6px;">
                              <label for="refund-verified-via-{{ $return->id }}">Recipient verified via</label>
                              <select id="refund-verified-via-{{ $return->id }}" name="recipient_verified_via" required style="display:block;width:100%;padding:0.55rem;margin:0.35rem 0;border:1px solid var(--eq-line);border-radius:6px;"><option value="">Choose channel</option><option value="order_contact">Contact on the original order</option><option value="in_person">In person</option></select>
                              <label for="refund-verification-note-{{ $return->id }}">Internal verification note (explain any different recipient)</label>
                              <textarea id="refund-verification-note-{{ $return->id }}" name="recipient_verification_note" required minlength="10" maxlength="1000" rows="2" style="display:block;width:100%;box-sizing:border-box;padding:0.55rem;margin:0.35rem 0;border:1px solid var(--eq-line);border-radius:6px;"></textarea>
                              <label style="display:flex;gap:0.5rem;align-items:flex-start;margin:0.5rem 0;"><input type="checkbox" name="confirm_recipient_verified" value="1" required> <span>I verified this recipient before recording the payout.</span></label>
                              <button type="submit" class="eq-admin-btn eq-admin-btn--gold">Record Recipient Verification</button>
                            </form>
                            @else
                            <p>Recipient verified for {{ str_replace('_', ' ', $return->refund->method) }}: {{ $return->refund->recipient_name }}{{ $return->refund->recipient_account_last4 ? ' · destination ending '.$return->refund->recipient_account_last4 : '' }}. No payment recorded yet.</p>
                            <form method="POST" action="{{ route('admin.orders.refund-complete', [$order->id, $return->refund->id]) }}">
                              @csrf
                              <p>Send the approved amount outside Rthquick to the verified recipient first. Record completion only after checking the transfer.</p>
                              <label for="refund-reference-{{ $return->id }}">External payment reference</label>
                              <input id="refund-reference-{{ $return->id }}" type="text" name="reference" required minlength="4" maxlength="100" style="display:block;width:100%;box-sizing:border-box;padding:0.55rem;margin:0.35rem 0;border:1px solid var(--eq-line);border-radius:6px;">
                              <label style="display:flex;gap:0.5rem;align-items:flex-start;margin:0.5rem 0;"><input type="checkbox" name="confirm_sent" value="1" required> <span>I confirm the full approved amount was sent to the customer.</span></label>
                              <button type="submit" class="eq-admin-btn eq-admin-btn--gold">Record Paid Refund</button>
                            </form>
                            @endif
                          @elseif($return->refund->status === 'completed')
                            <p>Paid via {{ str_replace('_', ' ', $return->refund->method) }}. Reference: {{ $return->refund->reference }}. Recorded {{ $return->refund->completed_at?->format('d M Y, h:i A') }} by {{ $return->refund->completer?->name ?? 'former admin' }}.</p>
                            @if($return->refund->recipient_verified_at)<p>Recipient verified: {{ $return->refund->recipient_name }}{{ $return->refund->recipient_account_last4 ? ' · destination ending '.$return->refund->recipient_account_last4 : '' }} via {{ str_replace('_', ' ', $return->refund->recipient_verified_via) }}. {{ $return->refund->recipient_verification_note }}</p>@endif
                          @else
                            <p>No refund due after discount allocation; no payment action required.</p>
                          @endif
                        </div>
                      @endif
                    @endif
                  @endif
                @endif
              @endif
            </div>
          @endforeach
        </section>
      @endif

      <!-- Lifecycle & Logistics Form Card -->
      <section class="eq-admin-card" style="border: 1px solid var(--eq-gold);">
        <div class="eq-admin-card__header">
          <h3 class="eq-admin-card__title" style="font-size: 1rem; color: var(--eq-gold-dark);">
            Fulfillment &amp; Courier Logistics
          </h3>
        </div>

        <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}">
          @csrf
          <div style="margin-bottom: 1.15rem;">
            <label for="order-status-select" style="display: block; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.45rem; color: var(--eq-charcoal);">
              Lifecycle Stage:
            </label>
            <select name="status" id="order-status-select" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-family: var(--font-body); font-size: 0.9rem; background: var(--eq-white); color: var(--eq-charcoal); outline: none;">
              <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending (Received)</option>
              <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed (Artisan Verified)</option>
              <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing (Packaging in Workshop)</option>
              <option value="in_transit" {{ $order->status === 'in_transit' ? 'selected' : '' }}>In Transit (Dispatched with Courier)</option>
              <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered (Complete)</option>
              <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
          </div>

          <div style="margin-bottom: 1.15rem;">
            <label for="order-courier-input" style="display: block; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.45rem; color: var(--eq-charcoal);">
              Courier Partner:
            </label>
            <input type="text" name="courier_name" id="order-courier-input" list="courier-list" value="{{ old('courier_name', $order->courier_name) }}" placeholder="e.g. Steadfast Courier, Sundarban, Pathao" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-family: var(--font-body); font-size: 0.9rem; background: var(--eq-white); color: var(--eq-charcoal); outline: none;">
            <datalist id="courier-list">
              <option value="Steadfast Courier">
              <option value="Sundarban Courier Service">
              <option value="Pathao Courier">
              <option value="RedX Logistics">
              <option value="Paperfly">
              <option value="eCourier">
              <option value="In-House Delivery Fleet">
            </datalist>
          </div>

          <div style="margin-bottom: 1.15rem;">
            <label for="order-tracking-input" style="display: block; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.45rem; color: var(--eq-charcoal);">
              Tracking ID / Consignment No:
            </label>
            <input type="text" name="tracking_number" id="order-tracking-input" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="e.g. ST-7894210" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-family: var(--font-body); font-size: 0.9rem; background: var(--eq-white); color: var(--eq-charcoal); outline: none;">
          </div>

          <div style="margin-bottom: 1.15rem;">
            <label for="order-admin-notes" style="display: block; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.45rem; color: var(--eq-charcoal);">
              Internal Warehouse Notes (required when cancelling):
            </label>
            <textarea name="admin_notes" id="order-admin-notes" rows="3" placeholder="Remarks visible to shop admin only..." style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--eq-line); font-family: var(--font-body); font-size: 0.86rem; background: var(--eq-white); color: var(--eq-charcoal); outline: none; resize: vertical;">{{ old('admin_notes', $order->admin_notes) }}</textarea>
          </div>

          <p style="font-size: 0.76rem; color: var(--eq-charcoal-soft); margin-bottom: 1.15rem; line-height: 1.45;">
            &bull; Saving updates synchronizes courier &amp; tracking code with customer tracking at <code>/account</code>.
          </p>

          <button type="submit" class="eq-admin-btn eq-admin-btn--gold" style="width: 100%; justify-content: center; padding: 0.65rem;">
            Update Fulfillment &amp; Status
          </button>
        </form>
      </section>

      <!-- Customer Details Card -->
      <section class="eq-admin-card">
        <div class="eq-admin-card__header">
          <h3 class="eq-admin-card__title" style="font-size: 1rem;">Customer &amp; Shipping Destination</h3>
        </div>

        <div style="font-size: 0.86rem; display: flex; flex-direction: column; gap: 0.85rem;">
          <div>
            <span style="display: block; font-size: 0.72rem; text-transform: uppercase; color: var(--eq-charcoal-soft); letter-spacing: 0.05em;">Customer Name</span>
            <strong style="color: var(--eq-navy); font-size: 0.95rem;">{{ $order->customer_name }}</strong>
            @if($order->user_id)
              <span style="display: inline-block; font-size: 0.68rem; background: #e0f2fe; color: #0369a1; padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.35rem;">
                Registered Account #{{ $order->user_id }}
              </span>
            @else
              <span style="display: inline-block; font-size: 0.68rem; background: var(--eq-cream-deep); color: var(--eq-charcoal-soft); padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.35rem;">
                Guest Checkout
              </span>
            @endif
          </div>

          <div>
            <span style="display: block; font-size: 0.72rem; text-transform: uppercase; color: var(--eq-charcoal-soft); letter-spacing: 0.05em;">Phone Number</span>
            <a href="tel:{{ $order->customer_phone }}" style="color: var(--eq-navy); font-weight: 500; text-decoration: none;">
              {{ $order->customer_phone }}
            </a>
          </div>

          @if($order->customer_email)
            <div>
              <span style="display: block; font-size: 0.72rem; text-transform: uppercase; color: var(--eq-charcoal-soft); letter-spacing: 0.05em;">Email Address</span>
              <a href="mailto:{{ $order->customer_email }}" style="color: var(--eq-navy); text-decoration: none;">
                {{ $order->customer_email }}
              </a>
            </div>
          @endif

          <div style="border-top: 1px solid var(--eq-line); padding-top: 0.75rem;">
            <span style="display: block; font-size: 0.72rem; text-transform: uppercase; color: var(--eq-charcoal-soft); letter-spacing: 0.05em;">Delivery Address</span>
            <p style="color: var(--eq-charcoal); line-height: 1.45; margin-top: 0.2rem;">
              {{ $order->address }}<br />
              <strong>{{ $order->area }}, {{ $order->district }}</strong><br />
              <span style="font-size: 0.78rem; color: var(--eq-charcoal-soft);">
                Zone: {{ $order->delivery_zone === 'inside_ctg' ? 'Chattogram Metropolitan' : 'Outside Chattogram (Nationwide)' }}
              </span>
            </p>
          </div>

          <div style="border-top: 1px solid var(--eq-line); padding-top: 0.75rem;">
            <span style="display: block; font-size: 0.72rem; text-transform: uppercase; color: var(--eq-charcoal-soft); letter-spacing: 0.05em;">Payment Arrangement</span>
            <strong style="text-transform: uppercase;">
              {{ $order->payment_method === 'cod' ? 'Cash on Delivery (COD)' : $order->payment_method }}
            </strong>
          </div>
        </div>
      </section>

    </div>

  </div>

  <section class="eq-admin-card" style="margin-top:1.25rem;">
    <div class="eq-admin-card__header">
      <h2 class="eq-admin-card__title" style="font-size:1rem;">Order retention</h2>
    </div>
    @if($order->archived_at)
      <p style="font-size:0.84rem;line-height:1.5;">Archived {{ $order->archived_at->format('d M Y') }}. It remains available here without an expiry date.</p>
      <form method="POST" action="{{ route('admin.orders.unarchive', $order->id) }}">@csrf<button type="submit" class="eq-admin-btn eq-admin-btn--outline">Restore to active orders</button></form>
    @elseif($canArchiveOrder)
      <p style="font-size:0.84rem;line-height:1.5;">Archive this completed/cancelled order to keep active orders tidy. It stays in the database and can be restored any time.</p>
      <form method="POST" action="{{ route('admin.orders.archive', $order->id) }}">@csrf<button type="submit" class="eq-admin-btn eq-admin-btn--outline">Archive order</button></form>
    @else
      <p style="font-size:0.84rem;line-height:1.5;">Active orders cannot be archived until delivered or cancelled.</p>
    @endif
    @if($canTrashOrder)
      <p style="font-size:0.84rem;line-height:1.55;color:var(--eq-charcoal-soft);margin-top:1rem;">This cancelled, unpaid COD order can go to Trash. It remains fully restorable for at least 30 days. Permanent deletion requires a separate admin action after that period. Stock is not changed again.</p>
      <form method="POST" action="{{ route('admin.orders.trash', $order->id) }}" style="display:grid;gap:0.85rem;max-width:560px;margin-top:1rem;" onsubmit="return confirm('Move this order to Trash? It can be restored for at least 30 days.');">
        @csrf
        @method('DELETE')
        <label style="display:grid;gap:0.3rem;font-size:0.82rem;font-weight:600;color:var(--eq-navy);" for="delete-order-number">
          Type order number {{ $order->order_number }}
          <input id="delete-order-number" name="confirm_order_number" type="text" required autocomplete="off" value="{{ old('confirm_order_number') }}" style="width:100%;box-sizing:border-box;padding:0.6rem;border:1px solid var(--eq-line);border-radius:6px;font:inherit;" />
        </label>
        <label style="display:grid;gap:0.3rem;font-size:0.82rem;font-weight:600;color:var(--eq-navy);" for="delete-order-reason">
          Internal deletion reason (do not include customer details)
          <textarea id="delete-order-reason" name="deletion_reason" required minlength="10" maxlength="500" rows="2" style="width:100%;box-sizing:border-box;padding:0.6rem;border:1px solid var(--eq-line);border-radius:6px;font:inherit;">{{ old('deletion_reason') }}</textarea>
        </label>
        <label style="display:flex;gap:0.5rem;align-items:flex-start;font-size:0.82rem;line-height:1.45;color:var(--eq-charcoal);">
          <input type="checkbox" name="confirm_permanent" value="1" required style="margin-top:0.2rem;" />
          <span>I understand this order will be hidden from active records and can be restored from Trash.</span>
        </label>
        <button type="submit" class="eq-admin-btn eq-admin-btn--danger" style="justify-self:start;">Move to Trash</button>
      </form>
    @else
      <p style="margin:1rem 0 0;font-size:0.84rem;line-height:1.55;color:var(--eq-charcoal-soft);">Trash is only for cancelled COD orders with no payment, dispatch, return or refund history. Paid and fulfilled orders remain available in Archive for support and financial records.</p>
    @endif
  </section>

@endsection

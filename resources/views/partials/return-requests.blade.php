@php
  $deliveredEvent = $order->statusEvents->firstWhere('to_status', 'delivered');
@endphp

@if($order->status === 'delivered' || $order->returnRequests->isNotEmpty())
  <section style="border: 1px solid var(--eq-line); border-radius: 8px; padding: 1rem; margin: 1.25rem 0; background: var(--eq-white);">
    <h2 style="font-size: 1rem; margin: 0 0 0.5rem;">Item returns</h2>
    <p style="font-size: 0.82rem; color: var(--eq-charcoal-soft);">A request is not a refund. Wait for authorization before sending an item; Rthquick will separately confirm receipt and any refund.</p>
    <p style="font-size: 0.82rem; color: var(--eq-charcoal-soft);">For a verified wrong, damaged or defective item, Rthquick covers verified return courier postage; keep the courier receipt. For a change of mind, fit or other reason, the customer pays return postage. Rthquick confirms the reason and payer before you send the item.</p>
    @if(session('success'))
      <p role="status" style="font-size: 0.84rem; color: #176a3a;">{{ session('success') }}</p>
    @endif
    @if($errors->any())
      <p role="alert" style="font-size: 0.84rem; color: #a32727;">{{ $errors->first() }}</p>
    @endif

    @foreach($order->items as $item)
      @php
        $itemRequests = $order->returnRequests->where('order_item_id', $item->id);
        $reserved = $itemRequests->whereIn('status', ['pending', 'authorized', 'received'])->sum('quantity');
        $remaining = max(0, $item->quantity - $reserved);
        $withinWindow = $deliveredEvent && $item->return_window_days
            && now()->lessThanOrEqualTo($deliveredEvent->created_at->copy()->addDays($item->return_window_days));
        $canRequest = $order->status === 'delivered' && $item->is_returnable === true
            && $withinWindow && $remaining > 0;
      @endphp
      <div style="border-top: 1px solid var(--eq-line); padding: 0.8rem 0; overflow-wrap: anywhere;">
        <strong style="font-size: 0.88rem;">{{ $item->product_name }}{{ $item->variant_label ? ' · '.$item->variant_label : '' }}</strong>
        <div style="font-size: 0.78rem; color: var(--eq-charcoal-soft);">Purchased: {{ $item->quantity }} · Available for a new request: {{ $remaining }}</div>
        @foreach($itemRequests as $itemRequest)
          <div style="font-size: 0.8rem; margin-top: 0.4rem;">{{ $itemRequest->quantity }} unit(s): <strong>{{ ucfirst($itemRequest->status) }}</strong> · requested {{ $itemRequest->created_at->format('d M Y') }}
            @if($itemRequest->status === 'authorized' || $itemRequest->status === 'received')
              @if($itemRequest->return_shipping_payer === 'earthquick') · Return courier: Rthquick (keep courier receipt)
              @elseif($itemRequest->return_shipping_payer === 'customer') · Return courier: customer
              @else · Return courier payer: contact support for review @endif
            @endif
            @if($itemRequest->status === 'received')
              @if($itemRequest->inspection_outcome === 'rejected') · Return declined after inspection
              @elseif($itemRequest->refund?->status === 'completed') · Refund paid: {{ number_format($itemRequest->refund->total_amount, 2) }}
              @elseif($itemRequest->refund?->status === 'approved') · Refund approved: {{ number_format($itemRequest->refund->total_amount, 2) }} (payment pending)
              @elseif($itemRequest->refund?->status === 'no_refund_due') · No refund due after discount
              @else · Refund review pending @endif
            @endif
          </div>
        @endforeach
        @if($canRequest)
          <form method="POST" action="{{ route('orders.return-request', [$order->order_number, $item->id]) }}" style="margin-top: 0.7rem; max-width: 440px;">
            @csrf
            <label for="return-quantity-{{ $item->id }}" style="display: block; font-size: 0.8rem; margin-bottom: 0.3rem;">Quantity to return</label>
            <input id="return-quantity-{{ $item->id }}" type="number" name="quantity" min="1" max="{{ $remaining }}" value="1" required style="width: 100%; box-sizing: border-box; padding: 0.55rem; border: 1px solid var(--eq-line); border-radius: 6px;" />
            <label for="return-issue-{{ $item->id }}" style="display: block; font-size: 0.8rem; margin: 0.55rem 0 0.3rem;">Reason category</label>
            <select id="return-issue-{{ $item->id }}" name="reported_issue_type" required style="width: 100%; box-sizing: border-box; padding: 0.55rem; border: 1px solid var(--eq-line); border-radius: 6px;">
              <option value="">Choose a reason</option>
              <option value="wrong_item">Wrong item sent</option>
              <option value="damaged_defective">Damaged or defective item</option>
              <option value="other">Fit, change of mind or other</option>
            </select>
            <label for="return-reason-{{ $item->id }}" style="display: block; font-size: 0.8rem; margin: 0.55rem 0 0.3rem;">Reason for return</label>
            <textarea id="return-reason-{{ $item->id }}" name="reason" rows="2" minlength="10" maxlength="1000" required style="width: 100%; box-sizing: border-box; padding: 0.55rem; border: 1px solid var(--eq-line); border-radius: 6px;"></textarea>
            <button type="submit" class="eq-btn eq-btn--outline" style="margin-top: 0.55rem;">Request Return</button>
          </form>
        @elseif($remaining > 0 && $order->status === 'delivered')
          <p style="font-size: 0.78rem; color: var(--eq-charcoal-soft); margin-top: 0.4rem;">Online return unavailable for this item or window. Contact Rthquick support for review.</p>
        @endif
      </div>
    @endforeach
  </section>
@endif

@php
  $pendingCancellation = $order->cancellationRequests->firstWhere('status', 'pending');
  $canRequestCancellation = in_array($order->status, ['pending', 'confirmed'], true)
      && $order->payment_method === 'cod'
      && $order->payment_status === 'due_on_delivery';
@endphp

<section style="border: 1px solid var(--eq-line); border-radius: 8px; padding: 1rem; margin: 1.25rem 0; background: var(--eq-white);">
  <h2 style="font-size: 1rem; margin: 0 0 0.5rem;">Cancellation request</h2>
  @if(session('success'))
    <p role="status" style="color: #176a3a; font-size: 0.85rem;">{{ session('success') }}</p>
  @endif
  @if($errors->any())
    <p role="alert" style="color: #a32727; font-size: 0.85rem;">{{ $errors->first() }}</p>
  @endif
  @if($order->status === 'cancelled')
    <p style="font-size: 0.85rem;">This order was cancelled by Earthquick.</p>
  @elseif($pendingCancellation)
    <p style="font-size: 0.85rem;">Your request is under review. The order remains active until Earthquick approves it.</p>
  @elseif($canRequestCancellation)
    <p style="font-size: 0.82rem; color: var(--eq-charcoal-soft);">You may request cancellation before processing begins. Submitting this form does not cancel the order immediately.</p>
    <form method="POST" action="{{ route('orders.cancellation-request', $order->order_number) }}">
      @csrf
      <label for="cancellation-reason-{{ $order->id }}" style="display: block; font-size: 0.82rem; margin-bottom: 0.35rem;">Reason for cancellation</label>
      <textarea id="cancellation-reason-{{ $order->id }}" name="reason" required minlength="10" maxlength="1000" rows="3" style="width: 100%; padding: 0.65rem; border: 1px solid var(--eq-line); border-radius: 6px; box-sizing: border-box;">{{ old('reason') }}</textarea>
      <button type="submit" class="eq-btn eq-btn--outline" style="margin-top: 0.7rem;">Request Cancellation</button>
    </form>
  @else
    <p style="font-size: 0.85rem;">Online cancellation is unavailable at this stage. Please contact Earthquick support.</p>
  @endif
  @if($order->cancellationRequests->first()?->status === 'rejected')
    <p style="font-size: 0.8rem; color: var(--eq-charcoal-soft); margin-top: 0.6rem;">A previous request was declined. The order remains active.</p>
  @endif
</section>

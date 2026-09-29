@extends('admin.layout')

@section('title', 'Trashed Order #'.$order->order_number.' — Earthquick Admin')
@section('page_title', 'Trashed Order #'.$order->order_number)

@section('content')
  @if($errors->any())
    <div class="eq-admin-alert eq-admin-alert--error" role="alert">{{ $errors->first() }}</div>
  @endif
  <a class="eq-admin-btn eq-admin-btn--outline" href="{{ route('admin.orders', ['folder' => 'trash']) }}">&larr; Back to Trash</a>
  <section class="eq-admin-card" style="margin-top:1rem;max-width:720px;">
    <div class="eq-admin-card__header"><h2 class="eq-admin-card__title">Recovery &amp; deletion</h2></div>
    <p>Order <strong>{{ $order->order_number }}</strong> was moved to Trash on {{ $order->deleted_at->format('d M Y, h:i A') }}. Its items and history are still stored. No stock or payment amount changed when it entered Trash.</p>
    @if($order->archived_at)
      <p>It was archived before deletion; restoring from Trash returns it to Archive.</p>
    @else
      <p>Restoring from Trash returns it to active orders.</p>
    @endif
    <form method="POST" action="{{ route('admin.orders.restore', $order->id) }}" style="margin:1rem 0;">
      @csrf
      <button type="submit" class="eq-admin-btn eq-admin-btn--primary">Restore order</button>
    </form>
    @if($order->deleted_at->lte(now()->subDays(\App\Services\OrderRetentionService::TRASH_DAYS)))
      <div style="border-top:1px solid var(--eq-line);padding-top:1rem;">
        <p>At least 30 days have passed. Permanent deletion is optional and cannot be undone; associated order items and history will be removed. A minimal deletion audit remains.</p>
        <form method="POST" action="{{ route('admin.orders.purge', $order->id) }}" style="display:grid;gap:0.75rem;max-width:500px;" onsubmit="return confirm('Permanently delete this order and its history? This cannot be undone.');">
          @csrf @method('DELETE')
          <label for="purge-order-number">Type order number {{ $order->order_number }}</label>
          <input id="purge-order-number" name="confirm_order_number" type="text" required autocomplete="off" style="padding:0.6rem;border:1px solid var(--eq-line);border-radius:6px;" />
          <label><input type="checkbox" name="confirm_permanent" value="1" required /> I understand permanent deletion cannot be undone.</label>
          <button type="submit" class="eq-admin-btn eq-admin-btn--danger" style="justify-self:start;">Permanently delete</button>
        </form>
      </div>
    @else
      <p>Permanent deletion is locked until {{ $order->deleted_at->copy()->addDays(\App\Services\OrderRetentionService::TRASH_DAYS)->format('d M Y, h:i A') }}. There is no automatic purge.</p>
    @endif
  </section>
@endsection

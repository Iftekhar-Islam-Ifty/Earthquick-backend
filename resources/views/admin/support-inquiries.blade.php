@extends('admin.layout')

@section('title', 'Support Inbox — Earthquick Admin')
@section('page_title', 'Support Inbox')

@section('content')
  @if(session('success'))
    <div class="eq-admin-alert" role="status">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="eq-admin-alert eq-admin-alert--error" role="alert">{{ $errors->first() }}</div>
  @endif

  <section class="eq-admin-card">
    <div class="eq-admin-card__header"><h2 class="eq-admin-card__title">Customer inquiries</h2></div>
    <p>Messages are saved here even while email notifications are disabled. Changing status does not send a reply.</p>
    <nav aria-label="Filter inquiries" style="display:flex;flex-wrap:wrap;gap:0.75rem;margin:1rem 0;">
      <a href="{{ route('admin.support.index') }}">All</a>
      <a href="{{ route('admin.support.index', ['status' => 'open']) }}">Open</a>
      <a href="{{ route('admin.support.index', ['status' => 'in_progress']) }}">In progress</a>
      <a href="{{ route('admin.support.index', ['status' => 'closed']) }}">Closed</a>
    </nav>

    @forelse($inquiries as $inquiry)
      <article style="border-top:1px solid var(--eq-line);padding:1rem 0;overflow-wrap:anywhere;">
        <h3 style="margin:0 0 0.35rem;font-size:1rem;">#{{ $inquiry->id }} · {{ $inquiry->subject }}</h3>
        <p style="font-size:0.82rem;color:var(--eq-charcoal-soft);">{{ $inquiry->created_at->format('d M Y, h:i A') }} · {{ str_replace('_', ' ', ucfirst($inquiry->status)) }}</p>
        <p><strong>{{ $inquiry->name }}</strong> · {{ $inquiry->phone }} @if($inquiry->email) · {{ $inquiry->email }} @endif</p>
        <p style="display:flex;flex-wrap:wrap;gap:0.75rem;font-size:0.88rem;">
          <a href="tel:{{ preg_replace('/[^+0-9]/', '', $inquiry->phone) }}">Call customer</a>
          @if($inquiry->email)<a href="mailto:{{ $inquiry->email }}?subject={{ rawurlencode('Earthquick inquiry #'.$inquiry->id) }}">Email customer</a>@endif
        </p>
        <p style="white-space:pre-wrap;">{{ $inquiry->message }}</p>
        @if($inquiry->handled_at)
          <p style="font-size:0.82rem;">Last handled by {{ $inquiry->handler?->name ?? 'former admin' }} on {{ $inquiry->handled_at->format('d M Y, h:i A') }}. Note: {{ $inquiry->internal_note }}</p>
        @endif
        <form method="POST" action="{{ route('admin.support.update', $inquiry) }}" style="max-width:560px;">
          @csrf
          <label for="support-status-{{ $inquiry->id }}">Status</label>
          <select id="support-status-{{ $inquiry->id }}" name="status" required style="display:block;width:100%;padding:0.55rem;margin:0.35rem 0;border:1px solid var(--eq-line);border-radius:6px;">
            <option value="open" @selected($inquiry->status === 'open')>Open</option>
            <option value="in_progress" @selected($inquiry->status === 'in_progress')>In progress</option>
            <option value="closed" @selected($inquiry->status === 'closed')>Closed</option>
          </select>
          <label for="support-note-{{ $inquiry->id }}">Internal follow-up note (not sent to customer)</label>
          <textarea id="support-note-{{ $inquiry->id }}" name="internal_note" required minlength="5" maxlength="2000" rows="2" style="display:block;width:100%;box-sizing:border-box;padding:0.55rem;margin:0.35rem 0;border:1px solid var(--eq-line);border-radius:6px;">{{ $inquiry->internal_note }}</textarea>
          <button type="submit" class="eq-admin-btn eq-admin-btn--gold">Save Follow-up</button>
        </form>
      </article>
    @empty
      <p>No inquiries found.</p>
    @endforelse
    {{ $inquiries->links() }}
  </section>
@endsection

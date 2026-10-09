@extends('admin.layout')

@section('title', 'Support Inbox - Rthquick Admin')
@section('page_title', 'Support Inbox')

@push('styles')
  <style>
    .eq-support { min-width: 0; }
    .eq-support-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; }
    .eq-support-summary { margin: .35rem 0 0; color: var(--eq-charcoal-soft); font-size: .88rem; }
    .eq-support-count { display: inline-flex; align-items: center; padding: .35rem .7rem; border-radius: 999px; background: var(--eq-cream); color: var(--eq-navy); font-size: .78rem; font-weight: 700; white-space: nowrap; }
    .eq-support-help { margin: 1rem 0; padding: .75rem 1rem; border-left: 3px solid var(--eq-teal); background: #f4f8f7; color: var(--eq-charcoal-soft); font-size: .84rem; line-height: 1.55; }
    .eq-support-filters { display: flex; flex-wrap: wrap; gap: .5rem; margin: 1rem 0 1.25rem; }
    .eq-support-filter { display: inline-flex; align-items: center; min-height: 2.35rem; padding: .45rem .85rem; border: 1px solid var(--eq-line); border-radius: 999px; color: var(--eq-navy); font-size: .84rem; font-weight: 600; text-decoration: none; transition: background .15s, border-color .15s; }
    .eq-support-filter:hover { background: var(--eq-cream); border-color: var(--eq-gold); }
    .eq-support-filter.is-active { background: var(--eq-navy); border-color: var(--eq-navy); color: #fff; }
    .eq-support-list { display: grid; gap: 1rem; }
    .eq-support-entry { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(270px, 1fr); min-width: 0; border: 1px solid var(--eq-line); border-radius: 12px; overflow: hidden; background: #fff; }
    .eq-support-entry__main, .eq-support-followup { min-width: 0; padding: 1.25rem; }
    .eq-support-entry__heading { display: flex; justify-content: space-between; align-items: flex-start; gap: .75rem; flex-wrap: wrap; }
    .eq-support-entry__title { min-width: 0; margin: 0; color: var(--eq-navy); font: 600 1.08rem/1.35 var(--font-display); overflow-wrap: anywhere; }
    .eq-support-entry__number { display: block; margin-bottom: .2rem; color: var(--eq-charcoal-soft); font: 700 .72rem/1.2 var(--font-body); letter-spacing: .06em; text-transform: uppercase; }
    .eq-support-status { display: inline-flex; align-items: center; padding: .3rem .65rem; border-radius: 999px; font-size: .73rem; font-weight: 700; white-space: nowrap; }
    .eq-support-status--open { background: #fff2d8; color: #76520d; }
    .eq-support-status--in_progress { background: #e4f2f2; color: #16565b; }
    .eq-support-status--closed { background: #edf0f2; color: #42505a; }
    .eq-support-entry__date { margin: .55rem 0 1rem; color: var(--eq-charcoal-soft); font-size: .8rem; }
    .eq-support-contact { display: grid; gap: .15rem; min-width: 0; margin-bottom: .85rem; font-size: .86rem; overflow-wrap: anywhere; }
    .eq-support-contact strong { color: var(--eq-navy); }
    .eq-support-actions { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1.1rem; }
    .eq-support-action { display: inline-flex; align-items: center; justify-content: center; min-height: 2.15rem; padding: .4rem .75rem; border: 1px solid var(--eq-line); border-radius: 6px; color: var(--eq-navy); font-size: .8rem; font-weight: 700; text-decoration: none; }
    .eq-support-action:hover { border-color: var(--eq-gold); background: var(--eq-cream); }
    .eq-support-entry__message { margin: 0; padding: .9rem 1rem; border-radius: 8px; background: #f7f8f7; color: var(--eq-navy); font-size: .88rem; line-height: 1.6; white-space: pre-wrap; overflow-wrap: anywhere; }
    .eq-support-history { margin: 1rem 0 0; padding-top: .8rem; border-top: 1px solid var(--eq-line); color: var(--eq-charcoal-soft); font-size: .8rem; line-height: 1.5; overflow-wrap: anywhere; }
    .eq-support-followup { display: flex; flex-direction: column; gap: .85rem; border-left: 1px solid var(--eq-line); background: #fbfaf7; }
    .eq-support-followup__title { margin: 0; color: var(--eq-navy); font: 600 1rem/1.3 var(--font-display); }
    .eq-support-followup__hint { margin: .25rem 0 0; color: var(--eq-charcoal-soft); font-size: .77rem; line-height: 1.45; }
    .eq-support-field { display: grid; gap: .35rem; min-width: 0; }
    .eq-support-field__label { color: var(--eq-navy); font-size: .8rem; font-weight: 700; }
    .eq-support-input { display: block; width: 100%; min-width: 0; box-sizing: border-box; padding: .65rem .75rem; border: 1px solid var(--eq-line); border-radius: 7px; background: #fff; color: var(--eq-navy); font: inherit; font-size: .85rem; }
    .eq-support-input:focus { outline: 2px solid var(--eq-gold); outline-offset: 1px; }
    .eq-support-note { min-height: 5.5rem; resize: vertical; }
    .eq-support-submit { align-self: flex-start; margin-top: .1rem; }
    .eq-support-empty { margin: 0; padding: 2rem 1rem; border: 1px dashed var(--eq-line); border-radius: 8px; color: var(--eq-charcoal-soft); text-align: center; }
    .eq-support-pagination { margin-top: 1.25rem; }
    @media (max-width: 900px) {
      .eq-support-entry { grid-template-columns: minmax(0, 1fr); }
      .eq-support-followup { border-left: 0; border-top: 1px solid var(--eq-line); }
    }
    @media (max-width: 480px) {
      .eq-support-entry__main, .eq-support-followup { padding: .95rem; }
      .eq-support-actions { margin-bottom: .85rem; }
      .eq-support-help { padding: .7rem .8rem; }
      .eq-support-filter { min-height: 2.15rem; padding: .35rem .65rem; }
    }
  </style>
@endpush

@section('content')
  @if($errors->any())
    <div class="eq-admin-alert eq-admin-alert--error" role="alert">{{ $errors->first() }}</div>
  @endif

  <section class="eq-admin-card eq-support">
    <div class="eq-support-head">
      <div>
        <h2 class="eq-admin-card__title">Customer inquiries</h2>
        <p class="eq-support-summary">Review messages and record each follow-up in one place.</p>
      </div>
      <span class="eq-support-count">{{ $inquiries->total() }} {{ $inquiries->total() === 1 ? 'inquiry' : 'inquiries' }}</span>
    </div>
    <p class="eq-support-help">Messages remain saved here even if email delivery fails. Status and internal notes are for the admin team; saving a follow-up does not send a reply to the customer.</p>

    <nav class="eq-support-filters" aria-label="Filter inquiries">
      <a class="eq-support-filter {{ !$status ? 'is-active' : '' }}" href="{{ route('admin.support.index') }}" @if(!$status) aria-current="page" @endif>All</a>
      <a class="eq-support-filter {{ $status === 'open' ? 'is-active' : '' }}" href="{{ route('admin.support.index', ['status' => 'open']) }}" @if($status === 'open') aria-current="page" @endif>Open</a>
      <a class="eq-support-filter {{ $status === 'in_progress' ? 'is-active' : '' }}" href="{{ route('admin.support.index', ['status' => 'in_progress']) }}" @if($status === 'in_progress') aria-current="page" @endif>In progress</a>
      <a class="eq-support-filter {{ $status === 'closed' ? 'is-active' : '' }}" href="{{ route('admin.support.index', ['status' => 'closed']) }}" @if($status === 'closed') aria-current="page" @endif>Closed</a>
    </nav>

    <div class="eq-support-list">
      @forelse($inquiries as $inquiry)
        <article class="eq-support-entry">
          <div class="eq-support-entry__main">
            <div class="eq-support-entry__heading">
              <h3 class="eq-support-entry__title"><span class="eq-support-entry__number">Inquiry #{{ $inquiry->id }}</span>{{ $inquiry->subject }}</h3>
              <span class="eq-support-status eq-support-status--{{ $inquiry->status }}">{{ str_replace('_', ' ', ucfirst($inquiry->status)) }}</span>
            </div>
            <p class="eq-support-entry__date">Received {{ $inquiry->created_at->copy()->timezone('Asia/Dhaka')->format('d M Y, h:i A') }} BDT</p>
            <div class="eq-support-contact">
              <strong>{{ $inquiry->name }}</strong>
              <span>{{ $inquiry->phone }}</span>
              @if($inquiry->email)<span>{{ $inquiry->email }}</span>@endif
            </div>
            <div class="eq-support-actions">
              <a class="eq-support-action" href="tel:{{ preg_replace('/[^+0-9]/', '', $inquiry->phone) }}">Call customer</a>
              @if($inquiry->email)<a class="eq-support-action" href="mailto:{{ $inquiry->email }}?subject={{ rawurlencode('Rthquick inquiry #'.$inquiry->id) }}">Email customer</a>@endif
            </div>
            <p class="eq-support-entry__message">{{ $inquiry->message }}</p>
            @if($inquiry->handled_at)
              <p class="eq-support-history">Last handled by {{ $inquiry->handler?->name ?? 'former admin' }} on {{ $inquiry->handled_at->copy()->timezone('Asia/Dhaka')->format('d M Y, h:i A') }} BDT. Note: {{ $inquiry->internal_note }}</p>
            @endif
          </div>
          <form class="eq-support-followup" method="POST" action="{{ route('admin.support.update', $inquiry) }}">
            @csrf
            <div>
              <h4 class="eq-support-followup__title">Follow-up</h4>
              <p class="eq-support-followup__hint">For internal tracking only. Contact the customer separately.</p>
            </div>
            <div class="eq-support-field">
              <label class="eq-support-field__label" for="support-status-{{ $inquiry->id }}">Status</label>
              <select class="eq-support-input" id="support-status-{{ $inquiry->id }}" name="status" required>
                <option value="open" @selected($inquiry->status === 'open')>Open</option>
                <option value="in_progress" @selected($inquiry->status === 'in_progress')>In progress</option>
                <option value="closed" @selected($inquiry->status === 'closed')>Closed</option>
              </select>
            </div>
            <div class="eq-support-field">
              <label class="eq-support-field__label" for="support-note-{{ $inquiry->id }}">Internal follow-up note</label>
              <textarea class="eq-support-input eq-support-note" id="support-note-{{ $inquiry->id }}" name="internal_note" required minlength="5" maxlength="2000" rows="3">{{ $inquiry->internal_note }}</textarea>
            </div>
            <button type="submit" class="eq-admin-btn eq-admin-btn--gold eq-support-submit">Save Follow-up</button>
          </form>
        </article>
      @empty
        <p class="eq-support-empty">No inquiries found for this filter.</p>
      @endforelse
    </div>
    <div class="eq-support-pagination">{{ $inquiries->links() }}</div>
  </section>
@endsection

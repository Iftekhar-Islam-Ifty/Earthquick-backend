@extends('admin.layout')

@section('title', 'Email Deliveries — Earthquick Admin')
@section('page_title', 'Email Deliveries')

@push('styles')
  <style>
    .eq-mail-summary { color: var(--eq-charcoal-soft); font-size: .85rem; line-height: 1.55; }
    .eq-mail-filters { display: flex; flex-wrap: wrap; gap: .5rem; margin: 1rem 0; }
    .eq-mail-filters a { text-decoration: none; }
    .eq-mail-list { display: grid; gap: .75rem; }
    .eq-mail-entry { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: .85rem; min-width: 0; padding: 1rem; border: 1px solid var(--eq-line); border-radius: 9px; background: #fff; }
    .eq-mail-entry__details { min-width: 0; overflow-wrap: anywhere; }
    .eq-mail-entry__details p { margin: .25rem 0; font-size: .82rem; color: var(--eq-charcoal-soft); }
    .eq-mail-entry__title { margin: 0; color: var(--eq-navy); font-size: .95rem; }
    .eq-mail-status { display: inline-block; padding: .2rem .55rem; border-radius: 999px; font-size: .72rem; font-weight: 700; text-transform: uppercase; }
    .eq-mail-status--sent { background: #e4f4ed; color: #14643d; }
    .eq-mail-status--failed { background: #fee8e5; color: #9a312b; }
    .eq-mail-status--pending, .eq-mail-status--sending { background: #fff2d8; color: #76520d; }
    .eq-mail-retry { display: grid; gap: .4rem; max-width: 240px; font-size: .78rem; }
    @media (max-width: 480px) { .eq-mail-entry { padding: .8rem; } .eq-mail-retry { max-width: 100%; } }
  </style>
@endpush

@section('content')
  @if($errors->any())
    <div class="eq-admin-alert eq-admin-alert--error" role="alert">{{ $errors->first() }}</div>
  @endif
  <section class="eq-admin-card">
    <h2 class="eq-admin-card__title">Transactional email outbox</h2>
    <p class="eq-mail-summary">A sent status means SMTP accepted the message, not that it reached the inbox. Pending messages retry automatically only while the Laravel scheduler is running. After five unsuccessful attempts, staff must review the failed entry. Recipient and message content are encrypted while pending and erased after SMTP handoff.</p>
    <nav class="eq-mail-filters" aria-label="Filter email deliveries">
      <a class="eq-admin-btn {{ $status === null ? 'eq-admin-btn--primary' : 'eq-admin-btn--outline' }}" href="{{ route('admin.email-deliveries') }}">All</a>
      @foreach(['pending', 'sending', 'failed', 'sent'] as $filter)
        <a class="eq-admin-btn {{ $status === $filter ? 'eq-admin-btn--primary' : 'eq-admin-btn--outline' }}" href="{{ route('admin.email-deliveries', ['status' => $filter]) }}">{{ ucfirst($filter) }} ({{ $counts[$filter] }})</a>
      @endforeach
    </nav>
    <div class="eq-mail-list">
      @forelse($messages as $message)
        <article class="eq-mail-entry">
          <div class="eq-mail-entry__details">
            <h3 class="eq-mail-entry__title">Message #{{ $message->id }} <span class="eq-mail-status eq-mail-status--{{ $message->status }}">{{ $message->status }}</span></h3>
            <p>{{ $message->context['event'] ?? 'notice' }} · {{ $message->recipient_hint }} · {{ $message->created_at->copy()->timezone('Asia/Dhaka')->format('d M Y, h:i A') }} BDT</p>
            <p>Attempts: {{ $message->attempts }}/{{ \App\Services\OutboundMailService::MAX_ATTEMPTS }}
              @if($message->next_attempt_at) · Next retry: {{ $message->next_attempt_at->copy()->timezone('Asia/Dhaka')->format('d M Y, h:i A') }} BDT @endif
              @if($message->sent_at) · SMTP handoff: {{ $message->sent_at->copy()->timezone('Asia/Dhaka')->format('d M Y, h:i A') }} BDT @endif
            </p>
            @if($message->last_error_class)<p>Last error type: {{ $message->last_error_class }}</p>@endif
          </div>
          @if($message->status === 'failed')
            <form class="eq-mail-retry" method="POST" action="{{ route('admin.email-deliveries.retry', $message->id) }}" onsubmit="return confirm('Retry this email to the original recipient now?');">
              @csrf
              <label><input type="checkbox" name="confirm_retry" value="1" required> I checked the transport and want to retry.</label>
              <button type="submit" class="eq-admin-btn eq-admin-btn--outline">Retry now</button>
            </form>
          @endif
        </article>
      @empty
        <p class="eq-mail-summary">No email records in this filter.</p>
      @endforelse
    </div>
    @include('partials.pagination-polished', ['paginator' => $messages])
  </section>
@endsection

@extends('admin.layout')

@section('title', 'Newsletter Campaigns — Rthquick Admin')
@section('page_title', 'Newsletter Campaigns')

@push('styles')
<style>
  .eq-campaign-form { display: grid; gap: .8rem; max-width: 48rem; }
  .eq-campaign-form label { display: grid; gap: .3rem; font-weight: 600; }
  .eq-campaign-form input, .eq-campaign-form textarea { width: 100%; padding: .7rem; border: 1px solid var(--eq-line); border-radius: 7px; font: inherit; }
  .eq-campaign-list { display: grid; gap: .7rem; margin-top: 1rem; }
  .eq-campaign-list a { display: block; padding: .8rem 1rem; border: 1px solid var(--eq-line); border-radius: 8px; color: var(--eq-navy); text-decoration: none; overflow-wrap: anywhere; }
  .eq-campaign-list small { display: block; margin-top: .3rem; color: var(--eq-charcoal-soft); }
</style>
@endpush

@section('content')
  @if($errors->any())<div class="eq-admin-alert eq-admin-alert--error" role="alert">{{ $errors->first() }}</div>@endif
  <section class="eq-admin-card">
    <h2 class="eq-admin-card__title">Create a Nous Telos newsletter</h2>
    <p>{{ $activeCount }} confirmed, active {{ $activeCount === 1 ? 'subscriber' : 'subscribers' }} right now. Only these addresses are eligible when you launch a campaign.</p>
    <p>Write a plain-text message. Rthquick adds an unsubscribe link to every email. Saving a draft does not send mail.</p>
    <form method="POST" action="{{ route('admin.newsletter-campaigns.store') }}" class="eq-campaign-form">
      @csrf
      <label>Subject <input name="subject" required maxlength="180" value="{{ old('subject') }}" placeholder="For example: New arrivals at Nous Telos"></label>
      <label>Message <textarea name="body" required minlength="10" maxlength="8000" rows="9" placeholder="Write the update subscribers will receive.">{{ old('body') }}</textarea></label>
      <button class="eq-admin-btn eq-admin-btn--primary" type="submit">Save draft</button>
    </form>
  </section>
  <section class="eq-admin-card">
    <h2 class="eq-admin-card__title">Campaigns</h2>
    <div class="eq-campaign-list">
      @forelse($campaigns as $campaign)
        <a href="{{ route('admin.newsletter-campaigns.show', $campaign) }}">
          <strong>{{ $campaign->subject }}</strong>
          <small>{{ ucfirst($campaign->status) }} · {{ $campaign->recipients_count }} recipients · {{ $campaign->created_at->timezone('Asia/Dhaka')->format('d M Y, h:i A') }} BDT</small>
        </a>
      @empty
        <p>No campaigns yet.</p>
      @endforelse
    </div>
    @include('partials.pagination-polished', ['paginator' => $campaigns])
  </section>
@endsection

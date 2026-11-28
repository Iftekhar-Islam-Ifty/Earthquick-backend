@extends('admin.layout')

@section('title', 'Newsletter Campaign — Rthquick Admin')
@section('page_title', 'Newsletter Campaign')

@push('styles')
<style>
  .eq-campaign-edit { display: grid; gap: .8rem; max-width: 48rem; }
  .eq-campaign-edit label { display: grid; gap: .3rem; font-weight: 600; }
  .eq-campaign-edit input, .eq-campaign-edit textarea { width: 100%; padding: .7rem; border: 1px solid var(--eq-line); border-radius: 7px; font: inherit; }
  .eq-campaign-preview { white-space: pre-wrap; overflow-wrap: anywhere; padding: 1rem; background: #f8f4eb; border-radius: 8px; }
  .eq-campaign-stats { display: flex; flex-wrap: wrap; gap: .5rem 1rem; }
</style>
@endpush

@section('content')
  <p><a href="{{ route('admin.newsletter-campaigns.index') }}">← All campaigns</a></p>
  @if($errors->any())<div class="eq-admin-alert eq-admin-alert--error" role="alert">{{ $errors->first() }}</div>@endif
  <section class="eq-admin-card">
    <h2 class="eq-admin-card__title">{{ $campaign->subject }}</h2>
    <p>Status: <strong>{{ ucfirst($campaign->status) }}</strong>. Sender: {{ config('mail.from.address') }}.</p>
    @if($campaign->status === 'draft')
      <form method="POST" action="{{ route('admin.newsletter-campaigns.update', $campaign) }}" class="eq-campaign-edit">
        @csrf @method('PUT')
        <label>Subject <input name="subject" required maxlength="180" value="{{ old('subject', $campaign->subject) }}"></label>
        <label>Message <textarea name="body" required minlength="10" maxlength="8000" rows="10">{{ old('body', $campaign->body) }}</textarea></label>
        <button class="eq-admin-btn eq-admin-btn--outline" type="submit">Save changes</button>
      </form>
      <p>Review the saved version below before launching. Launching sends to confirmed active subscribers and cannot be undone.</p>
      <form method="POST" action="{{ route('admin.newsletter-campaigns.launch', $campaign) }}">
        @csrf
        <label><input type="checkbox" name="confirm_send" value="1" required> I reviewed this message and want to send it to confirmed Nous Telos subscribers.</label><br>
        <button class="eq-admin-btn eq-admin-btn--primary" type="submit">Launch campaign</button>
      </form>
    @endif
    @if($campaign->status === 'sending')
      <form method="POST" action="{{ route('admin.newsletter-campaigns.cancel', $campaign) }}">
        @csrf
        <button class="eq-admin-btn eq-admin-btn--outline" type="submit">Stop remaining emails</button>
      </form>
    @endif
  </section>
  <section class="eq-admin-card">
    <h2 class="eq-admin-card__title">Saved email preview</h2>
    <p><strong>Subject:</strong> {{ $campaign->subject }}</p>
    <div class="eq-campaign-preview">{{ $campaign->body }}

Unsubscribe from Nous Telos updates: [personal link added to each email]</div>
  </section>
  @if($campaign->status !== 'draft')
    <section class="eq-admin-card">
      <h2 class="eq-admin-card__title">Delivery progress</h2>
      <p class="eq-campaign-stats">
        <span>Targeted: {{ $counts['targeted'] }}</span>
        <span>Waiting: {{ $counts['waiting'] }}</span>
        <span>SMTP accepted: {{ $counts['sent'] }}</span>
        <span>Pending/retrying: {{ $counts['pending'] + $counts['sending'] }}</span>
        <span>Failed: {{ $counts['failed'] }}</span>
        <span>Skipped: {{ $counts['skipped'] }}</span>
      </p>
      <p>SMTP accepted does not guarantee inbox delivery. Failed messages can be reviewed in <a href="{{ route('admin.email-deliveries', ['status' => 'failed']) }}">Email Deliveries</a>.</p>
    </section>
  @endif
@endsection

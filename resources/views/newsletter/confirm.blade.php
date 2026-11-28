@extends('layouts.app')

@section('title', 'Confirm Nous Telos updates | Rthquick')

@section('content')
<main class="eq-newsletter-action">
    <div class="eq-newsletter-action__card">
        <h1>Confirm your email</h1>
        @if($subscription->confirmed_at && ! $subscription->unsubscribed_at)
            <p>You are already subscribed to Nous Telos updates.</p>
        @elseif($subscription->unsubscribed_at || $subscription->confirmation_expires_at?->isPast())
            <p>This link has expired. Please subscribe again on the Nous Telos page.</p>
        @else
            <p>Confirm that you want to receive Nous Telos product updates at {{ $subscription->email }}.</p>
            <form method="POST" action="{{ route('newsletter.confirm.submit', ['subscription' => $subscription->id, 'token' => $token]) }}">
                @csrf
                <button type="submit">Confirm subscription</button>
            </form>
        @endif
        <a href="{{ route('stores.show', 'nous-telos') }}#newsletter-section">Back to Nous Telos</a>
    </div>
</main>
@endsection

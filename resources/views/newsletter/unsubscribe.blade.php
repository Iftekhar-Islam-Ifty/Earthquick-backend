@extends('layouts.app')

@section('title', 'Unsubscribe from Nous Telos updates | Rthquick')

@section('content')
<main class="eq-newsletter-action">
    <div class="eq-newsletter-action__card">
        <h1>Nous Telos updates</h1>
        @if($subscription->unsubscribed_at)
            <p>This address is already unsubscribed.</p>
        @else
            <p>Stop sending Nous Telos product updates to {{ $subscription->email }}?</p>
            <form method="POST" action="{{ request()->fullUrl() }}">
                @csrf
                <button type="submit">Unsubscribe</button>
            </form>
        @endif
        <a href="{{ route('stores.show', 'nous-telos') }}">Back to Nous Telos</a>
    </div>
</main>
@endsection

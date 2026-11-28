@extends('admin.layout')

@section('title', 'Newsletter Subscribers — Rthquick Admin')
@section('page_title', 'Newsletter Subscribers')

@section('content')
<section class="eq-admin-card">
    <h2 class="eq-admin-card__title">Nous Telos subscribers</h2>
    <p>Only confirmed, active addresses should receive future Nous Telos updates. This page records signups; marketing campaign sending is not enabled yet.</p>
    <p>
        <a href="{{ route('admin.newsletter.index') }}">All</a> ·
        <a href="{{ route('admin.newsletter.index', ['status' => 'confirmed']) }}">Confirmed</a> ·
        <a href="{{ route('admin.newsletter.index', ['status' => 'pending']) }}">Pending</a> ·
        <a href="{{ route('admin.newsletter.index', ['status' => 'unsubscribed']) }}">Unsubscribed</a>
    </p>
    @forelse($subscriptions as $subscription)
        <div style="padding:.85rem 0;border-top:1px solid #e7e2d9;overflow-wrap:anywhere">
            <strong>{{ $subscription->email }}</strong><br>
            <small>
                {{ $subscription->unsubscribed_at ? 'Unsubscribed' : ($subscription->confirmed_at ? 'Confirmed' : 'Pending confirmation') }}
                · Added {{ $subscription->created_at->format('d M Y') }}
            </small>
        </div>
    @empty
        <p>No subscribers in this view yet.</p>
    @endforelse
    {{ $subscriptions->links() }}
</section>
@endsection

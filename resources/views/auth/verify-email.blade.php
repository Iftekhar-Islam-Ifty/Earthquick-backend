@extends('layouts.app')
@section('title', 'Verify email — Rthquick')
@section('content')
<main class="eq-container" style="max-width:640px;padding:3rem 0 5rem"><section class="eq-auth-card">
  <h1>Verify your email</h1>
  @if(session('success'))<p>{{ session('success') }}</p>@endif
  @if(session('status'))<p>{{ session('status') }}</p>@endif
  <p>Use the secure link sent to <strong>{{ auth()->user()->email }}</strong>. The link expires in 60 minutes.</p>
  <form method="post" action="{{ route('verification.send') }}">@csrf<button class="eq-auth-submit-btn" type="submit">Send a new verification link</button></form>
</section></main>
@endsection

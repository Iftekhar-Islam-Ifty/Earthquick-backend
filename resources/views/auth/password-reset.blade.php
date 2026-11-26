@extends('layouts.app')
@section('title', 'Choose a new password — Rthquick')
@section('content')
<main class="eq-container" style="max-width:560px;padding:3rem 0 5rem"><section class="eq-auth-card">
  <h1>Choose a new password</h1>
  @if($errors->any())<p role="alert">{{ $errors->first() }}</p>@endif
  <form method="post" action="{{ route('password.update') }}">@csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" required>
    <label for="password">New password (at least 12 characters)</label><input id="password" type="password" name="password" autocomplete="new-password" minlength="12" required>
    <label for="password_confirmation">Confirm new password</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="12" required>
    <button class="eq-auth-submit-btn" type="submit">Save new password</button>
  </form>
</section></main>
@endsection

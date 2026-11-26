@extends('layouts.app')
@section('title', 'Reset password — Rthquick')
@section('content')
<main class="eq-container" style="max-width:560px;padding:3rem 0 5rem"><section class="eq-auth-card">
  <h1>Reset your password</h1><p>Enter the verified email address on your account.</p>
  @if(session('status'))<p role="status">{{ session('status') }}</p>@endif
  @if($errors->any())<p role="alert">{{ $errors->first() }}</p>@endif
  <form method="post" action="{{ route('password.email') }}">@csrf
    <label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
    <button class="eq-auth-submit-btn" type="submit">Send reset link</button>
  </form>
</section></main>
@endsection

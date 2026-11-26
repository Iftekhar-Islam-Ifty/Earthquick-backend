@extends('layouts.app')
@section('title', 'Confirm admin password — Rthquick')
@section('content')
<main class="eq-container" style="max-width:560px;padding:3rem 0 5rem"><section class="eq-auth-card">
  <h1>Confirm your password</h1><p>For this sensitive admin action, re-enter your password. Confirmation lasts 10 minutes.</p>
  @if($errors->any())<p role="alert">{{ $errors->first() }}</p>@endif
  <form method="post" action="{{ route('admin.security.confirm-password.submit') }}">@csrf
    <label for="password">Password</label><input id="password" type="password" name="password" autocomplete="current-password" required autofocus>
    <button class="eq-auth-submit-btn" type="submit">Confirm</button>
  </form>
</section></main>
@endsection

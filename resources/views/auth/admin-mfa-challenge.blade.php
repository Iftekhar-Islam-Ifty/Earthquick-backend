@extends('layouts.app')
@section('title', 'Admin verification — Rthquick')
@section('content')
<main class="eq-container" style="max-width:560px;padding:3rem 0 5rem"><section class="eq-auth-card">
  <h1>Verify it’s you</h1><p>Enter a current authenticator code or one unused recovery code.</p>
  @if($errors->any())<p role="alert">{{ $errors->first() }}</p>@endif
  <form method="post" action="{{ route('admin.security.challenge.verify') }}">@csrf
    <label for="code">Authentication code</label><input id="code" name="code" autocomplete="one-time-code" required autofocus>
    <button class="eq-auth-submit-btn" type="submit">Continue</button>
  </form>
</section></main>
@endsection

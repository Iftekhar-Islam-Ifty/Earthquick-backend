@extends('layouts.app')
@section('title', 'Admin security setup — Rthquick')
@section('content')
<main class="eq-container" style="max-width:680px;padding:3rem 0 5rem">
  <section class="eq-auth-card">
    <h1>Protect the admin account</h1>
    <p>Add this key to an authenticator app, then enter its current six-digit code to finish setup.</p>
    <p><strong>Setup key</strong></p>
    <code style="display:block;overflow-wrap:anywhere;padding:1rem;background:#f3eee4">{{ $secret }}</code>
    <p><a href="{{ $provisioningUri }}">Open authenticator setup link</a></p>
    @if($errors->any())<p role="alert">{{ $errors->first() }}</p>@endif
    <form method="post" action="{{ route('admin.security.setup.confirm') }}">@csrf
      <label for="code">Six-digit code</label><input id="code" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" required>
      <button type="submit" class="eq-auth-submit-btn">Confirm and enable MFA</button>
    </form>
  </section>
</main>
@endsection

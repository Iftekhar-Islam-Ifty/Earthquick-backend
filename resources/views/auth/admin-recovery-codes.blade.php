@extends('layouts.app')
@section('title', 'Save admin recovery codes — Rthquick')
@section('content')
<main class="eq-container" style="max-width:680px;padding:3rem 0 5rem"><section class="eq-auth-card">
  <h1>Save your recovery codes</h1><p>Each code works once. Store these privately; they will not be shown again.</p>
  <ul>@foreach($codes as $code)<li><code>{{ $code }}</code></li>@endforeach</ul>
  <a class="eq-auth-submit-btn" href="{{ route('admin.dashboard') }}">Continue to admin</a>
</section></main>
@endsection

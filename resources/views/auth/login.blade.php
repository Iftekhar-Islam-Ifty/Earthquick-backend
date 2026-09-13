@extends('layouts.app')

@section('title', 'Customer Login — Earthquick by Nous Telos')
@section('body_class', 'eq-auth-page')

@section('content')
<!-- Breadcrumb Bar -->
<nav class="eq-breadcrumb" aria-label="Breadcrumb" style="padding: 0.9rem 0; border-bottom: 1px solid var(--eq-line); background: var(--eq-white);">
  <div class="eq-container">
    <ol class="eq-breadcrumb__list" style="display: flex; align-items: center; gap: 0.5rem; list-style: none; margin: 0; padding: 0; font-size: 0.84rem; color: var(--eq-charcoal-soft);">
      <li><a href="{{ route('home') }}" style="color: inherit; text-decoration: none;">Home</a></li>
      <li style="color: var(--eq-line-dark);">&rsaquo;</li>
      <li style="color: var(--eq-charcoal); font-weight: 500;" aria-current="page">Login</li>
    </ol>
  </div>
</nav>

<!-- Login Form Container -->
<main class="eq-container" id="main-content" style="padding: 3rem 0 5rem;">
  <div class="eq-auth-wrap">
    <div class="eq-auth-card">
      
      <div class="eq-auth-card__header">
        <h1 class="eq-auth-card__title">Welcome Back</h1>
        <p class="eq-auth-card__desc">Sign in to your Nous Telos / Earthquick account</p>
      </div>

      @if(session('success'))
        <div style="background: rgba(39, 174, 96, 0.12); color: #27ae60; padding: 0.85rem 1rem; border-radius: 4px; font-size: 0.86rem; margin-bottom: 1.25rem;">
          {{ session('success') }}
        </div>
      @endif

      @if($errors->any())
        <div style="background: #fdf2f2; color: #9b1c1c; padding: 0.85rem 1rem; border-radius: 4px; font-size: 0.86rem; margin-bottom: 1.25rem; border: 1px solid #f8b4b4;">
          <ul style="margin: 0; padding-left: 1.2rem;">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form class="eq-auth-form" id="customer-login-form" method="POST" action="{{ route('login.submit') }}">
        @csrf
        
        <div class="eq-form-group">
          <label for="login-identifier" class="eq-form-label">Email or Mobile Number <span class="req">*</span></label>
          <input 
            type="text" 
            id="login-identifier" 
            name="identifier" 
            class="eq-form-input" 
            placeholder="e.g. 017XXXXXXXX or hello@earthquick.com" 
            value="{{ old('identifier') }}"
            required 
            autocomplete="username"
          />
        </div>

        <div class="eq-form-group">
          <div class="eq-auth-row-between" style="margin-bottom: 0.35rem;">
            <label for="login-password" class="eq-form-label" style="margin-bottom: 0;">Password <span class="req">*</span></label>
          </div>
          <input 
            type="password" 
            id="login-password" 
            name="password" 
            class="eq-form-input" 
            placeholder="Enter your account password" 
            required 
            autocomplete="current-password"
          />
        </div>

        <div class="eq-auth-row-between">
          <label style="display: flex; align-items: center; gap: 0.45rem; cursor: pointer; color: var(--eq-charcoal-soft); font-size: 0.86rem;">
            <input type="checkbox" name="remember" id="remember-me" checked style="accent-color: var(--eq-gold);" />
            <span>Remember me</span>
          </label>
        </div>

        <button type="submit" class="eq-auth-submit-btn" id="btn-login-submit">
          Sign In &rarr;
        </button>

      </form>

      <div class="eq-auth-footer-prompt">
        Don't have an account yet? 
        <a href="{{ route('register') }}" class="eq-auth-link" style="font-weight: 600;">Create Account</a>
      </div>

    </div>
  </div>
</main>
@endsection


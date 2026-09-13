@extends('layouts.app')

@section('title', 'Create Account — Earthquick by Nous Telos')
@section('body_class', 'eq-auth-page')

@section('content')
<!-- Breadcrumb Bar -->
<nav class="eq-breadcrumb" aria-label="Breadcrumb" style="padding: 0.9rem 0; border-bottom: 1px solid var(--eq-line); background: var(--eq-white);">
  <div class="eq-container">
    <ol class="eq-breadcrumb__list" style="display: flex; align-items: center; gap: 0.5rem; list-style: none; margin: 0; padding: 0; font-size: 0.84rem; color: var(--eq-charcoal-soft);">
      <li><a href="{{ route('home') }}" style="color: inherit; text-decoration: none;">Home</a></li>
      <li style="color: var(--eq-line-dark);">&rsaquo;</li>
      <li style="color: var(--eq-charcoal); font-weight: 500;" aria-current="page">Register</li>
    </ol>
  </div>
</nav>

<!-- Register Form Container -->
<main class="eq-container" id="main-content" style="padding: 3rem 0 5rem;">
  <div class="eq-auth-wrap" style="max-width: 520px;">
    <div class="eq-auth-card">
      
      <div class="eq-auth-card__header">
        <h1 class="eq-auth-card__title">Create Account</h1>
        <p class="eq-auth-card__desc">Join Nous Telos for artisanal fashion &amp; priority orders</p>
      </div>

      @if($errors->any())
        <div style="background: #fdf2f2; color: #9b1c1c; padding: 0.85rem 1rem; border-radius: 4px; font-size: 0.86rem; margin-bottom: 1.25rem; border: 1px solid #f8b4b4;">
          <ul style="margin: 0; padding-left: 1.2rem;">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form class="eq-auth-form" id="customer-register-form" method="POST" action="{{ route('register.submit') }}">
        @csrf
        
        <div class="eq-form-group">
          <label for="reg-name" class="eq-form-label">Full Name <span class="req">*</span></label>
          <input 
            type="text" 
            id="reg-name" 
            name="name" 
            class="eq-form-input" 
            placeholder="e.g. Iftekhar Islam Ifty" 
            value="{{ old('name') }}"
            required 
            autocomplete="name"
          />
        </div>

        <div class="eq-form-group">
          <label for="reg-phone" class="eq-form-label">Mobile Phone Number <span class="req">*</span></label>
          <input 
            type="tel" 
            id="reg-phone" 
            name="phone" 
            class="eq-form-input" 
            placeholder="e.g. 01793123456" 
            value="{{ old('phone') }}"
            required 
            autocomplete="tel"
          />
        </div>

        <div class="eq-form-group">
          <label for="reg-email" class="eq-form-label">Email Address <span class="req">*</span></label>
          <input 
            type="email" 
            id="reg-email" 
            name="email" 
            class="eq-form-input" 
            placeholder="e.g. iftekhar@earthquick.com" 
            value="{{ old('email') }}"
            required 
            autocomplete="email"
          />
        </div>

        <div class="eq-form-group">
          <label for="reg-city" class="eq-form-label">City / Region <span class="req">*</span></label>
          <select id="reg-city" name="city" class="eq-form-select" required>
            <option value="Chattogram" {{ old('city', 'Chattogram') === 'Chattogram' ? 'selected' : '' }}>Chattogram (Chittagong)</option>
            <option value="Dhaka" {{ old('city') === 'Dhaka' ? 'selected' : '' }}>Dhaka</option>
            <option value="Sylhet" {{ old('city') === 'Sylhet' ? 'selected' : '' }}>Sylhet</option>
            <option value="Rajshahi" {{ old('city') === 'Rajshahi' ? 'selected' : '' }}>Rajshahi</option>
            <option value="Khulna" {{ old('city') === 'Khulna' ? 'selected' : '' }}>Khulna</option>
            <option value="Barishal" {{ old('city') === 'Barishal' ? 'selected' : '' }}>Barishal</option>
            <option value="Other" {{ old('city') === 'Other' ? 'selected' : '' }}>Other Region</option>
          </select>
        </div>

        <div class="eq-form-group">
          <label for="reg-password" class="eq-form-label">Create Password <span class="req">*</span></label>
          <input 
            type="password" 
            id="reg-password" 
            name="password" 
            class="eq-form-input" 
            placeholder="Minimum 6 characters" 
            required 
            minlength="6"
            autocomplete="new-password"
          />
        </div>

        <div style="display: flex; align-items: flex-start; gap: 0.5rem; font-size: 0.84rem; color: var(--eq-charcoal-soft); margin-top: 0.25rem;">
          <input type="checkbox" id="agree-terms" checked required style="accent-color: var(--eq-gold); margin-top: 0.2rem;" />
          <label for="agree-terms">
            I agree to the <a href="{{ route('about') }}" class="eq-auth-link">Terms &amp; Conditions</a> and <a href="{{ route('about') }}" class="eq-auth-link">Privacy Policy</a>.
          </label>
        </div>

        <button type="submit" class="eq-auth-submit-btn" id="btn-register-submit">
          Create My Account &rarr;
        </button>

      </form>

      <div class="eq-auth-footer-prompt">
        Already have an account? 
        <a href="{{ route('login') }}" class="eq-auth-link" style="font-weight: 600;">Sign In</a>
      </div>

    </div>
  </div>
</main>
@endsection


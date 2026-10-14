@extends('admin.layout')

@section('title', 'Homepage Images — Rthquick Admin')
@section('page_title', 'Homepage Images')

@section('content')
<style>
  .eq-home-media-intro { margin-bottom: 1.1rem; color: var(--eq-charcoal-soft); line-height: 1.55; }
  .eq-home-media-section { margin: 1.4rem 0 0.8rem; color: var(--eq-navy); font-family: var(--font-display); font-size: 1.3rem; }
  .eq-home-media-note { margin-bottom: 1rem; padding: 0.8rem 1rem; background: var(--eq-gold-light); border-left: 3px solid var(--eq-gold); border-radius: 6px; line-height: 1.55; }
  .eq-home-media-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 275px), 1fr)); gap: 1rem; }
  .eq-home-media-card { margin: 0; display: flex; flex-direction: column; gap: 0.7rem; }
  .eq-home-media-card h3 { color: var(--eq-navy); font-size: 1rem; line-height: 1.3; }
  .eq-home-media-preview { width: 100%; aspect-ratio: 16 / 9; object-fit: cover; border-radius: 6px; background: var(--eq-cream-deep); border: 1px solid var(--eq-line); }
  .eq-home-media-preview--portrait { aspect-ratio: 9 / 12.8; max-height: 290px; object-position: center top; }
  .eq-home-media-status { color: var(--eq-charcoal-soft); font-size: 0.78rem; }
  .eq-home-media-card form { display: grid; gap: 0.55rem; margin-top: auto; }
  .eq-home-media-card label { color: var(--eq-charcoal); font-size: 0.8rem; font-weight: 600; }
  .eq-home-media-card input { width: 100%; min-width: 0; padding: 0.55rem; border: 1px solid var(--eq-line); border-radius: 5px; background: #fff; font: inherit; }
  .eq-home-media-card input[type="file"] { font-size: 0.78rem; }
  .eq-home-media-card small { font-size: 0.74rem; color: var(--eq-charcoal-soft); }
  .eq-home-media-card button { justify-self: start; margin-top: 0.25rem; }
  .eq-home-media-alert { padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 0.9rem; }
  .eq-home-media-alert--success { background: #e6f4eb; color: #165b32; }
  .eq-home-media-alert--error { background: #fff0ed; color: #962f22; }
  @media (max-width: 600px) { .eq-home-media-grid { grid-template-columns: 1fr; } }
</style>

<p class="eq-home-media-intro">Replace homepage photography without changing product cards or the slide/filter layout. Each position keeps its current image until you upload a replacement. Uploaded JPEG, PNG and WebP files are saved as WebP.</p>

@if(session('success'))
  <p class="eq-home-media-alert eq-home-media-alert--success" role="status">{{ session('success') }}</p>
@endif
@if($errors->any())
  <div class="eq-home-media-alert eq-home-media-alert--error" role="alert">
    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
  </div>
@endif

<h2 class="eq-home-media-section">Hero Slider</h2>
<p class="eq-home-media-note"><strong>Image guide:</strong> Use wide campaign photography, ideally <strong>1920 × 1080 px (16:9)</strong>; 1600 × 900–2400 × 1350 px works well. Minimum 1200 × 675 px, maximum upload 6 MB. The image fills the banner and crops differently on phones—keep the subject near the centre or right and avoid putting important text inside the image.</p>
<div class="eq-home-media-grid">
  @foreach($slots as $key => $slot)
    @if($slot['section'] === 'hero')
      <article class="eq-admin-card eq-home-media-card">
        <h3>{{ $slot['label'] }}</h3>
        <img class="eq-home-media-preview" src="{{ asset($slot['image']) }}" alt="Current {{ $slot['label'] }} image" loading="lazy">
        <p class="eq-home-media-status">{{ $slot['custom'] ? 'Custom image active' : 'Original image active' }}</p>
        <form action="{{ route('admin.homepage-images.update', $key) }}" method="POST" enctype="multipart/form-data">
          @csrf
          <label for="{{ $key }}-image">Replace image</label>
          <input id="{{ $key }}-image" type="file" name="image" accept="image/jpeg,image/png,image/webp">
          <label for="{{ $key }}-alt">Image description (alt text)</label>
          <input id="{{ $key }}-alt" type="text" name="alt_text" value="{{ $slot['alt'] }}" maxlength="160" required>
          <small>Choose a file to replace the picture, or save only the description.</small>
          <button type="submit" class="eq-admin-btn eq-admin-btn--primary">Save slide</button>
        </form>
      </article>
    @endif
  @endforeach
</div>

<h2 class="eq-home-media-section">Styled by You</h2>
<p class="eq-home-media-note"><strong>What to upload:</strong> Complete coordinated outfits, festive looks, everyday styling, heritage moments, or close-up accessory details that match each position's label and filter. Use photos you own or have permission to publish. These coverflow cards are taller than product cards: aim for <strong>900 × 1280 px portrait</strong> (roughly 7:10), minimum 600 × 800 px, maximum upload 6 MB. Keep the important person/product near the centre so mobile cropping stays clear.</p>
<div class="eq-home-media-grid">
  @foreach($slots as $key => $slot)
    @if($slot['section'] === 'styled')
      <article class="eq-admin-card eq-home-media-card">
        <h3>{{ $slot['label'] }}</h3>
        <img class="eq-home-media-preview eq-home-media-preview--portrait" src="{{ asset($slot['image']) }}" alt="Current {{ $slot['label'] }} image" loading="lazy">
        <p class="eq-home-media-status">{{ $slot['custom'] ? 'Custom image active' : 'Original image active' }}</p>
        <form action="{{ route('admin.homepage-images.update', $key) }}" method="POST" enctype="multipart/form-data">
          @csrf
          <label for="{{ $key }}-image">Replace image</label>
          <input id="{{ $key }}-image" type="file" name="image" accept="image/jpeg,image/png,image/webp">
          <label for="{{ $key }}-alt">Image description (alt text)</label>
          <input id="{{ $key }}-alt" type="text" name="alt_text" value="{{ $slot['alt'] }}" maxlength="160" required>
          <small>Choose a file to replace the picture, or save only the description.</small>
          <button type="submit" class="eq-admin-btn eq-admin-btn--primary">Save look</button>
        </form>
      </article>
    @endif
  @endforeach
</div>
@endsection

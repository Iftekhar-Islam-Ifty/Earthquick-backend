<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Admin Atelier') — Earthquick (Nous Telos)</title>

  <!-- Google Fonts: Fraunces (display) + Jost (body) -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet" />

  <style>
    :root {
      --eq-navy: #1b3a4b;
      --eq-navy-dark: #122834;
      --eq-gold: #c9962f;
      --eq-gold-dark: #a97a20;
      --eq-gold-light: #f9f3e5;
      --eq-teal: #2c7a78;
      --eq-cream: #f7f2e9;
      --eq-cream-deep: #efe7d8;
      --eq-charcoal: #2a2622;
      --eq-charcoal-soft: #5c564d;
      --eq-charcoal-muted: #8c857b;
      --eq-white: #ffffff;
      --eq-line: rgba(42, 38, 34, 0.12);
      --font-display: "Fraunces", Georgia, serif;
      --font-body: "Jost", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: var(--font-body);
      background-color: var(--eq-cream);
      color: var(--eq-charcoal);
      display: flex;
      min-height: 100vh;
      font-size: 0.92rem;
      line-height: 1.5;
      overflow-x: hidden;
      width: 100%;
    }

    /* Admin Sidebar */
    .eq-admin-sidebar {
      width: 260px;
      background-color: var(--eq-navy);
      color: #ffffff;
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
      position: sticky;
      top: 0;
      height: 100vh;
      overflow-y: auto;
      border-right: 1px solid rgba(255, 255, 255, 0.08);
      z-index: 100;
    }

    .eq-admin-sidebar__brand {
      padding: 1.75rem 1.5rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      gap: 0.85rem;
    }

    .eq-admin-sidebar__logo {
      height: 28px;
      filter: brightness(0) invert(1);
    }

    .eq-admin-badge {
      font-size: 0.65rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      background: rgba(201, 150, 47, 0.25);
      color: var(--eq-gold);
      border: 1px solid rgba(201, 150, 47, 0.4);
      padding: 0.15rem 0.5rem;
      border-radius: 999px;
      font-weight: 600;
    }

    .eq-admin-nav {
      list-style: none;
      padding: 1.25rem 0.75rem;
      flex: 1;
    }

    .eq-admin-nav__heading {
      font-size: 0.68rem;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.4);
      padding: 0.75rem 0.75rem 0.35rem;
      font-weight: 600;
    }

    .eq-admin-nav__link {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.65rem 0.85rem;
      color: rgba(255, 255, 255, 0.75);
      text-decoration: none;
      border-radius: 6px;
      font-size: 0.88rem;
      font-weight: 500;
      transition: all 0.2s ease;
      margin-bottom: 0.2rem;
    }

    .eq-admin-nav__link svg {
      width: 18px;
      height: 18px;
      opacity: 0.8;
      flex-shrink: 0;
    }

    .eq-admin-nav__link:hover {
      color: #ffffff;
      background-color: rgba(255, 255, 255, 0.08);
    }

    .eq-admin-nav__link.is-active {
      color: #ffffff;
      background-color: var(--eq-gold);
      font-weight: 600;
    }

    .eq-admin-nav__link.is-active svg {
      opacity: 1;
    }

    .eq-admin-sidebar__footer {
      padding: 1.25rem;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.75rem;
    }

    .eq-admin-user {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      min-width: 0;
    }

    .eq-admin-avatar {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: var(--eq-gold);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      font-size: 0.85rem;
      flex-shrink: 0;
    }

    .eq-admin-user__name {
      font-size: 0.82rem;
      font-weight: 600;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      color: #ffffff;
    }

    .eq-admin-user__role {
      font-size: 0.72rem;
      color: rgba(255, 255, 255, 0.5);
    }

    /* Main Content Area */
    .eq-admin-main {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
      background-color: var(--eq-cream);
      overflow-x: hidden;
    }

    .eq-admin-topbar {
      height: 64px;
      background-color: var(--eq-white);
      border-bottom: 1px solid var(--eq-line);
      padding: 0 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 90;
      min-width: 0;
    }

    .eq-admin-topbar__title {
      font-family: var(--font-display);
      font-size: 1.25rem;
      color: var(--eq-navy);
      font-weight: 500;
    }

    .eq-admin-content {
      padding: 2rem;
      flex: 1;
      min-width: 0;
      max-width: 100%;
      box-sizing: border-box;
    }

    /* Cards & Containers */
    .eq-admin-card {
      background: var(--eq-white);
      border: 1px solid var(--eq-line);
      border-radius: 8px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
      min-width: 0;
      max-width: 100%;
      box-sizing: border-box;
    }

    .eq-admin-card__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.25rem;
      padding-bottom: 0.75rem;
      border-bottom: 1px solid var(--eq-line);
      min-width: 0;
      gap: 0.75rem;
      flex-wrap: wrap;
    }

    .eq-admin-card__title {
      font-family: var(--font-display);
      font-size: 1.15rem;
      color: var(--eq-navy);
      margin: 0;
    }

    /* Metric Stat Grid */
    .eq-admin-metrics {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 1.25rem;
      margin-bottom: 1.5rem;
      min-width: 0;
      width: 100%;
    }

    @media (max-width: 1200px) {
      .eq-admin-metrics {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 640px) {
      .eq-admin-metrics {
        grid-template-columns: minmax(0, 1fr);
      }
    }

    .eq-metric-box {
      background: var(--eq-white);
      border: 1px solid var(--eq-line);
      border-radius: 8px;
      padding: 1.25rem;
      display: flex;
      flex-direction: column;
      box-shadow: 0 2px 8px rgba(0,0,0,0.02);
      transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .eq-metric-box:hover {
      transform: translateY(-2px);
      border-color: var(--eq-gold);
    }

    .eq-metric-box__title {
      font-size: 0.76rem;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--eq-charcoal-soft);
      margin-bottom: 0.4rem;
      font-weight: 500;
    }

    .eq-metric-box__value {
      font-family: var(--font-display);
      font-size: 1.8rem;
      font-weight: 600;
      color: var(--eq-navy);
      line-height: 1.2;
    }

    .eq-metric-box__sub {
      font-size: 0.78rem;
      color: var(--eq-charcoal-muted);
      margin-top: 0.35rem;
    }

    /* Tables */
    .eq-admin-table-wrap {
      width: 100%;
      overflow-x: auto;
    }

    .eq-admin-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 0.88rem;
    }

    .eq-admin-table th {
      background-color: var(--eq-cream-deep);
      color: var(--eq-charcoal);
      font-weight: 600;
      font-size: 0.78rem;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      padding: 0.85rem 1rem;
      border-bottom: 1px solid var(--eq-line);
      white-space: nowrap;
    }

    .eq-admin-table td {
      padding: 0.95rem 1rem;
      border-bottom: 1px solid var(--eq-line);
      color: var(--eq-charcoal);
      vertical-align: middle;
    }

    .eq-admin-table tr:last-child td {
      border-bottom: none;
    }

    .eq-admin-table tr:hover td {
      background-color: rgba(247, 242, 233, 0.4);
    }

    /* Status Badges */
    .eq-status-badge {
      display: inline-block;
      padding: 0.2rem 0.65rem;
      border-radius: 999px;
      font-size: 0.72rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    .eq-status-badge--pending {
      background: #fef3c7;
      color: #92400e;
      border: 1px solid #fde68a;
    }

    .eq-status-badge--confirmed {
      background: #e0f2fe;
      color: #0369a1;
      border: 1px solid #bae6fd;
    }

    .eq-status-badge--processing {
      background: #f3e8ff;
      color: #6b21a8;
      border: 1px solid #e9d5ff;
    }

    .eq-status-badge--in_transit {
      background: #e0e7ff;
      color: #3730a3;
      border: 1px solid #c7d2fe;
    }

    .eq-status-badge--delivered {
      background: #dcfce7;
      color: #166534;
      border: 1px solid #bbf7d0;
    }

    .eq-status-badge--cancelled {
      background: #fee2e2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    /* Buttons */
    .eq-admin-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.45rem 0.9rem;
      border-radius: 5px;
      font-size: 0.82rem;
      font-weight: 500;
      text-decoration: none;
      cursor: pointer;
      border: 1px solid transparent;
      transition: all 0.2s ease;
    }

    .eq-admin-btn--primary {
      background: var(--eq-navy);
      color: #ffffff;
    }

    .eq-admin-btn--primary:hover {
      background: var(--eq-navy-dark);
    }

    .eq-admin-btn--gold {
      background: var(--eq-gold);
      color: #ffffff;
    }

    .eq-admin-btn--gold:hover {
      background: var(--eq-gold-dark);
    }

    .eq-admin-btn--outline {
      background: transparent;
      border-color: var(--eq-line);
      color: var(--eq-charcoal);
    }

    .eq-admin-btn--outline:hover {
      background: var(--eq-cream);
      border-color: var(--eq-charcoal-soft);
    }

    .eq-admin-btn--danger {
      background: #fee2e2;
      color: #991b1b;
      border-color: #fca5a5;
    }

    .eq-admin-btn--danger:hover {
      background: #fecaca;
    }

    .eq-admin-btn--success {
      background: #dcfce7;
      color: #166534;
      border-color: #86efac;
    }

    .eq-admin-btn--success:hover {
      background: #bbf7d0;
    }

    /* Alerts */
    .eq-admin-alert {
      padding: 0.85rem 1.25rem;
      border-radius: 6px;
      margin-bottom: 1.5rem;
      font-size: 0.88rem;
      display: flex;
      align-items: center;
      gap: 0.65rem;
    }

    .eq-admin-alert--success {
      background: #dcfce7;
      color: #166534;
      border: 1px solid #bbf7d0;
    }

    .eq-admin-alert--error {
      background: #fee2e2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    /* Responsive */
    @media (max-width: 1024px) {
      .eq-admin-metrics {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 768px) {
      body {
        flex-direction: column;
      }
      .eq-admin-sidebar {
        width: 100%;
        height: auto;
        position: static;
      }
      .eq-admin-metrics {
        grid-template-columns: 1fr;
      }
      .eq-admin-content {
        padding: 1rem;
      }
      .eq-admin-topbar {
        padding: 0 1rem;
      }
    }
  </style>
  @stack('styles')
</head>
<body>

  <!-- Admin Navigation Sidebar -->
  <aside class="eq-admin-sidebar">
    <div class="eq-admin-sidebar__brand">
      <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 0.65rem; text-decoration: none;">
        <img src="{{ asset('images/logo/earthquick-logo.png') }}" alt="Earthquick" class="eq-admin-sidebar__logo" />
        <span class="eq-admin-badge">Admin Atelier</span>
      </a>
    </div>

    <ul class="eq-admin-nav">
      <li class="eq-admin-nav__heading">Overview</li>
      <li>
        <a href="{{ route('admin.dashboard') }}" class="eq-admin-nav__link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
          </svg>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="eq-admin-nav__heading">Commerce &amp; Inventory</li>
      <li>
        <a href="{{ route('admin.orders') }}" class="eq-admin-nav__link {{ request()->routeIs('admin.orders*') ? 'is-active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
            <path d="M3 6h18"></path>
            <path d="M16 10a4 4 0 0 1-8 0"></path>
          </svg>
          <span>Customer Orders</span>
        </a>
      </li>
      <li>
        <a href="{{ route('admin.products') }}" class="eq-admin-nav__link {{ request()->routeIs('admin.products*') ? 'is-active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
          </svg>
          <span>Stock &amp; Catalog</span>
        </a>
      </li>

      <li class="eq-admin-nav__heading">Storefront</li>
      <li>
        <a href="{{ route('home') }}" target="_blank" class="eq-admin-nav__link">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
            <polyline points="15 3 21 3 21 9"></polyline>
            <line x1="10" y1="14" x2="21" y2="3"></line>
          </svg>
          <span>View Live Store &nearr;</span>
        </a>
      </li>
    </ul>

    <div class="eq-admin-sidebar__footer">
      <div class="eq-admin-user">
        <div class="eq-admin-avatar">
          {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
        </div>
        <div style="min-width: 0;">
          <div class="eq-admin-user__name">{{ Auth::user()->name }}</div>
          <div class="eq-admin-user__role">Master Administrator</div>
        </div>
      </div>

      <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
        @csrf
        <button type="submit" title="Sign Out" style="background: none; border: none; color: rgba(255,255,255,0.6); cursor: pointer; display: flex; padding: 0.35rem;">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
        </button>
      </form>
    </div>
  </aside>

  <!-- Main Work Area -->
  <div class="eq-admin-main">
    <!-- Topbar -->
    <header class="eq-admin-topbar">
      <h1 class="eq-admin-topbar__title">@yield('page_title', 'Atelier Administration')</h1>
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span style="font-size: 0.8rem; color: var(--eq-charcoal-soft);">
          {{ now()->format('l, d M Y') }}
        </span>
        <a href="{{ route('home') }}" class="eq-admin-btn eq-admin-btn--outline" style="font-size: 0.78rem;">
          Storefront
        </a>
      </div>
    </header>

    <!-- Main Content Container -->
    <main class="eq-admin-content">
      @if(session('success'))
        <div class="eq-admin-alert eq-admin-alert--success">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      @if(session('error'))
        <div class="eq-admin-alert eq-admin-alert--error">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
          <span>{{ session('error') }}</span>
        </div>
      @endif

      @yield('content')
    </main>
  </div>

  @stack('scripts')
</body>
</html>


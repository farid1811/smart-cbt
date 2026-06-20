<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Smart CBT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ─── Reset ──────────────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:       #1E2A78;
            --primary-dark:  #141D54;
            --primary-soft:  #F1F3FB;
            --primary-mid:   #D9DEEE;
            --bg:            #F8FAFC;
            --surface:       #FFFFFF;
            --surface2:      #F1F5F9;
            --surface3:      #E2E8F0;
            --text:          #0F172A;
            --text-muted:    #64748B;
            --text-light:    #94A3B8;
            --border:        #E2E8F0;
            --success:       #10B981;
            --success-soft:  #ECFDF5;
            --error:         #EF4444;
            --error-soft:    #FEF2F2;
            --warning:       #F59E0B;
            --warning-soft:  #FFFBEB;
            --topbar-h:      60px;
            --radius:        10px;
            --radius-sm:     6px;
            --radius-lg:     14px;
            --shadow-sm:     0 1px 2px 0 rgba(0,0,0,0.05);
            --shadow:        0 1px 3px 0 rgba(0,0,0,0.07), 0 1px 2px -1px rgba(0,0,0,0.07);
            --shadow-md:     0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.07);
            --transition:    150ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── Topbar ─────────────────────────────────────────────────────── */
        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 1.5rem;
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 200;
            box-shadow: var(--shadow-sm);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            text-decoration: none;
        }

        .brand-logo {
            width: 30px; height: 30px;
            background: var(--primary);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-name {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.01em;
        }

        .topbar-divider {
            width: 1px;
            height: 20px;
            background: var(--border);
            flex-shrink: 0;
        }

        .topbar-nav {
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.625rem;
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.8125rem;
            font-weight: 500;
            transition: all var(--transition);
            -webkit-tap-highlight-color: transparent;
        }

        .nav-link:hover {
            background: var(--surface2);
            color: var(--text);
        }

        .nav-link.active {
            background: var(--primary-soft);
            color: var(--primary);
            font-weight: 600;
        }

        /* Mobile nav toggle */
        .nav-toggle {
            display: none;
            width: 34px; height: 34px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            transition: all var(--transition);
        }

        .nav-toggle:hover { background: var(--surface2); color: var(--text); }

        /* Mobile dropdown nav */
        .mobile-nav {
            display: none;
            position: absolute;
            top: var(--topbar-h);
            left: 0;
            right: 0;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0.5rem 1rem;
            box-shadow: var(--shadow-md);
            z-index: 199;
        }

        .mobile-nav.open { display: block; }

        .mobile-nav .nav-link {
            display: flex;
            width: 100%;
            padding: 0.625rem 0.75rem;
            margin-bottom: 2px;
            font-size: 0.875rem;
        }

        /* Topbar right */
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 99px;
            padding: 0.25rem 0.75rem 0.25rem 0.25rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--text);
        }

        .user-avatar {
            width: 26px; height: 26px;
            background: var(--primary);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-name-text { max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.375rem 0.625rem;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .btn-logout:hover {
            background: var(--error-soft);
            border-color: #FECACA;
            color: var(--error);
        }

        /* ─── Page Content ───────────────────────────────────────────────── */
        .page-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        /* ─── Alert Messages ─────────────────────────────────────────────── */
        .alert {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
            border: 1px solid transparent;
        }

        .alert svg { flex-shrink: 0; }
        .alert-success { background: var(--success-soft); color: #065F46; border-color: #A7F3D0; }
        .alert-error   { background: var(--error-soft);   color: #991B1B; border-color: #FECACA; }
        .alert-info    { background: var(--primary-soft); color: #1E40AF; border-color: var(--primary-mid); }
        .alert-warning { background: var(--warning-soft); color: #92400E; border-color: #FDE68A; }

        /* ─── Buttons ────────────────────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 0.875rem;
            border-radius: var(--radius-sm);
            font-size: 0.8125rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all var(--transition);
            border: 1px solid transparent;
            font-family: 'Inter', sans-serif;
            line-height: 1;
            white-space: nowrap;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .btn-primary:active { transform: scale(0.97); }

        .btn-secondary {
            background: var(--surface);
            color: var(--text);
            border-color: var(--border);
        }
        .btn-secondary:hover { background: var(--surface2); }
        .btn-secondary:active { transform: scale(0.97); }

        .btn-danger {
            background: var(--error-soft);
            color: #B91C1C;
            border-color: #FECACA;
        }
        .btn-danger:hover { background: #FEE2E2; }

        .btn-sm   { padding: 0.3125rem 0.625rem; font-size: 0.75rem; }
        .btn-lg   { padding: 0.75rem 1.375rem; font-size: 0.9375rem; }

        /* ─── Cards ──────────────────────────────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition);
        }

        .card:hover { border-color: var(--primary-mid); }

        .table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        /* ─── Tables ─────────────────────────────────────────────────────── */
        table { width: 100%; border-collapse: collapse; }

        thead th {
            padding: 0.625rem 1rem;
            background: var(--surface2);
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        tbody td {
            padding: 0.8125rem 1rem;
            font-size: 0.8125rem;
            color: var(--text);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        tbody tr:last-child td { border-bottom: none; }
        tbody tr { transition: background var(--transition); }
        tbody tr:hover { background: #FAFBFC; }

        /* ─── Badges ─────────────────────────────────────────────────────── */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.1875rem 0.5rem;
            border-radius: 99px;
            font-size: 0.6875rem;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .badge-TWK { background: #EFF6FF; color: #1D4ED8; border-color: #BFDBFE; }
        .badge-TIU { background: #F5F3FF; color: #6D28D9; border-color: #DDD6FE; }
        .badge-TKP { background: #ECFDF5; color: #059669; border-color: #A7F3D0; }
        .badge-active { background: #DCFCE7; color: #15803D; border-color: #BBF7D0; }
        .badge-selesai { background: #ECFDF5; color: #059669; border-color: #A7F3D0; }
        .badge-berlangsung { background: #FEF3C7; color: #B45309; border-color: #FDE68A; }
        .badge-batal { background: #FEE2E2; color: #DC2626; border-color: #FECACA; }

        /* ─── Responsive ─────────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .topbar { padding: 0 1rem; }
            .topbar-nav { display: none; }
            .nav-toggle { display: flex; }
            .topbar-divider { display: none; }
            .user-name-text { display: none; }
            .page-wrapper { padding: 1.25rem 1rem; }
        }

        @media (max-width: 480px) {
            .user-pill { background: transparent; border: none; padding: 0; }
        }

        /* Touch hover */
        @media (hover: none) {
            .btn:active { transform: scale(0.96); opacity: 0.9; }
            .nav-link:active { background: var(--surface2); }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="topbar" style="position:relative;">
        <div class="topbar-left">
            <a href="{{ route('peserta.dashboard') }}" class="topbar-brand" style="display:flex; align-items:center;">
                <img src="{{ asset('images/logo-plano.jpg') }}" alt="Bimbel Plano" style="max-height: 34px; width: auto; filter: drop-shadow(0 1px 2px rgba(30,42,120,0.05));">
            </a>
            <div class="topbar-divider"></div>
            <nav class="topbar-nav">
                <a href="{{ route('peserta.dashboard') }}" class="nav-link {{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('peserta.results.index') }}" class="nav-link {{ request()->routeIs('peserta.results.*') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
                    Riwayat Nilai
                </a>
            </nav>
        </div>

        <div class="topbar-right">
            <button class="nav-toggle" id="navToggle" onclick="toggleMobileNav()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div class="user-pill">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <span class="user-name-text">{{ auth()->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar
                </button>
            </form>
        </div>

        {{-- Mobile nav dropdown --}}
        <div class="mobile-nav" id="mobileNav">
            <a href="{{ route('peserta.dashboard') }}" class="nav-link {{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                Dashboard
            </a>
            <a href="{{ route('peserta.results.index') }}" class="nav-link {{ request()->routeIs('peserta.results.*') ? 'active' : '' }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
                Riwayat Nilai
            </a>
        </div>
    </header>

    <main class="page-wrapper">
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                {{ session('info') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('open');
    }
    document.addEventListener('click', function(e) {
        const toggle = document.getElementById('navToggle');
        const nav = document.getElementById('mobileNav');
        if (!toggle.contains(e.target) && !nav.contains(e.target)) {
            nav.classList.remove('open');
        }
    });
    </script>
    @stack('scripts')
</body>
</html>

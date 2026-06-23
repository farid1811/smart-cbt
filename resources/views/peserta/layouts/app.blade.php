<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Smart CBT Bimbel Plano</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- KaTeX for Math Formula Rendering -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js" onload="renderMathInElement(document.body, {delimiters:[{left:'$$',right:'$$',display:true},{left:'$',right:'$',display:false},{left:'\\(',right:'\\)',display:false},{left:'\\[',right:'\\]',display:true}],throwOnError:false});"></script>
    <style>
        /* ─── Global Reset & Custom properties ─────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:       #1E40AF;       /* Modern Ruangguru Blue */
            --primary-dark:  #1D4ED8;
            --primary-soft:  #EFF6FF;       /* Soft Blue background */
            --primary-mid:   #DBEAFE;
            --bg:            #F8FAFC;       /* Clean Light Gray */
            --surface:       #FFFFFF;
            --surface2:      #F1F5F9;
            --surface3:      #E2E8F0;
            --text:          #0F172A;       /* Slate 900 */
            --text-muted:    #475569;       /* Slate 600 */
            --text-light:    #94A3B8;       /* Slate 400 */
            --border:        #E2E8F0;
            --success:       #10B981;
            --success-soft:  #ECFDF5;
            --error:         #EF4444;
            --error-soft:    #FEF2F2;
            --warning:       #FACC15;
            --warning-soft:  #FEF9C3;
            --topbar-h:      72px;
            --radius-sm:     8px;
            --radius:        12px;
            --radius-lg:     16px;
            --shadow-sm:     0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow:        0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --shadow-md:     0 10px 15px -3px rgba(30, 64, 175, 0.05), 0 4px 6px -2px rgba(30, 64, 175, 0.03);
            --shadow-lg:     0 20px 25px -5px rgba(30, 64, 175, 0.08), 0 10px 10px -5px rgba(30, 64, 175, 0.04);
            --transition:    200ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--bg);
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 99px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }

        /* ─── Topbar ─────────────────────────────────────────────────────── */
        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
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
            gap: 2rem;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .topbar-divider {
            width: 1px;
            height: 24px;
            background: var(--border);
            flex-shrink: 0;
        }

        .topbar-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            height: 100%;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all var(--transition);
            position: relative;
            -webkit-tap-highlight-color: transparent;
        }

        .nav-link:hover {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .nav-link.active {
            color: var(--primary);
            background: var(--primary-soft);
        }

        /* Animated underline for active state */
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -16px;
            left: 1rem;
            right: 1rem;
            height: 3px;
            background: var(--primary);
            border-radius: 99px;
            animation: slide-in 250ms ease forwards;
        }

        @keyframes slide-in {
            from { transform: scaleX(0); }
            to { transform: scaleX(1); }
        }

        /* Mobile nav toggle */
        .nav-toggle {
            display: none;
            width: 38px; height: 38px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            transition: all var(--transition);
        }

        .nav-toggle:hover {
            background: var(--surface2);
            color: var(--text);
        }

        /* Mobile dropdown nav */
        .mobile-nav {
            display: none;
            position: absolute;
            top: var(--topbar-h);
            left: 0;
            right: 0;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 1rem;
            box-shadow: var(--shadow-lg);
            z-index: 199;
            transform-origin: top;
            animation: grow-down 200ms ease forwards;
        }

        @keyframes grow-down {
            from { transform: scaleY(0.9); opacity: 0; }
            to { transform: scaleY(1); opacity: 1; }
        }

        .mobile-nav.open { display: block; }

        .mobile-nav .nav-link {
            display: flex;
            width: 100%;
            padding: 0.75rem 1rem;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }
        .mobile-nav .nav-link.active::after { display: none; }

        /* Topbar right */
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 99px;
            padding: 0.375rem 1rem 0.375rem 0.375rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text);
            transition: all var(--transition);
        }

        .user-avatar {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, var(--primary) 0%, #3B82F6 100%);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 700;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(30, 64, 175, 0.15);
        }

        .user-name-text {
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .btn-logout:hover {
            background: var(--error-soft);
            border-color: #FECACA;
            color: var(--error);
            transform: translateY(-1px);
        }

        /* ─── Page Content ───────────────────────────────────────────────── */
        .page-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem 2rem;
            animation: fade-in 300ms ease-out;
        }

        @keyframes fade-in {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ─── Alert Messages ─────────────────────────────────────────────── */
        .alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 2rem;
            border: 1px solid transparent;
            box-shadow: var(--shadow-sm);
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
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all var(--transition);
            border: 1px solid transparent;
            font-family: 'Inter', sans-serif;
            line-height: 1.2;
            white-space: nowrap;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            box-shadow: 0 4px 6px -1px rgba(30, 64, 175, 0.2), 0 2px 4px -1px rgba(30, 64, 175, 0.1);
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(30, 64, 175, 0.3);
        }
        .btn-primary:active { transform: translateY(0); }

        .btn-secondary {
            background: var(--surface);
            color: var(--text-muted);
            border-color: var(--border);
        }
        .btn-secondary:hover {
            background: var(--surface2);
            color: var(--text);
            transform: translateY(-2px);
        }
        .btn-secondary:active { transform: translateY(0); }

        .btn-danger {
            background: var(--error-soft);
            color: #B91C1C;
            border-color: #FECACA;
        }
        .btn-danger:hover {
            background: #FEE2E2;
            transform: translateY(-2px);
        }

        .btn-sm   { padding: 0.45rem 0.875rem; font-size: 0.8125rem; }
        .btn-lg   { padding: 0.875rem 1.75rem; font-size: 1rem; }

        /* ─── Cards ──────────────────────────────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            box-shadow: var(--shadow);
            transition: all var(--transition);
        }

        .card:hover {
            border-color: var(--primary-mid);
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        /* ─── Tables ─────────────────────────────────────────────────────── */
        table { width: 100%; border-collapse: collapse; }

        thead th {
            padding: 1rem 1.25rem;
            background: var(--surface2);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        tbody td {
            padding: 1.25rem;
            font-size: 0.875rem;
            color: var(--text);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        tbody tr:last-child td { border-bottom: none; }
        tbody tr { transition: background var(--transition); }
        tbody tr:hover { background: #F8FAFC; }

        /* ─── Badges ─────────────────────────────────────────────────────── */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid transparent;
            letter-spacing: 0.02em;
        }

        .badge-TWK { background: #EFF6FF; color: #1D4ED8; border-color: #BFDBFE; }
        .badge-TIU { background: #F5F3FF; color: #6D28D9; border-color: #DDD6FE; }
        .badge-TKP { background: #ECFDF5; color: #059669; border-color: #A7F3D0; }
        .badge-active { background: #DCFCE7; color: #15803D; border-color: #BBF7D0; }
        .badge-selesai { background: #ECFDF5; color: #059669; border-color: #A7F3D0; }
        .badge-berlangsung { background: #FEF3C7; color: #B45309; border-color: #FDE68A; }
        .badge-batal { background: #FEE2E2; color: #DC2626; border-color: #FECACA; }

        /* ─── Responsive ─────────────────────────────────────────────────── */
        @media (max-width: 992px) {
            .topbar { padding: 0 1.5rem; }
            .topbar-nav { display: none; }
            .nav-toggle { display: flex; }
            .topbar-divider { display: none; }
            .user-name-text { display: none; }
            .page-wrapper { padding: 2rem 1.5rem; }
        }

        @media (max-width: 576px) {
            .topbar { padding: 0 1rem; }
            .user-pill { background: transparent; border: none; padding: 0; }
            .page-wrapper { padding: 1.5rem 1rem; }
        }

        /* Touch hover */
        @media (hover: none) {
            .btn:active { transform: scale(0.98); opacity: 0.9; }
            .nav-link:active { background: var(--surface2); }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="topbar">
        <div class="topbar-left">
            <a href="{{ route('peserta.dashboard') }}" class="topbar-brand">
                <img src="{{ asset('images/logo-plano.jpg') }}" alt="Bimbel Plano Logo" style="max-height: 40px; width: auto; border-radius: 8px; filter: drop-shadow(0 2px 4px rgba(30,64,175,0.08));">
            </a>
            <div class="topbar-divider"></div>
            <nav class="topbar-nav">
                <a href="{{ route('peserta.dashboard') }}" class="nav-link {{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('peserta.modules.index') }}" class="nav-link {{ request()->routeIs('peserta.modules.*') ? 'active' : '' }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    Modul
                </a>
                <a href="{{ route('peserta.drills.index') }}" class="nav-link {{ request()->routeIs('peserta.drills.*') ? 'active' : '' }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    Drill Soal
                </a>
                <a href="{{ route('peserta.tryouts.index') }}" class="nav-link {{ request()->routeIs('peserta.tryouts.*') ? 'active' : '' }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                    Tryout
                </a>
                <a href="{{ route('peserta.results.index') }}" class="nav-link {{ request()->routeIs('peserta.results.*') ? 'active' : '' }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
                    Riwayat & Evaluasi
                </a>
            </nav>
        </div>

        <div class="topbar-right">
            <button class="nav-toggle" id="navToggle" onclick="toggleMobileNav()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div class="user-pill">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <span class="user-name-text">{{ auth()->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar
                </button>
            </form>
        </div>

        {{-- Mobile nav dropdown --}}
        <div class="mobile-nav" id="mobileNav">
            <a href="{{ route('peserta.dashboard') }}" class="nav-link {{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                Dashboard
            </a>
            <a href="{{ route('peserta.modules.index') }}" class="nav-link {{ request()->routeIs('peserta.modules.*') ? 'active' : '' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                Modul
            </a>
            <a href="{{ route('peserta.drills.index') }}" class="nav-link {{ request()->routeIs('peserta.drills.*') ? 'active' : '' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                Drill Soal
            </a>
            <a href="{{ route('peserta.tryouts.index') }}" class="nav-link {{ request()->routeIs('peserta.tryouts.*') ? 'active' : '' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                Tryout
            </a>
            <a href="{{ route('peserta.results.index') }}" class="nav-link {{ request()->routeIs('peserta.results.*') ? 'active' : '' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
                Riwayat & Evaluasi
            </a>
        </div>
    </header>

    <main class="page-wrapper">
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
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
        if (toggle && nav && !toggle.contains(e.target) && !nav.contains(e.target)) {
            nav.classList.remove('open');
        }
    });
    </script>
    @stack('scripts')
</body>
</html>

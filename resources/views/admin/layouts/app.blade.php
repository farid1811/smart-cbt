<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Smart CBT Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- KaTeX for Math Formula Rendering -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js" onload="renderMathInElement(document.body, {delimiters:[{left:'$$',right:'$$',display:true},{left:'$',right:'$',display:false},{left:'\\(',right:'\\)',display:false},{left:'\\[',right:'\\]',display:true}],throwOnError:false});"></script>
    <style>
        /* ─── Reset & Base ───────────────────────────────────────────────── */
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
            --border-focus:  #93C5FD;
            --success:       #10B981;
            --success-soft:  #ECFDF5;
            --error:         #EF4444;
            --error-soft:    #FEF2F2;
            --warning:       #F59E0B;
            --warning-soft:  #FFFBEB;
            --info:          #3B82F6;
            --info-soft:     #EFF6FF;
            --sidebar-w:     256px;
            --topbar-h:      60px;
            --radius:        10px;
            --radius-sm:     6px;
            --radius-lg:     14px;
            --shadow-sm:     0 1px 2px 0 rgba(0,0,0,0.05);
            --shadow:        0 1px 3px 0 rgba(0,0,0,0.07), 0 1px 2px -1px rgba(0,0,0,0.07);
            --shadow-md:     0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.07);
            --shadow-lg:     0 10px 15px -3px rgba(0,0,0,0.07), 0 4px 6px -4px rgba(0,0,0,0.07);
            --transition:    150ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── Sidebar ────────────────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 200;
            overflow: hidden;
            transition: transform var(--transition);
        }

        .sidebar-brand {
            padding: 0 1.25rem;
            height: var(--topbar-h);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        .brand-logo {
            width: 32px; height: 32px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-text h1 {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .brand-text span {
            font-size: 0.6875rem;
            color: var(--text-muted);
            font-weight: 500;
            letter-spacing: 0.01em;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1rem 0.75rem;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--surface3) transparent;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: var(--surface3); border-radius: 99px; }

        .nav-section {
            margin-bottom: 0.25rem;
        }

        .nav-label {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-light);
            padding: 0.75rem 0.5rem 0.375rem;
            display: block;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.5rem 0.625rem;
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.8125rem;
            font-weight: 500;
            transition: all var(--transition);
            margin-bottom: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-item .nav-icon {
            width: 18px; height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: color var(--transition);
        }

        .nav-item:hover {
            background: var(--surface2);
            color: var(--text);
        }

        .nav-item.active {
            background: var(--primary-soft);
            color: var(--primary);
            font-weight: 600;
        }

        .nav-item.active .nav-icon { color: var(--primary); }

        /* Mobile sidebar overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 199;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 0.875rem 0.75rem;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        .user-info-card {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.5rem 0.5rem 0.75rem;
        }

        .user-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8125rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-name { font-size: 0.8125rem; font-weight: 600; color: var(--text); line-height: 1.2; }
        .user-role { font-size: 0.6875rem; color: var(--text-muted); margin-top: 1px; }

        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            width: 100%;
            padding: 0.5rem 0.75rem;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            font-size: 0.8125rem;
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

        /* ─── Main Content ───────────────────────────────────────────────── */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }

        /* ─── Topbar ─────────────────────────────────────────────────────── */
        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 1.75rem;
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-left { min-width: 0; }

        .topbar-title {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }

        .breadcrumb {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .breadcrumb span { color: var(--text-muted); }

        .topbar-actions { display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; }

        /* Mobile topbar toggle */
        .sidebar-toggle {
            display: none;
            width: 36px; height: 36px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            margin-right: 0.75rem;
            transition: all var(--transition);
        }

        .sidebar-toggle:hover { background: var(--surface2); color: var(--text); }

        /* ─── Page Content ───────────────────────────────────────────────── */
        .page-content {
            padding: 1.75rem;
            flex: 1;
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
        .alert-info    { background: var(--info-soft);    color: #1E40AF; border-color: #BFDBFE; }
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
            box-shadow: 0 1px 2px rgba(37,99,235,0.15);
        }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .btn-primary:active { transform: scale(0.98); }

        .btn-secondary {
            background: var(--surface);
            color: var(--text);
            border-color: var(--border);
        }
        .btn-secondary:hover { background: var(--surface2); border-color: var(--surface3); }
        .btn-secondary:active { transform: scale(0.98); }

        .btn-danger {
            background: var(--error-soft);
            color: #B91C1C;
            border-color: #FECACA;
        }
        .btn-danger:hover { background: #FEE2E2; }

        .btn-sm { padding: 0.3125rem 0.625rem; font-size: 0.75rem; }
        .btn-lg { padding: 0.625rem 1.25rem; font-size: 0.9375rem; }

        /* ─── Stats Grid ─────────────────────────────────────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            transition: all var(--transition);
            box-shadow: var(--shadow-sm);
            text-decoration: none;
            color: inherit;
        }

        .stat-card:hover {
            border-color: var(--primary-mid);
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon.blue   { background: #EFF6FF; color: #2563EB; }
        .stat-icon.green  { background: #ECFDF5; color: #059669; }
        .stat-icon.orange { background: #FFF7ED; color: #EA580C; }
        .stat-icon.purple { background: #F5F3FF; color: #7C3AED; }
        .stat-icon.teal   { background: #F0FDFA; color: #0D9488; }
        .stat-icon.rose   { background: #FFF1F2; color: #E11D48; }

        .stat-info { min-width: 0; }
        .stat-val   { font-size: 1.625rem; font-weight: 800; color: var(--text); line-height: 1; letter-spacing: -0.03em; }
        .stat-label { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.3rem; font-weight: 500; }

        /* ─── Table Card ─────────────────────────────────────────────────── */
        .table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .table-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .table-header h3 {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

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
            white-space: nowrap;
        }

        tbody td {
            padding: 0.75rem 1rem;
            font-size: 0.8125rem;
            color: var(--text);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        tbody tr:last-child td { border-bottom: none; }

        tbody tr {
            transition: background var(--transition);
        }

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

        .badge-active   { background: #DCFCE7; color: #15803D; border-color: #BBF7D0; }
        .badge-inactive { background: #FEE2E2; color: #DC2626; border-color: #FECACA; }
        .badge-warning  { background: #FEF3C7; color: #B45309; border-color: #FDE68A; }
        .badge-info     { background: #DBEAFE; color: #1D4ED8; border-color: #BFDBFE; }
        .badge-mudah    { background: #DCFCE7; color: #15803D; border-color: #BBF7D0; }
        .badge-sedang   { background: #FEF3C7; color: #B45309; border-color: #FDE68A; }
        .badge-sulit    { background: #FEE2E2; color: #DC2626; border-color: #FECACA; }
        .badge-TWK      { background: #EFF6FF; color: #2563EB; border-color: #BFDBFE; }
        .badge-TIU      { background: #F5F3FF; color: #7C3AED; border-color: #DDD6FE; }
        .badge-TKP      { background: #ECFDF5; color: #059669; border-color: #A7F3D0; }

        /* ─── Filter Bar ─────────────────────────────────────────────────── */
        .filter-bar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1.25rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 0.75rem 1rem;
            box-shadow: var(--shadow-sm);
        }

        /* ─── Forms ──────────────────────────────────────────────────────── */
        .form-group { margin-bottom: 1.125rem; }

        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.375rem;
        }

        .form-hint {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.5625rem 0.75rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            transition: all var(--transition);
            outline: none;
            -webkit-appearance: none;
            appearance: none;
        }

        .form-control::placeholder { color: var(--text-light); }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }

        .form-control:hover:not(:focus) { border-color: var(--surface3); }

        .form-control.is-invalid { border-color: var(--error); }
        .form-control.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.12); }
        .form-error { font-size: 0.75rem; color: var(--error); margin-top: 0.25rem; font-weight: 500; }

        textarea.form-control { resize: vertical; min-height: 90px; }

        select.form-control {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            padding-right: 2.25rem;
            cursor: pointer;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* ─── Empty State ────────────────────────────────────────────────── */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3.5rem 1.5rem;
            color: var(--text-muted);
            text-align: center;
        }

        .empty-state svg { opacity: 0.35; margin-bottom: 1rem; }
        .empty-state p { font-size: 0.9rem; font-weight: 600; color: var(--text-muted); }
        .empty-state .sub { font-size: 0.8rem; font-weight: 400; margin-top: 0.25rem; }

        /* ─── Action Buttons (inline table) ─────────────────────────────── */
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all var(--transition);
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        .action-btn-edit   { background:#EFF6FF; color:#2563EB; border-color:#BFDBFE; }
        .action-btn-delete { background:#FFF1F2; color:#E11D48; border-color:#FECDD3; }
        .action-btn-purple { background:#FAF5FF; color:#9333EA; border-color:#E9D5FF; }
        .action-btn-green  { background:#F0FDF4; color:#16A34A; border-color:#BBF7D0; }
        .action-btn-orange { background:#FFFBEB; color:#D97706; border-color:#FDE68A; }

        .action-btn:hover  { filter: brightness(0.96); transform: scale(0.98); }
        .action-btn:active { transform: scale(0.95); }

        /* ─── Section Title ──────────────────────────────────────────────── */
        .section-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* ─── Divider ────────────────────────────────────────────────────── */
        hr { border: none; border-top: 1px solid var(--border); margin: 1.25rem 0; }

        /* ─── Code ───────────────────────────────────────────────────────── */
        code {
            background: var(--surface2);
            padding: 0.125rem 0.375rem;
            border-radius: 4px;
            font-size: 0.75rem;
            color: var(--text-muted);
            font-family: 'SF Mono', 'Fira Code', monospace;
        }

        /* ─── Responsive ─────────────────────────────────────────────────── */
        @media (max-width: 1024px) {
            .form-row { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
        }

        @media (max-width: 768px) {
            :root { --sidebar-w: 256px; }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
                box-shadow: var(--shadow-lg);
            }

            .sidebar-overlay { display: block; }
            .sidebar-overlay.active { display: block; opacity: 1; }

            .sidebar-toggle { display: flex; }

            .main {
                margin-left: 0;
            }

            .topbar { padding: 0 1rem; }
            .page-content { padding: 1rem; }

            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
            .stat-card { padding: 1rem; }
            .stat-val { font-size: 1.375rem; }

            .filter-bar { padding: 0.625rem 0.75rem; }
            .filter-bar .form-control { flex: 1; min-width: 130px; }

            table { font-size: 0.75rem; }
            thead th { padding: 0.5rem 0.625rem; }
            tbody td { padding: 0.625rem 0.625rem; }

            .table-header { padding: 0.875rem 1rem; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 0.625rem; }
            .stat-icon { width: 36px; height: 36px; }
            .filter-bar { flex-direction: column; align-items: stretch; }
        }

        /* Touch-friendly hover */
        @media (hover: none) {
            .nav-item:active { background: var(--surface2); color: var(--text); }
            .btn:active { transform: scale(0.96); opacity: 0.9; }
            .stat-card:active { transform: scale(0.99); }
            .action-btn:active { transform: scale(0.94); opacity: 0.85; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Mobile Overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- Sidebar --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand" style="justify-content: center; padding: 0.5rem 1rem;">
        <img src="{{ asset('images/logo-plano.jpg') }}" alt="Bimbel Plano" style="max-height: 38px; width: auto; filter: drop-shadow(0 1px 2px rgba(30,42,120,0.05));">
    </div>

    <nav class="sidebar-nav">
        <!-- Dashboard -->
        <div class="nav-section">
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                </span>
                Dashboard
            </a>
        </div>

        <!-- Management -->
        <div class="nav-section">
            <span class="nav-label">Manajemen</span>
            <a href="{{ route('admin.peserta.index') }}" class="nav-item {{ request()->routeIs('admin.peserta.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
                Data Peserta
            </a>
        </div>

        <!-- Topik -->
        <div class="nav-section">
            <span class="nav-label">Topik</span>
            <a href="{{ route('admin.modules.index') }}" class="nav-item {{ request()->routeIs('admin.modules.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                </span>
                Modul
            </a>
            <a href="{{ route('admin.categories.index', ['tab' => 'category']) }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                </span>
                Kategori
            </a>
            <a href="{{ route('admin.questions.index') }}" class="nav-item {{ request()->routeIs('admin.questions.*') && !request()->routeIs('admin.questions.create') && !request()->routeIs('admin.questions.edit') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </span>
                Daftar Soal
            </a>
            @if(request()->routeIs('admin.questions.edit') || request()->routeIs('admin.questions.create'))
            <a href="#" class="nav-item active" style="pointer-events: none;">
                <span class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </span>
                Edit Soal
            </a>
            @endif
        </div>

        <!-- Drill & Tryout -->
        <div class="nav-section">
            <span class="nav-label">Drill & Tryout</span>
            <a href="{{ route('admin.tryouts.index', ['type' => 'drill']) }}" class="nav-item {{ (request('type') === 'drill' || (isset($tryout) && $tryout->jenis_ujian === 'drill')) ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
                </span>
                Paket Drill
            </a>
            <a href="{{ route('admin.tryouts.index', ['type' => 'tryout']) }}" class="nav-item {{ (request('type') === 'tryout' || (isset($tryout) && $tryout->jenis_ujian === 'tryout')) ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
                </span>
                Paket Tryout
            </a>
        </div>

        <!-- Results -->
        <div class="nav-section">
            <span class="nav-label">Hasil Ujian</span>
            <a href="{{ route('admin.rekap.index', ['view' => 'breakdown']) }}" class="nav-item {{ request()->routeIs('admin.rekap.*') && request('view') === 'breakdown' ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
                </span>
                Rekap Nilai
            </a>
            <a href="{{ route('admin.rekap.index', ['view' => 'ranking']) }}" class="nav-item {{ request()->routeIs('admin.rekap.*') && request('view') === 'ranking' ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                </span>
                Peringkat
            </a>
            <a href="{{ route('admin.rekap.index', ['view' => 'history']) }}" class="nav-item {{ request()->routeIs('admin.rekap.*') && (request('view') === 'history' || !request()->has('view')) ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </span>
                Riwayat
            </a>
        </div>

        <!-- Settings -->
        <div class="nav-section">
            <span class="nav-label">Pengaturan</span>
            <a href="#" class="nav-item" onclick="alert('Pengaturan Sistem telah dikonfigurasi penuh untuk operasional. Opsi umum dikelola melalui pengaturan Tryout dan Soal.'); return false;">
                <span class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                </span>
                Pengaturan
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info-card">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- Main --}}
<main class="main">
    <div class="topbar">
        <div style="display:flex;align-items:center;min-width:0;">
            <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div class="topbar-left">
                <div class="topbar-title">@yield('title', 'Dashboard')</div>
                <div class="breadcrumb">Admin / <span>@yield('title', 'Dashboard')</span></div>
            </div>
        </div>
        <div class="topbar-actions">@yield('topbar-actions')</div>
    </div>

    <div class="page-content">
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
        @if($errors->any())
            <div class="alert alert-error">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div style="display: flex; flex-direction: column; text-align: left;">
                    <strong style="margin-bottom: 0.25rem;">Terdapat kesalahan validasi:</strong>
                    <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.85rem; line-height: 1.4;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                {{ session('info') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                {{ session('warning') }}
            </div>
        @endif

        @yield('content')
    </div>
</main>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('open');
    overlay.classList.toggle('active');
    overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
    overlay.style.display = 'none';
}

// Close on escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeSidebar();
});
</script>
@stack('scripts')
</body>
</html>

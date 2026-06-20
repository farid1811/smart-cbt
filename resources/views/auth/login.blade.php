<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smart CBT Bimbel Plano — Sistem Computer Based Testing Premium">
    <title>Masuk — Smart CBT Bimbel Plano</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:      #1E2A78;
            --primary-dark: #141D54;
            --primary-light: #2A3B9E;
            --accent:       #F4C542;
            --accent-hover: #E5B22A;
            --bg:           #F8FAFC;
            --surface:      #FFFFFF;
            --surface2:     #F1F5F9;
            --text:         #0F172A;
            --text-muted:   #475569;
            --text-light:   #94A3B8;
            --border:       #E2E8F0;
            --error:        #EF4444;
            --error-soft:   #FEF2F2;
            --success:      #10B981;
            --success-soft: #ECFDF5;
            --radius:       8px;
            --radius-lg:    12px;
            --shadow:       0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
            --shadow-lg:    0 10px 25px -5px rgba(30,42,120,0.05), 0 8px 10px -6px rgba(30,42,120,0.03);
            --transition:   150ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        html, body {
            min-height: 100vh;
            background-color: var(--bg);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            -webkit-font-smoothing: antialiased;
            position: relative;
            overflow-y: auto;
        }

        /* ─── Login Card ─────────────────────────────────────────────────── */
        .login-container {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        /* Brand header */
        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .brand-logo {
            margin-bottom: 1rem;
        }

        .brand-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.03em;
            line-height: 1.1;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .brand-title span {
            color: var(--accent);
        }

        .brand-sub {
            font-size: 0.8125rem;
            color: var(--text-muted);
            margin-top: 0.375rem;
            font-weight: 500;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        /* Card */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2.5rem 2.25rem;
            box-shadow: var(--shadow-lg);
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.25rem;
        }

        .card-subtitle {
            font-size: 0.8125rem;
            color: var(--text-muted);
            margin-bottom: 1.75rem;
        }

        /* ─── Alerts ─────────────────────────────────────────────────────── */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            padding: 0.75rem 0.875rem;
            border-radius: var(--radius);
            font-size: 0.8125rem;
            margin-bottom: 1.25rem;
            border: 1px solid transparent;
            line-height: 1.5;
        }

        .alert svg { flex-shrink: 0; margin-top: 2px; }
        .alert-success { background: var(--success-soft); color: #065F46; border-color: #A7F3D0; }
        .alert-error   { background: var(--error-soft);   color: #991B1B; border-color: #FECACA; }

        /* ─── Form ───────────────────────────────────────────────────────── */
        .form-group { margin-bottom: 1.25rem; }

        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.4rem;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .form-input {
            display: block;
            width: 100%;
            padding: 0.65rem 0.85rem 0.65rem 2.35rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.9375rem;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            transition: all var(--transition);
            outline: none;
        }

        .form-input::placeholder { color: var(--text-light); font-size: 0.875rem; }

        .form-input:hover:not(:focus) { border-color: var(--text-light); }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30,42,120,0.08);
        }

        .form-input.is-invalid {
            border-color: var(--error);
        }

        .form-input.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239,68,68,0.08);
        }

        .form-error {
            font-size: 0.75rem;
            color: var(--error);
            margin-top: 0.35rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-light);
            padding: 0;
            display: flex;
            align-items: center;
            transition: color var(--transition);
        }

        .password-toggle:hover { color: var(--text-muted); }

        /* Remember */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.75rem;
        }

        .remember-row input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .remember-label {
            font-size: 0.8125rem;
            color: var(--text-muted);
            cursor: pointer;
            font-weight: 500;
            user-select: none;
        }

        /* Submit Button */
        .btn-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.75rem 1rem;
            background: var(--primary);
            border: none;
            border-radius: var(--radius);
            color: #fff;
            font-size: 0.9375rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all var(--transition);
            letter-spacing: -0.01em;
            border-bottom: 2px solid var(--primary-dark);
        }

        .btn-login:hover { background: var(--primary-light); }
        .btn-login:active { transform: scale(0.995); }
        .btn-login:disabled { opacity: 0.65; cursor: not-allowed; transform: none; }

        /* Demo credentials */
        .demo-info {
            margin-top: 1.5rem;
            padding: 0.85rem 1rem;
            background: #F1F3FB;
            border: 1px solid #D9DEEE;
            border-radius: var(--radius);
            font-size: 0.75rem;
            color: var(--primary);
            line-height: 1.6;
        }

        .demo-info .demo-title {
            font-weight: 700;
            margin-bottom: 0.35rem;
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .demo-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
        }

        .demo-cred {
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.72rem;
            background: rgba(30,42,120,0.06);
            padding: 0.15rem 0.45rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px dashed rgba(30,42,120,0.15);
        }

        .demo-cred:hover {
            background: var(--primary);
            color: #fff;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.75rem;
            color: var(--text-light);
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Brand Header -->
        <div class="brand-header">
            <img src="{{ asset('images/logo-plano.jpg') }}" alt="Bimbel Plano" style="max-height: 85px; width: auto; margin-bottom: 0.5rem; filter: drop-shadow(0 1px 3px rgba(30,42,120,0.05));">
            <div class="brand-sub" style="margin-top: 0.15rem;">Computer Based Test System</div>
        </div>

        <!-- Login Card -->
        <div class="card">
            <div class="card-title">{{ $isAdmin ? 'Masuk Administrator' : 'Masuk Ujian' }}</div>
            <div class="card-subtitle">{{ $isAdmin ? 'Gunakan kredensial admin Anda untuk mengelola sistem CBT.' : 'Silakan masuk menggunakan akun yang telah didaftarkan Admin.' }}</div>

            @if(session('success'))
                <div class="alert alert-success">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ $isAdmin ? route('admin.login.post') : route('login.post') }}" id="loginForm">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            value="{{ old('email') }}"
                            placeholder="email@contoh.com"
                            autocomplete="email"
                            required
                            autofocus
                        >
                    </div>
                    @error('email')
                        <div class="form-error">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword()" id="pwToggle" title="Tampilkan password">
                            <svg id="eyeIcon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Remember -->
                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember" class="remember-label">Ingat saya di perangkat ini</label>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login" id="submitBtn">
                    <svg id="btnIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    <span id="btnText">Masuk</span>
                </button>
            </form>

            <!-- Demo credentials -->
            <div class="demo-info">
                <div class="demo-title">🔑 Akun Demo</div>
                @if($isAdmin)
                    <div class="demo-row">
                        <span>Admin</span>
                        <span class="demo-cred" onclick="fillCredential('admin@smartcbt.com','password')" title="Klik untuk isi otomatis">admin@smartcbt.com / password</span>
                    </div>
                @else
                    <div class="demo-row">
                        <span>Peserta SKD</span>
                        <span class="demo-cred" onclick="fillCredential('peserta@smartcbt.com','password')" title="Klik untuk isi otomatis">peserta@smartcbt.com / password</span>
                    </div>
                    <div class="demo-row" style="margin-top:0.35rem;">
                        <span>Peserta SNBT</span>
                        <span class="demo-cred" onclick="fillCredential('peserta2@smartcbt.com','password')" title="Klik untuk isi otomatis">peserta2@smartcbt.com / password</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="login-footer">Smart CBT Bimbel Plano &copy; {{ date('Y') }} — Premium Testing System</div>
    </div>

    <script>
    // Form submit loading state
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const text = document.getElementById('btnText');
        const icon = document.getElementById('btnIcon');
        btn.disabled = true;
        text.textContent = 'Memproses...';
        icon.innerHTML = '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" stroke-dasharray="31" stroke-dashoffset="31"><animate attributeName="stroke-dashoffset" dur="1s" values="31;0;31" repeatCount="indefinite"/></circle>';
    });

    // Password visibility toggle
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    }

    // Demo credential fill
    function fillCredential(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
        document.getElementById('email').focus();
    }
    </script>
</body>
</html>

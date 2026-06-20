<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe Exam Browser Diperlukan — Smart CBT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        :root {
            --primary: #1E2A78;
            --primary-light: #2A3B9E;
            --accent: #ef4444;
            --bg-start: #0f172a;
            --bg-end: #1e1b4b;
            --card-bg: rgba(255, 255, 255, 0.04);
            --card-border: rgba(255, 255, 255, 0.08);
            --text: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, var(--bg-start) 0%, var(--bg-end) 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            overflow-x: hidden;
        }

        .container {
            max-width: 600px;
            width: 100%;
            text-align: center;
            position: relative;
        }

        .container::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            top: -150px;
            left: 50%;
            transform: translateX(-50%);
            z-index: -1;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            margin-bottom: 2rem;
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: var(--accent);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .icon-wrapper svg {
            width: 40px;
            height: 40px;
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
            background: linear-gradient(to right, #f8fafc, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        .btn-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 480px) {
            .btn-group {
                grid-template-columns: 1fr;
            }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.85rem 1.5rem;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(to bottom right, var(--primary-light), var(--primary));
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 14px rgba(30, 42, 120, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 42, 120, 0.6);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .instructions {
            text-align: left;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .instructions h3 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #f1f5f9;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .instructions ol {
            padding-left: 1.2rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .instructions li {
            margin-bottom: 0.5rem;
        }

        .footer {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon-wrapper">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h1>Safe Exam Browser Diperlukan</h1>
            <p>Ujian <strong>{{ $package->nama }}</strong> diatur dalam mode lockdown penuh dan hanya dapat diakses melalui aplikasi Safe Exam Browser (SEB).</p>
            
            <div class="btn-group">
                <a href="https://safeexambrowser.org/download_en.html" target="_blank" class="btn btn-secondary">
                    <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh SEB Resmi
                </a>
                <a href="{{ route('peserta.exam.sebConfig', $package) }}" class="btn btn-primary">
                    <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h7a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                    Unduh Config (.seb)
                </a>
            </div>

            <div class="instructions">
                <h3>
                    <svg style="width:16px; height:16px; color: var(--primary-light);" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Langkah-langkah Memulai Ujian:
                </h3>
                <ol>
                    <li>Jika belum memiliki Safe Exam Browser, klik tombol <strong>"Unduh SEB Resmi"</strong> dan instal aplikasi tersebut di komputer Anda.</li>
                    <li>Klik tombol <strong>"Unduh Config (.seb)"</strong> untuk mengunduh file konfigurasi khusus untuk ujian ini.</li>
                    <li>Buka file konfigurasi <code>.seb</code> yang baru saja diunduh. Aplikasi Safe Exam Browser akan otomatis terbuka dalam mode ujian terlockdown.</li>
                    <li>Silakan login dan kerjakan ujian Anda dengan aman dan tertib.</li>
                </ol>
            </div>
        </div>
        <div class="footer">
            &copy; 2026 Smart CBT. Didukung oleh teknologi Safe Exam Browser.
        </div>
    </div>
</body>
</html>

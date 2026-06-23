<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lembaga Bimbingan Belajar Premium untuk Persiapan SNBT, Sekolah Kedinasan, dan Seleksi CPNS. Kelas Tatap Muka & Tryout Online Terbaik.">
    <title>Bimbel Plano — Lembaga Bimbingan Belajar Premium</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E40AF',      /* Dark Blue */
                        'primary-dark': '#1e3a8a',
                        'primary-light': '#3b82f6',
                        accent: '#FACC15',       /* Yellow accent for SNBT */
                        'accent-dark': '#eab308',
                        background: '#F8FAFC',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #F8FAFC;
        }
        ::-webkit-scrollbar-thumb {
            background: #E2E8F0;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #CBD5E1;
        }

        /* Float animation for Hero Graphic */
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0.5deg); }
            50% { transform: translateY(-10px) rotate(-0.5deg); }
        }
        .float-anim {
            animation: float-slow 6s ease-in-out infinite;
        }

        /* Glow effects for cards */
        .premium-glow {
            position: relative;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .premium-glow:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px -10px rgba(30, 64, 175, 0.12);
        }

        /* Smooth accordion transition */
        details summary::-webkit-details-marker {
            display: none;
        }
        details[open] summary svg {
            transform: rotate(180deg);
        }
    </style>
</head>
<body class="text-slate-900 overflow-x-hidden antialiased bg-slate-50/50">

    <!-- ─── NAVBAR ────────────────────────────────────────────────────────── -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <div class="flex-shrink-0 transition-transform duration-300 hover:scale-102">
                    <a href="{{ route('landing') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo-plano.jpg') }}" alt="Bimbel Plano Logo" class="h-12 w-auto object-contain rounded-lg shadow-sm">
                        <div class="flex flex-col">
                            <span class="font-display font-extrabold text-xl tracking-tight text-primary leading-tight">BIMBEL PLANO</span>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Premium Education</span>
                        </div>
                    </a>
                </div>
                
                <!-- Desktop Nav Menu -->
                <nav class="hidden md:flex space-x-8">
                    <a href="#program" class="text-sm font-semibold text-slate-600 hover:text-primary transition-colors duration-200">Program Unggulan</a>
                    <a href="#alumni" class="text-sm font-semibold text-slate-600 hover:text-primary transition-colors duration-200">Alumni Sukses</a>
                    <a href="#testimoni" class="text-sm font-semibold text-slate-600 hover:text-primary transition-colors duration-200">Testimoni</a>
                    <a href="#faq" class="text-sm font-semibold text-slate-600 hover:text-primary transition-colors duration-200">FAQ</a>
                </nav>
                
                <!-- CTA Buttons -->
                <div class="hidden md:flex items-center space-x-3">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-primary hover:text-white px-4 py-2 border border-slate-200 rounded-xl hover:border-primary hover:bg-primary transition-all duration-200">Dashboard Admin</a>
                        @else
                            <a href="{{ route('admin.login') }}" class="text-sm font-semibold text-slate-500 hover:text-primary px-4 py-2 transition-all duration-200">Admin</a>
                        @endif
                        
                        @if(auth()->user()->isPeserta())
                            <a href="{{ route('peserta.dashboard') }}" class="bg-primary text-white text-sm font-bold px-5 py-2.5 rounded-xl hover:bg-primary-dark shadow-md shadow-blue-500/10 hover:shadow-lg transition-all duration-200">Dashboard Peserta</a>
                        @endif
                    @else
                        <a href="{{ route('admin.login') }}" class="text-sm font-semibold text-slate-500 hover:text-primary px-4 py-2 transition-all duration-200">Admin</a>
                        <a href="{{ route('login') }}" class="bg-primary text-white text-sm font-bold px-5 py-2.5 rounded-xl hover:bg-primary-dark shadow-md shadow-blue-500/10 hover:shadow-lg transition-all duration-200">Login Peserta</a>
                    @endauth
                </div>
                
                <!-- Mobile Nav Toggle -->
                <div class="md:hidden">
                    <button id="mobileMenuBtn" type="button" class="text-slate-600 hover:text-primary focus:outline-none p-2 rounded-md hover:bg-slate-50 transition-all duration-200">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Dropdown Nav Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-b border-slate-100 px-4 pt-2 pb-6 space-y-2">
            <a href="#program" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-600 hover:bg-slate-50 hover:text-primary transition-all">Program Unggulan</a>
            <a href="#alumni" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-600 hover:bg-slate-50 hover:text-primary transition-all">Alumni Sukses</a>
            <a href="#testimoni" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-600 hover:bg-slate-50 hover:text-primary transition-all">Testimoni</a>
            <a href="#faq" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-600 hover:bg-slate-50 hover:text-primary transition-all">FAQ</a>
            <div class="border-t border-slate-100 pt-4 flex flex-col gap-2 px-3">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-center text-sm font-semibold text-primary py-2 border border-slate-200 rounded-lg">Dashboard Admin</a>
                    @else
                        <a href="{{ route('admin.login') }}" class="text-center text-sm font-semibold text-slate-500 py-2">Admin</a>
                    @endif
                    
                    @if(auth()->user()->isPeserta())
                        <a href="{{ route('peserta.dashboard') }}" class="text-center bg-primary text-white text-sm font-bold py-2.5 rounded-lg">Dashboard Peserta</a>
                    @endif
                @else
                    <a href="{{ route('admin.login') }}" class="text-center text-sm font-semibold text-slate-500 py-2">Admin</a>
                    <a href="{{ route('login') }}" class="text-center bg-primary text-white text-sm font-bold py-2.5 rounded-lg">Login Peserta</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- ─── 1. HERO SECTION ────────────────────────────────────────────────── -->
    <section class="relative bg-white pt-12 pb-24 lg:pt-20 lg:pb-32 overflow-hidden border-b border-slate-100">
        <!-- Subtle Grid Background -->
        <div class="absolute inset-0 pointer-events-none -z-10 overflow-hidden">
            <svg class="absolute inset-0 h-full w-full stroke-slate-200/50 [mask-image:radial-gradient(100%_100%_at_top_right,white_80%,transparent_100%)]" aria-hidden="true">
                <defs>
                    <pattern id="grid-pattern" width="40" height="40" patternUnits="userSpaceOnUse" x="50%">
                        <path d="M.5 40V.5H40" fill="none" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid-pattern)" stroke-width="0" />
            </svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                <!-- Text Area -->
                <div class="lg:col-span-7 text-center lg:text-left space-y-6 sm:space-y-8 z-10">
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold bg-blue-50 border border-blue-100/50 text-primary uppercase tracking-wider">
                        🎓 Lembaga Bimbingan Belajar Premium
                    </span>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-slate-900 leading-tight tracking-tight">
                        Platform Tryout dan Bimbingan Belajar Terbaik untuk <span class="text-primary">SKD, CPNS, Kedinasan,</span> dan <span class="text-amber-500">SNBT</span>
                    </h1>
                    <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Bimbingan belajar untuk program SNBT, Sekolah Kedinasan, dan CPNS. Belajar tatap muka, latihan soal, serta tryout untuk membantu peserta mempersiapkan diri lebih baik.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        @auth
                            @if(auth()->user()->isPeserta())
                                <a href="{{ route('peserta.dashboard') }}" class="w-full sm:w-auto text-center bg-primary hover:bg-primary-dark text-white font-bold px-8 py-4 rounded-xl shadow-lg shadow-blue-500/10 hover:shadow-xl transition-all duration-200">
                                    Mulai Tryout
                                </a>
                                <a href="{{ route('peserta.dashboard') }}" class="w-full sm:w-auto text-center bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-semibold px-6 py-4 rounded-xl transition-all duration-200">
                                    Dashboard Ujian
                                </a>
                            @else
                                <a href="{{ route('admin.dashboard') }}" class="w-full sm:w-auto text-center bg-primary hover:bg-primary-dark text-white font-bold px-8 py-4 rounded-xl shadow-lg shadow-blue-500/10 hover:shadow-xl transition-all duration-200">
                                    Dashboard Admin
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="w-full sm:w-auto text-center bg-primary hover:bg-primary-dark text-white font-bold px-8 py-4 rounded-xl shadow-lg shadow-blue-500/10 hover:shadow-xl transition-all duration-200">
                                Mulai Tryout
                            </a>
                            <a href="{{ route('login') }}" class="w-full sm:w-auto text-center bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-semibold px-6 py-4 rounded-xl transition-all duration-200">
                                Login Peserta
                            </a>
                        @endauth
                        <a href="https://wa.me/6285233687867?text=Halo%20Admin%20Bimbel%20Plano,%20saya%20tertarik%20dengan%20program%20bimbingan..." target="_blank" class="w-full sm:w-auto text-center bg-emerald-50 text-emerald-700 border border-emerald-100 font-semibold px-6 py-4 rounded-xl hover:bg-emerald-100 transition-all duration-200 flex items-center justify-center gap-2 shadow-sm">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-2.02 0-3.659 1.64-3.659 3.66 0 .546.12 1.088.351 1.581l-.043-.092L8 14.5l3.29-.861.12.062a3.61 3.61 0 0 0 1.63.393c2.019 0 3.66-1.639 3.66-3.659s-1.634-3.663-3.659-3.663zm3.767 5.139c-.1.299-.5.599-.8.699-.3.1-.6.2-1.8-.3-1.4-.6-2.3-2-2.4-2.1-.1-.1-.8-1.1-.8-2.1s.5-1.5.7-1.7c.2-.2.4-.3.6-.3.1 0 .2 0 .3.1l.4.9c.1.2.1.4 0 .5l-.2.4c-.1.1-.2.3-.1.4.1.2.5.9 1.1 1.5.7.7 1.4 1 1.7 1.1.3.1.5 0 .6-.2.1-.2.6-.7.8-1 .2-.3.4-.2.6-.1l1.1.5c.2.1.4.3.4.4.1.3 0 1.1-.3 1.4zM12 2C6.477 2 2 6.477 2 12c0 1.891.524 3.66 1.434 5.178L2 22l4.981-1.393A9.954 9.954 0 0 0 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
                            Hubungi Admin
                        </a>
                    </div>
                </div>
                
                <!-- Illustration Area -->
                <div class="lg:col-span-5 relative">
                    <div class="float-anim">
                        <div class="relative mx-auto max-w-sm sm:max-w-md bg-gradient-to-br from-blue-50 to-indigo-50 border border-slate-200/50 p-4 rounded-3xl shadow-xl overflow-hidden group">
                            <img src="{{ asset('images/hero-education.png') }}" alt="Ilustrasi Siswa Belajar Modern" class="w-full h-auto object-cover rounded-2xl shadow-inner group-hover:scale-[1.02] transition-transform duration-500">
                            <!-- Overlay floating Badge -->
                            <div class="absolute bottom-6 left-6 bg-white/90 backdrop-blur-md border border-slate-100 rounded-2xl p-3.5 flex items-center gap-3 shadow-lg">
                                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-500 font-bold">
                                    ★
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Tingkat Kelulusan</span>
                                    <span class="font-display font-extrabold text-slate-800 text-sm">Alumni Lolos 98.6%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 2. SECTION PROGRAM UNGGULAN ────────────────────────────────────── -->
    <section id="program" class="py-20 sm:py-24 bg-slate-50 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-sm font-bold text-primary uppercase tracking-wider">Program Unggulan</span>
                <h2 class="text-3xl sm:text-4xl font-display font-extrabold text-slate-900">Program Bimbingan Belajar Terbaik</h2>
                <p class="text-slate-500 text-sm sm:text-base leading-relaxed">
                    Pilihan program belajar terbaik untuk membantu Anda meraih impian akademis dan karier masa depan.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 max-w-4xl mx-auto">
                <!-- Program 1: SKD CPNS & Kedinasan -->
                <div class="bg-white border border-slate-200/80 rounded-3xl p-8 premium-glow flex flex-col justify-between overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-full translate-x-8 -translate-y-8"></div>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-primary flex items-center justify-center font-display font-bold text-lg shadow-inner">SKD</div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-800">SKD CPNS & Kedinasan</h3>
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Program Seleksi ASN & Kedinasan</span>
                            </div>
                        </div>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            Mempersiapkan ujian seleksi Calon Aparatur Sipil Negara (CASN) dan masuk Sekolah Kedinasan seperti STAN, STIS, IPDN, dan lainnya.
                        </p>
                        <div class="border-t border-slate-100 pt-5">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-3">Materi Pembelajaran:</span>
                            <div class="flex flex-wrap gap-2">
                                <span class="bg-slate-100 text-slate-700 text-xs font-semibold px-3.5 py-1.5 rounded-lg border border-slate-200/50">TWK (Tes Wawasan Kebangsaan)</span>
                                <span class="bg-slate-100 text-slate-700 text-xs font-semibold px-3.5 py-1.5 rounded-lg border border-slate-200/50">TIU (Tes Inteligensia Umum)</span>
                                <span class="bg-slate-100 text-slate-700 text-xs font-semibold px-3.5 py-1.5 rounded-lg border border-slate-200/50">TKP (Tes Karakteristik Pribadi)</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Program 2: SNBT -->
                <div class="bg-white border-2 border-amber-400/80 rounded-3xl p-8 premium-glow flex flex-col justify-between overflow-hidden relative shadow-lg shadow-amber-500/5">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-amber-500/5 rounded-full translate-x-8 -translate-y-8"></div>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-display font-bold text-lg shadow-inner">SNBT</div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-800">SNBT</h3>
                                <span class="text-xs text-amber-500 font-bold uppercase tracking-wider">Program Masuk PTN</span>
                            </div>
                        </div>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            Mempersiapkan peserta menghadapi Seleksi Nasional Berdasarkan Tes untuk masuk perguruan tinggi negeri favorit di Indonesia.
                        </p>
                        <div class="border-t border-slate-100 pt-5">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-3">Materi Pembelajaran:</span>
                            <div class="flex flex-wrap gap-2">
                                <span class="bg-amber-50 text-amber-700 text-xs font-semibold px-3.5 py-1.5 rounded-lg border border-amber-100">TPS (Tes Potensi Skolastik)</span>
                                <span class="bg-amber-50 text-amber-700 text-xs font-semibold px-3.5 py-1.5 rounded-lg border border-amber-100">Literasi Bahasa Indonesia</span>
                                <span class="bg-amber-50 text-amber-700 text-xs font-semibold px-3.5 py-1.5 rounded-lg border border-amber-100">Literasi Bahasa Inggris</span>
                                <span class="bg-amber-50 text-amber-700 text-xs font-semibold px-3.5 py-1.5 rounded-lg border border-amber-100">Penalaran Matematika</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 3. SECTION KELULUSAN ALUMNI ────────────────────────────────────── -->
    <section id="alumni" class="py-20 sm:py-24 bg-white scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-sm font-bold text-primary uppercase tracking-wider">Keberhasilan Alumni</span>
                <h2 class="text-3xl sm:text-4xl font-display font-extrabold text-slate-900">Alumni yang Berhasil Meraih Impian</h2>
                <p class="text-slate-500 text-sm sm:text-base leading-relaxed">
                    Beberapa alumni peserta Bimbel Plano yang telah berhasil lulus di PTN dan instansi tujuan mereka.
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Alumni 1 -->
                <div class="bg-slate-50/50 border border-slate-200/50 rounded-2xl overflow-hidden premium-glow p-4 flex flex-col gap-4 text-center">
                    <img src="{{ asset('images/alumni-budi.png') }}" alt="Alumni Budi Santoso" class="w-full aspect-square object-cover rounded-xl shadow-sm">
                    <div class="space-y-1">
                        <h4 class="font-display font-bold text-slate-800 text-base">Budi Santoso</h4>
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-primary uppercase tracking-wider">PKN STAN</span>
                        <p class="text-xs text-slate-400 font-semibold pt-1">Lolos Angkatan 2024</p>
                    </div>
                </div>

                <!-- Alumni 2 -->
                <div class="bg-slate-50/50 border border-slate-200/50 rounded-2xl overflow-hidden premium-glow p-4 flex flex-col gap-4 text-center">
                    <img src="{{ asset('images/alumni-siti.png') }}" alt="Alumni Siti Aminah" class="w-full aspect-square object-cover rounded-xl shadow-sm">
                    <div class="space-y-1">
                        <h4 class="font-display font-bold text-slate-800 text-base">Siti Aminah</h4>
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-primary uppercase tracking-wider">IPDN</span>
                        <p class="text-xs text-slate-400 font-semibold pt-1">Lolos Angkatan 2025</p>
                    </div>
                </div>

                <!-- Alumni 3 -->
                <div class="bg-slate-50/50 border border-slate-200/50 rounded-2xl overflow-hidden premium-glow p-4 flex flex-col gap-4 text-center">
                    <img src="{{ asset('images/alumni-riki.png') }}" alt="Alumni Riki Wijaya" class="w-full aspect-square object-cover rounded-xl shadow-sm">
                    <div class="space-y-1">
                        <h4 class="font-display font-bold text-slate-800 text-base">Riki Wijaya</h4>
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 uppercase tracking-wider">Universitas Indonesia</span>
                        <p class="text-xs text-slate-400 font-semibold pt-1">Lolos Angkatan 2024</p>
                    </div>
                </div>

                <!-- Alumni 4 -->
                <div class="bg-slate-50/50 border border-slate-200/50 rounded-2xl overflow-hidden premium-glow p-4 flex flex-col gap-4 text-center">
                    <img src="{{ asset('images/alumni-ani.png') }}" alt="Alumni Ani Lestari" class="w-full aspect-square object-cover rounded-xl shadow-sm">
                    <div class="space-y-1">
                        <h4 class="font-display font-bold text-slate-800 text-base">Ani Lestari</h4>
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 uppercase tracking-wider">Universitas Syiah Kuala</span>
                        <p class="text-xs text-slate-400 font-semibold pt-1">Lolos Angkatan 2025</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 4. SECTION TESTIMONI ───────────────────────────────────────────── -->
    <section id="testimoni" class="py-20 sm:py-24 bg-slate-50 scroll-mt-20 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-sm font-bold text-primary uppercase tracking-wider">Testimoni Sukses</span>
                <h2 class="text-3xl sm:text-4xl font-display font-extrabold text-slate-900">Cerita Sukses Alumni</h2>
                <p class="text-slate-500 text-sm sm:text-base leading-relaxed">
                    Ungkapan langsung dari para peserta yang berhasil lolos ujian impian setelah belajar bersama Bimbel Plano.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                <!-- Testimoni 1 -->
                <div class="bg-white border border-slate-200/60 rounded-3xl p-8 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow duration-300">
                    <p class="text-slate-600 italic text-sm leading-relaxed mb-6">
                        "Belajar di Bimbel Plano sangat terarah. Latihan soalnya sangat relevan dengan tipe ujian asli dan tutor-tutornya memberikan tips penyelesaian cepat yang tidak diajarkan di sekolah. Sangat membantu saya hingga lolos STAN!"
                    </p>
                    <div class="flex items-center gap-4 border-t border-slate-100 pt-6">
                        <img src="{{ asset('images/alumni-budi.png') }}" alt="Budi Santoso Avatar" class="w-12 h-12 rounded-full object-cover shadow-sm border border-slate-200">
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Budi Santoso</h4>
                            <span class="text-xs text-primary font-bold uppercase tracking-wider">Alumni Lolos STAN</span>
                        </div>
                    </div>
                </div>

                <!-- Testimoni 2 -->
                <div class="bg-white border border-slate-200/60 rounded-3xl p-8 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow duration-300">
                    <p class="text-slate-600 italic text-sm leading-relaxed mb-6">
                        "Metode bimbingan tatap muka Bimbel Plano membuat materi sesulit apa pun menjadi mudah dipahami. Ditambah lagi dengan tryout online yang rutin melatih mental dan kecepatan pengerjaan saya."
                    </p>
                    <div class="flex items-center gap-4 border-t border-slate-100 pt-6">
                        <img src="{{ asset('images/alumni-siti.png') }}" alt="Siti Aminah Avatar" class="w-12 h-12 rounded-full object-cover shadow-sm border border-slate-200">
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Siti Aminah</h4>
                            <span class="text-xs text-primary font-bold uppercase tracking-wider">Alumni Lolos IPDN</span>
                        </div>
                    </div>
                </div>

                <!-- Testimoni 3 -->
                <div class="bg-white border border-slate-200/60 rounded-3xl p-8 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow duration-300">
                    <p class="text-slate-600 italic text-sm leading-relaxed mb-6">
                        "Tryout online dengan sistem penanda ragu-ragu dan pembahasan detail di platform Smart CBT Bimbel Plano melatih kesiapan taktis saya. Saya jadi sangat percaya diri di hari pelaksanaan ujian."
                    </p>
                    <div class="flex items-center gap-4 border-t border-slate-100 pt-6">
                        <img src="{{ asset('images/alumni-riki.png') }}" alt="Riki Wijaya Avatar" class="w-12 h-12 rounded-full object-cover shadow-sm border border-slate-200">
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Riki Wijaya</h4>
                            <span class="text-xs text-primary font-bold uppercase tracking-wider">Alumni Lolos UI</span>
                        </div>
                    </div>
                </div>

                <!-- Testimoni 4 -->
                <div class="bg-white border border-slate-200/60 rounded-3xl p-8 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow duration-300">
                    <p class="text-slate-600 italic text-sm leading-relaxed mb-6">
                        "Pembahasan soal-soal di Bimbel Plano disajikan secara sistematis. Saya berhasil lolos di Universitas Syiah Kuala berkat bimbingan intensif dan latihan intensif tatap muka di sini."
                    </p>
                    <div class="flex items-center gap-4 border-t border-slate-100 pt-6">
                        <img src="{{ asset('images/alumni-ani.png') }}" alt="Ani Lestari Avatar" class="w-12 h-12 rounded-full object-cover shadow-sm border border-slate-200">
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Ani Lestari</h4>
                            <span class="text-xs text-primary font-bold uppercase tracking-wider">Alumni Lolos USK</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 5. FAQ SECTION ────────────────────────────────────────────────── -->
    <section id="faq" class="py-20 sm:py-24 bg-white scroll-mt-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 space-y-3">
                <span class="text-sm font-bold text-primary uppercase tracking-wider">FAQ</span>
                <h2 class="text-3xl sm:text-4xl font-display font-extrabold text-slate-900">Pertanyaan Umum</h2>
                <p class="text-slate-500 text-sm sm:text-base leading-relaxed">
                    Berikut adalah jawaban atas beberapa pertanyaan umum yang sering ditanyakan mengenai Bimbel Plano.
                </p>
            </div>
            
            <div class="space-y-4">
                <!-- FAQ 1 -->
                <details class="group bg-slate-50 border border-slate-200/60 rounded-2xl [&_summary::-webkit-details-marker]:hidden" open>
                    <summary class="flex items-center justify-between p-6 cursor-pointer focus:outline-none">
                        <h3 class="font-bold text-slate-800 text-sm sm:text-base">Apakah tersedia kelas tatap muka?</h3>
                        <span class="ml-1.5 flex-shrink-0 rounded-full bg-white p-1.5 text-slate-500 border border-slate-200 shadow-sm transition-transform duration-200 group-open:rotate-180">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </span>
                    </summary>
                    <div class="px-6 pb-6 text-sm text-slate-600 leading-relaxed border-t border-slate-200/20 pt-4">
                        Ya, Bimbel Plano menyediakan kelas bimbingan tatap muka secara langsung dengan tutor berpengalaman di Kota Langsa untuk memastikan pemahaman materi yang mendalam.
                    </div>
                </details>

                <!-- FAQ 2 -->
                <details class="group bg-slate-50 border border-slate-200/60 rounded-2xl [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between p-6 cursor-pointer focus:outline-none">
                        <h3 class="font-bold text-slate-800 text-sm sm:text-base">Apakah tersedia tryout online?</h3>
                        <span class="ml-1.5 flex-shrink-0 rounded-full bg-white p-1.5 text-slate-500 border border-slate-200 shadow-sm transition-transform duration-200 group-open:rotate-180">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </span>
                    </summary>
                    <div class="px-6 pb-6 text-sm text-slate-600 leading-relaxed border-t border-slate-200/20 pt-4">
                        Ya, seluruh peserta bimbingan mendapatkan akses penuh ke platform Tryout Online Smart CBT kami yang dilengkapi dengan batasan waktu real-time dan analisis pembahasan lengkap.
                    </div>
                </details>

                <!-- FAQ 3 -->
                <details class="group bg-slate-50 border border-slate-200/60 rounded-2xl [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between p-6 cursor-pointer focus:outline-none">
                        <h3 class="font-bold text-slate-800 text-sm sm:text-base">Bagaimana cara mendaftar?</h3>
                        <span class="ml-1.5 flex-shrink-0 rounded-full bg-white p-1.5 text-slate-500 border border-slate-200 shadow-sm transition-transform duration-200 group-open:rotate-180">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </span>
                    </summary>
                    <div class="px-6 pb-6 text-sm text-slate-600 leading-relaxed border-t border-slate-200/20 pt-4">
                        Anda dapat mendaftar langsung dengan menghubungi Admin via WhatsApp (085233687867) atau mengunjungi kantor utama kami di Kota Langsa.
                    </div>
                </details>

                <!-- FAQ 4 -->
                <details class="group bg-slate-50 border border-slate-200/60 rounded-2xl [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between p-6 cursor-pointer focus:outline-none">
                        <h3 class="font-bold text-slate-800 text-sm sm:text-base">Apakah tersedia pembahasan soal?</h3>
                        <span class="ml-1.5 flex-shrink-0 rounded-full bg-white p-1.5 text-slate-500 border border-slate-200 shadow-sm transition-transform duration-200 group-open:rotate-180">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </span>
                    </summary>
                    <div class="px-6 pb-6 text-sm text-slate-600 leading-relaxed border-t border-slate-200/20 pt-4">
                        Tentu saja. Setiap selesai mengerjakan tryout online, sistem akan langsung menampilkan analisis hasil beserta pembahasan detail untuk setiap soal.
                    </div>
                </details>

                <!-- FAQ 5 -->
                <details class="group bg-slate-50 border border-slate-200/60 rounded-2xl [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between p-6 cursor-pointer focus:outline-none">
                        <h3 class="font-bold text-slate-800 text-sm sm:text-base">Apakah tersedia kelas SNBT dan Kedinasan?</h3>
                        <span class="ml-1.5 flex-shrink-0 rounded-full bg-white p-1.5 text-slate-500 border border-slate-200 shadow-sm transition-transform duration-200 group-open:rotate-180">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </span>
                    </summary>
                    <div class="px-6 pb-6 text-sm text-slate-600 leading-relaxed border-t border-slate-200/20 pt-4">
                        Ya, kami memiliki program khusus baik untuk persiapan SNBT masuk PTN maupun persiapan tes SKD untuk seleksi Sekolah Kedinasan dan seleksi penerimaan CPNS.
                    </div>
                </details>
            </div>
        </div>
    </section>

    <!-- ─── 6. FOOTER ──────────────────────────────────────────────────────── -->
    <footer class="bg-slate-900 text-slate-300 pt-16 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-12">
                <!-- Brand details -->
                <div class="lg:col-span-4 space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo-plano.jpg') }}" alt="Bimbel Plano Logo" class="h-12 w-auto object-contain rounded-lg">
                        <div class="flex flex-col">
                            <span class="font-display font-extrabold text-xl tracking-tight text-white leading-tight">BIMBEL PLANO</span>
                            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Premium Education</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Lembaga bimbingan belajar profesional yang membantu siswa lolos SNBT, CPNS, dan Sekolah Kedinasan melalui pembelajaran tatap muka, latihan soal, dan tryout online berkualitas.
                    </p>
                    <div class="pt-2 text-sm text-slate-400 space-y-2.5">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary-light flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Jana Residence No.2, Lr. Petua Usman, TM Bahrum, Kota Langsa, Provinsi Aceh</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary-light flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>085233687867</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary-light flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                            <a href="mailto:bimbelplano@gmail.com" class="hover:text-white transition-colors">bimbelplano@gmail.com</a>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary-light flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <a href="https://instagram.com/bimbelplano_" target="_blank" class="hover:text-white transition-colors">@bimbelplano_</a>
                        </div>
                    </div>
                </div>
                
                <!-- Google Maps -->
                <div class="lg:col-span-5 space-y-4">
                    <h3 class="font-display font-bold text-white text-base">Lokasi Kami</h3>
                    <div class="w-full rounded-2xl overflow-hidden border border-slate-800 shadow-lg h-60">
                        <iframe src="https://maps.google.com/maps?q=TM%20Bahrum,%20Kota%20Langsa,%20Aceh&t=&z=14&ie=UTF8&iwloc=&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="lg:col-span-3 space-y-4">
                    <h3 class="font-display font-bold text-white text-base">Navigasi</h3>
                    <div class="flex flex-col gap-2.5 text-sm text-slate-400">
                        <a href="#program" class="hover:text-white transition-colors">Program Unggulan</a>
                        <a href="#alumni" class="hover:text-white transition-colors">Alumni Sukses</a>
                        <a href="#testimoni" class="hover:text-white transition-colors">Testimoni</a>
                        <a href="#faq" class="hover:text-white transition-colors">FAQ</a>
                        <div class="pt-4 border-t border-slate-800 flex flex-col gap-2">
                            <a href="{{ route('login') }}" class="text-xs font-bold text-white uppercase tracking-wider hover:text-primary-light transition-colors">Dashboard Peserta</a>
                            <a href="{{ route('admin.login') }}" class="text-xs font-bold text-slate-500 uppercase tracking-wider hover:text-primary-light transition-colors">Admin Login</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-slate-800 pt-8 mt-8 text-center text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-4">
                <span>&copy; 2026 Bimbel Plano. Hak Cipta Dilindungi Undang-Undang.</span>
                <span class="flex gap-4">
                    <a href="https://instagram.com/bimbelplano_" target="_blank" class="hover:text-slate-400 transition-colors">Instagram</a>
                    <a href="https://wa.me/6285233687867" target="_blank" class="hover:text-slate-400 transition-colors">WhatsApp</a>
                </span>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/6285233687867?text=Halo%20Admin%20Bimbel%20Plano,%20saya%20ingin%20bertanya%20tentang%20program%20bimbingan..." target="_blank" class="fixed bottom-6 right-6 z-50 bg-emerald-500 hover:bg-emerald-600 text-white p-4 rounded-full shadow-2xl hover:scale-110 active:scale-95 transition-all duration-200 flex items-center justify-center border border-emerald-400/20" title="Hubungi Kami via WhatsApp">
        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.503-5.728-1.46L0 24zm6.59-4.846c1.6.95 3.1 1.45 4.8 1.45 5.5 0 10-4.5 10-10s-4.5-10-10-10-10 4.5-10 10c0 1.9.5 3.7 1.5 5.3l-.99 3.6 3.69-.95zm12.38-5.32c-.3-.15-1.79-.88-2.07-.98-.27-.1-.47-.15-.67.15-.2.3-.77.98-.95 1.18-.18.2-.35.23-.65.08-.3-.15-1.28-.47-2.45-1.51-.91-.81-1.53-1.82-1.71-2.12-.18-.3-.02-.47.13-.62.14-.14.3-.35.45-.53.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.07-.15-.67-1.62-.92-2.22-.24-.57-.49-.49-.67-.5-.18-.01-.38-.01-.58-.01-.2 0-.52.07-.79.37-.27.3-1.03 1-1.03 2.44 0 1.44 1.05 2.84 1.2 3.04.15.2 2.07 3.16 5.01 4.43.7.3 1.25.48 1.68.62.7.22 1.34.19 1.84.11.56-.08 1.79-.73 2.04-1.44.25-.7.25-1.3.17-1.44-.08-.13-.3-.23-.6-.38z"/></svg>
    </a>

    <!-- Mobile Menu Script -->
    <script>
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>

</body>
</html>

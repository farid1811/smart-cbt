<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Platform Tryout dan Simulasi CAT Terbaik untuk SKD, CPNS, dan SNBT Bimbel Plano.">
    <title>Bimbel Plano — Smart CBT Premium</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E40AF',
                        'primary-dark': '#1E3A8A',
                        'primary-light': '#3B82F6',
                        accent: '#FACC15',
                        'accent-dark': '#EAB308',
                        background: '#F8FAFC',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
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

        /* Easing Curves */
        .ease-premium {
            transition-timing-function: cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Ambient floating circles */
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0.5deg); }
            50% { transform: translateY(-12px) rotate(-0.5deg); }
        }
        .float-anim {
            animation: float-slow 7s ease-in-out infinite;
        }

        @keyframes pulse-slow-1 {
            0%, 100% { transform: scale(1) translate(0px, 0px); opacity: 0.25; }
            50% { transform: scale(1.1) translate(20px, -15px); opacity: 0.35; }
        }
        @keyframes pulse-slow-2 {
            0%, 100% { transform: scale(1.05) translate(0px, 0px); opacity: 0.35; }
            50% { transform: scale(0.95) translate(-30px, 20px); opacity: 0.2; }
        }
        .animate-pulse-slow-1 {
            animation: pulse-slow-1 12s ease-in-out infinite;
        }
        .animate-pulse-slow-2 {
            animation: pulse-slow-2 16s ease-in-out infinite;
        }

        /* Button micro-interactions */
        .btn-premium {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -8px rgba(30, 64, 175, 0.25);
        }
        .btn-premium:active {
            transform: scale(0.96) translateY(-0.5px);
            box-shadow: 0 6px 12px -6px rgba(30, 64, 175, 0.25);
        }

        /* Card lifts & custom spotlight glows (Vercel Style) */
        .card-premium {
            background-color: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.7);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }
        .card-premium:hover {
            transform: translateY(-6px);
            border-color: rgba(30, 64, 175, 0.25);
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08);
        }

        .card-spotlight {
            position: relative;
            isolation: isolate;
        }
        .card-spotlight::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1.5px;
            background: radial-gradient(
                450px circle at var(--mouse-x, 0) var(--mouse-y, 0),
                rgba(30, 64, 175, 0.22),
                transparent 50%
            );
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.5s ease;
            z-index: 10;
        }
        .card-spotlight::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: radial-gradient(
                350px circle at var(--mouse-x, 0) var(--mouse-y, 0),
                rgba(30, 64, 175, 0.025),
                transparent 45%
            );
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.5s ease;
            z-index: -1;
        }
        .card-spotlight:hover::before,
        .card-spotlight:hover::after {
            opacity: 1;
        }

        /* Viewport Scroll Entrance animations */
        .reveal-element {
            opacity: 0;
            transform: translateY(28px) scale(0.98);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal-element.reveal-visible {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    </style>
</head>
<body class="text-slate-900 overflow-x-hidden antialiased">

    <!-- ─── NAVBAR ────────────────────────────────────────────────────────── -->
    <header id="navHeader" class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-transparent transition-all duration-300 ease-premium">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div id="navContainer" class="flex items-center justify-between h-16 sm:h-20 transition-all duration-300">
                <!-- Brand Logo -->
                <div class="flex-shrink-0 transition-transform duration-300 hover:scale-105 active:scale-95">
                    <a href="{{ route('landing') }}" class="flex items-center">
                        <img src="{{ asset('images/logo-plano.jpg') }}" alt="Bimbel Plano" class="h-10 sm:h-12 w-auto object-contain">
                    </a>
                </div>
                
                <!-- Desktop Nav Menu -->
                <nav class="hidden md:flex space-x-8 lg:space-x-10">
                    <a href="#fitur" class="text-sm font-semibold text-slate-600 hover:text-primary transition-all duration-300 ease-in-out relative py-2 after:content-[''] after:absolute after:w-full after:scale-x-0 after:h-[2px] after:bottom-0 after:left-0 after:bg-primary after:origin-bottom-right after:transition-transform after:duration-300 hover:after:scale-x-100 hover:after:origin-bottom-left">Fitur</a>
                    <a href="#kategori" class="text-sm font-semibold text-slate-600 hover:text-primary transition-all duration-300 ease-in-out relative py-2 after:content-[''] after:absolute after:w-full after:scale-x-0 after:h-[2px] after:bottom-0 after:left-0 after:bg-primary after:origin-bottom-right after:transition-transform after:duration-300 hover:after:scale-x-100 hover:after:origin-bottom-left">Kategori</a>
                    <a href="#cara-kerja" class="text-sm font-semibold text-slate-600 hover:text-primary transition-all duration-300 ease-in-out relative py-2 after:content-[''] after:absolute after:w-full after:scale-x-0 after:h-[2px] after:bottom-0 after:left-0 after:bg-primary after:origin-bottom-right after:transition-transform after:duration-300 hover:after:scale-x-100 hover:after:origin-bottom-left">Cara Kerja</a>
                    <a href="#screenshots" class="text-sm font-semibold text-slate-600 hover:text-primary transition-all duration-300 ease-in-out relative py-2 after:content-[''] after:absolute after:w-full after:scale-x-0 after:h-[2px] after:bottom-0 after:left-0 after:bg-primary after:origin-bottom-right after:transition-transform after:duration-300 hover:after:scale-x-100 hover:after:origin-bottom-left">Preview</a>
                    <a href="#faq" class="text-sm font-semibold text-slate-600 hover:text-primary transition-all duration-300 ease-in-out relative py-2 after:content-[''] after:absolute after:w-full after:scale-x-0 after:h-[2px] after:bottom-0 after:left-0 after:bg-primary after:origin-bottom-right after:transition-transform after:duration-300 hover:after:scale-x-100 hover:after:origin-bottom-left">FAQ</a>
                </nav>
                
                <!-- CTA Buttons -->
                <div class="hidden md:flex items-center space-x-3 sm:space-x-4">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn-premium text-xs lg:text-sm font-semibold text-primary hover:text-white px-3 py-2 border border-slate-200 rounded-lg hover:border-primary hover:bg-primary">Dashboard Admin</a>
                        @else
                            <a href="{{ route('admin.login') }}" class="btn-premium text-xs lg:text-sm font-semibold text-primary hover:text-white px-3 py-2 border border-slate-200 rounded-lg hover:border-primary hover:bg-primary">Admin</a>
                        @endif
                        
                        @if(auth()->user()->isPeserta())
                            <a href="{{ route('peserta.dashboard') }}" class="btn-premium bg-primary text-white text-xs lg:text-sm font-bold px-5 py-2.5 rounded-lg hover:bg-primary-dark shadow-sm">Dashboard Peserta</a>
                        @endif
                    @else
                        <a href="{{ route('admin.login') }}" class="btn-premium text-xs lg:text-sm font-semibold text-primary hover:text-white px-3 py-2 border border-slate-200 rounded-lg hover:border-primary hover:bg-primary">Admin</a>
                        <a href="{{ route('login') }}" class="btn-premium bg-primary text-white text-xs lg:text-sm font-bold px-5 py-2.5 rounded-lg hover:bg-primary-dark shadow-sm">Login Peserta</a>
                    @endauth
                </div>
                
                <!-- Mobile Nav Toggle -->
                <div class="md:hidden">
                    <button id="mobileMenuBtn" type="button" class="text-slate-600 hover:text-primary focus:outline-none p-2 rounded-md hover:bg-slate-50 transition-all duration-300" aria-label="Toggle Menu">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Dropdown Nav Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-b border-slate-100 px-4 pt-2 pb-4 space-y-2 transition-all duration-300 ease-premium">
            <a href="#fitur" class="block px-3 py-2 rounded-md text-base font-semibold text-slate-600 hover:bg-slate-50 hover:text-primary transition-all">Fitur</a>
            <a href="#kategori" class="block px-3 py-2 rounded-md text-base font-semibold text-slate-600 hover:bg-slate-50 hover:text-primary transition-all">Kategori</a>
            <a href="#cara-kerja" class="block px-3 py-2 rounded-md text-base font-semibold text-slate-600 hover:bg-slate-50 hover:text-primary transition-all">Cara Kerja</a>
            <a href="#screenshots" class="block px-3 py-2 rounded-md text-base font-semibold text-slate-600 hover:bg-slate-50 hover:text-primary transition-all">Preview</a>
            <a href="#faq" class="block px-3 py-2 rounded-md text-base font-semibold text-slate-600 hover:bg-slate-50 hover:text-primary transition-all">FAQ</a>
            <div class="border-t border-slate-100 pt-3 flex items-center justify-between px-3 w-full">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-primary hover:underline transition-all">Dashboard Admin</a>
                    @else
                        <a href="{{ route('admin.login') }}" class="text-sm font-semibold text-primary hover:underline transition-all">Admin Panel</a>
                    @endif
                    
                    @if(auth()->user()->isPeserta())
                        <a href="{{ route('peserta.dashboard') }}" class="bg-primary text-white text-sm font-bold px-4 py-2 rounded-md hover:bg-primary-dark transition-all">Dashboard Peserta</a>
                    @endif
                @else
                    <a href="{{ route('admin.login') }}" class="text-sm font-semibold text-primary hover:underline transition-all">Admin Panel</a>
                    <a href="{{ route('login') }}" class="bg-primary text-white text-sm font-bold px-4 py-2 rounded-md hover:bg-primary-dark transition-all">Login Peserta</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- ─── 1. HERO SECTION ────────────────────────────────────────────────── -->
    <section class="relative bg-gradient-to-b from-white via-slate-50 to-slate-100/30 pt-12 pb-20 sm:pb-28 overflow-hidden">
        <!-- Premium Grid Background (Vercel Style) -->
        <div class="absolute inset-0 pointer-events-none -z-10 overflow-hidden">
            <svg class="absolute inset-0 h-full w-full stroke-slate-200/40 [mask-image:radial-gradient(100%_100%_at_top,white_70%,transparent_100%)]" aria-hidden="true">
                <defs>
                    <pattern id="grid-pattern" width="40" height="40" patternUnits="userSpaceOnUse" x="50%">
                        <path d="M.5 40V.5H40" fill="none" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid-pattern)" stroke-width="0" />
            </svg>
        </div>

        <!-- Floating Subtle Blur Backgrounds -->
        <div class="absolute -top-12 -left-12 w-80 h-80 bg-blue-200 rounded-full blur-3xl opacity-30 animate-pulse-slow-1 pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-yellow-100/60 rounded-full blur-3xl opacity-30 animate-pulse-slow-2 pointer-events-none"></div>
        
        <!-- Subtle Ambient floating shapes -->
        <div class="absolute top-1/4 left-10 w-20 h-20 border border-blue-200/30 rounded-3xl blur-[1px] float-anim pointer-events-none opacity-20 hidden lg:block" style="animation-duration: 8s;"></div>
        <div class="absolute bottom-12 right-12 w-16 h-16 border border-yellow-300/20 rounded-full blur-[1px] float-anim pointer-events-none opacity-25 hidden lg:block" style="animation-duration: 11s;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                <!-- Text Area -->
                <div class="lg:col-span-7 text-center lg:text-left space-y-6 sm:space-y-8 z-10 opacity-0 translate-y-8 transition-all duration-[800ms] ease-premium" id="heroText">
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold bg-blue-50 border border-blue-100 text-primary uppercase tracking-wider transition-all duration-300 hover:bg-blue-100">
                        ✨ Platform Premium Bimbel Plano
                    </span>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight tracking-tight max-w-2xl mx-auto lg:mx-0">
                        Platform Tryout dan Simulasi CAT Terbaik untuk <span class="text-primary relative inline-block">SKD, CPNS<span class="absolute w-full h-1.5 bottom-1 left-0 bg-accent/30 -z-10 rounded"></span></span>, dan <span class="text-primary relative inline-block">SNBT<span class="absolute w-full h-1.5 bottom-1 left-0 bg-accent/30 -z-10 rounded"></span></span>
                    </h1>
                    <p class="text-base sm:text-lg text-slate-600 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        Latihan soal, tryout online, analisis nilai, dan simulasi CAT yang dirancang untuk membantu peserta mencapai hasil terbaik.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        @auth
                            @if(auth()->user()->isPeserta())
                                <a href="{{ route('peserta.dashboard') }}" class="btn-premium w-full sm:w-auto text-center bg-primary text-white font-bold px-8 py-3.5 rounded-xl hover:bg-primary-dark shadow-md hover:shadow-xl">
                                    Mulai Tryout
                                </a>
                                <a href="{{ route('peserta.dashboard') }}" class="btn-premium w-full sm:w-auto text-center bg-white text-slate-700 border border-slate-200 font-semibold px-6 py-3.5 rounded-xl hover:bg-slate-50 hover:border-slate-300 hover:shadow">
                                    Dashboard Ujian
                                </a>
                            @else
                                <a href="{{ route('admin.dashboard') }}" class="btn-premium w-full sm:w-auto text-center bg-primary text-white font-bold px-8 py-3.5 rounded-xl hover:bg-primary-dark shadow-md hover:shadow-xl">
                                    Dashboard Admin
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn-premium w-full sm:w-auto text-center bg-primary text-white font-bold px-8 py-3.5 rounded-xl hover:bg-primary-dark shadow-md hover:shadow-xl">
                                Mulai Tryout
                            </a>
                            <a href="{{ route('login') }}" class="btn-premium w-full sm:w-auto text-center bg-white text-slate-700 border border-slate-200 font-semibold px-6 py-3.5 rounded-xl hover:bg-slate-50 hover:border-slate-300 hover:shadow">
                                Login Peserta
                            </a>
                        @endauth
                        <a href="https://wa.me/628123456789" target="_blank" class="btn-premium w-full sm:w-auto text-center bg-emerald-50 text-emerald-700 border border-emerald-100 font-semibold px-6 py-3.5 rounded-xl hover:bg-emerald-100 hover:shadow flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-2.02 0-3.659 1.64-3.659 3.66 0 .546.12 1.088.351 1.581l-.043-.092L8 14.5l3.29-.861.12.062a3.61 3.61 0 0 0 1.63.393c2.019 0 3.66-1.639 3.66-3.659s-1.634-3.663-3.659-3.663zm3.767 5.139c-.1.299-.5.599-.8.699-.3.1-.6.2-1.8-.3-1.4-.6-2.3-2-2.4-2.1-.1-.1-.8-1.1-.8-2.1s.5-1.5.7-1.7c.2-.2.4-.3.6-.3.1 0 .2 0 .3.1l.4.9c.1.2.1.4 0 .5l-.2.4c-.1.1-.2.3-.1.4.1.2.5.9 1.1 1.5.7.7 1.4 1 1.7 1.1.3.1.5 0 .6-.2.1-.2.6-.7.8-1 .2-.3.4-.2.6-.1l1.1.5c.2.1.4.3.4.4.1.3 0 1.1-.3 1.4zM12 2C6.477 2 2 6.477 2 12c0 1.891.524 3.66 1.434 5.178L2 22l4.981-1.393A9.954 9.954 0 0 0 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
                            Hubungi Admin
                        </a>
                    </div>
                </div>
                
                <!-- Mockup/Illustration Area (with Float and 3D Tilt) -->
                <div class="lg:col-span-5 relative opacity-0 translate-y-8 transition-all duration-[1000ms] ease-premium" id="heroMockup">
                    <div class="float-anim">
                        <!-- App Card Mockup -->
                        <div id="heroMockupCard" class="relative bg-white border border-slate-200/80 rounded-2xl shadow-xl overflow-hidden max-w-sm sm:max-w-md mx-auto hover:shadow-2xl transition-all duration-500 ease-out transform cursor-default">
                            <!-- Top header -->
                            <div class="bg-primary text-white p-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-red-400"></span>
                                    <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                                    <span class="w-3 h-3 rounded-full bg-green-400"></span>
                                </div>
                                <span class="text-[9px] sm:text-[10px] font-bold bg-white/10 px-2 py-0.5 rounded tracking-wide">CAT BKN SIMULASI</span>
                            </div>
                            
                            <!-- Screen body mockup -->
                            <div class="p-6 space-y-4 bg-slate-50 text-xs">
                                <div class="flex justify-between items-center bg-white p-3 border border-slate-100 rounded-lg">
                                    <div>
                                        <span class="text-slate-400 block text-[10px]">PAKET UJIAN</span>
                                        <strong class="text-primary font-bold">Tryout SKD CPNS #1</strong>
                                    </div>
                                    <div class="bg-red-50 text-red-600 px-2.5 py-1 rounded font-bold">59:58</div>
                                </div>
                                
                                <!-- Question container -->
                                <div class="bg-white p-4 border border-slate-100 rounded-lg space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="bg-primary text-white w-6 h-6 rounded flex items-center justify-center font-bold">1</span>
                                        <span class="text-slate-400 font-semibold text-[10px]">TES WAWASAN KEBANGSAAN</span>
                                    </div>
                                    <p class="text-slate-700 font-semibold leading-relaxed">
                                        Pancasila sebagai dasar negara Republik Indonesia disahkan oleh PPKI pada tanggal...
                                    </p>
                                </div>
                                
                                <!-- Options -->
                                <div class="space-y-2">
                                    <div class="bg-white p-2.5 border border-slate-100 rounded-lg flex items-center gap-3">
                                        <span class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center font-bold">A</span>
                                        <span class="text-slate-600">1 Juni 1945</span>
                                    </div>
                                    <div class="bg-blue-50 border border-primary/30 p-2.5 rounded-lg flex items-center gap-3">
                                        <span class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center font-bold">B</span>
                                        <span class="text-slate-700 font-semibold">18 Agustus 1945</span>
                                    </div>
                                    <div class="bg-white p-2.5 border border-slate-100 rounded-lg flex items-center gap-3">
                                        <span class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center font-bold">C</span>
                                        <span class="text-slate-600">17 Agustus 1945</span>
                                    </div>
                                </div>
                                
                                <!-- Grid and Status -->
                                <div class="grid grid-cols-5 gap-1.5 pt-2">
                                    <span class="bg-primary text-white p-2 text-center rounded font-bold shadow-sm">1</span>
                                    <span class="bg-slate-200 text-slate-600 p-2 text-center rounded font-semibold">2</span>
                                    <span class="bg-yellow-400 text-white p-2 text-center rounded font-bold">3</span>
                                    <span class="bg-slate-100 border border-slate-200 text-slate-400 p-2 text-center rounded font-semibold">4</span>
                                    <span class="bg-slate-100 border border-slate-200 text-slate-400 p-2 text-center rounded font-semibold">5</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 2. FITUR UNGGULAN ──────────────────────────────────────────────── -->
    <section id="fitur" class="py-20 sm:py-24 bg-white scroll-mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4 reveal-element">
                <span class="text-sm font-bold text-primary uppercase tracking-wider">Keunggulan Smart CBT</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900">
                    Fitur Premium untuk Efisiensi & Akurasi Belajar
                </h2>
                <p class="text-slate-500 text-sm sm:text-base">
                    Kami menghadirkan ekosistem ujian online yang handal dengan fitur lengkap setara tes CAT BKN sesungguhnya.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Fitur 1 -->
                <div class="reveal-element p-6 rounded-2xl card-premium card-spotlight space-y-4 group overflow-hidden" data-delay="0">
                    <div class="w-12 h-12 bg-blue-100 text-primary rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors duration-300 z-10 relative">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 z-10 relative">Simulasi CAT Real Time</h3>
                    <p class="text-sm text-slate-500 leading-relaxed z-10 relative">Antarmuka dibuat mirip 1:1 dengan sistem CAT BKN dan Ruangguru untuk adaptasi maksimal peserta.</p>
                </div>
                
                <!-- Fitur 2 -->
                <div class="reveal-element p-6 rounded-2xl card-premium card-spotlight space-y-4 group overflow-hidden" data-delay="50">
                    <div class="w-12 h-12 bg-blue-100 text-primary rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors duration-300 z-10 relative">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 z-10 relative">Timer Otomatis</h3>
                    <p class="text-sm text-slate-500 leading-relaxed z-10 relative">Penghitung waktu presisi yang membatasi durasi pengerjaan soal dan melakukan auto-submit saat habis.</p>
                </div>

                <!-- Fitur 3 -->
                <div class="reveal-element p-6 rounded-2xl card-premium card-spotlight space-y-4 group overflow-hidden" data-delay="100">
                    <div class="w-12 h-12 bg-blue-100 text-primary rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors duration-300 z-10 relative">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.2" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 z-10 relative">Random Soal & Opsi</h3>
                    <p class="text-sm text-slate-500 leading-relaxed z-10 relative">Pengacakan dinamis urutan soal dan pilihan ganda (A/B/C/D/E) unik untuk setiap sesi login peserta.</p>
                </div>

                <!-- Fitur 4 -->
                <div class="reveal-element p-6 rounded-2xl card-premium card-spotlight space-y-4 group overflow-hidden" data-delay="0">
                    <div class="w-12 h-12 bg-blue-100 text-primary rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors duration-300 z-10 relative">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 z-10 relative">Riwayat Nilai Lengkap</h3>
                    <p class="text-sm text-slate-500 leading-relaxed z-10 relative">Siswa dapat melihat rekap seluruh skor ujian mereka di masa lalu lengkap dengan grafik kelulusannya.</p>
                </div>

                <!-- Fitur 5 -->
                <div class="reveal-element p-6 rounded-2xl card-premium card-spotlight space-y-4 group overflow-hidden" data-delay="50">
                    <div class="w-12 h-12 bg-blue-100 text-primary rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors duration-300 z-10 relative">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 z-10 relative">Drill Soal Harian</h3>
                    <p class="text-sm text-slate-500 leading-relaxed z-10 relative">Jenis paket khusus untuk melatih ketajaman pemahaman per subkategori kategori soal kognitif secara mandiri.</p>
                </div>

                <!-- Fitur 6 -->
                <div class="reveal-element p-6 rounded-2xl card-premium card-spotlight space-y-4 group overflow-hidden" data-delay="100">
                    <div class="w-12 h-12 bg-blue-100 text-primary rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors duration-300 z-10 relative">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 z-10 relative">Tryout Online Terjadwal</h3>
                    <p class="text-sm text-slate-500 leading-relaxed z-10 relative">Ujian terjadwal dengan penentuan waktu mulai dan akhir untuk memfasilitasi ujian akbar serentak.</p>
                </div>

                <!-- Fitur 7 -->
                <div class="reveal-element p-6 rounded-2xl card-premium card-spotlight space-y-4 group overflow-hidden" data-delay="0">
                    <div class="w-12 h-12 bg-blue-100 text-primary rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors duration-300 z-10 relative">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 z-10 relative">Import Soal PDF</h3>
                    <p class="text-sm text-slate-500 leading-relaxed z-10 relative">Memudahkan administrator memasukkan ratusan soal sekaligus dari file dokumen PDF secara instan.</p>
                </div>

                <!-- Fitur 8 -->
                <div class="reveal-element p-6 rounded-2xl card-premium card-spotlight space-y-4 group overflow-hidden" data-delay="50">
                    <div class="w-12 h-12 bg-blue-100 text-primary rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors duration-300 z-10 relative">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 z-10 relative">Analisis Hasil Ujian</h3>
                    <p class="text-sm text-slate-500 leading-relaxed z-10 relative">Breakdown nilai komprehensif per subtopik materi untuk membantu menemukan titik kelemahan Anda.</p>
                </div>

                <!-- Fitur 9 -->
                <div class="reveal-element p-6 rounded-2xl card-premium card-spotlight space-y-4 group overflow-hidden" data-delay="100">
                    <div class="w-12 h-12 bg-blue-100 text-primary rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors duration-300 z-10 relative">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 z-10 relative">Sistem Anti Kecurangan</h3>
                    <p class="text-sm text-slate-500 leading-relaxed z-10 relative">Kombinasi kunci layar penuh (fullscreen) dan deteksi keluar jendela untuk integritas hasil tes yang objektif.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 3. KATEGORI UJIAN ──────────────────────────────────────────────── -->
    <section id="kategori" class="py-20 sm:py-24 bg-slate-50 scroll-mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4 reveal-element">
                <span class="text-sm font-bold text-primary uppercase tracking-wider">Kategori Program</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Ujian yang Dapat Anda Persiapkan</h2>
                <p class="text-slate-500 text-sm sm:text-base">
                    Kategori tryout Bimbel Plano dipetakan secara terstruktur sesuai dengan jenis seleksi terkini.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 max-w-4xl mx-auto">
                <!-- SKD -->
                <div class="reveal-element p-8 rounded-3xl card-premium card-spotlight space-y-6 overflow-hidden" data-delay="0">
                    <div class="flex items-center gap-4 z-10 relative">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-primary flex items-center justify-center font-bold text-lg">SKD</div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Seleksi Kompetensi Dasar</h3>
                            <span class="text-xs text-slate-400 font-semibold uppercase">Untuk CPNS & Kedinasan</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed z-10 relative">
                        Mempersiapkan ujian seleksi Aparatur Sipil Negara (ASN) dan masuk sekolah kedinasan (STAN, STIS, IPDN, dll).
                    </p>
                    <div class="border-t border-slate-100 pt-5 space-y-3 z-10 relative">
                        <div class="flex items-center gap-3 text-sm text-slate-700 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                            Kedinasan
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-700 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                            CPNS
                        </div>
                    </div>
                </div>
                
                <!-- SNBT -->
                <div class="reveal-element p-8 rounded-3xl card-premium card-spotlight space-y-6 overflow-hidden" data-delay="100">
                    <div class="flex items-center gap-4 z-10 relative">
                        <div class="w-12 h-12 rounded-2xl bg-yellow-100 text-amber-500 flex items-center justify-center font-bold text-lg">SNBT</div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Seleksi Nasional Berbasis Tes</h3>
                            <span class="text-xs text-slate-400 font-semibold uppercase">Masuk Perguruan Tinggi Negeri</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed z-10 relative">
                        Ujian saringan masuk PTN favorit di Indonesia dengan format subtes literasi dan penalaran matematika terbaru.
                    </p>
                    <div class="border-t border-slate-100 pt-5 grid grid-cols-1 sm:grid-cols-2 gap-3 z-10 relative">
                        <div class="flex items-center gap-3 text-sm text-slate-700 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                            TPS (Tes Potensi Skolastik)
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-700 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                            Literasi Indonesia
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-700 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                            Literasi Inggris
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-700 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                            Penalaran Matematika
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 4. CARA KERJA ──────────────────────────────────────────────────── -->
    <section id="cara-kerja" class="py-20 sm:py-24 bg-white scroll-mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4 reveal-element">
                <span class="text-sm font-bold text-primary uppercase tracking-wider">Alur Pengerjaan</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Bagaimana Cara Memulai?</h2>
                <p class="text-slate-500 text-sm sm:text-base">
                    Sistem pengerjaan teratur memudahkan siswa dan pengelola memonitor progres simulasi ujian.
                </p>
            </div>
            
            <div class="relative">
                <!-- Connector line (Desktop) -->
                <div class="hidden lg:block absolute top-1/2 left-8 right-8 h-0 border-t-2 border-dashed border-slate-100 -translate-y-12"></div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 relative z-10 text-center">
                    <!-- Langkah 1 -->
                    <div class="space-y-4 reveal-element" data-delay="0">
                        <div class="w-16 h-16 bg-blue-50 border border-primary/10 text-primary rounded-2xl flex items-center justify-center font-extrabold text-lg mx-auto shadow-sm transition-all duration-300 hover:scale-110 hover:shadow-md hover:bg-primary hover:text-white">1</div>
                        <h3 class="font-bold text-slate-800 text-sm">Akun Dibuat Admin</h3>
                        <p class="text-xs text-slate-500 px-4 leading-relaxed">Administrator mendaftarkan akun siswa di sistem admin.</p>
                    </div>
                    
                    <!-- Langkah 2 -->
                    <div class="space-y-4 reveal-element" data-delay="100">
                        <div class="w-16 h-16 bg-blue-50 border border-primary/10 text-primary rounded-2xl flex items-center justify-center font-extrabold text-lg mx-auto shadow-sm transition-all duration-300 hover:scale-110 hover:shadow-md hover:bg-primary hover:text-white">2</div>
                        <h3 class="font-bold text-slate-800 text-sm">Peserta Login</h3>
                        <p class="text-xs text-slate-500 px-4 leading-relaxed">Peserta masuk menggunakan email & password dari admin.</p>
                    </div>

                    <!-- Langkah 3 -->
                    <div class="space-y-4 reveal-element" data-delay="200">
                        <div class="w-16 h-16 bg-blue-50 border border-primary/10 text-primary rounded-2xl flex items-center justify-center font-extrabold text-lg mx-auto shadow-sm transition-all duration-300 hover:scale-110 hover:shadow-md hover:bg-primary hover:text-white">3</div>
                        <h3 class="font-bold text-slate-800 text-sm">Memilih Ujian</h3>
                        <p class="text-xs text-slate-500 px-4 leading-relaxed">Memilih paket Tryout Akbar atau Latihan Drill di dashboard.</p>
                    </div>

                    <!-- Langkah 4 -->
                    <div class="space-y-4 reveal-element" data-delay="300">
                        <div class="w-16 h-16 bg-blue-50 border border-primary/10 text-primary rounded-2xl flex items-center justify-center font-extrabold text-lg mx-auto shadow-sm transition-all duration-300 hover:scale-110 hover:shadow-md hover:bg-primary hover:text-white">4</div>
                        <h3 class="font-bold text-slate-800 text-sm">Mengerjakan Ujian</h3>
                        <p class="text-xs text-slate-500 px-4 leading-relaxed">Mengerjakan soal ujian CAT dengan batasan waktu presisi.</p>
                    </div>

                    <!-- Langkah 5 -->
                    <div class="space-y-4 reveal-element" data-delay="400">
                        <div class="w-16 h-16 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-2xl flex items-center justify-center font-extrabold text-lg mx-auto shadow-sm transition-all duration-300 hover:scale-110 hover:shadow-md hover:bg-emerald-500 hover:text-white">5</div>
                        <h3 class="font-bold text-slate-800 text-sm">Nilai Muncul</h3>
                        <p class="text-xs text-slate-500 px-4 leading-relaxed">Kalkulasi nilai otomatis lengkap dengan pembahasan soal.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 5. STATISTIK ───────────────────────────────────────────────────── -->
    <section class="py-16 sm:py-20 bg-gradient-to-r from-blue-900 via-primary to-blue-950 text-white scroll-mt-16" id="statsSection">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-12 text-center">
                <!-- Stat 1 -->
                <div class="reveal-element p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300 space-y-2" data-delay="0">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight stat-count" data-target="{{ $totalSoal }}">0</span>
                    <p class="text-xs sm:text-sm text-blue-100 font-semibold uppercase tracking-wider">Total Bank Soal</p>
                </div>
                
                <!-- Stat 2 -->
                <div class="reveal-element p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300 space-y-2" data-delay="100">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight stat-count" data-target="{{ $totalPeserta }}">0</span>
                    <p class="text-xs sm:text-sm text-blue-100 font-semibold uppercase tracking-wider">Siswa Terdaftar</p>
                </div>

                <!-- Stat 3 -->
                <div class="reveal-element p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300 space-y-2" data-delay="200">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight stat-count" data-target="{{ $totalTryout }}">0</span>
                    <p class="text-xs sm:text-sm text-blue-100 font-semibold uppercase tracking-wider">Paket Tryout Aktif</p>
                </div>

                <!-- Stat 4 -->
                <div class="reveal-element p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300 space-y-2" data-delay="300">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-accent stat-count" id="stat-kelulusan" data-target="98.6">0</span>
                    <p class="text-xs sm:text-sm text-blue-100 font-semibold uppercase tracking-wider">Rasio Kelulusan Alumni</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 6. SCREENSHOT APLIKASI ─────────────────────────────────────────── -->
    <section id="screenshots" class="py-20 sm:py-24 bg-slate-50 scroll-mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12 space-y-4 reveal-element">
                <span class="text-sm font-bold text-primary uppercase tracking-wider">Tampilan Aplikasi</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Desain Bersih & Premium</h2>
                <p class="text-slate-500 text-sm sm:text-base">Intip beberapa cuplikan antarmuka dashboard dan simulasi ujian dari Smart CBT Bimbel Plano.</p>
            </div>
            
            <!-- Carousel Container (with 3D perspective hover & zoom) -->
            <div class="relative max-w-4xl mx-auto reveal-element perspective-container" data-delay="0">
                <div id="screenshotCarouselCard" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg relative aspect-[16/10] perspective-element cursor-default">
                    <!-- Slide 1: Dashboard Admin -->
                    <div class="carousel-slide absolute inset-0 transition-opacity duration-500 flex flex-col">
                        <div class="bg-slate-100 px-4 py-2.5 text-xs text-slate-500 border-b border-slate-200 flex items-center justify-between">
                            <strong class="font-bold">Dashboard Panel Administrator</strong>
                            <span>1 dari 4</span>
                        </div>
                        <div class="p-6 bg-slate-50 flex-1 flex flex-col justify-between text-xs space-y-4">
                            <!-- Stats overview -->
                            <div class="grid grid-cols-4 gap-4">
                                <div class="bg-white p-3 border border-slate-200/60 rounded-xl shadow-sm hover:shadow transition-shadow">
                                    <span class="text-slate-400 block text-[9px] uppercase">Total Siswa</span>
                                    <strong class="text-base text-slate-800 font-bold">{{ $totalPeserta }} Peserta</strong>
                                </div>
                                <div class="bg-white p-3 border border-slate-200/60 rounded-xl shadow-sm hover:shadow transition-shadow">
                                    <span class="text-slate-400 block text-[9px] uppercase">Soal Aktif</span>
                                    <strong class="text-base text-slate-800 font-bold">{{ $totalSoal }} Item</strong>
                                </div>
                                <div class="bg-white p-3 border border-slate-200/60 rounded-xl shadow-sm hover:shadow transition-shadow">
                                    <span class="text-slate-400 block text-[9px] uppercase">Tryout Paket</span>
                                    <strong class="text-base text-slate-800 font-bold">{{ $totalTryout }} Paket</strong>
                                </div>
                                <div class="bg-white p-3 border border-slate-200/60 rounded-xl shadow-sm hover:shadow transition-shadow">
                                    <span class="text-slate-400 block text-[9px] uppercase">Kelulusan</span>
                                    <strong class="text-base text-primary font-bold">98.6% Rasio</strong>
                                </div>
                            </div>
                            <!-- Mock table list -->
                            <div class="bg-white border border-slate-200/60 rounded-xl flex-1 flex flex-col overflow-hidden">
                                <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-100 font-bold text-slate-500 uppercase text-[9px] tracking-wider">Peserta Terdaftar Baru</div>
                                <div class="p-3 space-y-2 flex-1">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                                        <strong>Ahmad Rifqi (ahmad@demo.com)</strong>
                                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold">SKD - Aktif</span>
                                    </div>
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                                        <strong>Siti Sarah (siti@demo.com)</strong>
                                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold">SKD - Aktif</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <strong>Budi Wijaya (budi@demo.com)</strong>
                                        <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded font-bold">SNBT - Aktif</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2: Dashboard Peserta -->
                    <div class="carousel-slide absolute inset-0 transition-opacity duration-500 opacity-0 flex flex-col">
                        <div class="bg-slate-100 px-4 py-2.5 text-xs text-slate-500 border-b border-slate-200 flex items-center justify-between">
                            <strong class="font-bold">Dashboard Siswa (Pilihan Paket Ujian)</strong>
                            <span>2 dari 4</span>
                        </div>
                        <div class="p-6 bg-slate-50 flex-1 flex flex-col justify-between text-xs space-y-4">
                            <div class="border-b border-slate-200 pb-2">
                                <h3 class="font-bold text-slate-800 text-sm">Selamat Datang, Peserta Demo 👋</h3>
                                <p class="text-slate-400 text-[10px]">Silakan pilih paket Tryout Akbar CPNS atau Latihan Drill di bawah.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4 flex-1">
                                <div class="bg-white p-4 border border-slate-200/60 rounded-xl shadow-sm flex flex-col justify-between">
                                    <div>
                                        <span class="bg-blue-100 text-primary px-2 py-0.5 rounded font-bold text-[9px] uppercase">Tryout</span>
                                        <h4 class="font-bold text-slate-800 mt-2 text-sm sm:text-base">Tryout SKD CPNS #1</h4>
                                        <p class="text-slate-400 text-[10px] mt-1">90 Menit · 15 Soal Acak</p>
                                    </div>
                                    <span class="bg-primary text-white text-center py-1.5 rounded-lg font-bold block mt-3 shadow">Mulai Ujian</span>
                                </div>
                                <div class="bg-white p-4 border border-slate-200/60 rounded-xl shadow-sm flex flex-col justify-between">
                                    <div>
                                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold text-[9px] uppercase">Drill Soal</span>
                                        <h4 class="font-bold text-slate-800 mt-2 text-sm sm:text-base">Drill Soal Kognitif SNBT</h4>
                                        <p class="text-slate-400 text-[10px] mt-1">30 Menit · 4 Soal Acak</p>
                                    </div>
                                    <span class="bg-primary text-white text-center py-1.5 rounded-lg font-bold block mt-3 shadow">Mulai Ujian</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 3: Halaman Ujian -->
                    <div class="carousel-slide absolute inset-0 transition-opacity duration-500 opacity-0 flex flex-col">
                        <div class="bg-slate-100 px-4 py-2.5 text-xs text-slate-500 border-b border-slate-200 flex items-center justify-between">
                            <strong class="font-bold">Simulasi Ujian CAT BKN Real Time</strong>
                            <span>3 dari 4</span>
                        </div>
                        <div class="p-6 bg-slate-50 flex-1 flex flex-col justify-between text-xs space-y-4">
                            <div class="flex justify-between items-center bg-white p-3 border border-slate-200/60 rounded-xl shadow-sm">
                                <strong class="font-bold">Tryout SKD CPNS #1</strong>
                                <span class="bg-red-50 text-red-600 px-2 py-0.5 rounded font-bold">⏱️ Sisa: 01:29:45</span>
                            </div>
                            <div class="grid grid-cols-12 gap-4 flex-1">
                                <div class="col-span-9 bg-white p-4 border border-slate-200/60 rounded-xl shadow-sm flex flex-col justify-between">
                                    <div>
                                        <span class="text-slate-400 text-[9px] font-bold block mb-1">SOAL 1</span>
                                        <p class="text-slate-800 font-semibold leading-relaxed">Pancasila disahkan sebagai dasar negara Indonesia pada tanggal...</p>
                                    </div>
                                    <div class="space-y-1.5 pt-2">
                                        <div class="p-2 border border-slate-100 rounded-lg">A. 17 Agustus 1945</div>
                                        <div class="p-2 border border-primary/30 bg-blue-50/50 rounded-lg font-bold text-primary">B. 18 Agustus 1945</div>
                                        <div class="p-2 border border-slate-100 rounded-lg">C. 1 Juni 1945</div>
                                    </div>
                                </div>
                                <div class="col-span-3 bg-white p-3 border border-slate-200/60 rounded-xl shadow-sm space-y-2 text-center">
                                    <strong class="block text-[10px] border-b pb-1 text-slate-400 font-bold">Navigasi</strong>
                                    <div class="grid grid-cols-3 gap-1">
                                        <span class="bg-emerald-500 text-white p-1 rounded font-bold">1</span>
                                        <span class="bg-slate-100 text-slate-400 p-1 rounded">2</span>
                                        <span class="bg-yellow-400 text-white p-1 rounded font-bold">3</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 4: Hasil Nilai -->
                    <div class="carousel-slide absolute inset-0 transition-opacity duration-500 opacity-0 flex flex-col">
                        <div class="bg-slate-100 px-4 py-2.5 text-xs text-slate-500 border-b border-slate-200 flex items-center justify-between">
                            <strong class="font-bold">Lembar Hasil Ujian & Review Jawaban</strong>
                            <span>4 dari 4</span>
                        </div>
                        <div class="p-6 bg-slate-50 flex-1 flex flex-col justify-between text-xs space-y-4">
                            <div class="text-center">
                                <span class="text-slate-400 block text-[9px] uppercase">SKOR AKHIR</span>
                                <strong class="text-3xl text-emerald-600 font-extrabold animate-pulse">95%</strong>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-white p-3 border border-slate-200/60 rounded-xl text-center shadow-sm">
                                    <span class="text-slate-400 block text-[9px] uppercase">TWK</span>
                                    <strong class="text-slate-800 text-sm font-bold">100%</strong>
                                </div>
                                <div class="bg-white p-3 border border-slate-200/60 rounded-xl text-center shadow-sm">
                                    <span class="text-slate-400 block text-[9px] uppercase">TIU</span>
                                    <strong class="text-slate-800 text-sm font-bold">90%</strong>
                                </div>
                                <div class="bg-white p-3 border border-slate-200/60 rounded-xl text-center shadow-sm">
                                    <span class="text-slate-400 block text-[9px] uppercase">TKP</span>
                                    <strong class="text-slate-800 text-sm font-bold">95%</strong>
                                </div>
                            </div>
                            <div class="bg-white p-3 border border-slate-200/60 rounded-xl flex justify-between items-center text-[10px] shadow-sm">
                                <span class="text-emerald-500 font-bold">✓ 14 Benar</span>
                                <span class="text-red-500 font-bold">✗ 1 Salah</span>
                                <span class="text-slate-400 font-bold">⬜ 0 Kosong</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Controls -->
                <button onclick="prevSlide()" class="btn-premium absolute -left-4 sm:-left-6 top-1/2 -translate-y-1/2 w-10 h-10 bg-white border border-slate-200 rounded-full flex items-center justify-center shadow-md hover:bg-slate-50 text-slate-600 font-bold text-lg" aria-label="Previous Slide">‹</button>
                <button onclick="nextSlide()" class="btn-premium absolute -right-4 sm:-right-6 top-1/2 -translate-y-1/2 w-10 h-10 bg-white border border-slate-200 rounded-full flex items-center justify-center shadow-md hover:bg-slate-50 text-slate-600 font-bold text-lg" aria-label="Next Slide">›</button>
            </div>
        </div>
    </section>

    <!-- ─── 7. TESTIMONI ───────────────────────────────────────────────────── -->
    <section class="py-20 sm:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4 reveal-element">
                <span class="text-sm font-bold text-primary uppercase tracking-wider">Testimoni Alumni</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Apa Kata Mereka?</h2>
                <p class="text-slate-500 text-sm sm:text-base">Ratusan alumni kami telah membuktikan keandalan sistem tryout Bimbel Plano dalam meloloskan mereka ke instansi impian.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testi 1 -->
                <div class="reveal-element p-6 rounded-2xl card-premium card-spotlight space-y-4 overflow-hidden" data-delay="0">
                    <p class="text-sm text-slate-600 italic leading-relaxed z-10 relative">
                        "Berkat simulasi CAT di platform Smart CBT Plano, saya tidak gugup lagi saat menghadapi ujian SKD CPNS yang sesungguhnya. Layoutnya mirip sekali!"
                    </p>
                    <div class="flex items-center gap-3 z-10 relative">
                        <div class="w-10 h-10 rounded-full bg-blue-900 text-white flex items-center justify-center font-bold text-xs shadow-sm">R</div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Rian Pratama</h4>
                            <span class="text-xs text-slate-400">Lolos CPNS Kemenkumham</span>
                        </div>
                    </div>
                </div>
                
                <!-- Testi 2 -->
                <div class="reveal-element p-6 rounded-2xl card-premium card-spotlight space-y-4 overflow-hidden" data-delay="100">
                    <p class="text-sm text-slate-600 italic leading-relaxed z-10 relative">
                        "Acakan opsi jawaban dan pengacak soal membuat saya benar-benar belajar memahami materi, bukan sekadar menghafal pola kunci jawaban."
                    </p>
                    <div class="flex items-center gap-3 z-10 relative">
                        <div class="w-10 h-10 rounded-full bg-blue-900 text-white flex items-center justify-center font-bold text-xs shadow-sm">A</div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Anisa Safitri</h4>
                            <span class="text-xs text-slate-400">Lolos IPDN (Kedinasan)</span>
                        </div>
                    </div>
                </div>

                <!-- Testi 3 -->
                <div class="reveal-element p-6 rounded-2xl card-premium card-spotlight space-y-4 overflow-hidden" data-delay="200">
                    <p class="text-sm text-slate-600 italic leading-relaxed z-10 relative">
                        "Hasil nilai per kategori yang dinamis dan grafik riwayat ujian sangat membantu saya memetakan titik lemah saya di Penalaran Matematika."
                    </p>
                    <div class="flex items-center gap-3 z-10 relative">
                        <div class="w-10 h-10 rounded-full bg-blue-900 text-white flex items-center justify-center font-bold text-xs shadow-sm">F</div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Fahmi Irawan</h4>
                            <span class="text-xs text-slate-400">Lolos SNBT Universitas Indonesia</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 8. FAQ ─────────────────────────────────────────────────────────── -->
    <section id="faq" class="py-20 sm:py-24 bg-slate-50 scroll-mt-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 space-y-4 reveal-element">
                <span class="text-sm font-bold text-primary uppercase tracking-wider">Tanya Jawab</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Pertanyaan yang Sering Diajukan</h2>
            </div>
            
            <div class="space-y-4 reveal-element">
                <!-- FAQ 1 -->
                <div id="faq-card-1" class="border border-slate-200/80 bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md hover:border-slate-300/80 transition-all duration-300">
                    <button onclick="toggleFaq(1)" class="w-full text-left px-6 py-4 flex items-center justify-between font-bold text-slate-800 hover:bg-slate-50/50 transition-colors">
                        <span>Bagaimana cara mendapatkan akun login peserta?</span>
                        <svg id="faq-arrow-1" class="w-5 h-5 text-slate-400 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="faq-answer-1" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out text-sm text-slate-600 border-t border-transparent leading-relaxed">
                        <div class="px-6 py-4">
                            Peserta tidak dapat mendaftar sendiri untuk menjaga keamanan. Akun peserta didaftarkan secara eksklusif oleh administrator Bimbel Plano. Silakan hubungi admin via WhatsApp untuk pendaftaran kelas.
                        </div>
                    </div>
                </div>
                
                <!-- FAQ 2 -->
                <div id="faq-card-2" class="border border-slate-200/80 bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md hover:border-slate-300/80 transition-all duration-300">
                    <button onclick="toggleFaq(2)" class="w-full text-left px-6 py-4 flex items-center justify-between font-bold text-slate-800 hover:bg-slate-50/50 transition-colors">
                        <span>Apakah hasil nilai langsung keluar setelah ujian selesai?</span>
                        <svg id="faq-arrow-2" class="w-5 h-5 text-slate-400 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="faq-answer-2" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out text-sm text-slate-600 border-t border-transparent leading-relaxed">
                        <div class="px-6 py-4">
                            Ya, sistem kami melakukan kalkulasi skor secara instan. Begitu Anda mengeklik tombol "Selesai" atau waktu habis, lembar nilai dan review pembahasan langsung ditampilkan secara real-time.
                        </div>
                    </div>
                </div>
 
                <!-- FAQ 3 -->
                <div id="faq-card-3" class="border border-slate-200/80 bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md hover:border-slate-300/80 transition-all duration-300">
                    <button onclick="toggleFaq(3)" class="w-full text-left px-6 py-4 flex items-center justify-between font-bold text-slate-800 hover:bg-slate-50/50 transition-colors">
                        <span>Apakah soal dan opsi pilihan ganda diacak?</span>
                        <svg id="faq-arrow-3" class="w-5 h-5 text-slate-400 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="faq-answer-3" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out text-sm text-slate-600 border-t border-transparent leading-relaxed">
                        <div class="px-6 py-4">
                            Ya. Sistem menggunakan algoritma pengacakan ganda. Urutan soal diacak otomatis untuk setiap peserta, dan urutan pilihan ganda (A/B/C/D/E) juga diacak secara unik agar mencegah kecurangan.
                        </div>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div id="faq-card-4" class="border border-slate-200/80 bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md hover:border-slate-300/80 transition-all duration-300">
                    <button onclick="toggleFaq(4)" class="w-full text-left px-6 py-4 flex items-center justify-between font-bold text-slate-800 hover:bg-slate-50/50 transition-colors">
                        <span>Apakah platform tryout ini bisa diakses melalui HP?</span>
                        <svg id="faq-arrow-4" class="w-5 h-5 text-slate-400 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="faq-answer-4" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out text-sm text-slate-600 border-t border-transparent leading-relaxed">
                        <div class="px-6 py-4">
                            Tentu saja. Smart CBT Plano didesain menggunakan framework responsif premium, sehingga sangat nyaman digunakan baik melalui smartphone (Android/iOS), tablet, maupun PC Desktop.
                        </div>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div id="faq-card-5" class="border border-slate-200/80 bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md hover:border-slate-300/80 transition-all duration-300">
                    <button onclick="toggleFaq(5)" class="w-full text-left px-6 py-4 flex items-center justify-between font-bold text-slate-800 hover:bg-slate-50/50 transition-colors">
                        <span>Apakah tersedia lembar pembahasan soal?</span>
                        <svg id="faq-arrow-5" class="w-5 h-5 text-slate-400 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="faq-answer-5" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out text-sm text-slate-600 border-t border-transparent leading-relaxed">
                        <div class="px-6 py-4">
                            Ya. Pada halaman hasil nilai ujian, terdapat tinjauan soal secara lengkap yang memuat status jawaban Anda (benar/salah), kunci jawaban yang valid, dan teks penjelasan/pembahasan untuk dipelajari kembali.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 9. CTA SECTION ─────────────────────────────────────────────────── -->
    <section class="py-24 bg-slate-950 text-white text-center relative overflow-hidden">
        <!-- Premium Ambient glow blurs -->
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary rounded-full blur-3xl opacity-25 pointer-events-none animate-pulse-slow-1"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-yellow-500 rounded-full blur-3xl opacity-10 pointer-events-none animate-pulse-slow-2"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-8 reveal-element">
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Siap Menghadapi Seleksi SKD dan SNBT?</h2>
            <p class="text-slate-300 text-sm sm:text-base max-w-xl mx-auto leading-relaxed">
                Bergabunglah bersama ribuan siswa sukses lainnya. Akses bank soal terbaik Plano dan uji kemampuan Anda hari ini.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                @auth
                    @if(auth()->user()->isPeserta())
                        <a href="{{ route('peserta.dashboard') }}" class="btn-premium w-full sm:w-auto text-center bg-accent text-slate-900 font-bold px-8 py-3.5 rounded-xl hover:bg-accent-dark shadow-md">
                            Mulai Sekarang
                        </a>
                        <a href="{{ route('peserta.dashboard') }}" class="btn-premium w-full sm:w-auto text-center bg-white/10 hover:bg-white/20 text-white border border-white/20 font-semibold px-8 py-3.5 rounded-xl">
                            Dashboard Ujian
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="btn-premium w-full sm:w-auto text-center bg-accent text-slate-900 font-bold px-8 py-3.5 rounded-xl hover:bg-accent-dark shadow-md">
                            Dashboard Admin
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-premium w-full sm:w-auto text-center bg-accent text-slate-900 font-bold px-8 py-3.5 rounded-xl hover:bg-accent-dark shadow-md">
                        Mulai Sekarang
                    </a>
                    <a href="{{ route('login') }}" class="btn-premium w-full sm:w-auto text-center bg-white/10 hover:bg-white/20 text-white border border-white/20 font-semibold px-8 py-3.5 rounded-xl">
                        Login Peserta
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- ─── 10. FOOTER ─────────────────────────────────────────────────────── -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <!-- Branding column -->
                <div class="space-y-4 md:col-span-1">
                    <a href="{{ route('landing') }}" class="inline-block transition-transform duration-300 hover:scale-105 active:scale-95">
                        <img src="{{ asset('images/logo-plano.jpg') }}" alt="Bimbel Plano" class="h-10 w-auto brightness-110 object-contain bg-white p-1 rounded shadow-sm">
                    </a>
                    <p class="text-xs leading-relaxed text-slate-500">
                        Platform ujian online premium yang memfasilitasi tryout komprehensif CAT SKD dan drill soal SNBT Bimbel Plano.
                    </p>
                </div>
                
                <!-- Quick links -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider">Menu Cepat</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('landing') }}" class="hover:text-white transition-colors duration-200">Beranda</a></li>
                        <li><a href="#fitur" class="hover:text-white transition-colors duration-200">Fitur Unggulan</a></li>
                        <li><a href="#kategori" class="hover:text-white transition-colors duration-200">Kategori Soal</a></li>
                        <li><a href="#faq" class="hover:text-white transition-colors duration-200">FAQ Tanya Jawab</a></li>
                    </ul>
                </div>
                
                <!-- Contacts -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider">Hubungi Kami</h4>
                    <ul class="space-y-2 text-xs">
                        <li>📍 Jl. Raya Bimbel Plano No. 12, Jakarta</li>
                        <li>✉️ info@bimbelplano.com</li>
                        <li>📞 +62 812-3456-7890</li>
                    </ul>
                </div>

                <!-- Socials -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider">Media Sosial</h4>
                    <div class="flex space-x-4">
                        <!-- WhatsApp -->
                        <a href="https://wa.me/628123456789" target="_blank" class="hover:text-white transition-colors duration-200" aria-label="WhatsApp">
                            WhatsApp
                        </a>
                        <!-- Instagram -->
                        <a href="https://instagram.com/bimbelplano" target="_blank" class="hover:text-white transition-colors duration-200" aria-label="Instagram">
                            Instagram
                        </a>
                        <!-- Email -->
                        <a href="mailto:info@bimbelplano.com" class="hover:text-white transition-colors duration-200" aria-label="Email">
                            Email
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-slate-800 pt-8 text-center text-xs text-slate-600">
                <p>&copy; {{ date('Y') }} Bimbel Plano. Hak Cipta Dilindungi Undang-Undang. Smart CBT Premium Engine.</p>
            </div>
        </div>
    </footer>

    <!-- ─── JAVASCRIPT ───────────────────────────────────────────────────── -->
    <script>
        // ─── STICKY NAVBAR EFFECT ─────────────────────────────────────────
        window.addEventListener('scroll', () => {
            const header = document.getElementById('navHeader');
            const navContainer = document.getElementById('navContainer');
            if (window.scrollY > 15) {
                header.classList.add('bg-white/95', 'shadow-lg', 'shadow-slate-100/40', 'border-slate-100/80');
                header.classList.remove('bg-white/80', 'border-transparent');
                if (navContainer) {
                    navContainer.classList.remove('h-16', 'sm:h-20');
                    navContainer.classList.add('h-14', 'sm:h-16');
                }
            } else {
                header.classList.remove('bg-white/95', 'shadow-lg', 'shadow-slate-100/40', 'border-slate-100/80');
                header.classList.add('bg-white/80', 'border-transparent');
                if (navContainer) {
                    navContainer.classList.remove('h-14', 'sm:h-16');
                    navContainer.classList.add('h-16', 'sm:h-20');
                }
            }
        });

        // ─── VIEWPORT SCROLL ANIMATIONS ──────────────────────────────────
        const scrollObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = entry.target.getAttribute('data-delay') || 0;
                    setTimeout(() => {
                        entry.target.classList.add('reveal-visible');
                    }, delay);
                    scrollObserver.unobserve(entry.target); // Trigger once
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -50px 0px' });

        // Trigger viewport animations
        window.addEventListener('DOMContentLoaded', () => {
            // Animating hero elements immediately
            const heroText = document.getElementById('heroText');
            const heroMockup = document.getElementById('heroMockup');
            if (heroText) {
                heroText.classList.remove('opacity-0', 'translate-y-8');
                heroText.classList.add('reveal-visible');
            }
            if (heroMockup) {
                heroMockup.classList.remove('opacity-0', 'translate-y-8');
                heroMockup.classList.add('reveal-visible');
            }

            document.querySelectorAll('.reveal-element').forEach(el => scrollObserver.observe(el));
            
            // Initialize Spotlight Cards mouse listener
            document.querySelectorAll('.card-spotlight').forEach(card => {
                card.addEventListener('mousemove', e => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    card.style.setProperty('--mouse-x', `${x}px`);
                    card.style.setProperty('--mouse-y', `${y}px`);
                });
            });

            // Initialize 3D Mouse Tilts (Only active on hover on Desktop)
            apply3DTilt('heroMockupCard', 6);
            apply3DTilt('screenshotCarouselCard', 4);
        });

        // 3D Tilt Helper
        function apply3DTilt(elementId, maxRotation = 6) {
            const el = document.getElementById(elementId);
            if (!el) return;
            
            el.addEventListener('mousemove', (e) => {
                const rect = el.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const xc = ((x / rect.width) - 0.5);
                const yc = ((y / rect.height) - 0.5);
                
                const rotX = (yc * -maxRotation).toFixed(2);
                const rotY = (xc * maxRotation).toFixed(2);
                
                el.style.transform = `perspective(1000px) rotateX(${rotX}deg) rotateY(${rotY}deg) scale3d(1.015, 1.015, 1.015)`;
            });
            
            el.addEventListener('mouseleave', () => {
                el.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
            });
        }

        // ─── DYNAMIC STATISTICS COUNT UP ──────────────────────────────────
        const statsSection = document.getElementById('statsSection');
        let statsStarted = false;

        function runCountUp() {
            document.querySelectorAll('.stat-count').forEach(el => {
                const target = parseFloat(el.getAttribute('data-target'));
                const isPercentage = el.id === 'stat-kelulusan';
                const duration = 2400; // 2.4 seconds duration
                const startTime = performance.now();

                function easeOutQuart(x) {
                    return 1 - Math.pow(1 - x, 4);
                }

                function updateCounter(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const ease = easeOutQuart(progress);
                    
                    let currentVal;
                    if (isPercentage) {
                        currentVal = (ease * target).toFixed(1);
                        el.textContent = currentVal + '%';
                    } else {
                        currentVal = Math.floor(ease * target);
                        el.textContent = currentVal.toLocaleString('id-ID');
                    }

                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    } else {
                        el.textContent = isPercentage ? (target + '%') : target.toLocaleString('id-ID');
                    }
                }
                requestAnimationFrame(updateCounter);
            });
        }

        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !statsStarted) {
                    statsStarted = true;
                    runCountUp();
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        if (statsSection) statsObserver.observe(statsSection);

        // ─── FAQ SMOOTH ACCORDION HEIGHT TRANSITION ────────────────────────
        function toggleFaq(id) {
            for (let i = 1; i <= 5; i++) {
                const card = document.getElementById('faq-card-' + i);
                const answer = document.getElementById('faq-answer-' + i);
                const arrow = document.getElementById('faq-arrow-' + i);
                
                if (i === id) {
                    const isOpen = answer.style.maxHeight && answer.style.maxHeight !== '0px';
                    if (isOpen) {
                        answer.style.maxHeight = '0px';
                        arrow.style.transform = 'rotate(0deg)';
                        card.classList.remove('border-primary/30', 'shadow-md', 'bg-blue-50/5');
                    } else {
                        answer.style.maxHeight = answer.scrollHeight + 'px';
                        arrow.style.transform = 'rotate(180deg)';
                        card.classList.add('border-primary/30', 'shadow-md', 'bg-blue-50/5');
                    }
                } else {
                    answer.style.maxHeight = '0px';
                    const otherArrow = document.getElementById('faq-arrow-' + i);
                    const otherCard = document.getElementById('faq-card-' + i);
                    if (otherArrow) otherArrow.style.transform = 'rotate(0deg)';
                    if (otherCard) otherCard.classList.remove('border-primary/30', 'shadow-md', 'bg-blue-50/5');
                }
            }
        }

        // ─── MOBILE NAVBAR MENU TOGGLE ────────────────────────────────────
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });

        // ─── SCREENSHOT CAROUSEL SLIDER ───────────────────────────────────
        let currentSlideIdx = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        
        function showSlide(index) {
            slides.forEach((slide, idx) => {
                if (idx === index) {
                    slide.classList.remove('opacity-0');
                    slide.classList.add('opacity-100', 'z-10');
                } else {
                    slide.classList.remove('opacity-100', 'z-10');
                    slide.classList.add('opacity-0');
                }
            });
        }
        
        function nextSlide() {
            currentSlideIdx = (currentSlideIdx + 1) % slides.length;
            showSlide(currentSlideIdx);
        }
        
        // Auto rotate slides every 7 seconds
        setInterval(nextSlide, 7000);
    </script>
</body>
</html>

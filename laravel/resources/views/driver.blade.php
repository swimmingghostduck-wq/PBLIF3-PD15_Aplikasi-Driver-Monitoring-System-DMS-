<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Driver Monitoring System (DMS)')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">
    <!-- Google Fonts: Poppins (Heading) & Inter (Body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        heading: ['Poppins', 'ui-sans-serif', 'sans-serif'],
                        mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'monospace']
                    },
                    colors: {
                        primary: '#2563EB',
                        secondary: '#0EA5E9',
                        danger: '#EF4444',
                        warning: '#F59E0B',
                        success: '#10B981'
                    }
                }
            }
        }
    </script>
    <!-- Tema (terang/gelap/otomatis) + warna aksen. Dibaca dari localStorage sebelum halaman digambar agar tidak berkedip. -->
    <script>
    (function () {
        var KEY = 'dsTheme';
        function read() { try { return JSON.parse(localStorage.getItem(KEY) || '{}'); } catch (e) { return {}; } }
        window.dsReadTheme = function () { var t = read(); return { mode: t.mode || 'light', accent: t.accent || 'blue' }; };
        window.dsApplyTheme = function (mode, accent, save) {
            var t = window.dsReadTheme();
            mode = mode || t.mode; accent = accent || t.accent;
            var dark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            var h = document.documentElement;
            h.setAttribute('data-theme', mode === 'auto' ? (dark ? 'dark' : 'light') : mode);
            h.setAttribute('data-accent', accent);
            if (save) { try { localStorage.setItem(KEY, JSON.stringify({ mode: mode, accent: accent })); } catch (e) {} }
        };
        window.dsApplyTheme();
        if (window.matchMedia) {
            var mq = window.matchMedia('(prefers-color-scheme: dark)');
            var onChange = function () { if (window.dsReadTheme().mode === 'auto') window.dsApplyTheme(); };
            if (mq.addEventListener) mq.addEventListener('change', onChange);
        }
    })();
    </script>
    <style>
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }
        @keyframes pulse-fast {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.02); }
        }
        .alarm-pulsing {
            animation: pulse-fast 0.6s infinite ease-in-out;
        }

        /* ===== Skema warna: variabel dasar ===== */
        :root {
            --ds-card: #FFFFFF; --ds-bg2: #F8FAFC; --ds-border: #E2E8F0; --ds-text: #0F172A; --ds-muted: #64748B;
            --ac-600: #2563EB; --ac-700: #1D4ED8; --ac-300: #93C5FD; --ac-rgb: 37, 99, 235;
        }
        html[data-accent="emerald"] { --ac-600: #059669; --ac-700: #047857; --ac-300: #6EE7B7; --ac-rgb: 5, 150, 105; }
        html[data-accent="violet"]  { --ac-600: #7C3AED; --ac-700: #6D28D9; --ac-300: #C4B5FD; --ac-rgb: 124, 58, 237; }
        html[data-accent="rose"]    { --ac-600: #E11D48; --ac-700: #BE123C; --ac-300: #FDA4AF; --ac-rgb: 225, 29, 72; }
        html[data-accent="orange"]  { --ac-600: #EA580C; --ac-700: #C2410C; --ac-300: #FDBA74; --ac-rgb: 234, 88, 12; }

        /* ===== Warna aksen: menimpa kelas biru Tailwind ===== */
        html[data-accent] .bg-blue-600 { background-color: var(--ac-600) !important; }
        html[data-accent] .hover\:bg-blue-700:hover { background-color: var(--ac-700) !important; }
        html[data-accent] .text-blue-600, html[data-accent] .text-blue-700 { color: var(--ac-700) !important; }
        html[data-accent] .bg-blue-50 { background-color: rgba(var(--ac-rgb), .09) !important; }
        html[data-accent] .bg-blue-100, html[data-accent] .hover\:bg-blue-100:hover { background-color: rgba(var(--ac-rgb), .17) !important; }
        html[data-accent] .border-blue-200, html[data-accent] .border-blue-100 { border-color: rgba(var(--ac-rgb), .30) !important; }
        html[data-accent] .focus\:border-blue-600:focus { border-color: var(--ac-600) !important; }
        html[data-accent] .bg-blue-500, html[data-accent] .bg-primary { background-color: var(--ac-600) !important; }
        html[data-accent] .bg-blue-700 { background-color: var(--ac-700) !important; }
        html[data-accent] .text-primary { color: var(--ac-700) !important; }
        html[data-accent] .text-blue-400 { color: var(--ac-300) !important; }
        html[data-accent] .hover\:text-blue-700:hover { color: var(--ac-700) !important; }
        html[data-accent] .border-blue-600, html[data-accent] .border-blue-500 { border-color: var(--ac-600) !important; }
        html[data-accent] .border-blue-500\/40 { border-color: rgba(var(--ac-rgb), .40) !important; }
        html[data-accent] .bg-blue-500\/20 { background-color: rgba(var(--ac-rgb), .20) !important; }
        html[data-accent] .focus\:ring-blue-500\/10:focus, html[data-accent] .focus\:ring-blue-100:focus { --tw-ring-color: rgba(var(--ac-rgb), .18) !important; }

        /* ===== Tema gelap ===== */
        html[data-theme="dark"] { color-scheme: dark; --ds-card: #111827; --ds-bg2: #0F172A; --ds-border: #2B3A52; --ds-text: #F1F5F9; --ds-muted: #94A3B8; }
        html[data-theme="dark"] body { background-color: #0B1120 !important; color: #E2E8F0; }
        html[data-theme="dark"] .bg-white { background-color: #111827 !important; }
        html[data-theme="dark"] .bg-white\/95 { background-color: rgba(17, 24, 39, .95) !important; }
        html[data-theme="dark"] .bg-slate-50 { background-color: #0F172A !important; }
        html[data-theme="dark"] .bg-slate-100 { background-color: #1E293B !important; }
        html[data-theme="dark"] .bg-slate-200 { background-color: #334155 !important; }
        html[data-theme="dark"] .hover\:bg-slate-50:hover { background-color: #1E293B !important; }
        html[data-theme="dark"] .hover\:bg-slate-100:hover, html[data-theme="dark"] .hover\:bg-slate-200:hover { background-color: #273449 !important; }
        html[data-theme="dark"] .text-slate-900, html[data-theme="dark"] .text-slate-800 { color: #F1F5F9 !important; }
        html[data-theme="dark"] .text-slate-700 { color: #E2E8F0 !important; }
        html[data-theme="dark"] .text-slate-600 { color: #CBD5E1 !important; }
        html[data-theme="dark"] .text-slate-500 { color: #94A3B8 !important; }
        html[data-theme="dark"] .border-slate-100 { border-color: #1E293B !important; }
        html[data-theme="dark"] .border-slate-200, html[data-theme="dark"] .border-slate-200\/90,
        html[data-theme="dark"] .border-slate-200\/80, html[data-theme="dark"] .border-slate-300 { border-color: #2B3A52 !important; }
        html[data-theme="dark"][data-accent] .text-blue-600, html[data-theme="dark"][data-accent] .text-blue-700 { color: var(--ac-300) !important; }
        html[data-theme="dark"] .bg-amber-50 { background-color: rgba(245, 158, 11, .13) !important; }
        html[data-theme="dark"] .text-amber-800, html[data-theme="dark"] .text-amber-600 { color: #FCD34D !important; }
        html[data-theme="dark"] .border-amber-200 { border-color: rgba(245, 158, 11, .35) !important; }
        html[data-theme="dark"] .bg-red-50, html[data-theme="dark"] .hover\:bg-red-50:hover { background-color: rgba(239, 68, 68, .14) !important; }
        html[data-theme="dark"] .bg-emerald-50 { background-color: rgba(16, 185, 129, .13) !important; }
        html[data-theme="dark"] .bg-purple-50 { background-color: rgba(168, 85, 247, .13) !important; }
        html[data-theme="dark"] select, html[data-theme="dark"] input:not([type="checkbox"]):not([type="radio"]):not([type="range"]), html[data-theme="dark"] textarea { background-color: #0F172A; color: #E2E8F0; border-color: #2B3A52; }
        html[data-theme="dark"] ::-webkit-scrollbar-track { background: #0F172A; }
        html[data-theme="dark"] ::-webkit-scrollbar-thumb { background: #334155; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 min-h-screen flex flex-col font-sans antialiased selection:bg-blue-600 selection:text-white">

    <!-- Navbar -->
    <nav class="bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-2xs sticky top-0 z-40" aria-label="Navigasi utama">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo & Nama -->
                <a href="{{ route('driver.dashboard') }}" class="flex items-center gap-3" aria-label="DriveSafe - beranda">
                    <img src="{{ asset('logo.svg') }}" alt="DriveSafe Logo" class="w-9 h-9">
                    <div>
                        <span class="font-heading font-bold text-lg tracking-tight text-slate-900 block leading-tight">DriveSafe</span>
                        <span class="text-[11px] text-slate-500 hidden sm:block leading-none">Driver Monitoring System</span>
                    </div>
                </a>

                @auth
                <!-- Menu desktop -->
                <div class="hidden md:flex items-center gap-1.5">
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Dashboard</a>
                        <a href="{{ route('admin.manage_drivers') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.manage_drivers') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Kelola Driver</a>
                        <a href="{{ route('admin.manage_models') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.manage_models') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Model AI</a>
                    @else
                        <a href="{{ route('driver.monitor') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('driver.monitor') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Kamera</a>
                        <a href="{{ route('driver.dashboard') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('driver.dashboard') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Riwayat</a>
                        <a href="{{ route('driver.profile') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('driver.profile') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Profil</a>
                    @endif

                    <div class="h-5 w-px bg-slate-200 mx-2"></div>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3.5 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:text-red-600 hover:bg-red-50 border border-slate-200 transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>

                <!-- Tombol hamburger (mobile) -->
                <button id="navToggle" type="button" onclick="dsNavOpen()" aria-label="Buka menu" class="md:hidden w-11 h-11 -mr-2 flex items-center justify-center rounded-xl text-slate-700 hover:bg-slate-100 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                @endauth
            </div>
        </div>
    </nav>

    @auth
    <!-- Menu samping (mobile) -->
    <div id="navOverlay" class="hidden fixed inset-0 z-40 bg-slate-900/50 md:hidden" onclick="dsNavClose()"></div>
    <aside id="navPanel" aria-label="Menu" class="fixed top-0 right-0 z-50 h-full w-72 max-w-[85%] bg-white shadow-xl flex flex-col md:hidden translate-x-full invisible transition-[transform,visibility] duration-300">
        <div class="flex items-center justify-between px-5 h-16 border-b border-slate-200">
            <span class="font-heading font-bold text-slate-900">Menu</span>
            <button type="button" onclick="dsNavClose()" aria-label="Tutup menu" class="w-11 h-11 -mr-2 flex items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100 text-2xl leading-none cursor-pointer">&times;</button>
        </div>
        <div class="px-5 py-4 border-b border-slate-100">
            <div class="text-sm font-semibold text-slate-900">{{ Auth::user()->full_name }}</div>
            <div class="text-xs text-slate-500 capitalize">{{ Auth::user()->role }}</div>
        </div>
        <nav class="flex-1 px-3 py-3 space-y-1" aria-label="Menu mobile">
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }}">Dashboard</a>
                <a href="{{ route('admin.manage_drivers') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.manage_drivers') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }}">Kelola Driver</a>
                <a href="{{ route('admin.manage_models') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.manage_models') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }}">Model AI</a>
            @else
                <a href="{{ route('driver.monitor') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('driver.monitor') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }}">Kamera</a>
                <a href="{{ route('driver.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('driver.dashboard') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }}">Riwayat</a>
                <a href="{{ route('driver.profile') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('driver.profile') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }}">Profil</a>
            @endif
        </nav>
        <div class="p-4 border-t border-slate-200">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition cursor-pointer">
                    Logout
                </button>
            </form>
        </div>
    </aside>
    <script>
    (function () {
        var panel = document.getElementById('navPanel');
        if (!panel) return;
        var overlay = document.getElementById('navOverlay');
        var btn = document.getElementById('navToggle');
        function open() {
            panel.classList.remove('translate-x-full', 'invisible');
            overlay.classList.remove('hidden');
            panel.setAttribute('aria-hidden', 'false');
            if (btn) btn.setAttribute('aria-expanded', 'true');
        }
        function close() {
            panel.classList.add('translate-x-full', 'invisible');
            overlay.classList.add('hidden');
            panel.setAttribute('aria-hidden', 'true');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        }
        window.dsNavOpen = open;
        window.dsNavClose = close;
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
        window.addEventListener('resize', function () { if (window.innerWidth >= 768) close(); });
    })();
    </script>
    @endauth

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="p-3.5 mb-3 rounded-xl border bg-emerald-50 border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-700 font-bold ml-4 leading-none cursor-pointer">&times;</button>
            </div>
        @endif
        @if(session('danger'))
            <div class="p-3.5 mb-3 rounded-xl border bg-red-50 border-red-200 text-red-800 text-sm font-medium flex items-center justify-between shadow-2xs">
                <span>{{ session('danger') }}</span>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-700 font-bold ml-4 leading-none cursor-pointer">&times;</button>
            </div>
        @endif
        @if(session('warning'))
            <div class="p-3.5 mb-3 rounded-xl border bg-amber-50 border-amber-200 text-amber-800 text-sm font-medium flex items-center justify-between shadow-2xs">
                <span>{{ session('warning') }}</span>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-700 font-bold ml-4 leading-none cursor-pointer">&times;</button>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200/80 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500">
            &copy; 2026 Tim PBL IF3-PD15 &mdash; Driver Monitoring System berbasis Computer Vision & AI.
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
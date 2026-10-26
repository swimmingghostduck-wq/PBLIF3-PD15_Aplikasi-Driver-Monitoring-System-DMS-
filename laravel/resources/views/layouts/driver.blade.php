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


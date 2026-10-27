<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Administrator - DriveSafe')</title>
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
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
                        heading: ['Poppins', 'Inter', 'ui-sans-serif', 'sans-serif'],
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
        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }
        /* Custom Utilitarian Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
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
        /* Utilitarian Focus Ring */
        :focus-visible {
            outline: 2px solid #2563EB;
            outline-offset: 1px;
        }
        /* Native Input Caret */
        input, select, textarea {
            caret-color: #2563EB;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 min-h-screen font-sans antialiased selection:bg-blue-600 selection:text-white">

    <div class="min-h-screen flex flex-col lg:flex-row bg-[#F8FAFC]">
        
        <!-- Mobile Sidebar Backdrop Overlay -->
        <div id="adminSidebarBackdrop" 
             onclick="toggleAdminSidebar(false)" 
             class="fixed inset-0 z-40 bg-slate-950/40 backdrop-blur-xs hidden transition-opacity lg:hidden"></div>

        <!-- Sidebar Navigation (Deep Royal Fleet Blue Theme: Distinct, Elegant, Non-Monotonous) -->
        <aside id="adminSidebar" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-[#1E3A8A] border-r border-[#172554] text-blue-100 flex flex-col justify-between -translate-x-full transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 lg:h-screen lg:sticky lg:top-0 shrink-0 shadow-lg">
            
            <div class="flex flex-col h-full">
                <!-- Brand Header -->
                <div class="h-16 px-5 flex items-center justify-between border-b border-blue-900/60 bg-[#172554]/30">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                        <img src="{{ asset('logo.svg') }}" alt="DriveSafe Logo" class="w-8 h-8 shrink-0 transition-transform group-hover:scale-105">
                        <div class="flex items-center gap-1.5">
                            <span class="font-heading font-bold text-base text-white tracking-tight">DriveSafe</span>
                            <span class="text-[10px] font-mono font-semibold px-1.5 py-0.5 rounded bg-blue-500/25 text-blue-200 border border-blue-400/30">ADMIN</span>
                        </div>
                    </a>
                    <!-- Mobile close button -->
                    <button type="button" 
                            onclick="toggleAdminSidebar(false)" 
                            class="p-1 rounded-md text-blue-200 hover:text-white hover:bg-white/10 lg:hidden cursor-pointer"
                            title="Tutup Menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <div class="p-3.5 space-y-6 flex-1 overflow-y-auto">
                    <!-- SECTION 1: MONITORING DRIVER -->
                    <div>
                        <span class="px-2.5 text-[11px] font-semibold text-blue-300 uppercase tracking-wider block mb-2 font-mono">MONITORING DRIVER</span>
                        <nav class="space-y-1">
                            <!-- Dashboard -->
                            <a href="{{ route('admin.dashboard') }}" 
                               class="group flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white font-semibold shadow-xs' : 'text-blue-100 hover:text-white hover:bg-white/10' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"></path>
                                </svg>
                                <span>Dashboard</span>
                            </a>

                            <!-- Ringkasan Insiden -->
                            <a href="{{ route('admin.alerts') }}" 
                               class="group flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.alerts') ? 'bg-blue-600 text-white font-semibold shadow-xs' : 'text-blue-100 hover:text-white hover:bg-white/10' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.alerts') ? 'text-white' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
                                </svg>
                                <span>Ringkasan Insiden</span>
                            </a>

                            <!-- Kelola Pengemudi -->
                            <a href="{{ route('admin.manage_drivers') }}" 
                               class="group flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.manage_drivers') ? 'bg-blue-600 text-white font-semibold shadow-xs' : 'text-blue-100 hover:text-white hover:bg-white/10' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.manage_drivers') ? 'text-white' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
                                </svg>
                                <span>Kelola Pengemudi</span>
                            </a>
                        </nav>
                    </div>

                    <!-- SECTION 2: SISTEM & AI -->
                    <div>
                        <span class="px-2.5 text-[11px] font-semibold text-blue-300 uppercase tracking-wider block mb-2 font-mono">SISTEM &amp; AI</span>
                        <nav class="space-y-1">
                            <!-- Model AI & Evaluasi -->
                            <a href="{{ route('admin.manage_models') }}" 
                               class="group flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.manage_models') ? 'bg-blue-600 text-white font-semibold shadow-xs' : 'text-blue-100 hover:text-white hover:bg-white/10' }}"
                               title="Statistik akurasi model AI & matriks evaluasi">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.manage_models') ? 'text-white' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>Statistik Model AI</span>
                            </a>
                        </nav>
                    </div>

                    <!-- SECTION 3: AKUN -->
                    <div>
                        <span class="px-2.5 text-[11px] font-semibold text-blue-300 uppercase tracking-wider block mb-2 font-mono">AKUN</span>
                        <nav class="space-y-1">
                            <!-- Profil Saya -->
                            <a href="{{ route('admin.profile') }}" 
                               class="group flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.profile') ? 'bg-blue-600 text-white font-semibold shadow-xs' : 'text-blue-100 hover:text-white hover:bg-white/10' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.profile') ? 'text-white' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                                </svg>
                                <span>Pengaturan Profil</span>
                            </a>

                            <!-- Keluar -->
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="w-full group flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-blue-200 hover:text-white hover:bg-rose-500/25 transition-all cursor-pointer text-left" 
                                        title="Keluar dari akun">
                                    <svg class="w-4 h-4 text-blue-300 group-hover:text-rose-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"></path>
                                    </svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </nav>
                    </div>
                </div>

                <!-- User Profile Anchored at Bottom -->
                <div class="p-3.5 border-t border-blue-900/60 bg-black/15">
                    <a href="{{ route('admin.profile') }}" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/10 transition-all group" title="Pengaturan Akun Administrator">
                        <div class="w-8 h-8 rounded-lg bg-white text-blue-900 flex items-center justify-center font-bold text-xs font-mono shrink-0 shadow-xs">
                            {{ strtoupper(substr(Auth::user()->full_name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-xs font-semibold text-white block truncate leading-tight">{{ Auth::user()->full_name }}</span>
                            <span class="text-[11px] text-blue-200 block truncate font-mono mt-0.5">{{ '@' . Auth::user()->username }} &bull; Admin</span>
                        </div>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Body Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            
            <!-- Topbar Header: Clean Breadcrumb & Contextual Page Actions -->
            <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/90 h-16 sticky top-0 z-30 px-6 sm:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button type="button" 
                            onclick="toggleAdminSidebar(true)" 
                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 lg:hidden cursor-pointer"
                            title="Buka Navigasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Breadcrumbs -->
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-slate-400 hidden sm:inline font-medium">DriveSafe</span>
                        <span class="text-slate-300 hidden sm:inline">/</span>
                        <span class="font-semibold text-slate-900">
                            @if(request()->routeIs('admin.dashboard'))
                                Dashboard
                            @elseif(request()->routeIs('admin.alerts'))
                                Ringkasan Insiden
                            @elseif(request()->routeIs('admin.manage_drivers'))
                                Kelola Pengemudi
                            @elseif(request()->routeIs('admin.manage_models'))
                                Statistik Model AI
                            @elseif(request()->routeIs('admin.profile'))
                                Pengaturan Profil
                            @else
                                Administrator
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Page-Level Action Slot -->
                <div class="flex items-center gap-2">
                    @yield('header_actions')
                </div>
            </header>

            <!-- Flash Notification Messages -->
            <div class="w-full px-6 sm:px-8 lg:px-10 mt-4">
                @if(session('success'))
                    <div class="p-3 mb-3 rounded-xl border text-xs font-medium flex items-center justify-between bg-emerald-50 border-emerald-200 text-emerald-800">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-700 font-bold ml-4 leading-none cursor-pointer text-sm">&times;</button>
                    </div>
                @endif
                @if(session('danger'))
                    <div class="p-3 mb-3 rounded-xl border text-xs font-medium flex items-center justify-between bg-rose-50 border-rose-200 text-rose-700">
                        <span>{{ session('danger') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-700 font-bold ml-4 leading-none cursor-pointer text-sm">&times;</button>
                    </div>
                @endif
                @if(session('info'))
                    <div class="p-3 mb-3 rounded-xl border text-xs font-medium flex items-center justify-between bg-slate-100 border-slate-200 text-slate-800">
                        <span>{{ session('info') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-700 font-bold ml-4 leading-none cursor-pointer text-sm">&times;</button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="p-3 mb-3 rounded-xl border text-xs font-medium bg-rose-50 border-rose-200 text-rose-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Main Content Canvas -->
            <main class="flex-1 w-full pb-10">
                @yield('content')
            </main>

            <!-- Minimalist Footer -->
            <footer class="border-t border-slate-200/80 py-4 mt-auto bg-white">
                <div class="w-full px-6 sm:px-8 lg:px-10 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-400 font-mono">
                    <div>
                        DriveSafe DMS &mdash; Driver Monitoring System (Laravel Edition)
                    </div>
                    <div>
                        Politeknik Negeri Batam
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Toggle Script for Mobile Sidebar -->
    <script>
        function toggleAdminSidebar(open) {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('adminSidebarBackdrop');
            if (open) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }
    </script>
    @yield('scripts')
</body>
</html>

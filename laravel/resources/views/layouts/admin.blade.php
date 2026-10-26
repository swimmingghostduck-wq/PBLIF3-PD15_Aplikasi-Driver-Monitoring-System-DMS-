<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Administrator - DriveSafe')</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
                        heading: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'monospace']
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
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        /* Utilitarian Focus Ring */
        :focus-visible {
            outline: 2px solid #0f172a;
            outline-offset: 1px;
        }
        /* Native Input Caret */
        input, select, textarea {
            caret-color: #0f172a;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen font-sans antialiased selection:bg-slate-900 selection:text-white">

    <div class="min-h-screen flex flex-col lg:flex-row bg-slate-50">
        
        <!-- Mobile Sidebar Backdrop Overlay -->
        <div id="adminSidebarBackdrop" 
             onclick="toggleAdminSidebar(false)" 
             class="fixed inset-0 z-40 bg-slate-950/60 hidden transition-opacity lg:hidden"></div>

        <!-- Sidebar Navigation (Linear / Vercel Dark Neutral Theme) -->
        <aside id="adminSidebar" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-950 border-r border-slate-900 text-slate-400 flex flex-col justify-between -translate-x-full transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 lg:h-screen lg:sticky lg:top-0 shrink-0">
            
            <div class="flex flex-col h-full">
                <!-- Brand Header -->
                <div class="h-14 px-5 flex items-center justify-between border-b border-slate-900 bg-slate-950">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                        <div class="w-7 h-7 bg-white text-slate-950 rounded-md flex items-center justify-center font-bold text-xs border border-white">
                            <svg class="w-4 h-4 text-slate-950" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 3v18M3 12h18"></path>
                            </svg>
                        </div>
                        <div class="flex items-baseline gap-1.5">
                            <span class="font-semibold text-sm text-white tracking-tight">DriveSafe</span>
                            <span class="text-[10px] text-slate-400 font-mono tracking-wider">DMS</span>
                        </div>
                    </a>
                    <!-- Mobile close button -->
                    <button type="button" 
                            onclick="toggleAdminSidebar(false)" 
                            class="p-1 rounded-md text-slate-400 hover:text-white hover:bg-slate-900 lg:hidden cursor-pointer"
                            title="Tutup Menu">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <div class="p-3 space-y-6 flex-1 overflow-y-auto">
                    <!-- SECTION 1: MONITORING DRIVER -->
                    <div>
                        <span class="px-2.5 text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1.5 font-mono">MONITORING DRIVER</span>
                        <nav class="space-y-0.5">
                            <!-- Dashboard -->
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"></path>
                                </svg>
                                <span>Dashboard</span>
                            </a>

                            <!-- Ringkasan Insiden -->
                            <a href="{{ route('admin.alerts') }}" 
                               class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ request()->routeIs('admin.alerts') ? 'bg-white/10 text-white font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.alerts') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
                                </svg>
                                <span>Ringkasan Insiden</span>
                            </a>

                            <!-- Kelola Pengemudi -->
                            <a href="{{ route('admin.manage_drivers') }}" 
                               class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ request()->routeIs('admin.manage_drivers') ? 'bg-white/10 text-white font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.manage_drivers') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
                                </svg>
                                <span>Kelola Pengemudi</span>
                            </a>
                        </nav>
                    </div>

                    <!-- SECTION 2: SISTEM & AI -->
                    <div>
                        <span class="px-2.5 text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1.5 font-mono">SISTEM &amp; AI</span>
                        <nav class="space-y-0.5">
                            <!-- Model AI & Evaluasi -->
                            <a href="{{ route('admin.manage_models') }}" 
                               class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ request()->routeIs('admin.manage_models') ? 'bg-white/10 text-white font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' }}"
                               title="Spesifikasi teknis model AI & evaluasi matriks konfusi">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.manage_models') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>Model AI &amp; Evaluasi</span>
                            </a>
                        </nav>
                    </div>

                    <!-- SECTION 3: AKUN -->
                    <div>
                        <span class="px-2.5 text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1.5 font-mono">AKUN</span>
                        <nav class="space-y-0.5">
                            <!-- Profil Saya -->
                            <a href="{{ route('admin.profile') }}" 
                               class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ request()->routeIs('admin.profile') ? 'bg-white/10 text-white font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.profile') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                                </svg>
                                <span>Profil Saya</span>
                            </a>

                            <!-- Keluar -->
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-xs text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-colors cursor-pointer text-left" 
                                        title="Keluar dari akun">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"></path>
                                    </svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </nav>
                    </div>
                </div>

                <!-- User Profile Anchored at Bottom (Linear Style) -->
                <div class="p-3 border-t border-slate-900 bg-slate-950">
                    <a href="{{ route('admin.profile') }}" class="flex items-center gap-2.5 p-1.5 rounded-md hover:bg-white/5 transition-colors group" title="Pengaturan Akun Administrator">
                        <div class="w-7 h-7 rounded bg-slate-800 text-slate-200 border border-slate-700/60 flex items-center justify-center font-bold text-[11px] font-mono shrink-0">
                            {{ strtoupper(substr(Auth::user()->full_name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-xs font-medium text-slate-200 block truncate group-hover:text-white transition-colors leading-tight">{{ Auth::user()->full_name }}</span>
                            <span class="text-[10px] text-slate-400 block truncate font-mono mt-0.5">{{ '@' . Auth::user()->username }} &bull; Admin</span>
                        </div>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Body Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            
            <!-- Topbar Header: Clean Breadcrumb & Contextual Page Actions -->
            <header class="bg-white border-b border-slate-200 h-14 sticky top-0 z-30 px-6 sm:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button type="button" 
                            onclick="toggleAdminSidebar(true)" 
                            class="p-1.5 rounded-md text-slate-500 hover:text-slate-900 hover:bg-slate-100 lg:hidden cursor-pointer"
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
                                Model AI &amp; Evaluasi
                            @elseif(request()->routeIs('admin.profile'))
                                Profil Saya
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
                    <div class="p-3 mb-3 rounded-md border text-xs font-medium flex items-center justify-between bg-emerald-50 border-emerald-200 text-emerald-800">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-700 font-bold ml-4 leading-none cursor-pointer text-sm">&times;</button>
                    </div>
                @endif
                @if(session('danger'))
                    <div class="p-3 mb-3 rounded-md border text-xs font-medium flex items-center justify-between bg-rose-50 border-rose-200 text-rose-700">
                        <span>{{ session('danger') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-700 font-bold ml-4 leading-none cursor-pointer text-sm">&times;</button>
                    </div>
                @endif
                @if(session('info'))
                    <div class="p-3 mb-3 rounded-md border text-xs font-medium flex items-center justify-between bg-slate-100 border-slate-200 text-slate-800">
                        <span>{{ session('info') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-700 font-bold ml-4 leading-none cursor-pointer text-sm">&times;</button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="p-3 mb-3 rounded-md border text-xs font-medium bg-rose-50 border-rose-200 text-rose-700">
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
            <footer class="border-t border-slate-200 py-3.5 mt-auto bg-white">
                <div class="w-full px-6 sm:px-8 lg:px-10 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-400 font-mono">
                    <div>
                        DriveSafe DMS &mdash; Driver Monitoring System (Laravel MVC Edition).
                    </div>
                    <div>
                        Versi 1.0 &bull; Politeknik Negeri Batam
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


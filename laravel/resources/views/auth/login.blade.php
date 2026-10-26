<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - DriveSafe</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">
    <!-- Google Fonts: Poppins (Heading) & Inter (Body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        heading: ['Poppins', 'Inter', 'ui-sans-serif', 'sans-serif'],
                        mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'monospace']
                    },
                    colors: {
                        primary: '#2563EB',
                        secondary: '#0EA5E9'
                    }
                }
            }
        }
    </script>
    <style>
        :focus-visible {
            outline: 2px solid #2563EB;
            outline-offset: 1px;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 min-h-screen flex flex-col justify-center items-center font-sans antialiased p-4 selection:bg-blue-600 selection:text-white">

<div class="w-full max-w-[400px] mx-auto py-8">
    <!-- Brand Header -->
    <div class="text-center mb-6">
        <a href="{{ url('/') }}" class="inline-block group mb-3">
            <img src="{{ asset('logo.svg') }}" alt="DriveSafe Logo" class="w-12 h-12 mx-auto transition-transform group-hover:scale-105">
        </a>
        <h1 class="text-2xl font-bold font-heading text-slate-900 tracking-tight">
            Masuk ke DriveSafe
        </h1>
        <p class="text-xs text-slate-500 mt-1">
            Sistem Pemantauan Keselamatan Pengemudi Armada
        </p>
    </div>

    <!-- Flash Messages -->
    @if(session('danger'))
        <div class="mb-4 p-3 rounded-xl text-xs font-medium border bg-rose-50 border-rose-200 text-rose-700 flex items-center justify-between">
            <span>{{ session('danger') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer">&times;</button>
        </div>
    @endif
    @if(session('success'))
        <div class="mb-4 p-3 rounded-xl text-xs font-medium border bg-emerald-50 border-emerald-200 text-emerald-800 flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer">&times;</button>
        </div>
    @endif
    @if(session('info'))
        <div class="mb-4 p-3 rounded-xl text-xs font-medium border bg-slate-100 border-slate-200 text-slate-800 flex items-center justify-between">
            <span>{{ session('info') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer">&times;</button>
        </div>
    @endif

    <!-- Card Form -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 sm:p-7">
        <!-- Role Quick Selector -->
        <div class="mb-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-500">Pilih Akun Masuk</span>
                <span class="text-[10px] text-slate-400 font-mono">Autofill kredensial demo</span>
            </div>
            <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-100/80 rounded-xl border border-slate-200/60">
                <button type="button" id="btnRoleDriver" onclick="fillAccount('driver')"
                        class="py-1.5 px-3 rounded-lg text-xs font-semibold transition-all text-center flex items-center justify-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200/80 shadow-2xs cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 3v18M3 12h18"></path>
                    </svg>
                    <span>Driver</span>
                </button>
                <button type="button" id="btnRoleAdmin" onclick="fillAccount('admin')"
                        class="py-1.5 px-3 rounded-lg text-xs font-medium transition-all text-center flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900 border border-transparent cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>Admin</span>
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-xs font-medium text-slate-700 mb-1">
                    Username
                </label>
                <input type="text" id="username" name="username" value="{{ old('username', 'apri') }}" required autofocus
                       class="w-full h-10 bg-slate-50/50 border border-slate-300 rounded-xl px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all font-mono"
                       placeholder="Masukkan username">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-medium text-slate-700">
                        Password
                    </label>
                    <span class="text-[10px] text-slate-400 font-mono">driver123 / admin123</span>
                </div>
                <div class="relative">
                    <input type="password" id="password" name="password" value="driver123" required
                           class="w-full h-10 bg-slate-50/50 border border-slate-300 rounded-xl px-3 pr-10 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all font-mono"
                           placeholder="Masukkan password">
                    <button type="button" onclick="togglePassword('password')" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                            aria-label="Toggle password">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-0.5">
                <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span>Ingat sesi masuk</span>
                </label>
            </div>

            <button type="submit"
                    class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center justify-center gap-2 shadow-xs hover:shadow">
                <span>Masuk Sekarang</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </form>

        <div class="mt-5 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Pengemudi baru? 
            <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                Daftar akun di sini
            </a>
        </div>
    </div>
</div>

<script>
    function fillAccount(role) {
        const u = document.getElementById('username');
        const p = document.getElementById('password');
        const bD = document.getElementById('btnRoleDriver');
        const bA = document.getElementById('btnRoleAdmin');

        const activeClass = 'py-1.5 px-3 rounded-lg text-xs font-semibold transition-all text-center flex items-center justify-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200/80 shadow-2xs cursor-pointer';
        const inactiveClass = 'py-1.5 px-3 rounded-lg text-xs font-medium transition-all text-center flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900 border border-transparent cursor-pointer';

        if (role === 'driver') {
            u.value = 'apri';
            p.value = 'driver123';
            bD.className = activeClass;
            bA.className = inactiveClass;
            bD.querySelector('svg').setAttribute('class', 'w-3.5 h-3.5 text-blue-600');
            bA.querySelector('svg').setAttribute('class', 'w-3.5 h-3.5 text-slate-500');
        } else {
            u.value = 'admin';
            p.value = 'admin123';
            bA.className = activeClass;
            bD.className = inactiveClass;
            bA.querySelector('svg').setAttribute('class', 'w-3.5 h-3.5 text-blue-600');
            bD.querySelector('svg').setAttribute('class', 'w-3.5 h-3.5 text-slate-500');
        }
    }

    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
</body>
</html>

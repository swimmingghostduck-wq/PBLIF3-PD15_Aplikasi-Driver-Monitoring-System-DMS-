<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - DriveSafe</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'monospace']
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col justify-center items-center font-sans antialiased p-4 selection:bg-slate-900 selection:text-white">

<div class="w-full max-w-[390px] mx-auto py-8">
    <!-- Brand Header -->
    <div class="text-center mb-6">
        <div class="w-12 h-12 bg-slate-900 text-white rounded-lg mx-auto mb-3 flex items-center justify-center font-bold text-lg">
            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 3v18M3 12h18"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            Masuk ke DriveSafe
        </h1>
        <p class="text-xs text-slate-500 mt-1">
            Sistem Pemantauan Keselamatan Pengemudi Armada
        </p>
    </div>

    <!-- Flash Messages -->
    @if(session('danger'))
        <div class="mb-4 p-3 rounded-md text-xs font-medium border bg-rose-50 border-rose-200 text-rose-700 flex items-center justify-between">
            <span>{{ session('danger') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>
    @endif
    @if(session('success'))
        <div class="mb-4 p-3 rounded-md text-xs font-medium border bg-emerald-50 border-emerald-200 text-emerald-800 flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>
    @endif
    @if(session('info'))
        <div class="mb-4 p-3 rounded-md text-xs font-medium border bg-slate-100 border-slate-200 text-slate-800 flex items-center justify-between">
            <span>{{ session('info') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>
    @endif

    <!-- Card Form -->
    <div class="bg-white rounded-lg border border-slate-200 p-6 sm:p-7">
        <!-- Role Quick Selector -->
        <div class="mb-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-500">Pilih Akun Cepat</span>
                <span class="text-[10px] text-slate-400 font-mono">Klik untuk autofill</span>
            </div>
            <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-md">
                <button type="button" id="btnRoleDriver" onclick="fillAccount('driver')"
                        class="py-1.5 px-3 rounded text-xs font-medium transition-all text-center flex items-center justify-center gap-1.5 bg-white text-slate-900 border border-slate-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-slate-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 3v18M3 12h18"></path>
                    </svg>
                    <span>Driver</span>
                </button>
                <button type="button" id="btnRoleAdmin" onclick="fillAccount('admin')"
                        class="py-1.5 px-3 rounded text-xs font-medium transition-all text-center flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900 cursor-pointer">
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
                <input type="text" id="username" name="username" value="{{ old('username', 'driver') }}" required autofocus
                       class="w-full h-9 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all font-mono"
                       placeholder="Masukkan username">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-medium text-slate-700">
                        Password
                    </label>
                    <span class="text-[10px] text-slate-400 font-mono">Default: driver123 / admin123</span>
                </div>
                <div class="relative">
                    <input type="password" id="password" name="password" value="driver123" required
                           class="w-full h-9 bg-white border border-slate-300 rounded-md px-3 pr-10 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all font-mono"
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

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-slate-900 focus:ring-0">
                    <span>Ingat saya</span>
                </label>
            </div>

            <button type="submit"
                    class="w-full h-9 bg-slate-900 hover:bg-slate-800 text-white rounded-md text-xs font-medium transition-colors cursor-pointer flex items-center justify-center gap-2">
                <span>Masuk Sekarang</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </form>

        <div class="mt-5 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Pengemudi baru? 
            <a href="{{ route('register') }}" class="font-medium text-slate-900 hover:underline">
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

        if (role === 'driver') {
            u.value = 'apri';
            p.value = 'driver123';
            bD.className = 'py-1.5 px-3 rounded text-xs font-medium transition-all text-center flex items-center justify-center gap-1.5 bg-white text-slate-900 border border-slate-200 cursor-pointer';
            bA.className = 'py-1.5 px-3 rounded text-xs font-medium transition-all text-center flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900 cursor-pointer';
        } else {
            u.value = 'admin';
            p.value = 'admin123';
            bA.className = 'py-1.5 px-3 rounded text-xs font-medium transition-all text-center flex items-center justify-center gap-1.5 bg-white text-slate-900 border border-slate-200 cursor-pointer';
            bD.className = 'py-1.5 px-3 rounded text-xs font-medium transition-all text-center flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900 cursor-pointer';
        }
    }

    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
</body>
</html>


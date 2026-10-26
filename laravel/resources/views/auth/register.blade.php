<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pengemudi - DriveSafe</title>
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

<div class="w-full max-w-[430px] mx-auto py-8">
    <!-- Brand Header -->
    <div class="text-center mb-6">
        <a href="{{ url('/') }}" class="inline-block group mb-3">
            <img src="{{ asset('logo.svg') }}" alt="DriveSafe Logo" class="w-12 h-12 mx-auto transition-transform group-hover:scale-105">
        </a>
        <h1 class="text-2xl font-bold font-heading text-slate-900 tracking-tight">
            Pendaftaran Pengemudi
        </h1>
        <p class="text-xs text-slate-500 mt-1">
            Buat akun untuk mengaktifkan pemantauan keselamatan kabin
        </p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="mb-4 p-3 rounded-xl text-xs font-medium border bg-rose-50 border-rose-200 text-rose-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card Form -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 sm:p-7">
        <form method="POST" action="{{ route('register') }}" class="space-y-4 text-xs font-sans">
            @csrf
            <div>
                <label for="full_name" class="block font-medium text-slate-700 mb-1">Nama Lengkap *</label>
                <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required autofocus
                       class="w-full h-10 bg-slate-50/50 border border-slate-300 rounded-xl px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all"
                       placeholder="Nama lengkap Anda">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="username" class="block font-medium text-slate-700 mb-1">Username *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-mono">@</span>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required
                               class="w-full h-10 bg-slate-50/50 border border-slate-300 rounded-xl pl-7 pr-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all font-mono"
                               placeholder="username">
                    </div>
                </div>

                <div>
                    <label for="nim_nik" class="block font-medium text-slate-700 mb-1">NIM / NIK / No. Induk</label>
                    <input type="text" id="nim_nik" name="nim_nik" value="{{ old('nim_nik') }}"
                           class="w-full h-10 bg-slate-50/50 border border-slate-300 rounded-xl px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all font-mono"
                           placeholder="33125010xx">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block font-medium text-slate-700 mb-1">Kata Sandi *</label>
                    <input type="password" id="password" name="password" required minlength="6"
                           class="w-full h-10 bg-slate-50/50 border border-slate-300 rounded-xl px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all font-mono"
                           placeholder="Min. 6 karakter">
                </div>
                <div>
                    <label for="password_confirmation" class="block font-medium text-slate-700 mb-1">Konfirmasi *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="6"
                           class="w-full h-10 bg-slate-50/50 border border-slate-300 rounded-xl px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all font-mono"
                           placeholder="Ulangi sandi">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center justify-center gap-2 shadow-xs hover:shadow">
                    <span>Daftar Akun Driver</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </div>
        </form>

        <div class="mt-5 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                Masuk di sini
            </a>
        </div>
    </div>
</div>
</body>
</html>

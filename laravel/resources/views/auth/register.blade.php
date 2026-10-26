<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pengemudi - DriveSafe</title>
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

<div class="w-full max-w-[420px] mx-auto py-8">
    <!-- Brand Header -->
    <div class="text-center mb-6">
        <div class="w-12 h-12 bg-slate-900 text-white rounded-lg mx-auto mb-3 flex items-center justify-center font-bold text-lg">
            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 3v18M3 12h18"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            Pendaftaran Pengemudi
        </h1>
        <p class="text-xs text-slate-500 mt-1">
            Buat akun untuk mengaktifkan pemantauan keselamatan kabin
        </p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="mb-4 p-3 rounded-md text-xs font-medium border bg-rose-50 border-rose-200 text-rose-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card Form -->
    <div class="bg-white rounded-lg border border-slate-200 p-6 sm:p-7">
        <form method="POST" action="{{ route('register') }}" class="space-y-3.5 text-xs font-sans">
            @csrf
            <div>
                <label for="full_name" class="block font-medium text-slate-700 mb-1">Nama Lengkap *</label>
                <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required autofocus
                       class="w-full h-8.5 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all"
                       placeholder="Nama lengkap Anda">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="username" class="block font-medium text-slate-700 mb-1">Username *</label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-2 text-xs text-slate-400 font-mono">@</span>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required
                               class="w-full h-8.5 bg-white border border-slate-300 rounded-md pl-6 pr-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all font-mono"
                               placeholder="username">
                    </div>
                </div>

                <div>
                    <label for="nim_nik" class="block font-medium text-slate-700 mb-1">NIM / NIK / No. Induk</label>
                    <input type="text" id="nim_nik" name="nim_nik" value="{{ old('nim_nik') }}"
                           class="w-full h-8.5 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all font-mono"
                           placeholder="33125010xx">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block font-medium text-slate-700 mb-1">Kata Sandi *</label>
                    <input type="password" id="password" name="password" required minlength="6"
                           class="w-full h-8.5 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all font-mono"
                           placeholder="Minimal 6 karakter">
                </div>
                <div>
                    <label for="password_confirmation" class="block font-medium text-slate-700 mb-1">Konfirmasi *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="6"
                           class="w-full h-8.5 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all font-mono"
                           placeholder="Ulangi sandi">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="w-full h-9 bg-slate-900 hover:bg-slate-800 text-white rounded-md text-xs font-medium transition-colors cursor-pointer flex items-center justify-center gap-2">
                    <span>Daftar Akun Driver</span>
                </button>
            </div>
        </form>

        <div class="mt-5 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-medium text-slate-900 hover:underline">
                Masuk di sini
            </a>
        </div>
    </div>
</div>
</body>
</html>


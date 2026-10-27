<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0A1329]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pengemudi - DriveSafe</title>
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
                        heading: ['Poppins', 'Inter', 'ui-sans-serif', 'sans-serif']
                    },
                    colors: {
                        primary: '#2563EB',
                        secondary: '#0EA5E9',
                        fleet: {
                            950: '#070E20',
                            900: '#0A1329',
                            800: '#0D1C3E',
                            700: '#10234E',
                            600: '#2563EB'
                        }
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
<body class="min-h-screen w-full bg-[#0A1329] bg-gradient-to-b from-[#10234E] via-[#0D1C3E] to-[#070E20] bg-fixed text-slate-900 font-sans antialiased selection:bg-blue-600 selection:text-white flex flex-col justify-between p-4 sm:p-6 lg:p-8 relative overflow-x-hidden">

    <!-- ========================================================
         HEADER ATAS (BERSIH, TANPA BADGE KETERANGAN OPERASIONAL)
         ======================================================== -->
    <header class="w-full max-w-[920px] mx-auto px-2 py-2 flex items-center justify-between relative z-10">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/25">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9" />
                    <circle cx="12" cy="12" r="3" />
                    <path d="M12 3v6m0 6v6M3 12h6m6 0h6" stroke-linecap="round" />
                </svg>
            </div>
            <div>
                <span class="text-lg font-bold font-heading text-white tracking-tight leading-none block">DriveSafe</span>
                <span class="text-[10px] font-medium text-blue-200">Driver Monitoring System</span>
            </div>
        </div>

        <div>
            <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-md transition">
                Masuk ke Akun
            </a>
        </div>
    </header>

    <!-- ========================================================
         KONTEN UTAMA: MODAL SPLIT DUA KOLOM PROPORSI BESAR (BORDERLESS)
         ======================================================== -->
    <main class="w-full max-w-[920px] min-h-[520px] md:min-h-[560px] mx-auto bg-white rounded-2xl shadow-2xl shadow-black/60 overflow-hidden grid grid-cols-1 md:grid-cols-2 relative my-auto border-0">
        
        <!-- ========================================================
             SEKSI KIRI: FORMULIR PENDAFTARAN (KOTAK INPUT LEGA & ERGONOMIS)
             ======================================================== -->
        <div class="p-7 sm:p-9 lg:p-10 flex flex-col justify-between bg-white order-2 md:order-1 overflow-y-auto">
            <div>
                <!-- Header Formulir -->
                <div class="mb-4">
                    <div class="flex items-center gap-2 mb-1.5">
                        <a href="{{ route('login') }}" class="text-slate-400 hover:text-slate-700 transition p-1 -ml-1 rounded-lg" title="Kembali ke login">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <h1 class="text-2xl sm:text-[1.65rem] font-bold font-heading text-slate-900 tracking-tight">
                            Daftar Akun Pengemudi
                        </h1>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 ml-6 leading-relaxed">
                        Lengkapi data diri Anda untuk mengaktifkan pemantauan keselamatan kabin.
                    </p>
                </div>

                <!-- Notifikasi Error Validasi -->
                @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-xs border border-red-200">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Formulir Registrasi dengan Kotak Input Standard & Umum -->
                <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                    @csrf

                    <!-- Nama Lengkap: Standar Web (h-11, rounded-lg, border-slate-300, bg-white) -->
                    <div>
                        <label for="full_name" class="block text-xs font-medium text-slate-700 mb-1.5">
                            Nama Lengkap
                        </label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required autofocus
                               placeholder="Contoh: Budi Santoso"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition shadow-xs">
                    </div>

                    <!-- Username & NIK (2 Kolom Proporsional) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label for="username" class="block text-xs font-medium text-slate-700 mb-1.5">
                                Username
                            </label>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" required
                                   placeholder="budi_driver"
                                   class="w-full h-11 px-3.5 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition shadow-xs">
                        </div>

                        <div>
                            <label for="nim_nik" class="block text-xs font-medium text-slate-700 mb-1.5">
                                NIK <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                            </label>
                            <input type="text" id="nim_nik" name="nim_nik" value="{{ old('nim_nik') }}"
                                   placeholder="16 digit NIK"
                                   class="w-full h-11 px-3.5 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition shadow-xs">
                        </div>
                    </div>

                    <!-- Kata Sandi & Konfirmasi Sandi (2 Kolom Proporsional) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label for="password" class="block text-xs font-medium text-slate-700 mb-1.5">
                                Kata Sandi
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required
                                       placeholder="Min 6 karakter"
                                       class="w-full h-11 pl-3.5 pr-11 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition shadow-xs">
                                <button type="button" onclick="togglePasswordVisibility('password', this)"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 rounded focus:outline-none transition cursor-pointer"
                                        title="Tampilkan / Sembunyikan Kata Sandi">
                                    <svg class="w-5 h-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-medium text-slate-700 mb-1.5">
                                Konfirmasi Sandi
                            </label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                       placeholder="Ulangi sandi"
                                       class="w-full h-11 pl-3.5 pr-11 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition shadow-xs">
                                <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 rounded focus:outline-none transition cursor-pointer"
                                        title="Tampilkan / Sembunyikan Kata Sandi">
                                    <svg class="w-5 h-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Submit Standar Umum Web (h-11, rounded-lg, solid blue) -->
                    <button type="submit"
                            class="w-full h-11 mt-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-medium text-sm rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2 cursor-pointer">
                        <span>Daftar Akun Pengemudi</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Tautan Masuk Bawah -->
            <div class="mt-5 pt-4 border-t border-slate-100 text-center">
                <p class="text-xs sm:text-sm text-slate-500">
                    Sudah memiliki akun pengemudi? 
                    <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">
                        Masuk di sini
                    </a>
                </p>
            </div>
        </div>

        <!-- ========================================================
             SEKSI KANAN: COVER VISUAL LIFESTYLE (LEGA, TEKS AKURAT)
             ======================================================== -->
        <div class="hidden md:flex relative h-full w-full bg-slate-900 overflow-hidden order-1 md:order-2 flex-col justify-end p-8 lg:p-10 select-none">
            <!-- Gambar Cover Vektor (Full Bleed, High-Res, No White Border) -->
            <img src="{{ asset('images/register-cover.svg') }}" 
                 alt="DriveSafe Driver Safety System" 
                 class="absolute inset-0 w-full h-full object-cover">
            
            <!-- Lapisan Gradien Bayangan Halus untuk Legibilitas Teks Bersih -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/45 to-transparent"></div>

            <!-- Konten Teks yang Diperbarui (Sistem Keselamatan Aktif) -->
            <div class="relative z-10 text-white">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-600 text-[10px] font-bold uppercase tracking-wider text-white mb-2.5 shadow-md">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    SISTEM KESELAMATAN AKTIF
                </div>
                <h3 class="text-xl sm:text-2xl font-bold font-heading text-white leading-tight">
                    Perjalanan Terlindungi, Fokus Terjaga
                </h3>
                <p class="text-xs sm:text-sm text-slate-200 mt-2 leading-relaxed max-w-sm">
                    Daftarkan akun Anda untuk mengaktifkan pemantauan Computer Vision cerdas yang menjaga fokus dan mencegah risiko microsleep di setiap kilometer.
                </p>
            </div>
        </div>

    </main>

    <!-- Script Toggle Password Visibility -->
    <script>
        function togglePasswordVisibility(fieldId, btn) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                btn.innerHTML = `
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                `;
            } else {
                field.type = 'password';
                btn.innerHTML = `
                    <svg class="w-4 h-4 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                `;
            }
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0A1329]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - DriveSafe</title>
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
<body class="min-h-screen w-full bg-[#0A1329] bg-gradient-to-b from-[#10234E] via-[#0D1C3E] to-[#070E20] bg-fixed text-slate-900 font-sans antialiased selection:bg-blue-600 selection:text-white flex items-center justify-center p-4 sm:p-6 lg:p-8 relative overflow-x-hidden">

    <!-- ========================================================
         KONTEN UTAMA: MODAL SPLIT DUA KOLOM PROPORSI STANDARD
         ======================================================== -->
    <main class="w-full max-w-[920px] min-h-[520px] md:min-h-[560px] mx-auto bg-white rounded-2xl shadow-2xl shadow-black/60 overflow-hidden grid grid-cols-1 md:grid-cols-2 relative my-auto border-0">
        
        <!-- ========================================================
             SEKSI KIRI: FORMULIR AUTENTIKASI (STANDARD & UMUM DIGUNAKAN)
             ======================================================== -->
        <div class="p-7 sm:p-9 lg:p-10 flex flex-col justify-between bg-white order-2 md:order-1 overflow-y-auto">
            <div>
                <!-- Navbar di dalam Card: Brand DriveSafe & Daftar Akun -->
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/25">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9" />
                                <circle cx="12" cy="12" r="3" />
                                <path d="M12 3v6m0 6v6M3 12h6m6 0h6" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-base font-bold font-heading text-slate-900 tracking-tight leading-none block">DriveSafe</span>
                            <span class="text-[10px] font-medium text-slate-500">Driver Monitoring System</span>
                        </div>
                    </div>

                    <a href="{{ route('register') }}" class="px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200/80 text-slate-700 hover:text-slate-900 text-xs font-semibold transition border border-slate-200/80">
                        Daftar Akun
                    </a>
                </div>

                <!-- Header Formulir -->
                <div class="mb-5">
                    <div class="flex items-center gap-2 mb-1.5">
                        <a href="/" class="text-slate-400 hover:text-slate-700 transition p-1 -ml-1 rounded-lg" title="Kembali ke beranda">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <h1 class="text-2xl sm:text-[1.65rem] font-bold font-heading text-slate-900 tracking-tight">
                            Masuk ke DriveSafe
                        </h1>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 ml-6 leading-relaxed">
                        Masuk ke akun Anda untuk memulai pemantauan keselamatan kabin.
                    </p>
                </div>

                <!-- Pemilih Peran Akun (Pengemudi / Admin) -->
                <div class="mb-5">
                    <div class="grid grid-cols-2 p-1 bg-slate-100 rounded-lg gap-1 border border-slate-200/80">
                        <button type="button" id="roleBtnDriver" onclick="selectRole('driver')"
                                class="py-2 px-3 rounded-md text-xs font-semibold {{ old('role', 'driver') === 'driver' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900' }} flex items-center justify-center gap-2 transition-all cursor-pointer">
                            <svg class="w-4 h-4 {{ old('role', 'driver') === 'driver' ? 'text-blue-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Pengemudi</span>
                        </button>
                        <button type="button" id="roleBtnAdmin" onclick="selectRole('admin')"
                                class="py-2 px-3 rounded-md text-xs font-semibold {{ old('role', 'driver') === 'admin' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900' }} flex items-center justify-center gap-2 transition-all cursor-pointer">
                            <svg class="w-4 h-4 {{ old('role', 'driver') === 'admin' ? 'text-blue-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Admin</span>
                        </button>
                    </div>
                </div>

                <!-- Notifikasi Status / Error / Sukses -->
                @if (session('danger') || session('error'))
                <div role="alert" class="mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start justify-between gap-3 shadow-2xs">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div class="leading-relaxed">
                            <span class="font-semibold block text-rose-900">Autentikasi Gagal</span>
                            <span>{{ session('danger') ?? session('error') }}</span>
                        </div>
                    </div>
                    <button type="button" onclick="this.closest('[role=alert]').remove()" class="text-rose-400 hover:text-rose-700 p-0.5 rounded cursor-pointer leading-none font-bold text-base" title="Tutup pemberitahuan">&times;</button>
                </div>
                @endif

                @if ($errors->any() && !session('danger') && !session('error'))
                <div role="alert" class="mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start justify-between gap-3 shadow-2xs">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <ul class="list-disc list-inside space-y-0.5 leading-relaxed">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" onclick="this.closest('[role=alert]').remove()" class="text-rose-400 hover:text-rose-700 p-0.5 rounded cursor-pointer leading-none font-bold text-base" title="Tutup pemberitahuan">&times;</button>
                </div>
                @endif

                @if (session('success'))
                <div role="alert" class="mb-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-start justify-between gap-3 shadow-2xs">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="leading-relaxed">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.closest('[role=alert]').remove()" class="text-emerald-400 hover:text-emerald-700 p-0.5 rounded cursor-pointer leading-none font-bold text-base" title="Tutup pemberitahuan">&times;</button>
                </div>
                @endif

                @if (session('info') || session('status'))
                <div role="alert" class="mb-4 p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs flex items-start justify-between gap-3 shadow-2xs">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="leading-relaxed">{{ session('info') ?? session('status') }}</span>
                    </div>
                    <button type="button" onclick="this.closest('[role=alert]').remove()" class="text-blue-400 hover:text-blue-700 p-0.5 rounded cursor-pointer leading-none font-bold text-base" title="Tutup pemberitahuan">&times;</button>
                </div>
                @endif

                <!-- Formulir Login dengan Kotak Input Standard & Umum -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="role" id="roleInput" value="{{ old('role', 'driver') }}">

                    <!-- Field Username: Standar Umum Web -->
                    <div>
                        <label for="username" class="block text-xs font-medium text-slate-700 mb-1.5">
                            Username
                        </label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required
                               @if(!old('username')) autofocus @endif
                               placeholder="{{ old('role', 'driver') === 'admin' ? 'Masukkan username admin (contoh: admin)' : 'Masukkan username pengemudi (contoh: apri)' }}"
                               class="w-full h-11 px-3.5 rounded-lg border {{ (session('danger') || session('error') || $errors->has('username')) ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : 'border-slate-300 focus:border-blue-600 focus:ring-blue-100' }} bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 transition shadow-xs">
                    </div>

                    <!-- Field Password: Standar Umum Web -->
                    <div>
                        <label for="password" class="block text-xs font-medium text-slate-700 mb-1.5">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   @if(old('username')) autofocus @endif
                                   placeholder="Masukkan kata sandi"
                                   class="w-full h-11 pl-3.5 pr-11 rounded-lg border {{ (session('danger') || session('error') || $errors->has('password')) ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : 'border-slate-300 focus:border-blue-600 focus:ring-blue-100' }} bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 transition shadow-xs">
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

                    <!-- Remember Me -->
                    <div class="flex items-center pt-0.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-xs text-slate-600">Ingat sesi saya</span>
                        </label>
                    </div>

                    <!-- Tombol Utama Submit: Standar Tombol Web Biasa (h-11, rounded-lg, solid blue) -->
                    <button type="submit"
                            class="w-full h-11 mt-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-medium text-sm rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2 cursor-pointer">
                        <span>Masuk</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>

                <!-- Petunjuk Cepat Akun Demo (Memudahkan Penguji / Evaluator) -->
                <div class="mt-4 p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-500 flex items-center justify-between gap-2">
                    <span class="font-medium text-slate-500">Akun Cepat:</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="quickFill('apri', 'driver123', 'driver')"
                                class="px-2 py-1 rounded-md bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-mono text-[11px] cursor-pointer transition shadow-2xs">
                            Driver: <span class="font-semibold text-blue-600">apri</span>
                        </button>
                        <button type="button" onclick="quickFill('admin', 'admin123', 'admin')"
                                class="px-2 py-1 rounded-md bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-mono text-[11px] cursor-pointer transition shadow-2xs">
                            Admin: <span class="font-semibold text-blue-600">admin</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tautan Pendaftaran Bawah -->
            <div class="mt-5 pt-4 border-t border-slate-100 text-center">
                <p class="text-xs sm:text-sm text-slate-500">
                    Belum memiliki akun pengemudi? 
                    <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">
                        Daftar akun di sini
                    </a>
                </p>
            </div>
        </div>

        <!-- ========================================================
             SEKSI KANAN: COVER VISUAL LIFESTYLE (BORDERLESS & SPASIOUS)
             ======================================================== -->
        <div class="hidden md:flex relative h-full w-full bg-slate-900 overflow-hidden order-1 md:order-2 flex-col justify-end p-8 lg:p-10 select-none">
            <!-- Gambar Cover Vektor (Full Bleed, No White Border) -->
            <img src="{{ asset('images/login-cover.svg') }}" 
                 alt="DriveSafe Driver Lifestyle" 
                 class="absolute inset-0 w-full h-full object-cover">
            
            <!-- Lapisan Gradien Bayangan Halus untuk Legibilitas Teks Bersih -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/45 to-transparent"></div>

            <!-- Konten Teks di Atas Area Dasar Gambar -->
            <div class="relative z-10 text-white">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-600 text-[10px] font-bold uppercase tracking-wider text-white mb-2.5 shadow-md">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    AI MONITORING AKTIF
                </div>
                <h3 class="text-xl sm:text-2xl font-bold font-heading text-white leading-tight">
                    Perjalanan Aman Bersama Pengemudi Fokus
                </h3>
                <p class="text-xs sm:text-sm text-slate-200 mt-2 leading-relaxed max-w-sm">
                    Sistem cerdas mendeteksi tanda kantuk dan menjaga konsentrasi di setiap kilometer perjalanan.
                </p>
            </div>
        </div>

    </main>

    <!-- Script Interaktif untuk Toggle Peran, Password, dan Quick Fill -->
    <script>
        function selectRole(role) {
            const roleInput = document.getElementById('roleInput');
            const btnDriver = document.getElementById('roleBtnDriver');
            const btnAdmin = document.getElementById('roleBtnAdmin');
            const usernameInput = document.getElementById('username');

            roleInput.value = role;

            if (role === 'driver') {
                btnDriver.className = "py-2 px-3 rounded-md text-xs font-semibold bg-white text-blue-700 shadow-xs border border-slate-200/80 flex items-center justify-center gap-2 transition-all cursor-pointer";
                const svgD = btnDriver.querySelector('svg');
                if (svgD) svgD.className.baseVal = "w-4 h-4 text-blue-600";
                
                btnAdmin.className = "py-2 px-3 rounded-md text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center justify-center gap-2 transition-all cursor-pointer";
                const svgA = btnAdmin.querySelector('svg');
                if (svgA) svgA.className.baseVal = "w-4 h-4 text-slate-400";

                if (usernameInput && !usernameInput.value) {
                    usernameInput.placeholder = "Masukkan username pengemudi (contoh: apri)";
                }
            } else {
                btnAdmin.className = "py-2 px-3 rounded-md text-xs font-semibold bg-white text-blue-700 shadow-xs border border-slate-200/80 flex items-center justify-center gap-2 transition-all cursor-pointer";
                const svgA = btnAdmin.querySelector('svg');
                if (svgA) svgA.className.baseVal = "w-4 h-4 text-blue-600";

                btnDriver.className = "py-2 px-3 rounded-md text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center justify-center gap-2 transition-all cursor-pointer";
                const svgD = btnDriver.querySelector('svg');
                if (svgD) svgD.className.baseVal = "w-4 h-4 text-slate-400";

                if (usernameInput && !usernameInput.value) {
                    usernameInput.placeholder = "Masukkan username admin (contoh: admin)";
                }
            }
        }

        function quickFill(user, pass, role) {
            selectRole(role);
            const uInput = document.getElementById('username');
            const pInput = document.getElementById('password');
            if (uInput) uInput.value = user;
            if (pInput) {
                pInput.value = pass;
                pInput.focus();
            }
        }

        function togglePasswordVisibility(fieldId, btn) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                btn.innerHTML = `
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                `;
            } else {
                field.type = 'password';
                btn.innerHTML = `
                    <svg class="w-5 h-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                `;
            }
        }
    </script>
</body>
</html>
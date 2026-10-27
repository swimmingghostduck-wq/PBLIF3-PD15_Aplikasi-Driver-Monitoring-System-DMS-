<!DOCTYPE html>
<html lang="id" class="h-full">
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
<body class="min-h-screen lg:h-screen lg:overflow-hidden w-full bg-white text-slate-900 font-sans antialiased selection:bg-blue-600 selection:text-white flex flex-col lg:flex-row">

    <!-- ========================================================
         SEKSI KIRI: ILUSTRASI CANVA FLAT & VALUE PROPOSITION
         ======================================================== -->
    <div class="w-full lg:w-1/2 lg:h-screen bg-slate-50 border-b lg:border-b-0 lg:border-r border-slate-200 p-6 sm:p-8 lg:p-8 xl:p-10 flex flex-col justify-between shrink-0">
        
        <!-- Bagian Atas: Heading Bersih & Jelas (Tanpa Eyebrow Kaku) -->
        <div>
            <h2 class="text-xl sm:text-2xl lg:text-[1.65rem] xl:text-3xl font-bold font-heading text-slate-900 leading-snug tracking-tight">
                Pantau Keselamatan <span class="text-blue-600">Pengemudi</span> Secara Real-Time
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1.5 max-w-md leading-relaxed">
                Sistem cerdas Computer Vision untuk deteksi dini rasa kantuk dan perlindungan fokus berkendara di setiap perjalanan.
            </p>
        </div>

        <!-- Bagian Tengah: Ilustrasi Canva Flat (Responsif, Fleksibel, Bebas Potong) -->
        <div class="flex-1 min-h-0 flex items-center justify-center my-auto py-2">
            <img src="{{ asset('images/driver-canva.svg') }}" 
                 alt="Ilustrasi Pengemudi Aman DriveSafe" 
                 class="w-auto h-auto max-h-[220px] sm:max-h-[250px] lg:max-h-[270px] xl:max-h-[310px] max-w-full object-contain select-none pointer-events-none">
        </div>

        <!-- Bagian Bawah: Indikator Nilai Sistem -->
        <div class="pt-3 border-t border-slate-200/80 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-600">
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium text-slate-700">Deteksi Cepat EAR</span>
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium text-slate-700">Kamera Real-Time</span>
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium text-slate-700">Log Insiden Terdata</span>
            </div>
        </div>
    </div>

    <!-- ========================================================
         SEKSI KANAN: FORMULIR AUTENTIKASI MINIMALIS & PRESISI
         ======================================================== -->
    <div class="w-full lg:w-1/2 lg:h-screen flex flex-col justify-center px-6 sm:px-10 lg:px-12 xl:px-16 py-6 sm:py-8 lg:py-6 overflow-y-auto">
        <div class="w-full max-w-[390px] mx-auto">
            
            <!-- Brand Logo & Header -->
            <div class="mb-4">
                <div class="flex items-center gap-2.5 mb-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9" />
                            <circle cx="12" cy="12" r="3" />
                            <path d="M12 3v6m0 6v6M3 12h6m6 0h6" stroke-linecap="round" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-lg font-bold font-heading text-slate-900 tracking-tight leading-none block">DriveSafe</span>
                        <span class="text-[10px] font-medium text-slate-400">Driver Safety Intelligence</span>
                    </div>
                </div>

                <h1 class="text-xl sm:text-2xl font-bold font-heading text-slate-900 tracking-tight">
                    Masuk ke DriveSafe
                </h1>
                <p class="text-xs text-slate-500 mt-1 leading-normal">
                    Hai, selamat datang kembali! Belum punya akun? 
                    <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">
                        Daftar akun di sini
                    </a>
                </p>
            </div>

            <!-- Segmented Role Selector -->
            <div class="mb-4">
                <div class="grid grid-cols-2 p-1 bg-slate-100 rounded-xl border border-slate-200">
                    <button type="button" id="roleBtnDriver" onclick="selectRole('driver')"
                            class="py-2 px-3 rounded-lg text-xs font-semibold bg-white text-blue-700 shadow-xs border border-slate-200/80 flex items-center justify-center gap-1.5 transition-all">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Pengemudi</span>
                    </button>
                    <button type="button" id="roleBtnAdmin" onclick="selectRole('admin')"
                            class="py-2 px-3 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center justify-center gap-1.5 transition-all">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Admin</span>
                    </button>
                </div>
                
                <!-- Quick Demo Autofill Bar -->
                <div class="mt-2 py-1 px-2.5 rounded-lg bg-slate-50 border border-slate-200/70 flex items-center justify-between text-xs text-slate-600">
                    <span class="text-[11px] truncate">Akun demo: <code id="demoCredText" class="text-blue-600 font-semibold font-mono">apri &bull; driver123</code></span>
                    <button type="button" onclick="fillDemo()" class="shrink-0 text-[11px] text-blue-600 hover:text-blue-800 font-medium underline ml-2">
                        Isi Cepat
                    </button>
                </div>
            </div>

            <!-- Pesan Notifikasi Sukses / Status -->
            @if (session('status'))
            <div class="mb-3 p-2.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-xs flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            <!-- Notifikasi Error Validasi -->
            @if ($errors->any())
            <div class="mb-3 p-2.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Formulir Login -->
            <form method="POST" action="{{ route('login') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="role" id="roleInput" value="driver">

                <!-- Field Username -->
                <div>
                    <label for="username" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Username
                    </label>
                    <input type="text" id="username" name="username" value="{{ old('username', 'apri') }}" required autofocus
                           placeholder="Masukkan username"
                           class="w-full h-10 px-3 rounded-lg border border-slate-300 text-slate-900 text-xs sm:text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition">
                </div>

                <!-- Field Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" value="driver123" required
                               placeholder="Masukkan kata sandi"
                               class="w-full h-10 pl-3 pr-10 rounded-lg border border-slate-300 text-slate-900 text-xs sm:text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition">
                        <button type="button" onclick="togglePasswordVisibility('password', this)"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 rounded-md focus:outline-none"
                                title="Tampilkan / Sembunyikan Kata Sandi">
                            <svg class="w-4 h-4 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Pilihan Remember Me & Informasi Sesi -->
                <div class="flex items-center justify-between pt-0.5">
                    <label class="flex items-center gap-1.5 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-xs text-slate-600">Ingat sesi saya</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Batas: 2 jam</span>
                </div>

                <!-- Tombol Submit -->
                <button type="submit"
                        class="w-full h-10 mt-1 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm rounded-lg shadow-sm transition-all flex items-center justify-center gap-1.5 group">
                    <span>Masuk Sekarang</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            <!-- Catatan Privasi & Ketentuan -->
            <p class="text-[11px] text-slate-400 text-center mt-4 leading-normal">
                Dengan melanjutkan, Anda menyetujui Ketentuan Layanan & Kebijakan Privasi DriveSafe.
            </p>
        </div>
    </div>

    <!-- Script Interaktif untuk Toggle Peran, Autofill, dan Password -->
    <script>
        function selectRole(role) {
            const roleInput = document.getElementById('roleInput');
            const btnDriver = document.getElementById('roleBtnDriver');
            const btnAdmin = document.getElementById('roleBtnAdmin');
            const demoCred = document.getElementById('demoCredText');
            const userInput = document.getElementById('username');
            const passInput = document.getElementById('password');

            roleInput.value = role;

            if (role === 'driver') {
                btnDriver.className = "py-2 px-3 rounded-lg text-xs font-semibold bg-white text-blue-700 shadow-xs border border-slate-200/80 flex items-center justify-center gap-1.5 transition-all";
                btnAdmin.className = "py-2 px-3 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center justify-center gap-1.5 transition-all";
                demoCred.innerHTML = "apri &bull; driver123";
                userInput.value = "apri";
                passInput.value = "driver123";
            } else {
                btnAdmin.className = "py-2 px-3 rounded-lg text-xs font-semibold bg-white text-blue-700 shadow-xs border border-slate-200/80 flex items-center justify-center gap-1.5 transition-all";
                btnDriver.className = "py-2 px-3 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center justify-center gap-1.5 transition-all";
                demoCred.innerHTML = "admin &bull; admin123";
                userInput.value = "admin";
                passInput.value = "admin123";
            }
        }

        function fillDemo() {
            const role = document.getElementById('roleInput').value;
            selectRole(role);
        }

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
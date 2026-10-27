<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - DriveSafe</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">
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
                        heading: ['Poppins', 'Inter', 'ui-sans-serif', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        :focus-visible { outline: 2px solid #2563EB; outline-offset: 1px; }

        /* Kartu selamat datang: tersembunyi dulu, membuka setelah splash selesai */
        .splash-card {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transform: translateY(12px);
            transition: max-height .7s ease, opacity .6s ease .2s, transform .6s ease .2s;
        }
        #splash.ready .splash-card { max-height: 340px; opacity: 1; transform: none; }
        .splash-logo { animation: splashPop .7s ease both; }
        @keyframes splashPop {
            from { opacity: 0; transform: scale(.85); }
            to   { opacity: 1; transform: none; }
        }
        /* Kunjungan berikutnya dalam sesi yang sama: langsung tampil tanpa animasi */
        #splash.instant, #splash.instant * { animation: none !important; transition: none !important; }
        @media (prefers-reduced-motion: reduce) {
            #splash, #splash * { animation: none !important; transition: none !important; }
        }
    </style>
    <noscript>
        <style>.splash-card { max-height: none; opacity: 1; transform: none; }</style>
    </noscript>
</head>
<body class="bg-[#F8FAFC] text-slate-900 min-h-screen flex flex-col justify-center items-center font-sans antialiased p-4 selection:bg-blue-600 selection:text-white">

<div id="splash" class="w-full max-w-[390px] mx-auto flex flex-col items-center text-center">

    <div class="splash-logo">
        <img src="{{ asset('logo.svg') }}" alt="DriveSafe" class="w-16 h-16 mx-auto mb-4">
        <h1 class="text-3xl font-heading font-bold text-slate-900 tracking-tight">DriveSafe</h1>
        <p class="text-sm text-slate-500 mt-1">Sistem pemantauan keselamatan pengemudi</p>
    </div>

    <div class="splash-card w-full mt-8">
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7">
            <h2 class="text-xl font-heading font-bold text-slate-900">Selamat Datang, Pengguna</h2>
            <p class="text-sm text-slate-500 mt-1">Masuk atau buat akun baru untuk mulai.</p>
            <div class="grid grid-cols-2 gap-3 mt-5">
                <a href="{{ route('register') }}"
                   class="py-2.5 px-4 rounded-xl text-sm font-semibold text-slate-800 bg-white border border-slate-300 hover:bg-slate-50 transition-colors text-center">
                    Daftar
                </a>
                <a href="{{ route('login') }}"
                   class="py-2.5 px-4 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors text-center">
                    Masuk
                </a>
            </div>
        </div>
    </div>

</div>

<script>
(function () {
    var el = document.getElementById('splash');
    var seen = false;
    try { seen = sessionStorage.getItem('splashSeen') === '1'; } catch (e) {}

    if (seen) {
        el.classList.add('instant', 'ready');
        return;
    }
    setTimeout(function () {
        el.classList.add('ready');
        try { sessionStorage.setItem('splashSeen', '1'); } catch (e) {}
    }, 2000);
})();
</script>
</body>
</html>
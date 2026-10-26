<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengemudi - DriveSafe</title>
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
<body class="bg-slate-50 text-slate-900 min-h-screen font-sans antialiased selection:bg-slate-900 selection:text-white">

<div class="min-h-screen flex flex-col">
    <!-- Driver Navbar -->
    <header class="bg-white border-b border-slate-200 h-14 px-6 sm:px-8 flex items-center justify-between sticky top-0 z-30">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 bg-slate-900 text-white rounded-md flex items-center justify-center font-bold text-xs">
                <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 3v18M3 12h18"></path>
                </svg>
            </div>
            <span class="font-semibold text-sm text-slate-900">DriveSafe</span>
            <span class="text-xs text-slate-400 font-mono">/ Pengemudi</span>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('driver.monitor') }}" 
               class="h-8 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-xs font-medium transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                <span>Mulai Monitoring</span>
            </a>

            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="h-8 px-2.5 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 rounded-md text-xs font-medium transition-colors cursor-pointer">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 w-full max-w-5xl mx-auto px-6 sm:px-8 py-8 space-y-6">
        <!-- Header Profil Driver -->
        <div class="bg-white p-6 rounded-lg border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-base font-mono">
                    {{ strtoupper(substr($user->full_name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900">{{ $user->full_name }}</h1>
                    <p class="text-xs text-slate-500 font-mono">@ {{ $user->username }} &bull; {{ $user->nim_nik ?? 'ID Driver' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('driver.monitor') }}" class="h-8.5 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-md text-xs font-medium transition-colors inline-flex items-center gap-1.5">
                    <span>Buka Kabin Kamera &rarr;</span>
                </a>
            </div>
        </div>

        <!-- 3 KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-lg border border-slate-200">
                <span class="text-xs text-slate-500 font-medium">Skor Keselamatan Pribadi</span>
                <div class="mt-2 text-2xl font-bold font-mono {{ $safetyScore >= 85 ? 'text-slate-900' : 'text-amber-700' }}">
                    {{ number_format($safetyScore, 1) }}%
                </div>
            </div>

            <div class="bg-white p-5 rounded-lg border border-slate-200">
                <span class="text-xs text-slate-500 font-medium">Total Sesi Berkendara</span>
                <div class="mt-2 text-2xl font-bold font-mono text-slate-900">
                    {{ $totalSessions }}
                </div>
            </div>

            <div class="bg-white p-5 rounded-lg border border-slate-200">
                <span class="text-xs text-slate-500 font-medium">Total Catatan Insiden</span>
                <div class="mt-2 text-2xl font-bold font-mono {{ $totalMyAlerts > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                    {{ $totalMyAlerts }}
                </div>
            </div>
        </div>

        <!-- Riwayat Insiden Terkini -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Riwayat Insiden Terakhir Anda</h2>
                <span class="text-xs text-slate-400 font-mono">{{ $myAlerts->count() }} kejadian</span>
            </div>

            <div class="divide-y divide-slate-100 text-xs">
                @forelse($myAlerts as $a)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50/60 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full {{ $a->alert_type === 'kantuk' ? 'bg-rose-600' : ($a->alert_type === 'tidak_fokus' ? 'bg-amber-500' : 'bg-amber-600') }}"></span>
                        <div>
                            <span class="font-medium text-slate-900 capitalize">{{ str_replace('_', ' ', $a->alert_type) }}</span>
                            <span class="text-slate-400 font-mono text-[11px] ml-2">{{ $a->clean_notes }}</span>
                        </div>
                    </div>
                    <div class="text-right font-mono text-slate-500 tabular-nums">
                        <div>{{ $a->timestamp ? $a->timestamp->format('H:i:s') : '-' }}</div>
                        <div class="text-[10px] text-slate-400">{{ $a->timestamp ? $a->timestamp->format('d/m/Y') : '-' }}</div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Belum ada insiden tercatat. Anda berkendara dengan sangat aman!
                </div>
                @endforelse
            </div>
        </div>
    </main>
</div>
</body>
</html>


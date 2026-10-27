@extends('layouts.admin')
@section('title', 'Dashboard Pemantauan - DriveSafe')

@section('header_actions')
<!-- Action di Topbar: Link Cepat ke Ringkasan Insiden -->
<a href="{{ route('admin.alerts') }}"
   class="h-9 px-3.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200/90 shadow-2xs transition-colors inline-flex items-center gap-2"
   title="Buka daftar lengkap seluruh insiden pengemudi">
    <span>Semua Insiden</span>
    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
    </svg>
</a>
@endsection

@section('content')
<div class="px-6 sm:px-8 lg:px-10 py-6 sm:py-8 space-y-6">

    <!-- 1. Page Header -->
    <div class="pb-4 border-b border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold font-heading text-slate-900 tracking-tight">Dashboard Pemantauan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pengawasan aktivitas berkendara, kondisi pengemudi, dan penanganan insiden secara langsung.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Sistem Pemantauan Aktif
            </span>
        </div>
    </div>

    <!-- 2. KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

        <!-- KPI 1: Skor Keselamatan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Skor Keselamatan</span>
                @if($fleetSafetyScore >= 85)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Optimal
                    </span>
                @elseif($fleetSafetyScore >= 70)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                        Waspada
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                        Perlu Evaluasi
                    </span>
                @endif
            </div>
            <div class="mt-3">
                <div class="text-3xl font-bold font-heading text-slate-900 font-mono tabular-nums">
                    {{ number_format($fleetSafetyScore, 1) }}%
                </div>
            </div>
        </div>

        <!-- KPI 2: Pengemudi Aktif -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Pengemudi Aktif</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-200/80">
                    Terdaftar
                </span>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-bold font-heading text-slate-900 font-mono tabular-nums">
                    {{ $totalDrivers }}
                </div>
            </div>
        </div>

        <!-- KPI 3: Sesi Perjalanan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Sesi Perjalanan</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                    Terekam
                </span>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-bold font-heading text-slate-900 font-mono tabular-nums">
                    {{ $totalSessions }}
                </div>
            </div>
        </div>

        <!-- KPI 4: Total Insiden -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Total Insiden</span>
                @if($totalAlerts > 0)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                        {{ $totalDrowsy }} kantuk &bull; {{ $totalDistraction }} distraksi
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Nol Insiden
                    </span>
                @endif
            </div>
            <div class="mt-3">
                <div class="text-3xl font-bold font-heading {{ $totalAlerts > 0 ? 'text-rose-600' : 'text-slate-900' }} font-mono tabular-nums">
                    {{ $totalAlerts }}
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Operations Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">

        <!-- Kolom Kiri: Kategori Peringatan (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm font-heading">Kategori Peringatan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Distribusi jenis insiden yang terdeteksi selama perjalanan pengemudi</p>
                </div>
                <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-200/80 text-[11px] font-mono font-medium rounded-full tabular-nums">
                    {{ $totalAlerts }} Insiden
                </span>
            </div>

            @php
                $pctDrowsy = $totalAlerts > 0 ? round(($totalDrowsy / $totalAlerts) * 100, 1) : 0;
                $pctDistract = $totalAlerts > 0 ? round(($totalDistraction / $totalAlerts) * 100, 1) : 0;
                $pctYawn = $totalAlerts > 0 ? round(($totalYawn / $totalAlerts) * 100, 1) : 0;
            @endphp

            <!-- Single Clean Proportional Progress Bar -->
            <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden flex">
                @if($totalAlerts > 0)
                    @if($totalDrowsy > 0)
                        <div style="width: {{ $pctDrowsy }}%" class="bg-rose-600 transition-all duration-300" title="Mengantuk: {{ $totalDrowsy }} ({{ $pctDrowsy }}%)"></div>
                    @endif
                    @if($totalDistraction > 0)
                        <div style="width: {{ $pctDistract }}%" class="bg-amber-500 transition-all duration-300" title="Distraksi: {{ $totalDistraction }} ({{ $pctDistract }}%)"></div>
                    @endif
                    @if($totalYawn > 0)
                        <div style="width: {{ $pctYawn }}%" class="bg-amber-600 transition-all duration-300" title="Menguap: {{ $totalYawn }} ({{ $pctYawn }}%)"></div>
                    @endif
                @else
                    <div style="width: 100%" class="bg-emerald-500 transition-all duration-300" title="0 Peringatan: Seluruh Pengemudi Aman"></div>
                @endif
            </div>

            <!-- Horizontal Rows -->
            <div class="divide-y divide-slate-100 pt-1">
                <!-- 1. Mengantuk -->
                <div class="py-3 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600 shrink-0"></span>
                        <span class="font-medium text-slate-800">Mengantuk</span>
                    </div>
                    <div class="flex items-center gap-3 tabular-nums font-mono">
                        <span class="font-semibold text-slate-900">{{ $totalDrowsy }} <span class="text-slate-400 font-sans">kali</span></span>
                        <span class="text-slate-500 w-14 text-right">({{ number_format($pctDrowsy, 1) }}%)</span>
                    </div>
                </div>

                <!-- 2. Distraksi -->
                <div class="py-3 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                        <span class="font-medium text-slate-800">Distraksi</span>
                    </div>
                    <div class="flex items-center gap-3 tabular-nums font-mono">
                        <span class="font-semibold text-slate-900">{{ $totalDistraction }} <span class="text-slate-400 font-sans">kali</span></span>
                        <span class="text-slate-500 w-14 text-right">({{ number_format($pctDistract, 1) }}%)</span>
                    </div>
                </div>

                <!-- 3. Menguap -->
                <div class="py-3 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-600 shrink-0"></span>
                        <span class="font-medium text-slate-800">Menguap</span>
                    </div>
                    <div class="flex items-center gap-3 tabular-nums font-mono">
                        <span class="font-semibold text-slate-900">{{ $totalYawn }} <span class="text-slate-400 font-sans">kali</span></span>
                        <span class="text-slate-500 w-14 text-right">({{ number_format($pctYawn, 1) }}%)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Waktu Kejadian (24 Jam) (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 sm:p-6 space-y-4">
            <div class="pb-3.5 border-b border-slate-100">
                <h2 class="font-bold text-slate-900 text-sm font-heading">Waktu Kejadian</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pola distribusi insiden sepanjang 24 jam</p>
            </div>

            <!-- Horizontal Rows -->
            <div class="divide-y divide-slate-100">
                @foreach($timeBins as $label => $cnt)
                @php
                    $pctBin = $totalAlerts > 0 ? round(($cnt / $totalAlerts) * 100, 1) : 0;
                @endphp
                <div class="py-2.5 first:pt-0 last:pb-0 space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-700">{{ $label }}</span>
                        <div class="tabular-nums font-mono flex items-center gap-1.5">
                            <span class="font-semibold text-slate-900">{{ $cnt }}</span>
                            <span class="text-slate-400 font-sans">kali</span>
                            <span class="text-slate-400">({{ number_format($pctBin, 1) }}%)</span>
                        </div>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300" style="width: {{ $pctBin }}%;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 4. Peringatan Terbaru -->
    <div class="bg-white border border-slate-200/90 shadow-2xs rounded-2xl overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-slate-900 text-sm font-heading">Peringatan Terbaru</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar insiden terkini yang memerlukan perhatian pengawas sistem</p>
            </div>
            <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-200/80 text-[11px] font-mono font-medium rounded-full tabular-nums">
                {{ $recentAlerts->count() }} Kejadian Terkini
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($recentAlerts as $alert)
            <div class="px-5 sm:px-6 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 hover:bg-slate-50/60 transition-colors">

                <!-- Info Pengemudi & Operational Context -->
                <div class="flex items-center gap-3.5 min-w-0 flex-1">
                    <!-- Thumbnail Snapshot Minimalis -->
                    <div class="shrink-0">
                        @if($alert->snapshot_path)
                            <img src="{{ asset('snapshots/' . $alert->snapshot_path) }}"
                                 alt="Bukti"
                                 class="w-12 h-10 object-cover rounded-lg border border-slate-200 shadow-2xs">
                        @else
                            <div class="w-12 h-10 bg-slate-100 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 text-[10px] font-mono">
                                NO SNAP
                            </div>
                        @endif
                    </div>

                    <!-- Driver Identity & Secondary Operational Line -->
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-semibold text-slate-900 text-xs truncate">
                                {{ $alert->driver ? $alert->driver->full_name : 'Pengemudi #' . $alert->driver_id }}
                            </span>

                            <!-- Semantic Incident Type Badge -->
                            @if($alert->alert_type === 'kantuk')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200/90">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                    Mengantuk
                                </span>
                            @elseif($alert->alert_type === 'tidak_fokus')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200/90">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Distraksi
                                </span>
                            @elseif($alert->alert_type === 'menguap')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200/90">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    Menguap
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ ucfirst($alert->alert_type) }}
                                </span>
                            @endif

                            <!-- Severity Badge -->
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold font-mono {{ $alert->severity === 'BAHAYA' ? 'bg-rose-50 text-rose-700 border border-rose-200/90' : 'bg-amber-50 text-amber-700 border border-amber-200/90' }}">
                                {{ $alert->severity }}
                            </span>
                        </div>

                        <!-- Secondary Operational Context Line -->
                        <div class="flex items-center gap-2 text-[11px] text-slate-500 mt-1">
                            <span class="font-mono text-slate-600 font-medium">ID: {{ $alert->driver ? ($alert->driver->nim_nik ?? sprintf('DRV-%03d', $alert->driver_id)) : sprintf('DRV-%03d', $alert->driver_id) }}</span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-slate-600 truncate max-w-xs">{{ $alert->clean_notes }}</span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="tabular-nums font-mono text-slate-400">{{ $alert->timestamp ? $alert->timestamp->diffForHumans() : '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Meta Waktu & Aksi Buka Bukti -->
                <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                    <div class="text-left sm:text-right text-xs tabular-nums font-mono">
                        <span class="text-slate-800 font-medium block">{{ $alert->timestamp ? $alert->timestamp->format('H:i:s') : '-' }}</span>
                        <span class="text-slate-400 text-[11px] block">{{ $alert->timestamp ? $alert->timestamp->format('d/m/Y') : '-' }}</span>
                    </div>

                    @if($alert->snapshot_path)
                    <button type="button"
                            class="btn-open-snapshot h-7 px-2.5 bg-white hover:bg-slate-50 text-slate-700 rounded-lg border border-slate-200 transition-colors inline-flex items-center gap-1.5 cursor-pointer text-xs font-medium shadow-2xs hover:border-slate-300"
                            data-img="{{ asset('snapshots/' . $alert->snapshot_path) }}"
                            data-driver="{{ $alert->driver ? $alert->driver->full_name : 'Pengemudi #' . $alert->driver_id }}"
                            data-driver-id="{{ $alert->driver ? ($alert->driver->nim_nik ?? sprintf('DRV-%03d', $alert->driver_id)) : sprintf('DRV-%03d', $alert->driver_id) }}"
                            data-type="{{ ucfirst($alert->alert_type) }}"
                            data-severity="{{ $alert->severity }}"
                            data-time="{{ $alert->timestamp ? $alert->timestamp->format('d/m/Y H:i:s') : '-' }}"
                            data-duration="{{ number_format($alert->duration_seconds, 1) }} detik"
                            data-context="{{ $alert->clean_notes }}"
                            title="Buka bukti foto insiden">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span>Bukti</span>
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="py-12 px-6 text-center">
                <div class="max-w-sm mx-auto space-y-2">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 mx-auto flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-900 font-heading">Tidak Ada Insiden</h3>
                    <p class="text-xs text-slate-500">Semua pengemudi saat ini berkendara dalam kondisi fokus dan aman.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL BUKTI FOTO INSIDEN & TELEMETRI OPERASIONAL
     ========================================================================= -->
<div id="snapshotModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
    <div class="bg-white border border-slate-200/90 rounded-2xl max-w-3xl w-full overflow-hidden shadow-xl animate-in fade-in zoom-in-95 duration-150 my-auto">
        <!-- Header Modal -->
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
            <div class="flex items-center gap-2.5">
                <span id="modalAlertBadge" class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-rose-50 text-rose-700 border border-rose-200 uppercase tracking-wider"></span>
                <div>
                    <h3 id="modalDriverName" class="font-bold text-slate-900 text-sm font-heading leading-tight"></h3>
                    <p id="modalDriverId" class="text-[11px] text-slate-500 font-mono"></p>
                </div>
            </div>
            <button type="button" onclick="closeSnapshotModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition-colors cursor-pointer" title="Tutup Modal">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Body Modal -->
        <div class="grid grid-cols-1 md:grid-cols-12 divide-y md:divide-y-0 md:divide-x divide-slate-100 bg-white">
            <!-- Kolom Kiri: Foto Snapshot Bukti -->
            <div class="md:col-span-7 bg-[#0B1528] p-4 sm:p-5 flex flex-col justify-between relative min-h-[280px]">
                <div class="relative flex-1 flex items-center justify-center">
                    <img id="modalImg" src="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22/%3E"
                         alt="Foto Insiden"
                         class="rounded-xl max-h-[260px] w-auto object-contain border border-slate-800">
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-800 flex items-center justify-between text-xs font-mono text-slate-400">
                    <span>Tangkapan Kamera Real-Time</span>
                    <a id="modalFullLink" href="#" target="_blank" class="text-blue-400 hover:underline inline-flex items-center gap-1 font-semibold">
                        <span>Buka Resolusi Penuh</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Kolom Kanan: Detail Kejadian -->
            <div class="md:col-span-5 p-5 flex flex-col justify-between space-y-4">
                <div class="space-y-4">
                    <div class="space-y-3 pb-3 border-b border-slate-100">
                        <div>
                            <span class="text-xs text-slate-500 block">Waktu Kejadian</span>
                            <span id="modalTimestamp" class="text-xs font-semibold text-slate-900 font-mono tabular-nums mt-0.5 block"></span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 block">Durasi Terdeteksi</span>
                            <span id="modalDuration" class="text-xs font-semibold text-slate-900 font-mono tabular-nums mt-0.5 block"></span>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs text-slate-500 block mb-1">Status Observasi:</span>
                        <div id="modalContext" class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-slate-800 text-xs leading-relaxed font-medium"></div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                    <button type="button" onclick="closeSnapshotModal()" class="h-9 px-4 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-medium transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openSnapshotModal(imgUrl, driverName, driverId, alertType, timestamp, context, duration) {
        document.getElementById('modalImg').src = imgUrl;
        document.getElementById('modalDriverName').textContent = driverName || 'Pengemudi';
        document.getElementById('modalDriverId').textContent = 'ID: ' + (driverId || '-');
        document.getElementById('modalAlertBadge').textContent = alertType || 'INSIDEN';
        document.getElementById('modalTimestamp').textContent = timestamp || '-';
        document.getElementById('modalDuration').textContent = duration || '-';
        document.getElementById('modalContext').textContent = context || 'Insiden kewaspadaan terdeteksi saat mengemudi';
        document.getElementById('modalFullLink').href = imgUrl;
        document.getElementById('snapshotModal').classList.remove('hidden');
    }

    function closeSnapshotModal() {
        document.getElementById('snapshotModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-open-snapshot').forEach(function(button) {
            button.addEventListener('click', function() {
                openSnapshotModal(
                    this.getAttribute('data-img'),
                    this.getAttribute('data-driver'),
                    this.getAttribute('data-driver-id'),
                    this.getAttribute('data-type'),
                    this.getAttribute('data-time'),
                    this.getAttribute('data-context'),
                    this.getAttribute('data-duration')
                );
            });
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSnapshotModal();
        }
    });
</script>
@endsection

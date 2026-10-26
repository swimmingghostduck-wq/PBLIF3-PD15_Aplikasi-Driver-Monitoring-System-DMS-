@extends('layouts.admin')
@section('title', 'Ringkasan Insiden - DriveSafe')

@section('header_actions')
<!-- Action di Topbar: Ekspor CSV -->
<a href="{{ route('admin.export_alerts_csv', ['driver_id' => $selectedDriverId, 'alert_type' => $selectedAlertType, 'q' => $searchQuery]) }}" 
   class="h-8 px-3 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium rounded-md border border-slate-200 transition-colors inline-flex items-center gap-1.5"
   title="Unduh log insiden dalam format file CSV">
    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    <span>Ekspor CSV</span>
</a>
@endsection

@section('content')
<div class="px-6 sm:px-8 lg:px-10 py-6 sm:py-8 space-y-5">
    
    <!-- Page Title & Operational Subtitle -->
    <div class="pb-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold font-heading text-slate-900 tracking-tight">Ringkasan Insiden Armada</h1>
            <p class="text-sm text-slate-500 mt-0.5">Audit kepatuhan keselamatan, log peringatan berkendara, dan verifikasi bukti insiden.</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-500 font-mono">
            <span>Total Insiden: <strong class="text-slate-900 tabular-nums">{{ $totalAlerts }}</strong></span>
        </div>
    </div>

    <!-- Minimalist Metric Strip -->
    <div class="bg-white border border-slate-200 rounded-lg divide-y sm:divide-y-0 sm:divide-x divide-slate-100 grid grid-cols-2 sm:grid-cols-4">
        <!-- 1. Total Insiden -->
        <div class="p-4 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Total Peringatan</span>
            <div class="mt-2 flex items-baseline gap-1.5 font-mono">
                <span class="text-2xl font-bold font-heading {{ $totalAlerts > 0 ? 'text-rose-600' : 'text-slate-900' }} tabular-nums">{{ $totalAlerts }}</span>
                <span class="text-xs text-slate-400 font-sans">kejadian</span>
            </div>
        </div>

        <!-- 2. Mengantuk -->
        <div class="p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Mengantuk</span>
                <span class="w-2 h-2 rounded-full bg-rose-600"></span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5 font-mono">
                <span class="text-2xl font-bold font-heading text-slate-900 tabular-nums">{{ $totalDrowsy }}</span>
                <span class="text-xs text-slate-400 font-sans">kali</span>
            </div>
        </div>

        <!-- 3. Distraksi -->
        <div class="p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Distraksi</span>
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5 font-mono">
                <span class="text-2xl font-bold font-heading text-slate-900 tabular-nums">{{ $totalDistraction }}</span>
                <span class="text-xs text-slate-400 font-sans">kali</span>
            </div>
        </div>

        <!-- 4. Menguap -->
        <div class="p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Menguap</span>
                <span class="w-2 h-2 rounded-full bg-amber-600"></span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5 font-mono">
                <span class="text-2xl font-bold font-heading text-slate-900 tabular-nums">{{ $totalYawn }}</span>
                <span class="text-xs text-slate-400 font-sans">kali</span>
            </div>
        </div>
    </div>

    <!-- High-Density Incident Data Table with Integrated Filter Toolbar -->
    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        
        <!-- Filter Toolbar -->
        <div class="p-3 sm:p-4 border-b border-slate-200 bg-slate-50/50 flex flex-wrap items-center justify-between gap-3">
            <form method="GET" action="{{ route('admin.alerts') }}" class="flex flex-wrap items-center gap-2 flex-1 min-w-0">
                <!-- Dropdown Pengemudi -->
                <select name="driver_id" class="h-8 bg-white border border-slate-300 rounded-md px-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-800 font-sans">
                    <option value="">Semua Pengemudi</option>
                    @foreach($allDrivers as $d)
                        <option value="{{ $d->id }}" {{ $selectedDriverId == $d->id ? 'selected' : '' }}>
                            {{ $d->full_name }}
                        </option>
                    @endforeach
                </select>

                <!-- Dropdown Jenis Insiden -->
                <select name="alert_type" class="h-8 bg-white border border-slate-300 rounded-md px-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-800 font-sans">
                    <option value="">Semua Kategori</option>
                    <option value="kantuk" {{ $selectedAlertType === 'kantuk' ? 'selected' : '' }}>Mengantuk</option>
                    <option value="tidak_fokus" {{ $selectedAlertType === 'tidak_fokus' ? 'selected' : '' }}>Distraksi</option>
                    <option value="menguap" {{ $selectedAlertType === 'menguap' ? 'selected' : '' }}>Menguap</option>
                </select>

                <!-- Input Pencarian -->
                <div class="relative flex-1 min-w-[160px] max-w-xs">
                    <input type="text" name="q" value="{{ $searchQuery }}" placeholder="Cari pengemudi atau keterangan..."
                           class="w-full h-8 bg-white border border-slate-300 rounded-md pl-8 pr-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 font-sans">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <!-- Tombol Filter -->
                <button type="submit" class="h-8 px-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-medium rounded-md transition-colors cursor-pointer">
                    Terapkan
                </button>

                <!-- Reset Link -->
                @if($selectedDriverId || $selectedAlertType || $searchQuery)
                <a href="{{ route('admin.alerts') }}" class="h-8 px-2.5 bg-white hover:bg-slate-50 text-slate-600 text-xs font-medium rounded-md border border-slate-200 transition-colors inline-flex items-center" title="Reset filter">
                    Reset
                </a>
                @endif
            </form>

            <!-- Table Actions / Count & Bulk Clear -->
            <div class="flex items-center gap-3 shrink-0 text-xs font-mono text-slate-500">
                <span>
                    <strong class="text-slate-900 tabular-nums">{{ $recentAlerts->count() }}</strong> data
                </span>

                @if($recentAlerts->count() > 0)
                <div class="w-px h-3.5 bg-slate-200"></div>
                <form action="{{ route('admin.clear_alerts') }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh log insiden? Tindakan ini tidak dapat dibatalkan.');">
                    @csrf
                    <button type="submit" class="h-7 px-2 bg-white hover:bg-rose-50 text-rose-600 rounded border border-slate-200 hover:border-rose-200 transition-colors flex items-center gap-1 cursor-pointer font-sans text-xs font-medium">
                        <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Hapus Semua</span>
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Table Body -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-slate-50/80 text-[11px] uppercase font-mono font-semibold text-slate-500 border-b border-slate-200 tracking-wider">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap">Waktu</th>
                        <th class="px-4 py-3 whitespace-nowrap">Pengemudi</th>
                        <th class="px-4 py-3 whitespace-nowrap">Kategori Insiden</th>
                        <th class="px-4 py-3 whitespace-nowrap">Tingkat</th>
                        <th class="px-4 py-3 whitespace-nowrap">Durasi</th>
                        <th class="px-4 py-3">Keterangan Operasional</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-sans">
                    @forelse($recentAlerts as $alert)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <!-- Waktu -->
                        <td class="px-4 py-3 text-slate-600 whitespace-nowrap tabular-nums font-mono">
                            <div class="font-medium text-slate-900">{{ $alert->timestamp ? $alert->timestamp->format('H:i:s') : '-' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $alert->timestamp ? $alert->timestamp->format('d/m/Y') : '-' }}</div>
                        </td>

                        <!-- Pengemudi -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="font-medium text-slate-900 text-xs">
                                {{ $alert->driver ? $alert->driver->full_name : 'Pengemudi #' . $alert->driver_id }}
                            </div>
                            <div class="text-slate-400 font-mono text-[11px]">
                                {{ $alert->driver ? ($alert->driver->nim_nik ?? '@' . $alert->driver->username) : '-' }}
                            </div>
                        </td>

                        <!-- Kategori Insiden -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($alert->alert_type === 'kantuk')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                    <span>Mengantuk</span>
                                </span>
                            @elseif($alert->alert_type === 'tidak_fokus')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>Distraksi</span>
                                </span>
                            @elseif($alert->alert_type === 'menguap')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    <span>Menguap</span>
                                </span>
                            @else
                                <span class="text-slate-700 text-xs">{{ ucfirst($alert->alert_type) }}</span>
                            @endif
                        </td>

                        <!-- Tingkat Bahaya -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($alert->severity === 'BAHAYA')
                                <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-mono font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    BAHAYA
                                </span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-mono font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    {{ $alert->severity }}
                                </span>
                            @endif
                        </td>

                        <!-- Durasi -->
                        <td class="px-4 py-3 text-slate-600 whitespace-nowrap tabular-nums font-mono">
                            {{ number_format($alert->duration_seconds, 1) }}s
                        </td>

                        <!-- Keterangan Operasional -->
                        <td class="px-4 py-3 text-slate-700 max-w-sm truncate text-xs" title="{{ $alert->notes ?? '' }}">
                            <span>{{ $alert->clean_notes }}</span>
                            <span class="text-slate-400 font-mono text-[10px] ml-1.5">({{ $alert->timestamp ? $alert->timestamp->diffForHumans() : '-' }})</span>
                        </td>

                        <!-- Aksi: Lihat Bukti & Hapus -->
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                @if($alert->snapshot_path)
                                <button type="button" 
                                        class="btn-open-snapshot h-7 px-2.5 bg-white hover:bg-slate-50 text-slate-700 rounded-md border border-slate-200 transition-colors inline-flex items-center gap-1 cursor-pointer text-xs font-medium"
                                        data-img="{{ asset('snapshots/' . $alert->snapshot_path) }}"
                                        data-driver="{{ $alert->driver ? $alert->driver->full_name : 'Pengemudi #' . $alert->driver_id }}"
                                        data-driver-id="{{ $alert->driver ? ($alert->driver->nim_nik ?? sprintf('DRV-%03d', $alert->driver_id)) : sprintf('DRV-%03d', $alert->driver_id) }}"
                                        data-type="{{ ucfirst($alert->alert_type) }}"
                                        data-severity="{{ $alert->severity }}"
                                        data-time="{{ $alert->timestamp ? $alert->timestamp->format('d/m/Y H:i:s') : '-' }}"
                                        data-duration="{{ number_format($alert->duration_seconds, 1) }} detik"
                                        data-context="{{ $alert->clean_notes }}"
                                        data-raw-notes="{{ $alert->notes ?? '' }}"
                                        title="Buka bukti kamera dan data telemetri forensik">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <span>Lihat Bukti</span>
                                </button>
                                @endif

                                <form action="{{ route('admin.delete_alert', $alert->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus catatan insiden #{{ $alert->id }}?');">
                                    @csrf
                                    <button type="submit" class="text-slate-400 hover:text-rose-600 p-1 rounded hover:bg-rose-50 transition-colors cursor-pointer" title="Hapus insiden ini">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 px-4 text-center">
                            <div class="max-w-sm mx-auto space-y-1.5">
                                <div class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 mx-auto flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xs font-semibold text-slate-900 font-heading">Tidak Ada Insiden Tercatat</h3>
                                <p class="text-[11px] text-slate-400">Tidak ada kejadian yang cocok dengan kriteria filter yang Anda pilih.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL BUKTI FOTO & AUDIT TELEMETRI TEKNIS
     ========================================================================= -->
<div id="snapshotModal" class="fixed inset-0 z-50 hidden bg-slate-950/60 flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
    <div class="bg-white border border-slate-200 rounded-lg max-w-3xl w-full overflow-hidden shadow-sm animate-in fade-in zoom-in-95 duration-150 my-auto">
        <!-- Header Modal -->
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
            <div class="flex items-center gap-2.5">
                <span id="modalAlertBadge" class="px-2 py-0.5 rounded-full text-xs font-mono font-bold bg-rose-50 text-rose-700 border border-rose-200 uppercase tracking-wider"></span>
                <div>
                    <h3 id="modalDriverName" class="font-bold text-slate-900 text-sm font-heading leading-tight"></h3>
                    <p id="modalDriverId" class="text-[11px] text-slate-500 font-mono"></p>
                </div>
            </div>
            <button type="button" onclick="closeSnapshotModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-md hover:bg-slate-200 transition-colors cursor-pointer" title="Tutup Modal">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Body Modal -->
        <div class="grid grid-cols-1 md:grid-cols-12 divide-y md:divide-y-0 md:divide-x divide-slate-100 bg-white">
            <!-- Kolom Kiri: Foto Snapshot Bukti -->
            <div class="md:col-span-7 bg-slate-950 p-4 sm:p-5 flex flex-col justify-between relative min-h-[280px]">
                <div class="relative flex-1 flex items-center justify-center">
                    <img id="modalImg" src="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22/%3E" 
                         alt="Foto Insiden" 
                         class="rounded max-h-[260px] w-auto object-contain border border-slate-800">
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-800 flex items-center justify-between text-xs font-mono text-slate-400">
                    <span>Tangkapan Kamera Real-Time</span>
                    <a id="modalFullLink" href="#" target="_blank" class="text-emerald-400 hover:underline inline-flex items-center gap-1 font-semibold">
                        <span>Buka Resolusi Penuh</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Kolom Kanan: Detail Operasional & Audit Teknis -->
            <div class="md:col-span-5 p-5 flex flex-col justify-between space-y-4">
                <div class="space-y-3.5">
                    <div class="space-y-2 pb-3 border-b border-slate-100">
                        <div>
                            <span class="text-[11px] text-slate-400 block font-mono uppercase">Waktu Kejadian</span>
                            <span id="modalTimestamp" class="text-xs font-semibold text-slate-900 tabular-nums font-mono mt-0.5 block"></span>
                        </div>
                        <div>
                            <span class="text-[11px] text-slate-400 block font-mono uppercase">Durasi Terdeteksi</span>
                            <span id="modalDuration" class="text-xs font-semibold text-slate-900 tabular-nums font-mono mt-0.5 block"></span>
                        </div>
                    </div>

                    <div>
                        <span class="text-[11px] text-slate-400 block font-mono uppercase mb-1">Status Operasional:</span>
                        <div id="modalContext" class="bg-slate-50 p-2.5 rounded-md border border-slate-200 text-slate-800 text-xs leading-relaxed font-medium"></div>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-[10px] text-slate-400 font-mono uppercase tracking-wider block mb-1.5 font-semibold">Data Audit Telemetri (AI):</span>
                        <div class="bg-slate-900 text-slate-300 p-2.5 rounded-md text-[11px] font-mono space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Model:</span>
                                <span class="text-slate-200">Random Forest Classifier</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Ambang Durasi:</span>
                                <span class="text-emerald-400">&ge; 1.2s</span>
                            </div>
                            <div id="modalRawNotesRow" class="pt-1 border-t border-slate-800 text-[10px] text-slate-400 break-words">
                                <span id="modalRawNotes"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                    <button type="button" onclick="closeSnapshotModal()" class="h-8 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md text-xs font-medium transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openSnapshotModal(imgUrl, driverName, driverId, alertType, timestamp, context, duration, rawNotes) {
        document.getElementById('modalImg').src = imgUrl;
        document.getElementById('modalDriverName').textContent = driverName || 'Pengemudi';
        document.getElementById('modalDriverId').textContent = 'ID: ' + (driverId || '-');
        document.getElementById('modalAlertBadge').textContent = alertType || 'INSIDEN';
        document.getElementById('modalTimestamp').textContent = timestamp || '-';
        document.getElementById('modalDuration').textContent = duration || '-';
        document.getElementById('modalContext').textContent = context || 'Insiden kewaspadaan terdeteksi saat berkendara';
        document.getElementById('modalRawNotes').textContent = rawNotes || 'Tidak ada catatan sensor tambahan';
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
                    this.getAttribute('data-duration'),
                    this.getAttribute('data-raw-notes')
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


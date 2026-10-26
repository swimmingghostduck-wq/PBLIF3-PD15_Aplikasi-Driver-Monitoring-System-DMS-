@extends('layouts.driver')
@section('title', 'Riwayat Peringatan Driver - DriveSafe DMS')

@section('content')
<div class="space-y-6">
    <!-- Header Halaman & Aksi Cepat -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-200 gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold font-heading text-slate-900 tracking-tight">Riwayat Peringatan Saya</h1>
            <p class="text-sm text-slate-500 mt-0.5">Catatan telemetri keselamatan kabin: <strong class="text-slate-800 font-semibold">{{ Auth::user()->full_name }}</strong></p>
        </div>
        <a href="{{ route('driver.monitor') }}" 
           class="h-9 px-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-xs transition-colors inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            <span>Buka Kamera Monitoring</span>
        </a>
    </div>

    <!-- Metric Strip -->
    <div class="bg-white border border-slate-200 rounded-lg divide-y sm:divide-y-0 sm:divide-x divide-slate-100 grid grid-cols-1 sm:grid-cols-3 shadow-xs">
        <!-- 1. Total Kantuk -->
        <div class="p-4 sm:p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono font-semibold text-slate-400 block uppercase tracking-wider">Total Kantuk (EAR)</span>
                <span class="w-2 h-2 rounded-full bg-red-600"></span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-bold font-heading text-slate-900 tabular-nums">{{ $total_kantuk ?? 0 }}</span>
                <span class="text-xs text-slate-400 font-mono">insiden</span>
            </div>
            <p class="text-xs text-slate-500 font-mono mt-1">Mata terpejam &gt; 1.2 detik</p>
        </div>

        <!-- 2. Total Distraksi -->
        <div class="p-4 sm:p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono font-semibold text-slate-400 block uppercase tracking-wider">Total Tidak Fokus</span>
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-bold font-heading text-slate-900 tabular-nums">{{ $total_distraksi ?? 0 }}</span>
                <span class="text-xs text-slate-400 font-mono">insiden</span>
            </div>
            <p class="text-xs text-slate-500 font-mono mt-1">Menoleh / menunduk &gt; 2.5 detik</p>
        </div>

        <!-- 3. Total Menguap -->
        <div class="p-4 sm:p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono font-semibold text-slate-400 block uppercase tracking-wider">Total Menguap</span>
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-bold font-heading text-slate-900 tabular-nums">{{ $total_menguap ?? 0 }}</span>
                <span class="text-xs text-slate-400 font-mono">insiden</span>
            </div>
            <p class="text-xs text-slate-500 font-mono mt-1">Bukaan mulut terindikasi lelah</p>
        </div>
    </div>

    <!-- Tabel Riwayat Insiden -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
            <h2 class="font-bold text-slate-900 text-sm font-heading">Daftar Insiden Tersimpan</h2>
            <span class="text-xs text-slate-500 font-mono">
                <strong class="text-slate-900 tabular-nums">{{ method_exists($alerts, 'total') ? $alerts->total() : count($alerts) }}</strong> rekaman
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-800">
                <thead class="bg-slate-50 text-xs uppercase font-mono font-semibold text-slate-500 border-b border-slate-200 tracking-wider">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap">Waktu Insiden</th>
                        <th class="px-4 py-3 whitespace-nowrap">Klasifikasi Insiden</th>
                        <th class="px-4 py-3 whitespace-nowrap">Tingkat</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Durasi</th>
                        <th class="px-4 py-3">Keterangan Insiden</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Bukti Foto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($alerts as $item)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- Waktu Insiden -->
                        <td class="px-4 py-3 text-slate-500 whitespace-nowrap tabular-nums text-xs font-mono">
                            {{ \Carbon\Carbon::parse($item->timestamp)->format('d/m/Y H:i:s') }}
                        </td>

                        <!-- Klasifikasi Insiden -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($item->alert_type == 'kantuk')
                                <span class="inline-flex items-center gap-2 font-medium text-slate-900 text-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 shrink-0"></span>
                                    <span>Mengantuk (EAR)</span>
                                </span>
                            @elseif($item->alert_type == 'tidak_fokus')
                                <span class="inline-flex items-center gap-2 font-medium text-slate-900 text-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                    <span>Tidak Fokus (Pose)</span>
                                </span>
                            @elseif($item->alert_type == 'menguap')
                                <span class="inline-flex items-center gap-2 font-medium text-slate-900 text-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                    <span>Menguap (MAR)</span>
                                </span>
                            @else
                                <span class="text-slate-700 text-sm">{{ $item->alert_type }}</span>
                            @endif
                        </td>

                        <!-- Tingkat Bahaya -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($item->severity == 'BAHAYA')
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-mono font-bold bg-red-50 text-red-700 border border-red-200 tracking-wider">
                                    BAHAYA
                                </span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-mono font-bold bg-amber-50 text-amber-700 border border-amber-200 tracking-wider">
                                    {{ $item->severity }}
                                </span>
                            @endif
                        </td>

                        <!-- Estimasi Durasi -->
                        <td class="px-4 py-3 text-center text-xs font-mono tabular-nums text-slate-600 whitespace-nowrap">
                            {{ number_format($item->duration_seconds ?? 0, 1) }}s
                        </td>

                        <!-- Keterangan Insiden -->
                        <td class="px-4 py-3 text-slate-700 max-w-xs truncate text-xs font-sans" title="{{ $item->notes ?? '' }}">
                            {{ $item->clean_notes ?? ($item->notes ?? '-') }}
                        </td>

                        <!-- Bukti Foto -->
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @if($item->snapshot_path)
                                <a href="{{ asset('snapshots/' . $item->snapshot_path) }}" target="_blank"
                                   class="h-7.5 px-2.5 bg-white hover:bg-slate-50 text-slate-700 rounded-md border border-slate-200 transition-colors inline-flex items-center gap-1.5 font-sans text-xs font-medium shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>Bukti Foto</span>
                                </a>
                            @else
                                <span class="text-xs text-slate-400 font-mono">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500 font-sans">
                            <div class="max-w-xs mx-auto space-y-1.5">
                                <svg class="w-8 h-8 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-sm font-semibold text-slate-700">Belum ada riwayat peringatan</p>
                                <p class="text-xs text-slate-400">Pertahankan konsentrasi dan keselamatan berkendara Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($alerts, 'links'))
            <div class="px-5 py-3 border-t border-slate-200">
                {{ $alerts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

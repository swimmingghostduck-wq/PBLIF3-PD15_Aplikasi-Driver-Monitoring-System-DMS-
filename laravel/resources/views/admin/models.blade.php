@extends('layouts.admin')
@section('title', 'Statistik Model AI - DriveSafe')

@section('header_actions')
<form action="{{ route('admin.reload_model') }}" method="POST">
    @csrf
    <button type="submit" 
            class="h-9 px-3.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200/90 shadow-2xs transition-colors flex items-center gap-2 cursor-pointer">
        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        <span>Perbarui Data</span>
    </button>
</form>
@endsection

@section('content')
<div class="px-6 sm:px-8 lg:px-10 py-6 sm:py-8 space-y-6">
    
    <!-- Page Header (Clean, Zero Badges) -->
    <div class="pb-4 border-b border-slate-200/90">
        <h1 class="text-xl sm:text-2xl font-bold font-heading text-slate-900 tracking-tight">Status &amp; Performa Model AI</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Statistik evaluasi akurasi sistem pengenalan kewaspadaan pengemudi.</p>
    </div>

    <!-- Sleek High-Density Stat Strip -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs divide-y sm:divide-y-0 sm:divide-x divide-slate-100 grid grid-cols-2 sm:grid-cols-4 overflow-hidden">
        <div class="p-5">
            <span class="text-xs font-medium text-slate-500 block">Total Pengujian</span>
            <div class="text-2xl sm:text-3xl font-bold font-heading text-slate-900 font-mono tabular-nums mt-1">
                290 <span class="text-xs font-normal text-slate-400 font-sans">data</span>
            </div>
        </div>
        <div class="p-5">
            <span class="text-xs font-medium text-slate-500 block">Tingkat Akurasi</span>
            <div class="text-2xl sm:text-3xl font-bold font-heading text-blue-600 font-mono tabular-nums mt-1">
                99.66%
            </div>
        </div>
        <div class="p-5">
            <span class="text-xs font-medium text-slate-500 block">Salah Deteksi</span>
            <div class="text-2xl sm:text-3xl font-bold font-heading text-slate-900 font-mono tabular-nums mt-1">
                1 <span class="text-xs font-normal text-slate-400 font-sans">kasus (0.34%)</span>
            </div>
        </div>
        <div class="p-5">
            <span class="text-xs font-medium text-slate-500 block">Waktu Respons</span>
            <div class="text-2xl sm:text-3xl font-bold font-heading text-slate-900 font-mono tabular-nums mt-1">
                &lt; 15 <span class="text-xs font-normal text-slate-400 font-sans">ms</span>
            </div>
        </div>
    </div>

    <!-- Main Analytics Grid: Matriks Hasil Prediksi (7) & Rincian per Kategori (5) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">
        
        <!-- Kolom Kiri: Matriks Hasil Prediksi (Confusion Matrix) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200/80 bg-slate-50/60 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm font-heading">Matriks Hasil Deteksi</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Perbandingan kondisi aktual pengemudi vs hasil pengenalan sistem</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="toggleMatrixBtn" onclick="toggleMatrixDisplay()" 
                            class="text-xs font-semibold text-slate-700 hover:text-slate-900 px-3 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 shadow-2xs transition-colors cursor-pointer">
                        <span id="toggleMatrixLabel">Grafik PNG</span>
                    </button>
                    @if($cmExists)
                    <a href="{{ route('admin.view_confusion_matrix') }}" target="_blank" 
                       class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline font-mono inline-flex items-center gap-1"
                       title="Buka berkas grafik resolusi penuh">
                        <span>HD ↗</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Grid Matriks Interaktif -->
            <div id="interactiveMatrixView" class="p-5 space-y-4">
                <div class="text-[11px] font-mono font-medium text-slate-400 text-center uppercase tracking-wider">
                    &darr; Kondisi Sebenarnya &nbsp;&bull;&nbsp; Hasil Deteksi &rarr;
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs font-mono border-collapse">
                        <thead>
                            <tr class="text-slate-500">
                                <th class="p-2.5 text-left font-sans text-xs font-semibold w-28">Kondisi</th>
                                <th class="p-2.5 text-center font-semibold bg-slate-100/70 rounded-t-lg">Mengantuk</th>
                                <th class="p-2.5 text-center font-semibold bg-slate-100/70 rounded-t-lg">Menguap</th>
                                <th class="p-2.5 text-center font-semibold bg-slate-100/70 rounded-t-lg">Terjaga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <!-- Baris 1: Mengantuk -->
                            <tr>
                                <td class="p-3 font-medium font-sans text-slate-900 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-rose-600 shrink-0"></span>
                                        <span>Mengantuk</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">80 data</span>
                                </td>
                                <td class="p-1.5">
                                    <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-center font-bold">
                                        <div class="text-base tabular-nums">80</div>
                                        <div class="text-[10px] text-blue-600 font-normal">100% tepat</div>
                                    </div>
                                </td>
                                <td class="p-1.5">
                                    <div class="p-3 rounded-xl bg-slate-50/60 text-center text-slate-300">
                                        <div class="text-base tabular-nums">0</div>
                                    </div>
                                </td>
                                <td class="p-1.5">
                                    <div class="p-3 rounded-xl bg-slate-50/60 text-center text-slate-300">
                                        <div class="text-base tabular-nums">0</div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Baris 2: Menguap -->
                            <tr>
                                <td class="p-3 font-medium font-sans text-slate-900 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                        <span>Menguap</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">80 data</span>
                                </td>
                                <td class="p-1.5">
                                    <div class="p-3 rounded-xl bg-slate-50/60 text-center text-slate-300">
                                        <div class="text-base tabular-nums">0</div>
                                    </div>
                                </td>
                                <td class="p-1.5">
                                    <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-center font-bold">
                                        <div class="text-base tabular-nums">80</div>
                                        <div class="text-[10px] text-blue-600 font-normal">100% tepat</div>
                                    </div>
                                </td>
                                <td class="p-1.5">
                                    <div class="p-3 rounded-xl bg-slate-50/60 text-center text-slate-300">
                                        <div class="text-base tabular-nums">0</div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Baris 3: Terjaga -->
                            <tr>
                                <td class="p-3 font-medium font-sans text-slate-900 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span>Terjaga</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">130 data</span>
                                </td>
                                <td class="p-1.5">
                                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-center font-bold">
                                        <div class="text-base tabular-nums">1</div>
                                        <div class="text-[10px] text-amber-700 font-normal">selip (0.8%)</div>
                                    </div>
                                </td>
                                <td class="p-1.5">
                                    <div class="p-3 rounded-xl bg-slate-50/60 text-center text-slate-300">
                                        <div class="text-base tabular-nums">0</div>
                                    </div>
                                </td>
                                <td class="p-1.5">
                                    <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-center font-bold">
                                        <div class="text-base tabular-nums">129</div>
                                        <div class="text-[10px] text-blue-600 font-normal">99.2% tepat</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tiga Indikator Ringkas -->
                <div class="pt-3 border-t border-slate-100 grid grid-cols-3 gap-3 text-center font-mono">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="text-slate-500 text-[11px] block">Deteksi Akurat</span>
                        <span class="text-base font-bold text-slate-900 block mt-0.5 tabular-nums">289 (99.7%)</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="text-slate-500 text-[11px] block">Salah Deteksi</span>
                        <span class="text-base font-bold text-slate-900 block mt-0.5 tabular-nums">1 (0.3%)</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="text-slate-500 text-[11px] block">Luput Terdeteksi</span>
                        <span class="text-base font-bold text-slate-900 block mt-0.5 tabular-nums">0 (0.0%)</span>
                    </div>
                </div>
            </div>

            <!-- Tampilan Grafik PNG (Toggle) -->
            <div id="graphicMatrixView" class="p-6 bg-slate-50/50 flex flex-col items-center justify-center hidden">
                @if($cmExists)
                    <img src="{{ route('admin.view_confusion_matrix') }}"
                         alt="Grafik Confusion Matrix"
                         class="max-h-[280px] w-auto rounded-xl border border-slate-200 object-contain bg-white shadow-2xs">
                @else
                    <div class="text-center p-6 text-slate-400 text-xs font-mono space-y-1">
                        <p class="font-semibold text-slate-600">Grafik belum tersedia</p>
                        <p>File visualisasi matriks belum dibuat.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Rincian per Kategori Deteksi -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 sm:p-6 space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h2 class="font-bold text-slate-900 text-sm font-heading">Akurasi per Kategori Deteksi</h2>
                <p class="text-xs text-slate-500 mt-0.5">Keberhasilan pengenalan masing-masing kondisi pengemudi</p>
            </div>

            <div class="space-y-4">
                <!-- 1. Mengantuk -->
                <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/70 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-600 shrink-0"></span>
                            <span class="text-xs font-semibold text-slate-900">Mengantuk</span>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-900">99.7%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-rose-600 h-1.5 rounded-full" style="width: 99.7%;"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 font-mono">
                        <span>80 sampel teruji</span>
                        <span class="text-emerald-700 font-medium">Sangat Akurat</span>
                    </div>
                </div>

                <!-- 2. Menguap -->
                <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/70 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                            <span class="text-xs font-semibold text-slate-900">Menguap</span>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-900">100.0%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-amber-500 h-1.5 rounded-full" style="width: 100%;"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 font-mono">
                        <span>80 sampel teruji</span>
                        <span class="text-emerald-700 font-medium">Sempurna</span>
                    </div>
                </div>

                <!-- 3. Terjaga -->
                <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/70 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="text-xs font-semibold text-slate-900">Terjaga / Normal</span>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-900">99.2%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: 99.2%;"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 font-mono">
                        <span>130 sampel teruji</span>
                        <span class="text-emerald-700 font-medium">Sangat Akurat</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fokus Parameter Pengamatan (Human-Readable, Zero Dev Jargon) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 sm:p-6 space-y-4">
        <div class="pb-3 border-b border-slate-100">
            <h2 class="font-bold text-slate-900 text-sm font-heading">Parameter Pemantauan Pengemudi</h2>
            <p class="text-xs text-slate-500 mt-0.5">Tiga indikator visual utama yang dianalisis secara real-time</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/70 space-y-1">
                <span class="font-semibold text-slate-900 block">1. Bukaan Kelopak Mata</span>
                <p class="text-slate-500 text-[11px] leading-relaxed">
                    Menganalisis rasio keterbukaan mata untuk mendeteksi kondisi mengantuk dan mata terpejam saat berkendara.
                </p>
            </div>

            <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/70 space-y-1">
                <span class="font-semibold text-slate-900 block">2. Pola Bukaan Mulut</span>
                <p class="text-slate-500 text-[11px] leading-relaxed">
                    Mengukur rasio bukaan rahang untuk mendeteksi gejala kelelahan dini akibat pengemudi sering menguap.
                </p>
            </div>

            <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/70 space-y-1">
                <span class="font-semibold text-slate-900 block">3. Orientasi Arah Kepala</span>
                <p class="text-slate-500 text-[11px] leading-relaxed">
                    Memantau sudut pandangan kepala (menoleh, menunduk, atau mendongak) untuk mendeteksi distraksi fokus jalan.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleMatrixDisplay() {
        const interactive = document.getElementById('interactiveMatrixView');
        const graphic = document.getElementById('graphicMatrixView');
        const label = document.getElementById('toggleMatrixLabel');
        if (interactive.classList.contains('hidden')) {
            interactive.classList.remove('hidden');
            graphic.classList.add('hidden');
            label.textContent = 'Grafik PNG';
        } else {
            interactive.classList.add('hidden');
            graphic.classList.remove('hidden');
            label.textContent = 'Tabel Interaktif';
        }
    }
</script>
@endsection

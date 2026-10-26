@extends('layouts.admin')
@section('title', 'Model AI & Evaluasi - DriveSafe')

@section('header_actions')
<!-- Action di Topbar: Muat Ulang Model -->
<form action="{{ route('admin.reload_model') }}" method="POST">
    @csrf
    <button type="submit" 
            class="h-8 px-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-medium rounded-md transition-colors flex items-center gap-1.5 cursor-pointer"
            title="Muat ulang artefak model AI ke memori server">
        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        <span>Muat Ulang Model</span>
    </button>
</form>
@endsection

@section('content')
<div class="px-6 sm:px-8 lg:px-10 py-6 sm:py-8 space-y-5">
    
    <!-- Page Header & Technical Architecture Subtitle -->
    <div class="pb-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold font-heading text-slate-900 tracking-tight">Status &amp; Performa Model AI</h1>
            <p class="text-sm text-slate-500 mt-0.5">Spesifikasi arsitektur Random Forest, evaluasi Confusion Matrix, dan metrik inferensi computer vision.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-mono font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Engine Aktif
            </span>
        </div>
    </div>

    <!-- 4 Clean Structured Stat Blocks -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Model Klasifikasi -->
        <div class="bg-white p-5 rounded-lg border border-slate-200 flex flex-col justify-between">
            <div>
                <span class="text-xs font-mono font-semibold text-slate-400 block uppercase tracking-wider">Arsitektur Model</span>
                <h3 class="text-lg font-bold font-heading text-slate-900 mt-1">Random Forest</h3>
                <p class="text-xs text-slate-500 font-mono mt-0.5">50 Decision Trees &bull; Scikit-Learn</p>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs font-mono">
                <span class="text-slate-400">Status:</span>
                <span class="text-emerald-700 font-medium">Tersinkronisasi</span>
            </div>
        </div>

        <!-- 2. Akurasi Validasi -->
        <div class="bg-white p-5 rounded-lg border border-slate-200 flex flex-col justify-between">
            <div>
                <span class="text-xs font-mono font-semibold text-slate-400 block uppercase tracking-wider">Akurasi Validasi</span>
                <div class="mt-1 flex items-baseline gap-1.5 font-mono">
                    <span class="text-2xl font-bold font-heading text-slate-900 tabular-nums">99.66%</span>
                    <span class="text-xs text-emerald-700 font-medium">Optimal</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs font-mono text-slate-500">
                <span>Skor Evaluasi:</span>
                <span class="tabular-nums">289 / 290 Tepat</span>
            </div>
        </div>

        <!-- 3. False Alarm Rate -->
        <div class="bg-white p-5 rounded-lg border border-slate-200 flex flex-col justify-between">
            <div>
                <span class="text-xs font-mono font-semibold text-slate-400 block uppercase tracking-wider">False Alarm Rate</span>
                <div class="mt-1 flex items-baseline gap-1.5 font-mono">
                    <span class="text-2xl font-bold font-heading text-slate-900 tabular-nums">0.34%</span>
                    <span class="text-xs text-emerald-700 font-medium">Sangat Rendah</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs font-mono text-slate-500">
                <span>Presisi Makro:</span>
                <span class="tabular-nums">99.7%</span>
            </div>
        </div>

        <!-- 4. Latensi Inferensi -->
        <div class="bg-white p-5 rounded-lg border border-slate-200 flex flex-col justify-between">
            <div>
                <span class="text-xs font-mono font-semibold text-slate-400 block uppercase tracking-wider">Latensi Inferensi</span>
                <div class="mt-1 flex items-baseline gap-1.5 font-mono">
                    <span class="text-2xl font-bold font-heading text-slate-900 tabular-nums">&lt; 15 ms</span>
                    <span class="text-xs text-slate-400 font-sans">per frame</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs font-mono text-slate-500">
                <span>Throughput:</span>
                <span class="tabular-nums">&ge; 30 FPS (CPU)</span>
            </div>
        </div>
    </div>

    <!-- Row 1: Evaluasi Klasifikasi per Kategori & Matriks Konfusi (7 : 5) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">
        
        <!-- Kolom Kiri: Tabel Performa Klasifikasi per Kategori (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/60 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm font-heading">Metrik Klasifikasi per Kategori</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Nilai presisi, recall, dan F1-score pada 290 sampel data uji fisiologis</p>
                </div>
                <span class="px-2 py-0.5 bg-white text-slate-700 border border-slate-200 text-xs font-mono font-medium rounded">
                    290 Sampel
                </span>
            </div>

            <!-- Tabel Evaluasi per Kategori -->
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-800">
                    <thead class="bg-slate-50/80 text-[11px] uppercase font-mono font-semibold text-slate-500 border-b border-slate-200 tracking-wider">
                        <tr>
                            <th class="px-4 py-3 whitespace-nowrap">Kategori Target</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap">Presisi</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap">Recall</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap">F1-Score</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap">Sampel</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono text-xs">
                        <!-- 1. Mengantuk -->
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-3 text-slate-900 font-sans whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-rose-600 shrink-0"></span>
                                    <span class="font-medium">Mengantuk (EAR &le; 0.22)</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700 font-medium">0.99</td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700 font-medium">1.00</td>
                            <td class="px-4 py-3 text-center tabular-nums font-bold text-slate-900">0.99</td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-500">80</td>
                        </tr>

                        <!-- 2. Menguap -->
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-3 text-slate-900 font-sans whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-600 shrink-0"></span>
                                    <span class="font-medium">Menguap (MAR &ge; 0.65)</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700 font-medium">1.00</td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700 font-medium">1.00</td>
                            <td class="px-4 py-3 text-center tabular-nums font-bold text-slate-900">1.00</td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-500">80</td>
                        </tr>

                        <!-- 3. Terjaga -->
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-3 text-slate-900 font-sans whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                    <span class="font-medium">Terjaga / Normal</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700 font-medium">1.00</td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700 font-medium">0.99</td>
                            <td class="px-4 py-3 text-center tabular-nums font-bold text-slate-900">1.00</td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-500">130</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200 font-mono text-xs tabular-nums font-semibold">
                        <tr>
                            <th class="px-4 py-2.5 text-slate-900 font-sans">Rata-rata Terbobot</th>
                            <th class="px-4 py-2.5 text-center text-slate-900">1.00</th>
                            <th class="px-4 py-2.5 text-center text-slate-900">1.00</th>
                            <th class="px-4 py-2.5 text-center text-slate-900">1.00</th>
                            <th class="px-4 py-2.5 text-center text-slate-900">290</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Kolom Kanan: Matriks Konfusi (Confusion Matrix) (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/60 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm font-heading">Matriks Konfusi</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Prediksi vs Aktual Ground Truth</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="toggleMatrixBtn" onclick="toggleMatrixDisplay()" 
                            class="text-xs font-mono font-medium text-slate-600 hover:text-slate-900 px-2 py-0.5 rounded border border-slate-200 bg-white hover:bg-slate-50 transition-colors cursor-pointer">
                        <span id="toggleMatrixLabel">Grafik PNG</span>
                    </button>
                    @if($cmExists)
                    <a href="{{ route('admin.view_confusion_matrix') }}" target="_blank" 
                       class="text-xs font-medium text-slate-900 hover:underline font-mono inline-flex items-center gap-0.5"
                       title="Buka berkas grafik PNG">
                        <span>Lihat HD ↗</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- 1. Interactive 3x3 HTML/CSS Confusion Matrix -->
            <div id="interactiveMatrixView" class="p-4 bg-white border-b border-slate-100 space-y-2">
                <div class="text-[10px] font-mono font-semibold text-slate-400 uppercase tracking-wider text-center">
                    &darr; Ground Truth &nbsp;&bull;&nbsp; Prediksi AI &rarr;
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-xs font-mono border-collapse">
                        <thead>
                            <tr>
                                <th class="p-1.5 text-left font-sans text-slate-400 font-semibold w-24">Target</th>
                                <th class="p-1.5 text-center text-slate-700 bg-slate-100 rounded-t font-semibold">Mengantuk</th>
                                <th class="p-1.5 text-center text-slate-700 bg-slate-100 rounded-t font-semibold">Menguap</th>
                                <th class="p-1.5 text-center text-slate-700 bg-slate-100 rounded-t font-semibold">Terjaga</th>
                            </tr>
                        </thead>
                        <tbody class="space-y-1">
                            <!-- Baris 1: Aktual Mengantuk -->
                            <tr>
                                <td class="p-1.5 font-medium font-sans text-slate-900 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600 shrink-0"></span>
                                        <span>Mengantuk</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">n=80</span>
                                </td>
                                <td class="p-1">
                                    <div class="p-2 rounded bg-slate-900 text-white text-center font-bold">
                                        <div class="tabular-nums">80</div>
                                        <div class="text-[10px] text-slate-400 font-normal">100%</div>
                                    </div>
                                </td>
                                <td class="p-1">
                                    <div class="p-2 rounded bg-slate-50 border border-slate-200 text-center text-slate-400">
                                        <div class="tabular-nums">0</div>
                                    </div>
                                </td>
                                <td class="p-1">
                                    <div class="p-2 rounded bg-slate-50 border border-slate-200 text-center text-slate-400">
                                        <div class="tabular-nums">0</div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Baris 2: Aktual Menguap -->
                            <tr>
                                <td class="p-1.5 font-medium font-sans text-slate-900 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 shrink-0"></span>
                                        <span>Menguap</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">n=80</span>
                                </td>
                                <td class="p-1">
                                    <div class="p-2 rounded bg-slate-50 border border-slate-200 text-center text-slate-400">
                                        <div class="tabular-nums">0</div>
                                    </div>
                                </td>
                                <td class="p-1">
                                    <div class="p-2 rounded bg-slate-900 text-white text-center font-bold">
                                        <div class="tabular-nums">80</div>
                                        <div class="text-[10px] text-slate-400 font-normal">100%</div>
                                    </div>
                                </td>
                                <td class="p-1">
                                    <div class="p-2 rounded bg-slate-50 border border-slate-200 text-center text-slate-400">
                                        <div class="tabular-nums">0</div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Baris 3: Aktual Terjaga -->
                            <tr>
                                <td class="p-1.5 font-medium font-sans text-slate-900 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span>Terjaga</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">n=130</span>
                                </td>
                                <td class="p-1">
                                    <div class="p-2 rounded bg-amber-50 border border-amber-300 text-center text-amber-800 font-bold" title="1 False Alarm">
                                        <div class="tabular-nums">1</div>
                                        <div class="text-[10px] text-amber-700 font-medium">0.8%</div>
                                    </div>
                                </td>
                                <td class="p-1">
                                    <div class="p-2 rounded bg-slate-50 border border-slate-200 text-center text-slate-400">
                                        <div class="tabular-nums">0</div>
                                    </div>
                                </td>
                                <td class="p-1">
                                    <div class="p-2 rounded bg-slate-900 text-white text-center font-bold">
                                        <div class="tabular-nums">129</div>
                                        <div class="text-[10px] text-slate-400 font-normal">99.2%</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Preview Matplotlib PNG Image (Toggleable) -->
            <div id="graphicMatrixView" class="p-4 bg-slate-50/50 flex flex-col items-center justify-center border-b border-slate-200 hidden">
                @if($cmExists)
                    <img src="{{ route('admin.view_confusion_matrix') }}"
                         alt="Grafik Confusion Matrix"
                         class="max-h-[260px] w-auto rounded border border-slate-200 object-contain bg-white">
                @else
                    <div class="text-center p-6 text-slate-400 text-xs font-mono space-y-1">
                        <p class="font-semibold text-slate-600">Grafik belum tersedia</p>
                        <p>Jalankan <code>python training_suite/evaluate.py</code> untuk menghasilkan grafik evaluasi.</p>
                    </div>
                @endif
            </div>

            <!-- Breakdown Ringkasan Forensik Matriks -->
            <div class="p-3.5 grid grid-cols-3 gap-2 text-center text-xs font-mono">
                <div class="p-2 rounded bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block text-[11px]">True Positive</span>
                    <span class="text-sm font-bold text-slate-900 block mt-0.5 tabular-nums">289</span>
                    <span class="text-emerald-700 font-medium text-[11px]">99.66%</span>
                </div>
                <div class="p-2 rounded bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block text-[11px]">False Positive</span>
                    <span class="text-sm font-bold text-slate-900 block mt-0.5 tabular-nums">1</span>
                    <span class="text-amber-700 font-medium text-[11px]">0.34%</span>
                </div>
                <div class="p-2 rounded bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block text-[11px]">False Negative</span>
                    <span class="text-sm font-bold text-slate-900 block mt-0.5 tabular-nums">0</span>
                    <span class="text-slate-500 font-medium text-[11px]">0.00%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Berkas Artefak Model & Spesifikasi Inferensi -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">
        
        <!-- Berkas Artefak di Server -->
        <div class="lg:col-span-6 bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/60 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm font-heading">Berkas Artefak Model</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Direktori penyimpanan lokal: <code>models/</code></p>
                </div>
                <span class="text-xs text-slate-500 font-mono">{{ count($modelsList) }} berkas</span>
            </div>

            <div class="divide-y divide-slate-100 text-xs font-sans">
                @forelse($modelsList as $m)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50/60 transition-colors">
                    <div class="min-w-0 pr-3">
                        <div class="font-medium text-slate-900 truncate flex items-center gap-2">
                            <span class="font-mono text-slate-800">{{ $m['filename'] }}</span>
                        </div>
                        <div class="text-slate-400 text-[11px] font-mono mt-0.5 tabular-nums">
                            Ukuran: {{ $m['size_kb'] }} KB &bull; Format: {{ strtoupper(pathinfo($m['filename'], PATHINFO_EXTENSION)) }}
                        </div>
                    </div>
                    <span class="text-[11px] font-mono font-medium px-2 py-0.5 rounded-full shrink-0 {{ str_contains($m['filename'], 'trained_model.joblib') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                        {{ str_contains($m['filename'], 'trained_model.joblib') ? 'AKTIF DI ENGINE' : 'ARTEFAK EVALUASI' }}
                    </span>
                </div>
                @empty
                <div class="p-6 text-center text-xs text-slate-400 font-mono">
                    Belum ada file model terlatih di direktori <code>models/</code>.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Spesifikasi Inferensi & Parameter Model -->
        <div class="lg:col-span-6 bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/60 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm font-heading">Spesifikasi Inferensi &amp; Parameter</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Konfigurasi parameter runtime pipeline AI telematika</p>
                </div>
                <span class="text-xs text-slate-500 font-mono">5 Parameter</span>
            </div>

            <div class="p-4 space-y-2 font-mono text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400">Target Klasifikasi:</span>
                    <span class="font-medium text-slate-800">3 Kelas (Mengantuk, Menguap, Terjaga)</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400">Vektor Fitur:</span>
                    <span class="font-medium text-slate-800">10D (EAR, MAR, Head Pose Yaw/Pitch/Roll)</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400">Ekstraktor Landmark:</span>
                    <span class="font-medium text-slate-800">MediaPipe FaceLandmarker (468 Mesh)</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400">Ambang Batas Deteksi:</span>
                    <span class="font-medium text-slate-800">&ge; 1.2 detik kumulatif</span>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <span class="text-slate-400">Pipeline Runtime:</span>
                    <span class="font-medium text-slate-800">Scikit-Learn Joblib Loader + Fallback Heuristik</span>
                </div>
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
            label.textContent = 'Grid Interaktif';
        }
    }
</script>
@endsection


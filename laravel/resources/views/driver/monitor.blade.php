@extends('layouts.driver')
@section('title', 'Monitoring Pengemudi Real-Time - DriveSafe DMS')

@section('content')
<!-- Cadangan tema: dipakai HANYA jika layouts/driver.blade.php belum diperbarui. Otomatis dibuang jika layout sudah mendukung tema. -->
<style id="dsThemeFallbackCss">
        /* ===== Skema warna: variabel dasar ===== */
        :root {
            --ds-card: #FFFFFF; --ds-bg2: #F8FAFC; --ds-border: #E2E8F0; --ds-text: #0F172A; --ds-muted: #64748B;
            --ac-600: #2563EB; --ac-700: #1D4ED8; --ac-300: #93C5FD; --ac-rgb: 37, 99, 235;
        }
        html[data-accent="emerald"] { --ac-600: #059669; --ac-700: #047857; --ac-300: #6EE7B7; --ac-rgb: 5, 150, 105; }
        html[data-accent="violet"]  { --ac-600: #7C3AED; --ac-700: #6D28D9; --ac-300: #C4B5FD; --ac-rgb: 124, 58, 237; }
        html[data-accent="rose"]    { --ac-600: #E11D48; --ac-700: #BE123C; --ac-300: #FDA4AF; --ac-rgb: 225, 29, 72; }
        html[data-accent="orange"]  { --ac-600: #EA580C; --ac-700: #C2410C; --ac-300: #FDBA74; --ac-rgb: 234, 88, 12; }

        /* ===== Warna aksen: menimpa kelas biru Tailwind ===== */
        html[data-accent] .bg-blue-600 { background-color: var(--ac-600) !important; }
        html[data-accent] .hover\:bg-blue-700:hover { background-color: var(--ac-700) !important; }
        html[data-accent] .text-blue-600, html[data-accent] .text-blue-700 { color: var(--ac-700) !important; }
        html[data-accent] .bg-blue-50 { background-color: rgba(var(--ac-rgb), .09) !important; }
        html[data-accent] .bg-blue-100, html[data-accent] .hover\:bg-blue-100:hover { background-color: rgba(var(--ac-rgb), .17) !important; }
        html[data-accent] .border-blue-200, html[data-accent] .border-blue-100 { border-color: rgba(var(--ac-rgb), .30) !important; }
        html[data-accent] .focus\:border-blue-600:focus { border-color: var(--ac-600) !important; }
        html[data-accent] .bg-blue-500, html[data-accent] .bg-primary { background-color: var(--ac-600) !important; }
        html[data-accent] .bg-blue-700 { background-color: var(--ac-700) !important; }
        html[data-accent] .text-primary { color: var(--ac-700) !important; }
        html[data-accent] .text-blue-400 { color: var(--ac-300) !important; }
        html[data-accent] .hover\:text-blue-700:hover { color: var(--ac-700) !important; }
        html[data-accent] .border-blue-600, html[data-accent] .border-blue-500 { border-color: var(--ac-600) !important; }
        html[data-accent] .border-blue-500\/40 { border-color: rgba(var(--ac-rgb), .40) !important; }
        html[data-accent] .bg-blue-500\/20 { background-color: rgba(var(--ac-rgb), .20) !important; }
        html[data-accent] .focus\:ring-blue-500\/10:focus, html[data-accent] .focus\:ring-blue-100:focus { --tw-ring-color: rgba(var(--ac-rgb), .18) !important; }

        /* ===== Tema gelap ===== */
        html[data-theme="dark"] { color-scheme: dark; --ds-card: #111827; --ds-bg2: #0F172A; --ds-border: #2B3A52; --ds-text: #F1F5F9; --ds-muted: #94A3B8; }
        html[data-theme="dark"] body { background-color: #0B1120 !important; color: #E2E8F0; }
        html[data-theme="dark"] .bg-white { background-color: #111827 !important; }
        html[data-theme="dark"] .bg-white\/95 { background-color: rgba(17, 24, 39, .95) !important; }
        html[data-theme="dark"] .bg-slate-50 { background-color: #0F172A !important; }
        html[data-theme="dark"] .bg-slate-100 { background-color: #1E293B !important; }
        html[data-theme="dark"] .bg-slate-200 { background-color: #334155 !important; }
        html[data-theme="dark"] .hover\:bg-slate-50:hover { background-color: #1E293B !important; }
        html[data-theme="dark"] .hover\:bg-slate-100:hover, html[data-theme="dark"] .hover\:bg-slate-200:hover { background-color: #273449 !important; }
        html[data-theme="dark"] .text-slate-900, html[data-theme="dark"] .text-slate-800 { color: #F1F5F9 !important; }
        html[data-theme="dark"] .text-slate-700 { color: #E2E8F0 !important; }
        html[data-theme="dark"] .text-slate-600 { color: #CBD5E1 !important; }
        html[data-theme="dark"] .text-slate-500 { color: #94A3B8 !important; }
        html[data-theme="dark"] .border-slate-100 { border-color: #1E293B !important; }
        html[data-theme="dark"] .border-slate-200, html[data-theme="dark"] .border-slate-200\/90,
        html[data-theme="dark"] .border-slate-200\/80, html[data-theme="dark"] .border-slate-300 { border-color: #2B3A52 !important; }
        html[data-theme="dark"][data-accent] .text-blue-600, html[data-theme="dark"][data-accent] .text-blue-700 { color: var(--ac-300) !important; }
        html[data-theme="dark"] .bg-amber-50 { background-color: rgba(245, 158, 11, .13) !important; }
        html[data-theme="dark"] .text-amber-800, html[data-theme="dark"] .text-amber-600 { color: #FCD34D !important; }
        html[data-theme="dark"] .border-amber-200 { border-color: rgba(245, 158, 11, .35) !important; }
        html[data-theme="dark"] .bg-red-50, html[data-theme="dark"] .hover\:bg-red-50:hover { background-color: rgba(239, 68, 68, .14) !important; }
        html[data-theme="dark"] .bg-emerald-50 { background-color: rgba(16, 185, 129, .13) !important; }
        html[data-theme="dark"] .bg-purple-50 { background-color: rgba(168, 85, 247, .13) !important; }
        html[data-theme="dark"] select, html[data-theme="dark"] input:not([type="checkbox"]):not([type="radio"]):not([type="range"]), html[data-theme="dark"] textarea { background-color: #0F172A; color: #E2E8F0; border-color: #2B3A52; }
        html[data-theme="dark"] ::-webkit-scrollbar-track { background: #0F172A; }
        html[data-theme="dark"] ::-webkit-scrollbar-thumb { background: #334155; }
</style>
<script>if (window.dsApplyTheme) { var __f = document.getElementById('dsThemeFallbackCss'); if (__f) __f.remove(); }</script>

<div class="space-y-6">
    <!-- Selamat Datang -->
    <section id="welcomeSection" class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs px-6 py-10 text-center">
        <img src="{{ asset('logo.svg') }}" alt="" class="w-14 h-14 mx-auto mb-4">
        <h2 class="text-3xl font-heading font-bold text-slate-900 tracking-tight">Selamat Datang, {{ Auth::user()->full_name }}</h2>
        <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto">Nyalakan kamera agar DriveSafe dapat memantau kewaspadaan Anda selama berkendara.</p>
        <button type="button" onclick="startFromWelcome()"
                class="mt-5 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition cursor-pointer">
            Akses Kamera
        </button>
        <p class="text-xs text-slate-500 mt-3">Browser akan meminta izin kamera. Pilih "Izinkan".</p>
    </section>

    <!-- ====== BAGIAN KAMERA (tersembunyi sampai tombol Akses Kamera diklik) ====== -->
    <div id="cameraSection" class="hidden space-y-6">
    <!-- Header Ringkasan Status & Kontrol Kamera -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-200/80 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight font-heading flex items-center gap-2.5">
                <span>Pemantauan Kamera Real-Time</span>
                <span id="liveBadge" class="flex h-3 w-3 relative">
                    <span id="livePing" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75 hidden"></span>
                    <span id="liveDot" class="relative inline-flex rounded-full h-3 w-3 bg-slate-400"></span>
                </span>
            </h1>
            <p class="text-sm text-slate-500 mt-0.5 font-sans">Pengemudi: <strong class="text-slate-800">{{ Auth::user()->full_name }}</strong> &middot; Mode: <span class="text-blue-600 font-semibold" id="modeText">Kamera Browser (WebRTC)</span></p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Audio Alarm Toggle -->
            <button id="toggleAudioBtn" onclick="toggleAudio()" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl border border-slate-300 shadow-2xs flex items-center gap-2 transition cursor-pointer">
                <svg id="audioIcon" class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                </svg>
                <span id="audioText">Audio Alarm: AKTIF</span>
            </button>
            <!-- Tombol Tes Suara Alarm -->
            <button id="testAlarmBtn" onclick="testAlarm()" title="Uji coba suara alarm buzzer" class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-semibold rounded-xl border border-amber-200 shadow-2xs flex items-center gap-1.5 transition cursor-pointer">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span>Tes Alarm</span>
            </button>
            <button type="button" onclick="openSettings()" title="Pengaturan suara, peringatan, kamera &amp; tampilan"
                    class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl border border-slate-300 shadow-2xs flex items-center gap-1.5 transition cursor-pointer">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Pengaturan</span>
            </button>
            <a href="{{ route('driver.dashboard') }}" class="px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-xl border border-blue-200 shadow-2xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Riwayat Peringatan</span>
            </a>
        </div>
    </div>

    <!-- Panel Izin & Pemilihan Perangkat Webcam (White Card) -->
    <div class="bg-white border border-slate-200/90 p-4 rounded-2xl shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div id="camStatusIcon" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-lg border border-slate-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Kamera:</span>
                    <span id="camStatusBadge" class="text-xs px-2.5 py-0.5 rounded-full font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        Menunggu Izin Akses...
                    </span>
                </div>
                <div class="text-xs text-slate-500 mt-0.5" id="camDetailText">
                    Klik tombol untuk meminta izin akses webcam laptop Anda.
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Dropdown Pilihan Kamera -->
            <select id="cameraSelect" onchange="switchCamera()"
                    class="bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3 py-2 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100 transition">
                <option value="">Mencari kamera aktif...</option>
            </select>

            <!-- Tombol Utama ON / OFF Kamera -->
            <button id="toggleCamBtn" onclick="toggleCamera()"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                <span id="toggleCamIcon">📹</span> <span id="toggleCamText">Nyalakan Kamera</span>
            </button>

            <!-- Tombol Minta Izin Kamera Eksplisit -->
            <button id="requestPermBtn" onclick="requestAndStartCamera()" title="Minta ulang izin akses kamera browser"
                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <span>Izin Kamera</span>
            </button>
        </div>
    </div>

    <!-- ====== KLASTER KAMERA: CAM 1 (utama) kiri, CAM 2 & CAM 3 bertumpuk di kanan ====== -->
    <style>
    #cameraCluster { display: flex; flex-direction: column; gap: 12px; }

    /* ---------- Layout normal ----------
       Mobile  : semua tile bertumpuk (utama dulu)
       >=768px : CAM 1 besar di kiri, CAM 2 dan CAM 3 bertumpuk di kanan */
    .cluster-grid {
        --gh: clamp(480px, calc(100vh - 240px), 720px);   /* tinggi area kamera */
        --n: 2;                                           /* jumlah tile samping (diatur JS) */
        --cols: 2; --cols-eq: 2;                          /* jumlah kolom untuk preset top / grid (diatur JS) */
        position: relative;
        display: grid;
        gap: 12px;
        grid-template-columns: minmax(0, 1fr);
    }
    @media (min-width: 768px) {
        /* --- Preset "side": utama kiri besar, sisanya bertumpuk di kanan (default, sesuai mockup) --- */
        .cluster-grid[data-layout="side"], .cluster-grid[data-layout="side-right"] {
            grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
            grid-template-rows: repeat(var(--n), minmax(230px, 1fr));
            height: var(--gh);
            overflow-x: hidden; overflow-y: auto;   /* kolom samping bisa di-scroll jika kamera > 3 */
            scrollbar-width: thin;
        }
        .cluster-grid[data-layout="side-right"] { grid-template-columns: minmax(0, 1fr) minmax(0, 1.4fr); }
        [data-layout="side"] .cam-tile[data-slot="main"], [data-layout="side-right"] .cam-tile[data-slot="main"] {
            grid-row: 1 / -1; position: sticky; top: 0; align-self: start; height: var(--gh);
        }
        [data-layout="side"] .cam-tile[data-slot="main"] { grid-column: 1; }
        [data-layout="side"] .cam-tile[data-slot="side"] { grid-column: 2; }
        [data-layout="side-right"] .cam-tile[data-slot="main"] { grid-column: 2; }
        [data-layout="side-right"] .cam-tile[data-slot="side"] { grid-column: 1; }

        /* --- Preset "top": utama lebar di atas, sisanya berjajar di bawah --- */
        .cluster-grid[data-layout="top"] {
            grid-template-columns: repeat(var(--cols), minmax(0, 1fr));
            grid-template-rows: clamp(300px, 52vh, 520px);
            grid-auto-rows: clamp(250px, 36vh, 340px);
        }
        [data-layout="top"] .cam-tile[data-slot="main"] { grid-column: 1 / -1; grid-row: 1; }

        /* --- Preset "grid": semua kamera sama besar --- */
        .cluster-grid[data-layout="grid"] {
            grid-template-columns: repeat(var(--cols-eq), minmax(0, 1fr));
            grid-auto-rows: clamp(260px, 40vh, 380px);
        }
    }
    /* urutan tile diatur lewat style.order (JS: applySlots) */

    .cam-tile {
        display: flex; flex-direction: column;
        min-width: 0; min-height: 0;
        border: 2px solid var(--ds-border, #E2E8F0); border-radius: 16px;
        overflow: hidden; background: var(--ds-card, #fff);
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .cam-tile.is-main { border-color: var(--ac-600, #2563EB); }   /* garis biru = kamera utama */
    .cam-tile > .tile-head,
    .cam-tile > .tile-foot,
    .cam-tile > .tile-controls { flex: none; }

    .tile-view {
        position: relative; flex: 1 1 auto; min-height: 0;
        background: #020617; overflow: hidden;
        display: flex; align-items: center; justify-content: center;
    }
    @media (max-width: 767px) {
        .is-main  .tile-view { min-height: 300px; }
        .is-thumb .tile-view { min-height: 190px; }
    }
    /* Canvas mengisi tile tanpa mengubah rasio gambar */
    .cam-canvas { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: var(--cam-fit, contain); }
    .is-main .make-main-btn { display: none; }

    /* Isi panel detail bisa di-scroll di dalam tile */
    .detail-body { flex: 1 1 auto; min-height: 0; overflow-y: auto; padding: 12px; background: var(--ds-bg2, #F8FAFC); }

    /* Tile kecil (bukan layar penuh): ringkas teks placeholder */
    #cameraCluster:not(.fs-on) .is-thumb #standbyPlaceholder,
    #cameraCluster:not(.fs-on) .is-thumb #objStandby { padding: 12px; }
    #cameraCluster:not(.fs-on) .is-thumb #standbyPlaceholder p,
    #cameraCluster:not(.fs-on) .is-thumb #objStandby p,
    #cameraCluster:not(.fs-on) .is-thumb #permissionDeniedNotice ol { display: none; }
    #cameraCluster:not(.fs-on) .is-thumb #standbyPlaceholder .w-20 { width: 3rem; height: 3rem; font-size: 1.5rem; }
    #cameraCluster:not(.fs-on) .is-thumb #permissionDeniedNotice { margin: 8px; padding: 12px; }

    /* ---------- Kamera tambahan ---------- */
    .cam-video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: var(--cam-fit, contain); background: #020617; }
    .x-standby { padding: 16px; text-align: center; color: #94a3b8; font-size: 12px; }
    .x-standby .x-emoji { font-size: 28px; margin-bottom: 4px; }
    .x-btn-del { font-size: 11px; font-weight: 700; color: #dc2626; cursor: pointer; background: none; border: 0; padding: 0 2px; }
    .x-btn-del:hover { text-decoration: underline; }
    #cameraCluster:not(.fs-on) .is-thumb .x-standby p { display: none; }

    /* ---------- Opsi tampilan dari Pengaturan ---------- */
    #cameraCluster.no-overlay #driverOverlay { display: none !important; }

    /* Kilatan layar saat peringatan */
    #alertFlash { position: fixed; inset: 0; z-index: 70; pointer-events: none; opacity: 0; transition: opacity .15s;
                  box-shadow: inset 0 0 0 10px rgba(239,68,68,.95), inset 0 0 120px 30px rgba(239,68,68,.45); }
    #alertFlash.on { opacity: 1; animation: ds-flash .5s infinite alternate ease-in-out; }
    @keyframes ds-flash { from { opacity: .35; } to { opacity: 1; } }

    /* ---------- Panel Pengaturan ---------- */
    .st-overlay { position: fixed; inset: 0; z-index: 80; display: none; }
    .st-overlay.open { display: block; }
    .st-backdrop { position: absolute; inset: 0; background: rgba(2, 6, 23, .55); }
    .st-panel { position: absolute; top: 0; right: 0; height: 100%; width: min(440px, 100%); display: flex; flex-direction: column;
                background: var(--ds-card, #fff); color: var(--ds-text, #0F172A); box-shadow: -12px 0 40px rgba(0,0,0,.25);
                font-family: Inter, ui-sans-serif, system-ui, sans-serif; font-size: 13px; }
    .st-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid var(--ds-border, #E2E8F0); }
    .st-head h2 { margin: 0; font-size: 16px; font-weight: 700; }
    .st-x { width: 32px; height: 32px; border-radius: 9999px; border: 1px solid var(--ds-border, #E2E8F0); background: transparent; color: inherit; cursor: pointer; }
    .st-tabs { display: flex; gap: 4px; padding: 8px 12px 0; border-bottom: 1px solid var(--ds-border, #E2E8F0); overflow-x: auto; }
    .st-tab { padding: 8px 12px; border: 0; background: none; color: var(--ds-muted, #64748B); font-weight: 600; font-size: 12.5px; cursor: pointer;
              border-bottom: 2px solid transparent; white-space: nowrap; }
    .st-tab.active { color: var(--ac-700, #1D4ED8); border-bottom-color: var(--ac-600, #2563EB); }
    html[data-theme="dark"] .st-tab.active { color: var(--ac-300, #93C5FD); }
    .st-body { flex: 1 1 auto; overflow-y: auto; padding: 14px 18px 24px; }
    .st-pane { display: none; } .st-pane.active { display: block; }
    .st-h { margin: 16px 0 8px; font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--ds-muted, #64748B); }
    .st-h:first-child { margin-top: 0; }
    .st-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 9px 0; border-bottom: 1px solid var(--ds-border, #E2E8F0); cursor: pointer; }
    .st-row:last-child { border-bottom: 0; }
    .st-row b { display: block; font-weight: 600; }
    .st-row small { display: block; color: var(--ds-muted, #64748B); font-size: 11.5px; margin-top: 1px; }
    .st-switch { appearance: none; -webkit-appearance: none; flex: none; width: 38px; height: 22px; border-radius: 9999px; background: #CBD5E1; position: relative; cursor: pointer; transition: background .15s; }
    .st-switch::after { content: ""; position: absolute; top: 2px; left: 2px; width: 18px; height: 18px; border-radius: 9999px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.3); transition: transform .15s; }
    .st-switch:checked { background: var(--ac-600, #2563EB); }
    .st-switch:checked::after { transform: translateX(16px); }
    .st-field { width: 100%; padding: 8px 10px; border-radius: 10px; border: 1px solid var(--ds-border, #CBD5E1); background: var(--ds-bg2, #F8FAFC); color: inherit; font-size: 13px; }
    .st-range { width: 100%; accent-color: var(--ac-600, #2563EB); }
    .st-btn { padding: 8px 14px; border-radius: 10px; border: 1px solid var(--ds-border, #CBD5E1); background: var(--ds-card, #fff); color: inherit; font-weight: 600; font-size: 12.5px; cursor: pointer; }
    .st-btn.primary { background: var(--ac-600, #2563EB); border-color: var(--ac-600, #2563EB); color: #fff; }
    .st-btn.danger { color: #DC2626; }
    .st-inline { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 8px; }
    .st-seg { display: flex; border: 1px solid var(--ds-border, #CBD5E1); border-radius: 10px; overflow: hidden; }
    .st-seg label { flex: 1; text-align: center; padding: 7px 6px; cursor: pointer; font-weight: 600; font-size: 12px; color: var(--ds-muted, #64748B); }
    .st-seg input { display: none; }
    .st-seg label:has(input:checked) { background: var(--ac-600, #2563EB); color: #fff; }
    .st-layouts { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .st-lay { border: 2px solid var(--ds-border, #E2E8F0); border-radius: 12px; padding: 8px; cursor: pointer; text-align: center; font-size: 11.5px; font-weight: 600; color: var(--ds-muted, #64748B); }
    .st-lay input { display: none; }
    .st-lay:has(input:checked) { border-color: var(--ac-600, #2563EB); color: var(--ds-text, #0F172A); }
    .st-lay em { display: block; margin-top: 6px; font-style: normal; }
    .st-mini { display: grid; gap: 3px; width: 100%; aspect-ratio: 16 / 10; }
    .st-mini i { background: var(--ds-border, #CBD5E1); border-radius: 3px; }
    .st-mini i:first-child { background: var(--ac-600, #2563EB); }
    .mini-side   { grid-template-columns: 1.4fr 1fr; grid-template-rows: 1fr 1fr; }
    .mini-side i:first-child { grid-row: 1 / 3; }
    .mini-side-r { grid-template-columns: 1fr 1.4fr; grid-template-rows: 1fr 1fr; }
    .mini-side-r i:first-child { grid-column: 2; grid-row: 1 / 3; }
    .mini-side-r i:nth-child(2) { grid-column: 1; grid-row: 1; } .mini-side-r i:nth-child(3) { grid-column: 1; grid-row: 2; }
    .mini-top    { grid-template-columns: 1fr 1fr; grid-template-rows: 1.5fr 1fr; }
    .mini-top i:first-child { grid-column: 1 / 3; }
    .mini-grid   { grid-template-columns: 1fr 1fr 1fr; }
    .st-swatches { display: flex; gap: 10px; flex-wrap: wrap; }
    .st-sw { position: relative; width: 34px; height: 34px; border-radius: 9999px; cursor: pointer; border: 3px solid transparent; box-shadow: 0 0 0 1px var(--ds-border, #CBD5E1); }
    .st-sw input { display: none; }
    .st-sw:has(input:checked) { border-color: var(--ds-card, #fff); box-shadow: 0 0 0 3px var(--ds-text, #0F172A); }
    .st-foot { display: flex; justify-content: space-between; gap: 8px; padding: 12px 18px; border-top: 1px solid var(--ds-border, #E2E8F0); }
    .st-note { color: var(--ds-muted, #64748B); font-size: 11.5px; margin-top: 6px; }

    /* ---------- Layar penuh: 1 kamera per layar, geser untuk kamera lain ---------- */
    .fs-nav { display: none; }
    #cameraCluster.fs-fallback { position: fixed; inset: 0; z-index: 60; }
    #cameraCluster.fs-on {
        background: var(--ds-card, #fff); padding: 12px; box-sizing: border-box;
        width: 100vw; height: 100vh; height: 100dvh;
        border-radius: 0; border: 0; overflow: hidden;
        margin: 0 !important;   /* cegah margin dari space-y-6 induknya */
    }
    #cameraCluster.fs-on .cluster-grid {
        display: flex; flex-direction: row; gap: 12px;
        flex: 1 1 auto; min-height: 0; height: auto;
        overflow-x: auto; overflow-y: hidden;
        scroll-snap-type: x mandatory; scroll-behavior: smooth;
        overscroll-behavior-x: contain; -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    #cameraCluster.fs-on .cluster-grid::-webkit-scrollbar { display: none; }
    #cameraCluster.fs-on .cam-tile {
        flex: 0 0 100%; width: 100%; height: 100%;
        position: relative; top: auto; align-self: stretch;
        scroll-snap-align: start; scroll-snap-stop: always;
        border-radius: 12px;
    }
    #cameraCluster.fs-on .make-main-btn { display: none; }
    #cameraCluster.fs-on .fs-nav {
        display: flex; flex: none; align-items: center; justify-content: center; gap: 14px;
    }
    .fs-nav button.fs-arrow {
        width: 36px; height: 36px; border-radius: 9999px; border: 1px solid var(--ds-border, #CBD5E1);
        background: var(--ds-card, #fff); color: var(--ds-text, #334155); font-weight: 700; cursor: pointer;
    }
    .fs-nav button.fs-arrow:disabled { opacity: .35; cursor: default; }
    .fs-nav-center { display: flex; flex-direction: column; align-items: center; gap: 6px; }
    .fs-dots { display: flex; align-items: center; gap: 8px; }
    .fs-dot { width: 10px; height: 10px; border-radius: 9999px; background: var(--ds-border, #CBD5E1); border: 0; cursor: pointer; padding: 0; transition: all .2s; }
    .fs-dot.active { background: var(--ac-600, #2563EB); width: 24px; }
    </style>

    <div id="cameraCluster" class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-3 sm:p-4">

        <!-- Alert Banner Darurat (muncul otomatis saat kantuk/distraksi terdeteksi) -->
        <div id="alarmBanner" class="hidden p-4 rounded-2xl bg-red-600 text-white shadow-xl alarm-pulsing flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl font-bold">⚠️</div>
                <div>
                    <h3 id="alarmTitle" class="text-lg font-extrabold uppercase tracking-wide font-heading">PERINGATAN BAHAYA</h3>
                    <p id="alarmDesc" class="text-sm text-red-100 font-medium font-sans">Pengemudi terdeteksi mengantuk! Segera buka mata Anda.</p>
                </div>
            </div>
            <span class="px-3 py-1 bg-black/40 rounded-lg text-xs font-mono font-bold">NFR-03 REAL-TIME</span>
        </div>

        <!-- Toolbar klaster -->
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kamera</h2>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openSettings()" title="Pengaturan" aria-label="Pengaturan"
                        class="w-9 h-9 flex items-center justify-center bg-white hover:bg-slate-50 text-slate-700 rounded-xl border border-slate-300 transition cursor-pointer">&#9881;</button>
                <button type="button" id="addCamBtn" onclick="addCamera()"
                        class="px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-xl border border-blue-200 transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                    + Tambah Kamera <span id="addCamCount" class="font-mono font-medium"></span>
                </button>
                <button type="button" id="swapBtn" onclick="swapCameras()"
                        class="px-3 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl border border-slate-300 transition cursor-pointer">
                    &#8646; Tukar Kamera Utama
                </button>
                <button type="button" id="fsBtn" onclick="toggleClusterFullscreen()" aria-pressed="false"
                        class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4"></path></svg>
                    <span id="fsText">Layar Penuh</span>
                </button>
            </div>
        </div>

        <div class="cluster-grid" id="clusterGrid">

            <!-- ===== CAM 1: Kamera Pengemudi (utama di awal) ===== -->
            <section id="tileDriver" class="cam-tile is-main" data-slot="main" data-title="Kamera Pengemudi" aria-label="Kamera Pengemudi">
                <div class="tile-head bg-slate-50 px-4 py-2.5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                        <span id="streamIndicator" class="w-2 h-2 rounded-full bg-slate-400"></span> Kamera Pengemudi
                    </span>
                    <span class="flex items-center gap-2">
                        <span id="modelBadge" class="text-[11px] px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-mono font-medium">Model: Memuat...</span>
                        <button type="button" onclick="makeMain(this.closest('.cam-tile'))" class="make-main-btn text-[11px] font-semibold text-blue-700 hover:underline cursor-pointer">Jadikan utama</button>
                    </span>
                </div>

                <div class="tile-view">
                    <video id="webcamVideo" autoplay playsinline muted class="hidden"></video>
                    <canvas id="captureCanvas" width="640" height="480" class="hidden"></canvas>
                    <canvas id="displayCanvas" width="640" height="480" class="cam-canvas hidden"></canvas>

                    <!-- Placeholder standby sebelum izin diberikan / saat kamera mati -->
                    <div id="standbyPlaceholder" class="p-8 text-center flex flex-col items-center justify-center space-y-4">
                        <div class="w-20 h-20 rounded-full bg-slate-800 text-slate-300 flex items-center justify-center text-4xl shadow-inner border border-slate-700">📹</div>
                        <div>
                            <h3 class="text-lg font-bold text-white font-heading">Kamera Sedang Nonaktif</h3>
                            <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                                Klik tombol biru <strong>"Nyalakan Kamera"</strong> untuk memulai streaming dan deteksi AI secara real-time.
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center justify-center gap-3">
                            <button onclick="requestAndStartCamera()"
                                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                                <span>📹</span> Nyalakan Kamera &amp; Izinkan Akses
                            </button>
                        </div>
                    </div>

                    <!-- Petunjuk jika izin ditolak browser -->
                    <div id="permissionDeniedNotice" class="hidden p-6 text-center bg-red-950/80 border border-red-800 rounded-2xl m-4 max-w-md">
                        <span class="text-3xl block mb-2">🚫</span>
                        <h4 class="text-base font-bold text-red-300">Akses Kamera Diblokir Browser</h4>
                        <p class="text-xs text-slate-300 mt-2 leading-relaxed">Browser Anda memblokir akses ke webcam. Untuk mengizinkan:</p>
                        <ol class="text-xs text-slate-400 text-left mt-2 space-y-1 list-decimal list-inside bg-slate-900/60 p-3 rounded-lg">
                            <li>Klik ikon <strong>Gembok 🔒</strong> di samping URL browser.</li>
                            <li>Ubah izin Kamera menjadi <strong>Izinkan (Allow)</strong>.</li>
                            <li>Klik tombol coba izin lagi di bawah.</li>
                        </ol>
                        <div class="mt-4">
                            <button onclick="requestAndStartCamera()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow transition">
                                🔄 Coba Minta Izin Ulang
                            </button>
                        </div>
                    </div>

                    <!-- Overlay status & arah pandangan (kini berada DI DALAM tile-view) -->
                    <div id="driverOverlay" class="hidden absolute left-2 top-2 z-10 flex flex-col items-start gap-1 pointer-events-none">
                        <span id="ovStatus" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-700 text-white">STANDBY</span>
                        <span id="ovPose" class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-black/70 text-white">Arah: -</span>
                    </div>
                </div>

                <div class="tile-foot bg-slate-50 px-4 py-2 border-t border-slate-200 text-xs text-slate-500">
                    <span id="fpsCounter" class="font-mono">FPS: 0 &middot; Resolusi: 640x480</span>
                </div>
            </section>

            <!-- ===== CAM 2: Kamera Objek (jalan) ===== -->
            <section id="tileObject" class="cam-tile is-thumb" data-slot="s2" data-title="Kamera Objek (Jalan)" aria-label="Kamera Objek">
                <div class="tile-head bg-slate-50 px-4 py-2.5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kamera Objek (Jalan)</span>
                    <button type="button" onclick="makeMain(this.closest('.cam-tile'))" class="make-main-btn text-[11px] font-semibold text-blue-700 hover:underline cursor-pointer">Jadikan utama</button>
                </div>
                <div class="tile-controls px-3 py-2 border-b border-slate-200 flex flex-wrap items-center gap-2">
                    <label for="objCamSelect" class="sr-only">Pilih kamera objek</label>
                    <select id="objCamSelect" class="min-w-0 flex-1 bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-2.5 py-1.5 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100 transition">
                        <option value="">Mencari kamera...</option>
                    </select>
                    <button id="objToggleBtn" type="button" onclick="toggleObjectCamera()"
                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer">
                        <span id="objToggleText">Nyalakan Kamera Objek</span>
                    </button>
                </div>
                <div id="objAlert" class="hidden flex-none px-3 py-2 bg-red-600 text-white text-xs font-bold flex items-center gap-2">
                    <span>⚠️</span><span id="objAlertText">Objek terlalu dekat!</span>
                </div>
                <div class="tile-view">
                    <video id="objVideo" autoplay playsinline muted class="hidden"></video>
                    <canvas id="objCanvas" width="640" height="480" class="cam-canvas hidden"></canvas>
                    <div id="objStandby" class="p-6 text-center space-y-2">
                        <div class="text-3xl">🚗</div>
                        <h3 class="text-sm font-bold text-white font-heading">Kamera Objek Nonaktif</h3>
                        <p class="text-xs text-slate-400 max-w-md mx-auto">Arahkan kamera ke jalan di depan, lalu klik <strong>Nyalakan Kamera Objek</strong>. Disarankan memakai kamera kedua (USB / kamera ponsel).</p>
                    </div>
                </div>
                <div class="tile-foot bg-slate-50 px-3 py-2 border-t border-slate-200 text-xs text-slate-500 flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                    <span id="objStatus">Kamera objek belum aktif</span>
                    <span class="flex items-center gap-2.5">
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>Jauh</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>Sedang</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>Dekat</span>
                    </span>
                    <span id="objSummary" class="font-mono text-slate-600">-</span>
                </div>
            </section>

            <!-- ===== CAM 3 (slot ketiga): Panel Status & Metrik Deteksi ===== -->
            <section id="tileDetail" class="cam-tile is-thumb" data-slot="s3" data-title="Status &amp; Metrik Deteksi" aria-label="Detail deteksi">
                <div class="tile-head bg-slate-50 px-4 py-2.5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Status &amp; Metrik Deteksi</span>
                    <button type="button" onclick="makeMain(this.closest('.cam-tile'))" class="make-main-btn text-[11px] font-semibold text-blue-700 hover:underline cursor-pointer">Jadikan utama</button>
                </div>
                <div class="detail-body space-y-4">
                    <!-- Kartu Status Utama -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Status Kewaspadaan Saat Ini</h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <div id="statusText" class="text-2xl font-extrabold text-slate-800 font-heading">STANDBY</div>
                                <p id="statusSub" class="text-xs text-slate-500 mt-0.5">Menunggu kamera aktif</p>
                            </div>
                            <div id="statusIcon" class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-3xl">👤</div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-slate-600 font-medium">Skor Tingkat Kantuk:</span>
                                <span id="drowsyScoreText" class="font-bold text-slate-800">0%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div id="drowsyScoreBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-slate-600 font-medium">Indikator Menoleh (Distraksi):</span>
                                <span id="distractScoreText" class="font-bold text-slate-800">0%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div id="distractScoreBar" class="bg-purple-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Metrik Geometris Wajah -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-4">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Metrik Landmark MediaPipe</h3>
                        <div>
                            <div class="flex justify-between items-center text-xs mb-1">
                                <span class="text-slate-700 font-medium">Eye Aspect Ratio (EAR)</span>
                                <span id="earVal" class="font-mono font-bold text-blue-600">0.000</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div id="earBar" class="bg-blue-600 h-2 rounded-full transition-all duration-150" style="width: 0%"></div>
                            </div>
                            <span class="text-[10px] text-slate-400">Ambang Batas Tertutup: &lt; 0.220</span>
                        </div>
                        <div>
                            <div class="flex justify-between items-center text-xs mb-1">
                                <span class="text-slate-700 font-medium">Mouth Aspect Ratio (MAR)</span>
                                <span id="marVal" class="font-mono font-bold text-amber-600">0.000</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div id="marBar" class="bg-amber-500 h-2 rounded-full transition-all duration-150" style="width: 0%"></div>
                            </div>
                            <span class="text-[10px] text-slate-400">Ambang Batas Menguap: &gt; 0.650</span>
                        </div>
                        <div class="pt-2 border-t border-slate-100">
                            <div class="flex justify-between items-center text-xs mb-1">
                                <span class="text-slate-700 font-medium">Arah Pandangan (Pose)</span>
                                <span id="yawVal" class="font-mono font-bold text-purple-600">0.0° (Tengah)</span>
                            </div>
                            <div class="flex justify-between items-center text-[10px] text-slate-500 mb-1">
                                <span>Sudut Vertikal (Pitch):</span>
                                <span id="pitchVal" class="font-mono text-slate-700 font-medium">0.0°</span>
                            </div>
                            <p id="poseNotice" class="text-[10px] text-slate-500">Menghadap lurus ke depan</p>
                        </div>
                    </div>
                </div>
            </section>
        </div><!-- /cluster-grid -->

        <!-- Kilatan layar (peringatan) -->
        <div id="alertFlash" aria-hidden="true"></div>

        <!-- ===== PANEL PENGATURAN (di dalam klaster agar tetap bisa dibuka saat layar penuh) ===== -->
        <div id="settingsModal" class="st-overlay" role="presentation">
            <div class="st-backdrop" onclick="closeSettings()"></div>
            <aside class="st-panel" role="dialog" aria-modal="true" aria-labelledby="stTitle">
                <div class="st-head">
                    <h2 id="stTitle">Pengaturan</h2>
                    <button type="button" class="st-x" onclick="closeSettings()" aria-label="Tutup">&#10005;</button>
                </div>
                <div class="st-tabs" role="tablist">
                    <button type="button" class="st-tab active" data-tab="sound" onclick="stTab('sound')">Suara</button>
                    <button type="button" class="st-tab" data-tab="alert" onclick="stTab('alert')">Peringatan</button>
                    <button type="button" class="st-tab" data-tab="camera" onclick="stTab('camera')">Kamera</button>
                    <button type="button" class="st-tab" data-tab="look" onclick="stTab('look')">Tampilan</button>
                </div>

                <div class="st-body">
                    <!-- ===== SUARA ===== -->
                    <section class="st-pane active" data-pane="sound">
                        <div class="st-h">Alarm</div>
                        <label class="st-row"><span><b>Suara alarm aktif</b><small>Sama dengan tombol &ldquo;Audio Alarm&rdquo;.</small></span>
                            <input type="checkbox" class="st-switch" data-k="sound.enabled"></label>

                        <div class="st-h">Jenis suara</div>
                        <select class="st-field" data-k="sound.type" aria-label="Jenis suara alarm">
                            <option value="siren">Sirine (naik-turun)</option>
                            <option value="beep">Bip ganda</option>
                            <option value="chime">Lonceng lembut</option>
                            <option value="buzzer">Buzzer</option>
                            <option value="horn">Klakson</option>
                            <option value="custom">Suara kustom (unggah sendiri)</option>
                        </select>
                        <div id="stCustomWrap" class="st-inline" style="display:none">
                            <input type="file" id="stCustomFile" accept="audio/*" onchange="onCustomSoundFile(this)" class="text-xs">
                            <span id="stCustomName" class="st-note"></span>
                        </div>

                        <div class="st-h">Volume</div>
                        <input type="range" class="st-range" min="0" max="100" step="1" data-k="sound.volume" data-scale="100" aria-label="Volume alarm">
                        <div class="st-inline">
                            <button type="button" class="st-btn" onclick="previewSound()">&#9654; Uji suara</button>
                            <span id="stVolText" class="st-note"></span>
                        </div>
                    </section>

                    <!-- ===== PERINGATAN ===== -->
                    <section class="st-pane" data-pane="alert">
                        <div class="st-h">Jenis peringatan (bisa lebih dari satu)</div>
                        <label class="st-row"><span><b>Banner merah</b><small>Pesan besar di atas kamera.</small></span>
                            <input type="checkbox" class="st-switch" data-k="alerts.banner"></label>
                        <label class="st-row"><span><b>Kilatan layar</b><small>Bingkai merah berkedip di seluruh layar.</small></span>
                            <input type="checkbox" class="st-switch" data-k="alerts.flash"></label>
                        <label class="st-row"><span><b>Suara ucapan</b><small>Membacakan pesan peringatan (Bahasa Indonesia).</small></span>
                            <input type="checkbox" class="st-switch" data-k="alerts.voice"></label>
                        <label class="st-row"><span><b>Getar</b><small>Hanya di perangkat yang mendukung (mis. ponsel Android).</small></span>
                            <input type="checkbox" class="st-switch" data-k="alerts.vibrate"></label>

                        <div class="st-h">Peringatkan saat</div>
                        <label class="st-row"><span><b>Mengantuk</b><small>Mata tertutup terlalu lama.</small></span>
                            <input type="checkbox" class="st-switch" data-k="events.KANTUK"></label>
                        <label class="st-row"><span><b>Tidak fokus</b><small>Menoleh / menunduk terlalu lama.</small></span>
                            <input type="checkbox" class="st-switch" data-k="events.TIDAK_FOKUS"></label>
                        <label class="st-row"><span><b>Menguap</b><small>Menguap berulang kali.</small></span>
                            <input type="checkbox" class="st-switch" data-k="events.MENGUAP"></label>
                        <label class="st-row"><span><b>Wajah tidak terdeteksi</b></span>
                            <input type="checkbox" class="st-switch" data-k="events.WAJAH_HILANG"></label>
                        <label class="st-row"><span><b>Objek terlalu dekat</b><small>Dari kamera objek (jalan).</small></span>
                            <input type="checkbox" class="st-switch" data-k="events.OBJEK_DEKAT"></label>

                        <div class="st-inline"><button type="button" class="st-btn primary" onclick="testAlert()">Tes peringatan</button></div>
                        <p class="st-note">Tes memakai pengaturan di atas selama 3 detik.</p>
                    </section>

                    <!-- ===== KAMERA ===== -->
                    <section class="st-pane" data-pane="camera">
                        <div class="st-h">Tata letak</div>
                        <div class="st-layouts">
                            <label class="st-lay"><input type="radio" name="stLayout" value="side" data-k="camera.layout"><span class="st-mini mini-side"><i></i><i></i><i></i></span><em>Utama di kiri</em></label>
                            <label class="st-lay"><input type="radio" name="stLayout" value="side-right" data-k="camera.layout"><span class="st-mini mini-side-r"><i></i><i></i><i></i></span><em>Utama di kanan</em></label>
                            <label class="st-lay"><input type="radio" name="stLayout" value="top" data-k="camera.layout"><span class="st-mini mini-top"><i></i><i></i><i></i></span><em>Utama di atas</em></label>
                            <label class="st-lay"><input type="radio" name="stLayout" value="grid" data-k="camera.layout"><span class="st-mini mini-grid"><i></i><i></i><i></i></span><em>Sama besar</em></label>
                        </div>
                        <p class="st-note">Di layar penuh selalu 1 kamera per layar (geser untuk kamera lain).</p>

                        <div class="st-h">Kamera utama saat halaman dibuka</div>
                        <select class="st-field" data-k="camera.mainCam" aria-label="Kamera utama">
                            <option value="driver">Kamera Pengemudi</option>
                            <option value="object">Kamera Objek (Jalan)</option>
                            <option value="detail">Status &amp; Metrik Deteksi</option>
                        </select>

                        <div class="st-h">Tampilan video</div>
                        <div class="st-seg">
                            <label><input type="radio" name="stFit" value="contain" data-k="camera.fit">Muat utuh</label>
                            <label><input type="radio" name="stFit" value="cover" data-k="camera.fit">Isi penuh (potong tepi)</label>
                        </div>
                        <label class="st-row"><span><b>Cerminkan kamera pengemudi</b><small>Seperti cermin: kiri-kanan dibalik.</small></span>
                            <input type="checkbox" class="st-switch" data-k="camera.mirror"></label>
                        <label class="st-row"><span><b>Overlay status di atas video</b><small>Label status dan arah pandangan.</small></span>
                            <input type="checkbox" class="st-switch" data-k="camera.overlay"></label>
                        <label class="st-row"><span><b>Tampilkan panel Status &amp; Metrik</b><small>Matikan agar hanya kamera yang tampil.</small></span>
                            <input type="checkbox" class="st-switch" data-k="camera.showDetail"></label>
                    </section>

                    <!-- ===== TAMPILAN ===== -->
                    <section class="st-pane" data-pane="look">
                        <div class="st-h">Tema</div>
                        <div class="st-seg">
                            <label><input type="radio" name="stMode" value="light" data-k="theme.mode">Terang</label>
                            <label><input type="radio" name="stMode" value="dark" data-k="theme.mode">Gelap</label>
                            <label><input type="radio" name="stMode" value="auto" data-k="theme.mode">Otomatis</label>
                        </div>
                        <p class="st-note">&ldquo;Otomatis&rdquo; mengikuti pengaturan perangkat Anda. Berlaku di semua halaman pengemudi.</p>

                        <p id="stThemeNote" class="st-note" style="display:none;color:#B45309">Mode cadangan: tema hanya berlaku di halaman ini. Ganti juga <b>layouts/driver.blade.php</b> agar berlaku di semua halaman.</p>
                        <div class="st-h">Warna aksen</div>
                        <div class="st-swatches">
                            <label class="st-sw" style="background:#2563EB" title="Biru"><input type="radio" name="stAccent" value="blue" data-k="theme.accent"></label>
                            <label class="st-sw" style="background:#059669" title="Hijau"><input type="radio" name="stAccent" value="emerald" data-k="theme.accent"></label>
                            <label class="st-sw" style="background:#7C3AED" title="Ungu"><input type="radio" name="stAccent" value="violet" data-k="theme.accent"></label>
                            <label class="st-sw" style="background:#E11D48" title="Merah muda"><input type="radio" name="stAccent" value="rose" data-k="theme.accent"></label>
                            <label class="st-sw" style="background:#EA580C" title="Oranye"><input type="radio" name="stAccent" value="orange" data-k="theme.accent"></label>
                        </div>
                    </section>
                </div>

                <div class="st-foot">
                    <button type="button" class="st-btn danger" onclick="resetSettings()">Kembalikan default</button>
                    <button type="button" class="st-btn primary" onclick="closeSettings()">Selesai</button>
                </div>
            </aside>
        </div>

        <!-- Navigasi geser (hanya tampil di layar penuh) -->
        <div id="fsNav" class="fs-nav" aria-label="Navigasi kamera layar penuh">
            <button type="button" class="fs-arrow" id="fsPrev" onclick="fsStep(-1)" aria-label="Kamera sebelumnya">&#8592;</button>
            <div class="fs-nav-center">
                <div id="fsDots" class="fs-dots"></div>
                <span id="fsLabel" class="text-[11px] font-semibold text-slate-600"></span>
            </div>
            <button type="button" class="fs-arrow" id="fsNext" onclick="fsStep(1)" aria-label="Kamera berikutnya">&#8594;</button>
        </div>
    </div><!-- /cameraCluster -->
    </div><!-- /cameraSection -->
    <!-- ====== AKHIR BAGIAN KAMERA ====== -->

    <!-- Cara Penggunaan -->
    <section class="bg-slate-100 rounded-2xl px-6 py-8">
        <h2 class="text-xl font-heading font-bold text-slate-900 text-center">Cara Penggunaan</h2>
        <div class="text-center mt-3"><button type="button" onclick="openTutorial()" class="px-4 py-2 bg-white hover:bg-slate-50 text-blue-700 text-xs font-semibold rounded-xl border border-blue-200 transition cursor-pointer">Lihat Tutorial Bergambar</button></div>
        <ol class="mt-4 max-w-xl mx-auto space-y-2 text-sm text-slate-700 list-decimal list-inside">
            <li>Klik <strong>Akses Kamera</strong>, lalu pilih <strong>Izinkan</strong> pada pop-up browser.</li>
            <li>Posisikan wajah di tengah layar dengan pencahayaan yang cukup.</li>
            <li>Lihat ke depan seperti saat berkendara. Sistem memantau mata dan arah kepala Anda.</li>
            <li>Jika terdeteksi mengantuk atau tidak fokus, alarm suara dan banner merah akan muncul.</li>
            <li>Riwayat peringatan dapat dilihat di menu <strong>Riwayat</strong>.</li>
        </ol>
    </section>

    <!-- Tentang Kami -->
    <section class="bg-white rounded-2xl border border-slate-200/90 px-6 py-8 text-center">
        <h2 class="text-xl font-heading font-bold text-slate-900">Tentang Kami</h2>
        <p class="mt-3 max-w-xl mx-auto text-sm text-slate-600">
            DriveSafe adalah Driver Monitoring System berbasis AI yang memantau kondisi pengemudi secara real-time
            melalui kamera. Proyek PBL IF3-PD15, Teknik Informatika, Politeknik Negeri Batam.
        </p>
    </section>
</div>

    <!-- Tutorial Modal: overlay layar penuh -->
<div
    id="tutorialModal"
    class="hidden fixed inset-0 z-[99999] items-center justify-center bg-black/60 p-4"
    style="position: fixed; inset: 0; width: 100%; height: 100%;"
    role="dialog"
    aria-modal="true"
    aria-label="Tutorial penggunaan DriveSafe"
>
    <!-- Kotak tutorial -->
    <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">

        <!-- Area slide -->
        <div class="overflow-hidden">
            <div id="tutorialTrack" class="flex transition-transform duration-300 ease-out">

    <!-- SLIDE 1 -->
    <div class="min-w-full px-5">
        <div class="rounded-xl overflow-hidden border border-slate-200 aspect-video bg-blue-50">
            <img
                src="{{ asset('images/tutorial/camera-permission.png') }}"
                alt="Cara memberikan izin kamera"
                class="w-full h-full object-cover"
            >
        </div>

        <h3 class="mt-4 text-lg font-heading font-bold text-slate-900">
            1. Izinkan Akses Kamera
        </h3>

        <p class="mt-1 text-sm text-slate-600">
            Klik tombol Akses Kamera, lalu pilih "Izinkan" ketika browser meminta akses ke kamera.
        </p>
    </div>


    <!-- SLIDE 2 -->
    <div class="min-w-full px-5">
        <div class="rounded-xl overflow-hidden border border-slate-200 aspect-video bg-blue-50">
            <img
                src="{{ asset('images/tutorial/position-face.png') }}"
                alt="Posisikan wajah di tengah kamera"
                class="w-full h-full object-cover"
            >
        </div>

        <h3 class="mt-4 text-lg font-heading font-bold text-slate-900">
            2. Posisikan Wajah
        </h3>

        <p class="mt-1 text-sm text-slate-600">
            Duduk menghadap kamera dengan wajah di tengah layar dan pencahayaan yang cukup.
            Hindari cahaya kuat dari belakang.
        </p>
    </div>


    <!-- SLIDE 3 -->
    <div class="min-w-full px-5">
        <div class="rounded-xl overflow-hidden border border-slate-200 aspect-video bg-blue-50">
            <img
                src="{{ asset('images/tutorial/look-forward.png') }}"
                alt="Pengemudi melihat ke depan"
                class="w-full h-full object-cover"
            >
        </div>

        <h3 class="mt-4 text-lg font-heading font-bold text-slate-900">
            3. Lihat ke Depan
        </h3>

        <p class="mt-1 text-sm text-slate-600">
            Pandang ke depan seperti saat berkendara. Sistem memantau mata, mulut,
            dan arah kepala Anda secara real-time.
        </p>
    </div>


    <!-- SLIDE 4 -->
    <div class="min-w-full px-5">
        <div class="rounded-xl overflow-hidden border border-slate-200 aspect-video bg-blue-50">
            <img
                src="{{ asset('images/tutorial/warning.png') }}"
                alt="Peringatan ketika pengemudi mengantuk"
                class="w-full h-full object-cover"
            >
        </div>

        <h3 class="mt-4 text-lg font-heading font-bold text-slate-900">
            4. Peringatan Muncul
        </h3>

        <p class="mt-1 text-sm text-slate-600">
            Jika terdeteksi mengantuk atau tidak fokus, banner merah dan alarm suara
            akan muncul. Riwayatnya tersimpan di menu Riwayat.
        </p>
    </div>


    <!-- SLIDE 5 -->
    <div class="min-w-full px-5">
        <div class="rounded-xl overflow-hidden border border-slate-200 aspect-video bg-blue-50">
            <img
                src="{{ asset('images/tutorial/object-detection.png') }}"
                alt="Kamera mendeteksi objek di jalan"
                class="w-full h-full object-cover"
            >
        </div>

        <h3 class="mt-4 text-lg font-heading font-bold text-slate-900">
            5. Kamera Deteksi Objek
        </h3>

        <p class="mt-1 text-sm text-slate-600">
            Nyalakan kamera kedua yang menghadap jalan. Objek di depan diberi label
            Jauh, Sedang, atau Dekat. Objek yang terlalu dekat memicu peringatan.
        </p>
    </div>

</div>
            <div class="px-5 pb-5 pt-4">
                <div class="flex justify-center gap-2 mb-4"><button type="button" onclick="tutorialGo(0)" aria-label="Slide 1" class="tut-dot w-2.5 h-2.5 rounded-full bg-slate-300 cursor-pointer"></button><button type="button" onclick="tutorialGo(1)" aria-label="Slide 2" class="tut-dot w-2.5 h-2.5 rounded-full bg-slate-300 cursor-pointer"></button><button type="button" onclick="tutorialGo(2)" aria-label="Slide 3" class="tut-dot w-2.5 h-2.5 rounded-full bg-slate-300 cursor-pointer"></button><button type="button" onclick="tutorialGo(3)" aria-label="Slide 4" class="tut-dot w-2.5 h-2.5 rounded-full bg-slate-300 cursor-pointer"></button><button type="button" onclick="tutorialGo(4)" aria-label="Slide 5" class="tut-dot w-2.5 h-2.5 rounded-full bg-slate-300 cursor-pointer"></button></div>
                <div class="flex items-center justify-between">
                    <button id="tutPrev" type="button" onclick="tutorialPrev()" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 disabled:opacity-40 cursor-pointer">Sebelumnya</button>
                    <button type="button" onclick="closeTutorial()" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 cursor-pointer">Lewati</button>
                    <button id="tutNext" type="button" onclick="tutorialNext()" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 cursor-pointer">Berikutnya</button>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Inisialisasi State Kamera dan Audio
    let currentStream = null;
    let isProcessing = false;
    let frameInterval = null;
    // ===== Cadangan fungsi tema (jika layout belum diperbarui) =====
    window.__dsThemeFallback = false;
    if (!window.dsApplyTheme) {
        window.__dsThemeFallback = true;
        window.dsReadTheme = function () {
            try { var t = JSON.parse(localStorage.getItem('dsTheme') || '{}'); return { mode: t.mode || 'light', accent: t.accent || 'blue' }; }
            catch (e) { return { mode: 'light', accent: 'blue' }; }
        };
        window.dsApplyTheme = function (mode, accent, save) {
            var t = window.dsReadTheme(); mode = mode || t.mode; accent = accent || t.accent;
            var dark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.setAttribute('data-theme', mode === 'auto' ? (dark ? 'dark' : 'light') : mode);
            document.documentElement.setAttribute('data-accent', accent);
            if (save) { try { localStorage.setItem('dsTheme', JSON.stringify({ mode: mode, accent: accent })); } catch (e) {} }
        };
        window.dsApplyTheme();
    }

    let audioCtx = null;
    let isAudioEnabled = true;
    let isBeeping = false;
    let fpsFrames = 0;
    let lastFpsTime = Date.now();

    const videoEl = document.getElementById('webcamVideo');
    const captureCanvas = document.getElementById('captureCanvas');
    const captureCtx = captureCanvas.getContext('2d');
    const displayCanvas = document.getElementById('displayCanvas');
    const displayCtx = displayCanvas.getContext('2d');

    const camSelect = document.getElementById('cameraSelect');
    const toggleCamBtn = document.getElementById('toggleCamBtn');
    const toggleCamIcon = document.getElementById('toggleCamIcon');
    const toggleCamText = document.getElementById('toggleCamText');
    const requestPermBtn = document.getElementById('requestPermBtn');
    const camStatusBadge = document.getElementById('camStatusBadge');
    const camStatusIcon = document.getElementById('camStatusIcon');
    const camDetailText = document.getElementById('camDetailText');
    const standbyPlaceholder = document.getElementById('standbyPlaceholder');
    const permissionDeniedNotice = document.getElementById('permissionDeniedNotice');
    const livePing = document.getElementById('livePing');
    const liveDot = document.getElementById('liveDot');
    const streamIndicator = document.getElementById('streamIndicator');
    const fpsCounter = document.getElementById('fpsCounter');

    // Telemetri Elemen
    const alarmBanner = document.getElementById('alarmBanner');
    const alarmTitle = document.getElementById('alarmTitle');
    const alarmDesc = document.getElementById('alarmDesc');
    const statusText = document.getElementById('statusText');
    const statusSub = document.getElementById('statusSub');
    const statusIcon = document.getElementById('statusIcon');
    const modelBadge = document.getElementById('modelBadge');
    const earVal = document.getElementById('earVal');
    const earBar = document.getElementById('earBar');
    const marVal = document.getElementById('marVal');
    const marBar = document.getElementById('marBar');
    const yawVal = document.getElementById('yawVal');
    const pitchVal = document.getElementById('pitchVal');
    const poseNotice = document.getElementById('poseNotice');
    const drowsyScoreText = document.getElementById('drowsyScoreText');
    const drowsyScoreBar = document.getElementById('drowsyScoreBar');
    const distractScoreText = document.getElementById('distractScoreText');
    const distractScoreBar = document.getElementById('distractScoreBar');

    let consecNoFaceFrames = 0;

    // Web Audio Synthesizer
    async function initAudioContext() {
        if (!audioCtx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            audioCtx = new AudioContext();
        }
        if (audioCtx.state === 'suspended') {
            try {
                await audioCtx.resume();
            } catch (e) {
                console.warn("AudioContext resume pending user interaction:", e);
            }
        }
    }

    // ===== Preset suara alarm (disintesis dengan Web Audio, tanpa file) =====
    function tone(c, t, freq, dur, type, peak) {
        const o = c.createOscillator(), g = c.createGain();
        o.type = type; o.frequency.setValueAtTime(freq, t);
        g.gain.setValueAtTime(Math.max(peak, 0.0002), t);
        g.gain.exponentialRampToValueAtTime(0.0002, t + dur);
        o.connect(g); g.connect(c.destination); o.start(t); o.stop(t + dur + 0.02);
    }
    const SOUND_PRESETS = {
        siren:  { ms: 380, play(c, t, v) {            // sirine naik-turun (default lama)
            const o = c.createOscillator(), g = c.createGain();
            o.type = 'sawtooth';
            o.frequency.setValueAtTime(920, t); o.frequency.linearRampToValueAtTime(680, t + 0.15); o.frequency.linearRampToValueAtTime(920, t + 0.3);
            g.gain.setValueAtTime(0.4 * v, t); g.gain.exponentialRampToValueAtTime(0.01, t + 0.35);
            o.connect(g); g.connect(c.destination); o.start(t); o.stop(t + 0.36); } },
        beep:   { ms: 460, play(c, t, v) { tone(c, t, 1000, 0.14, 'square', 0.28 * v); tone(c, t + 0.22, 1000, 0.14, 'square', 0.28 * v); } },
        chime:  { ms: 1000, play(c, t, v) { tone(c, t, 880, 0.6, 'sine', 0.5 * v); tone(c, t + 0.3, 1320, 0.7, 'sine', 0.45 * v); } },
        buzzer: { ms: 520, play(c, t, v) { tone(c, t, 180, 0.45, 'sawtooth', 0.45 * v); tone(c, t, 190, 0.45, 'square', 0.2 * v); } },
        horn:   { ms: 700, play(c, t, v) { tone(c, t, 420, 0.55, 'square', 0.22 * v); tone(c, t, 530, 0.55, 'square', 0.22 * v); } }
    };
    let customAudio = null;     // suara kustom (diunggah di Pengaturan)

    function playAlarmSound(force) {
        if (!isAudioEnabled && !force) return;
        const vol = (typeof cfg !== 'undefined') ? cfg.sound.volume : 0.7;
        if (vol <= 0.001) return;
        const type = (typeof cfg !== 'undefined') ? cfg.sound.type : 'siren';

        if (type === 'custom' && customAudio) {
            if (!customAudio.paused && !customAudio.ended) return;      // jangan tumpang tindih
            customAudio.currentTime = 0;
            customAudio.volume = Math.min(1, vol);
            customAudio.play().catch(() => {});
            return;
        }
        initAudioContext();
        if (isBeeping) return;
        isBeeping = true;
        const preset = SOUND_PRESETS[type] || SOUND_PRESETS.siren;
        try {
            preset.play(audioCtx, audioCtx.currentTime, Math.min(1, vol));
            setTimeout(() => { isBeeping = false; }, preset.ms);
        } catch (e) {
            console.error("Audio error:", e);
            isBeeping = false;
        }
    }

    function stopBuzzer() {
        isBeeping = false;
    }

    const AUDIO_BTN_ON  = "px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl border border-slate-300 shadow-2xs flex items-center gap-2 transition cursor-pointer";
    const AUDIO_BTN_OFF = "px-3.5 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold rounded-xl border border-red-200 shadow-2xs flex items-center gap-2 transition cursor-pointer";

    function setAudioUi() {
        const text = document.getElementById('audioText');
        const btn = document.getElementById('toggleAudioBtn');
        if (text) text.innerText = 'Audio Alarm: ' + (isAudioEnabled ? 'AKTIF' : 'SENYAP');
        if (btn) btn.className = isAudioEnabled ? AUDIO_BTN_ON : AUDIO_BTN_OFF;
    }

    function testAlarm() {
        initAudioContext();
        isAudioEnabled = true;
        if (typeof cfg !== 'undefined') { cfg.sound.enabled = true; saveCfg(); syncSettingsUi(); }
        setAudioUi();
        playAlarmSound();
    }

    function toggleAudio() {
        isAudioEnabled = !isAudioEnabled;
        if (isAudioEnabled) initAudioContext();
        if (typeof cfg !== 'undefined') { cfg.sound.enabled = isAudioEnabled; saveCfg(); syncSettingsUi(); }
        setAudioUi();
    }

    // Enumerasi dan Pindai Daftar Perangkat Kamera
    async function enumerateCameras() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
            camDetailText.innerText = "Browser ini tidak mendukung navigator.mediaDevices API.";
            return;
        }

        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(d => d.kind === 'videoinput');

            camSelect.innerHTML = '';
            if (videoDevices.length === 0) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.innerText = 'Tidak ada webcam terdeteksi';
                camSelect.appendChild(opt);
            } else {
                videoDevices.forEach((dev, idx) => {
                    const opt = document.createElement('option');
                    opt.value = dev.deviceId;
                    opt.innerText = dev.label || `Kamera ${idx + 1}`;
                    camSelect.appendChild(opt);
                });
            }
        } catch (err) {
            console.error("Gagal mendata kamera:", err);
        }
    }

    // Meminta Izin dan Menyalakan Kamera (HTML5 getUserMedia)
    async function requestAndStartCamera(deviceId = null) {
        initAudioContext();
        permissionDeniedNotice.classList.add('hidden');

        camStatusBadge.className = "text-xs px-2.5 py-0.5 rounded-full font-semibold bg-amber-900/60 text-amber-300 border border-amber-700";
        camStatusBadge.innerText = "Meminta Izin Browser...";
        camDetailText.innerText = "Silakan klik 'Allow / Izinkan' pada pop-up browser.";

        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }

        const constraints = {
            video: {
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: 'user'
            },
            audio: false
        };

        if (deviceId) {
            constraints.video.deviceId = { exact: deviceId };
        }

        try {
            // Minta Izin Kamera ke Browser
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            currentStream = stream;
            videoEl.srcObject = stream;

            await new Promise((resolve) => {
                videoEl.onloadedmetadata = () => {
                    videoEl.play();
                    resolve();
                };
            });

            // Update UI status sukses
            camStatusBadge.className = "text-xs px-2.5 py-0.5 rounded-full font-semibold bg-emerald-900/60 text-emerald-300 border border-emerald-700";
            camStatusBadge.innerText = "Kamera Aktif (On)";
            camStatusIcon.className = "w-10 h-10 rounded-xl bg-emerald-600/30 text-emerald-400 flex items-center justify-center text-xl";
            camDetailText.innerText = "Webcam terhubung dan mengirim frame ke AI detector.";

            // Ubah tombol toggle menjadi merah (Matikan Kamera)
            toggleCamBtn.className = "px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-rose-600/20 transition flex items-center gap-2";
            toggleCamIcon.innerText = "⏹️";
            toggleCamText.innerText = "Matikan Kamera";

            standbyPlaceholder.classList.add('hidden');
            displayCanvas.classList.remove('hidden');
            livePing.classList.remove('hidden');
            liveDot.className = "relative inline-flex rounded-full h-3 w-3 bg-emerald-500";
            streamIndicator.className = "w-2 h-2 rounded-full bg-emerald-500 animate-pulse";

            // Update daftar kamera setelah izin diperoleh (nama perangkat akan muncul)
            await enumerateCameras();

            // Mulai loop inferensi frame
            if (frameInterval) clearInterval(frameInterval);
            frameInterval = setInterval(sendFrameToAI, 80); // ~12-15 FPS

        } catch (err) {
            console.error("Gagal mendapatkan izin webcam:", err);
            camStatusBadge.className = "text-xs px-2.5 py-0.5 rounded-full font-semibold bg-red-900/60 text-red-300 border border-red-700";
            camStatusBadge.innerText = "Izin Ditolak / Kamera Sibuk";
            camStatusIcon.className = "w-10 h-10 rounded-xl bg-red-600/30 text-red-400 flex items-center justify-center text-xl";
            camDetailText.innerText = "Browser tidak diizinkan membuka kamera atau webcam sedang dipakai aplikasi lain.";
            standbyPlaceholder.classList.add('hidden');
            permissionDeniedNotice.classList.remove('hidden');

            toggleCamBtn.className = "px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center gap-2";
            toggleCamIcon.innerText = "📹";
            toggleCamText.innerText = "Coba Nyalakan Lagi";
        }
    }

    // Mematikan Kamera Secara Total (OFF)
    function stopCamera() {
        if (frameInterval) {
            clearInterval(frameInterval);
            frameInterval = null;
        }
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
        if (videoEl) {
            videoEl.srcObject = null;
        }
        isProcessing = false;

        // Reset UI status
        camStatusBadge.className = "text-xs px-2.5 py-0.5 rounded-full font-semibold bg-slate-800 text-slate-400 border border-slate-700";
        camStatusBadge.innerText = "Kamera Mati (Off)";
        camStatusIcon.className = "w-10 h-10 rounded-xl bg-slate-700 text-slate-400 flex items-center justify-center text-xl";
        camDetailText.innerText = "Kamera dinonaktifkan. Klik 'Nyalakan Kamera' untuk memulai pemantauan.";

        toggleCamBtn.className = "px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center gap-2";
        toggleCamIcon.innerText = "📹";
        toggleCamText.innerText = "Nyalakan Kamera";

        standbyPlaceholder.classList.remove('hidden');
        displayCanvas.classList.add('hidden');
        permissionDeniedNotice.classList.add('hidden');

        livePing.classList.add('hidden');
        liveDot.className = "relative inline-flex rounded-full h-3 w-3 bg-slate-500";
        streamIndicator.className = "w-2 h-2 rounded-full bg-slate-500";
        fpsCounter.innerText = "FPS: 0 | Resolusi: 640x480";

        statusText.innerText = "Standby (Kamera Mati)";
        driverOverlay.classList.add('hidden');
        statusSub.innerText = "Kamera sedang dinonaktifkan";
        statusText.className = "text-2xl font-black text-slate-400";
        statusIcon.innerText = "⚪";
        alarmBanner.classList.add('hidden');
        stopBuzzer();
    }

    // Toggle Nyala / Mati Kamera
    function toggleCamera() {
        if (currentStream && currentStream.active) {
            stopCamera();
        } else {
            requestAndStartCamera();
        }
    }

    function switchCamera() {
        const selectedDevId = camSelect.value;
        if (selectedDevId) {
            requestAndStartCamera(selectedDevId);
        }
    }

    // Mengambil frame dari video, mengirim ke backend Flask, dan menggambar overlay
    async function sendFrameToAI() {
        if (!currentStream || isProcessing || videoEl.paused || videoEl.ended) return;

        isProcessing = true;
        try {
            // Gambar frame video ke canvas offscreen
            captureCtx.drawImage(videoEl, 0, 0, 640, 480);
            const dataUrl = captureCanvas.toDataURL('image/jpeg', 0.65);
            // Render langsung ke canvas tampilan agar kamera selalu aktif dan tidak hitam
            drawDriverFrame();

            // Hitung FPS
            fpsFrames++;
            const now = Date.now();
            if (now - lastFpsTime >= 1000) {
                fpsCounter.innerText = `FPS: ${fpsFrames} | Resolusi: 640x480`;
                fpsFrames = 0;
                lastFpsTime = now;
            }

            // Kirim frame ke endpoint /driver/process_frame
            const res = await fetch("{{ route('driver.process_frame') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    image: dataUrl,
                    driver_id: {{ Auth::id() }}
                })
            });

            if (!res.ok) {
                isProcessing = false;
                return;
            }

            const data = await res.json();

            // Render ke canvas tampilan utama
            drawDriverFrame();

            // Anotasi Landmark & Bounding Box Wajah
            if (data.face_detected) {
                consecNoFaceFrames = 0;
                displayCtx.save();
                if (cfg.camera.mirror) { displayCtx.translate(640, 0); displayCtx.scale(-1, 1); }   // landmark ikut dicerminkan
                // Gambar titik hijau pada mata
                displayCtx.fillStyle = "#10B981";
                if (data.left_eye_pts) {
                    data.left_eye_pts.forEach(pt => {
                        displayCtx.beginPath();
                        displayCtx.arc(pt[0], pt[1], 3, 0, 2 * Math.PI);
                        displayCtx.fill();
                    });
                }
                if (data.right_eye_pts) {
                    data.right_eye_pts.forEach(pt => {
                        displayCtx.beginPath();
                        displayCtx.arc(pt[0], pt[1], 3, 0, 2 * Math.PI);
                        displayCtx.fill();
                    });
                }

                // Kotak Wajah (Bounding Box)
                if (data.bbox) {
                    const [x1, y1, x2, y2] = data.bbox;
                    displayCtx.lineWidth = 2.5;
                    displayCtx.strokeStyle = data.is_alarm_active ? "#EF4444" : "#10B981";
                    displayCtx.strokeRect(x1, y1, x2 - x1, y2 - y1);
                }
                displayCtx.restore();
            } else {
                consecNoFaceFrames++;
                // Hanya tampilkan banner jika wajah hilang sejenak tanpa kondisi alarm/peringatan distraksi aktif
                if (consecNoFaceFrames > 4 && !data.is_alarm_active && data.status_label !== 'TIDAK_FOKUS') {
                    displayCtx.fillStyle = "rgba(0, 0, 0, 0.65)";
                    displayCtx.fillRect(100, 200, 440, 80);
                    displayCtx.fillStyle = "#F59E0B";
                    displayCtx.font = "bold 16px sans-serif";
                    displayCtx.textAlign = "center";
                    displayCtx.fillText("Mencari Posisi Wajah Pengemudi...", 320, 235);
                    displayCtx.fillStyle = "#E2E8F0";
                    displayCtx.font = "12px sans-serif";
                    displayCtx.fillText("Pastikan wajah menghadap lurus ke depan jalan", 320, 260);
                    displayCtx.textAlign = "left";
                }
            }

            // Banner Visual Merah jika Alarm Aktif
            if (data.is_alarm_active && cfg.alerts.banner && cfg.events[data.alarm_type || 'BAHAYA'] !== false) {
                displayCtx.fillStyle = "rgba(220, 38, 38, 0.85)";
                displayCtx.fillRect(0, 0, 640, 50);
                displayCtx.fillStyle = "#FFFFFF";
                displayCtx.font = "bold 16px sans-serif";
                displayCtx.fillText("! " + (data.alarm_message || "PERINGATAN BAHAYA"), 20, 32);
            }

            // Update Panel Telemetri UI
            updateTelemetryUI(data);

        } catch (err) {
            console.error("Proses frame error:", err);
        } finally {
            isProcessing = false;
        }
    }

    function updateTelemetryUI(data) {
        // Model badge
        modelBadge.innerText = "Model: " + (data.method || "Baseline");

        // Metrik EAR & MAR
        const ear = data.ear_avg || 0.0;
        const mar = data.mar || 0.0;
        earVal.innerText = ear.toFixed(3);
        marVal.innerText = mar.toFixed(3);

        const earPct = Math.min(100, Math.max(0, (ear / 0.40) * 100));
        const marPct = Math.min(100, Math.max(0, (mar / 1.00) * 100));
        earBar.style.width = earPct + "%";
        marBar.style.width = marPct + "%";

        // Pose Kepala (Yaw & Pitch) & Pandangan Mata (Gaze Priority)
        const yaw = data.yaw || 0.0;
        const pitch = data.pitch || 0.0;
        const isGazeForward = data.is_gaze_forward !== false;
        if (pitchVal) pitchVal.innerText = `${pitch.toFixed(1)}°`;

        let poseDirection = "Tengah";
        let poseClass = "text-[10px] text-slate-400";
        let poseDesc = "Menghadap lurus ke depan";

        if (isGazeForward && Math.abs(yaw) < 35 && pitch < 25) {
            poseDirection = "Fokus Depan";
            poseDesc = "Mata fokus menatap jalan ke depan";
            poseClass = "text-[10px] text-emerald-400 font-medium";
        } else if (yaw > 25) {
            poseDirection = "Menoleh Kanan";
            poseDesc = "Pengemudi menoleh ke kanan";
            poseClass = "text-[10px] text-amber-400 font-medium";
        } else if (yaw < -25) {
            poseDirection = "Menoleh Kiri";
            poseDesc = "Pengemudi menoleh ke kiri";
            poseClass = "text-[10px] text-amber-400 font-medium";
        } else if (pitch > 22) {
            poseDirection = "Menunduk";
            poseDesc = "Pengemudi menunduk / melihat ke bawah";
            poseClass = "text-[10px] text-amber-400 font-medium";
        } else if (pitch < -20) {
            poseDirection = "Mendongak";
            poseDesc = "Pengemudi mendongak ke atas";
            poseClass = "text-[10px] text-amber-400 font-medium";
        }

        yawVal.innerText = `${yaw.toFixed(1)}° (${poseDirection})`;
        poseNotice.innerText = poseDesc;
        poseNotice.className = poseClass;

        // Overlay di atas video: status + arah pandangan
        const ovLabel = data.is_alarm_active ? (data.alarm_type || 'BAHAYA') : (data.status_label || 'TERJAGA');
        const ovTone = data.is_alarm_active ? 'red'
            : (ovLabel === 'TERJAGA' ? 'green' : (['TIDAK_FOKUS', 'MENGUAP', 'MENGANTUK'].includes(ovLabel) ? 'amber' : 'slate'));
        setOverlay(ovLabel.replace(/_/g, ' '), ovTone, 'Arah: ' + poseDirection + ' (' + Math.abs(yaw).toFixed(0) + '\u00B0)');
        driverOverlay.classList.remove('hidden');

        // Skor Kantuk
        const score = (data.drowsy_score || 0.0) * 100;
        drowsyScoreText.innerText = Math.round(score) + "%";
        drowsyScoreBar.style.width = Math.round(score) + "%";

        if (score > 70) {
            drowsyScoreBar.className = "bg-red-500 h-2.5 rounded-full transition-all duration-300";
        } else if (score > 40) {
            drowsyScoreBar.className = "bg-amber-500 h-2.5 rounded-full transition-all duration-300";
        } else {
            drowsyScoreBar.className = "bg-emerald-500 h-2.5 rounded-full transition-all duration-300";
        }

        // Indikator Distraksi (Menoleh)
        const distractPct = data.distraction_progress || 0;
        if (distractScoreText && distractScoreBar) {
            distractScoreText.innerText = distractPct + "%";
            distractScoreBar.style.width = distractPct + "%";
            if (distractPct > 70) {
                distractScoreBar.className = "bg-red-500 h-2.5 rounded-full transition-all duration-300";
            } else if (distractPct > 30) {
                distractScoreBar.className = "bg-amber-500 h-2.5 rounded-full transition-all duration-300";
            } else {
                distractScoreBar.className = "bg-emerald-500 h-2.5 rounded-full transition-all duration-300";
            }
        }

        // Status Alarm & Notifikasi
        const aType = data.alarm_type || 'BAHAYA';
        const alertOn = !!data.is_alarm_active && cfg.events[aType] !== false;   // jenis kejadian bisa dimatikan di Pengaturan
        if (alertOn) {
            alarmBanner.classList.toggle('hidden', !cfg.alerts.banner);
            alarmTitle.innerText = "PERINGATAN: " + (data.alarm_type || "BAHAYA");
            alarmDesc.innerText = data.alarm_message || "Kondisi berbahaya terdeteksi!";
            statusText.innerText = data.alarm_type || "BAHAYA";
            statusText.className = "text-2xl font-black text-red-500";
            statusIcon.className = "w-14 h-14 rounded-2xl bg-red-500/20 border border-red-500/40 flex items-center justify-center text-3xl";
            statusIcon.innerText = "🚨";
            statusSub.innerText = data.alarm_message;

            fireAlert(aType, data.alarm_message || '');
        } else {
            if (Date.now() >= testAlertUntil) alarmBanner.classList.add('hidden');
            clearAlert();
            const label = data.status_label || "TERJAGA";
            statusText.innerText = label;

            if (label === 'TERJAGA') {
                statusText.className = "text-2xl font-black text-emerald-400";
                statusIcon.className = "w-14 h-14 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-3xl";
                statusIcon.innerText = "👁️";
                statusSub.innerText = "Pengemudi fokus pada jalan";
            } else if (label === 'TIDAK_FOKUS') {
                statusText.className = "text-2xl font-black text-amber-400";
                statusIcon.className = "w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-3xl";
                statusIcon.innerText = "👀";
                statusSub.innerText = "Pandangan tidak fokus ke jalan";
            } else if (label === 'MENGUAP') {
                statusText.className = "text-2xl font-black text-amber-400";
                statusIcon.className = "w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-3xl";
                statusIcon.innerText = "🥱";
                statusSub.innerText = "Mulai terindikasi kelelahan";
            } else if (label === 'MENGANTUK') {
                statusText.className = "text-2xl font-black text-amber-500";
                statusIcon.className = "w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-3xl";
                statusIcon.innerText = "😴";
                statusSub.innerText = "Mata terpejam, indikasi kantuk";
            } else {
                statusText.className = "text-2xl font-black text-blue-400";
                statusIcon.className = "w-14 h-14 rounded-2xl bg-blue-500/20 border border-blue-500/40 flex items-center justify-center text-3xl";
                statusIcon.innerText = "👤";
                statusSub.innerText = "Kondisi stabil";
            }
        }
    }

    // Inisialisasi saat halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', () => {
        enumerateCameras();

        // Kamera TIDAK menyala otomatis: pengguna harus klik "Akses Kamera".
        // Pengguna baru melihat tutorial bergambar sekali.
        initTutorial();
    });

    // Klik "Akses Kamera": sembunyikan welcome, tampilkan bagian kamera, lalu minta izin kamera
    async function startFromWelcome() {
        document.getElementById('welcomeSection').classList.add('hidden');
        const cam = document.getElementById('cameraSection');
        cam.classList.remove('hidden');
        cam.scrollIntoView({ behavior: 'smooth', block: 'start' });
        await requestAndStartCamera();
        loadObjCameras();
    }

    // ===== Klaster kamera: tukar kamera utama & layar penuh =====
    const clusterEl = document.getElementById('cameraCluster');
    const tileDriver = document.getElementById('tileDriver');
    const tileObject = document.getElementById('tileObject');
    const tileDetail = document.getElementById('tileDetail');
    const clusterGrid = document.getElementById('clusterGrid');
    const fsText = document.getElementById('fsText');
    const fsBtn = document.getElementById('fsBtn');
    const fsDots = document.getElementById('fsDots');
    const fsLabel = document.getElementById('fsLabel');
    const fsPrev = document.getElementById('fsPrev');
    const fsNext = document.getElementById('fsNext');
    const driverOverlay = document.getElementById('driverOverlay');
    const ovStatus = document.getElementById('ovStatus');
    const ovPose = document.getElementById('ovPose');

    // ===== Slot kamera: index 0 = utama (kiri besar), 1 = kanan atas, 2 = kanan bawah =====
    let slotOrder = [tileDriver, tileObject, tileDetail];

    function applySlots() {
        const nSide = Math.max(1, slotOrder.length - 1);
        clusterGrid.style.setProperty('--n', nSide);
        clusterGrid.style.setProperty('--cols', Math.min(nSide, 3));                       // preset "top"
        clusterGrid.style.setProperty('--cols-eq', slotOrder.length <= 2 ? slotOrder.length : (slotOrder.length <= 4 ? 2 : 3));  // preset "grid"
        slotOrder.forEach((tile, i) => {
            tile.dataset.slot = (i === 0) ? 'main' : 'side';
            tile.style.order = i;
            tile.classList.toggle('is-main', i === 0);
            tile.classList.toggle('is-thumb', i !== 0);
        });
        renderFsNav();
        updateAddBtn();
    }

    // Jadikan tile tertentu sebagai kamera utama (tile utama lama menempati posisi tile tsb)
    function makeMain(tile) {
        const i = slotOrder.indexOf(tile);
        if (i <= 0) return;
        [slotOrder[0], slotOrder[i]] = [slotOrder[i], slotOrder[0]];
        applySlots();
    }

    // Tombol "Tukar Kamera Utama": putar utama -> CAM 2 -> CAM 3 -> kembali
    function swapCameras() {
        slotOrder = [slotOrder[1], slotOrder[2], slotOrder[0]];
        applySlots();
        if (isFsOn()) fsGoTo(0, true);
    }

    function setOverlay(label, tone, pose) {
        const tones = {
            red: 'bg-red-600 text-white',
            amber: 'bg-amber-400 text-slate-900',
            green: 'bg-emerald-600 text-white',
            slate: 'bg-slate-700 text-white'
        };
        ovStatus.className = 'px-2.5 py-1 rounded-lg text-xs font-bold ' + (tones[tone] || tones.slate);
        ovStatus.innerText = label;
        if (pose !== undefined) ovPose.innerText = pose;
    }

    function isFsOn() { return clusterEl.classList.contains('fs-on'); }

    function setFsUi(on) {
        clusterEl.classList.toggle('fs-on', on);
        fsText.innerText = on ? 'Keluar Layar Penuh' : 'Layar Penuh';
        fsBtn.setAttribute('aria-pressed', on ? 'true' : 'false');
        if (on) {
            // Layar penuh selalu mulai dari kamera utama; sisanya digeser
            requestAnimationFrame(() => { fsGoTo(0, true); updateFsNav(); });
        } else {
            clusterGrid.scrollLeft = 0;
        }
    }

    // ===== Layar penuh: 1 kamera per layar, geser / panah / titik / keyboard =====
    function fsIndex() {
        // tile yang paling dekat dengan posisi scroll saat ini
        let best = 0, bestDist = Infinity;
        slotOrder.forEach((t, i) => {
            const d = Math.abs(t.offsetLeft - clusterGrid.scrollLeft);
            if (d < bestDist) { bestDist = d; best = i; }
        });
        return best;
    }
    function fsGoTo(i, instant) {
        i = Math.max(0, Math.min(slotOrder.length - 1, i));
        clusterGrid.scrollTo({ left: slotOrder[i].offsetLeft, behavior: instant ? 'instant' : 'smooth' });
        updateFsNav(i);
    }
    function fsStep(dir) { fsGoTo(fsIndex() + dir); }

    function renderFsNav() {
        fsDots.innerHTML = '';
        slotOrder.forEach((t, i) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'fs-dot';
            b.setAttribute('aria-label', t.dataset.title);
            b.onclick = () => fsGoTo(i);
            fsDots.appendChild(b);
        });
        updateFsNav();
    }
    function updateFsNav(idx) {
        const i = (idx === undefined) ? fsIndex() : idx;
        [...fsDots.children].forEach((d, k) => d.classList.toggle('active', k === i));
        fsLabel.innerHTML = 'CAM ' + (i + 1) + ' / ' + slotOrder.length + ' &middot; ' + slotOrder[i].dataset.title +
                            ' <span class="font-normal text-slate-400">(geser untuk kamera lain)</span>';
        fsPrev.disabled = (i === 0);
        fsNext.disabled = (i === slotOrder.length - 1);
    }
    let fsScrollTimer = null;
    clusterGrid.addEventListener('scroll', () => {
        if (!isFsOn()) return;
        clearTimeout(fsScrollTimer);
        fsScrollTimer = setTimeout(() => updateFsNav(), 60);
    }, { passive: true });
    window.addEventListener('resize', () => { if (isFsOn()) fsGoTo(fsIndex(), true); });
    document.addEventListener('keydown', (e) => {
        if (!isFsOn() || (typeof settingsModal !== 'undefined' && settingsModal.classList.contains('open'))) return;
        if (e.key === 'ArrowRight') { fsStep(1); }
        if (e.key === 'ArrowLeft') { fsStep(-1); }
    });

    // (applySlots() dipanggil di akhir script, setelah semua const terdefinisi)
    function enterFsFallback() {          // untuk browser yang tidak mendukung Fullscreen API (mis. iPhone)
        clusterEl.classList.add('fs-fallback');
        setFsUi(true);
        document.body.style.overflow = 'hidden';
    }
    function exitFsFallback() {
        clusterEl.classList.remove('fs-fallback');
        setFsUi(false);
        document.body.style.overflow = '';
    }
    
function toggleClusterFullscreen() {
    if (!clusterEl || !fsBtn || !fsText) return;

    const nativeEl =
        document.fullscreenElement ||
        document.webkitFullscreenElement;

    // If already in native fullscreen, exit it
    if (nativeEl === clusterEl) {
        const exitFullscreen =
            document.exitFullscreen ||
            document.webkitExitFullscreen;

        if (exitFullscreen) {
            exitFullscreen.call(document).catch?.(() => {
                enterFsFallback();
            });
        }
        return;
    }

    // If using the fallback view, close it
    if (clusterEl.classList.contains('fs-fallback')) {
        exitFsFallback();
        return;
    }

    // Try the browser's native fullscreen
    const requestFullscreen =
        clusterEl.requestFullscreen ||
        clusterEl.webkitRequestFullscreen;

    if (requestFullscreen) {
        try {
            const result = requestFullscreen.call(clusterEl);

            if (result && typeof result.catch === 'function') {
                result.catch(() => enterFsFallback());
            }
        } catch (error) {
            enterFsFallback();
        }
    } else {
        // Use the page's existing fallback mode
        enterFsFallback();
    }
}

    function syncFullscreenState() {
        const nativeEl = document.fullscreenElement || document.webkitFullscreenElement;
        if (nativeEl === clusterEl) setFsUi(true);
        else if (!clusterEl.classList.contains('fs-fallback')) setFsUi(false);
    }
    document.addEventListener('fullscreenchange', syncFullscreenState);
    document.addEventListener('webkitfullscreenchange', syncFullscreenState);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && clusterEl.classList.contains('fs-fallback')) exitFsFallback();
    });


    // ===== Tambah kamera (+): tile kamera tambahan, preview langsung =====
    const MAX_EXTRA_CAMS = 4;            // batas kamera tambahan (ubah sesuai kebutuhan)
    let extraCamSeq = 0;
    const extraTiles = [];
    const addCamBtn = document.getElementById('addCamBtn');
    const addCamCount = document.getElementById('addCamCount');

    function updateAddBtn() {
        if (!addCamBtn) return;
        addCamBtn.disabled = extraTiles.length >= MAX_EXTRA_CAMS;
        addCamCount.innerText = extraTiles.length ? '(' + extraTiles.length + '/' + MAX_EXTRA_CAMS + ')' : '';
    }

    function usedDeviceIds() {
        const ids = [];
        [currentStream, objStream].concat(extraTiles.map(t => t._stream)).forEach(st => {
            const tr = st && st.getVideoTracks()[0];
            if (tr && tr.getSettings().deviceId) ids.push(tr.getSettings().deviceId);
        });
        return ids;
    }

    async function refreshExtraSelect(tile, preferFree) {
        const sel = tile.querySelector('.x-select');
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) return;
        const keep = sel.value;
        const devices = (await navigator.mediaDevices.enumerateDevices()).filter(d => d.kind === 'videoinput');
        sel.innerHTML = '';
        if (!devices.length) {
            sel.innerHTML = '<option value="">Tidak ada kamera terdeteksi</option>';
            return;
        }
        devices.forEach((d, i) => {
            const o = document.createElement('option');
            o.value = d.deviceId;
            o.innerText = d.label || ('Kamera ' + (i + 1));
            sel.appendChild(o);
        });
        if (keep && devices.some(d => d.deviceId === keep)) {
            sel.value = keep;
        } else if (preferFree) {                     // pilih kamera yang belum dipakai tile lain
            const used = usedDeviceIds();
            const free = devices.find(d => !used.includes(d.deviceId));
            if (free) sel.value = free.deviceId;
        }
    }

    function addCamera() {
        if (extraTiles.length >= MAX_EXTRA_CAMS) return;
        extraCamSeq++;
        const title = 'Kamera Tambahan ' + extraCamSeq;
        const tile = document.createElement('section');
        tile.className = 'cam-tile is-thumb';
        tile.dataset.title = title;
        tile.setAttribute('aria-label', title);
        tile.innerHTML =
            '<div class="tile-head bg-slate-50 px-4 py-2.5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">' +
                '<span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">' +
                    '<span class="x-dot w-2 h-2 rounded-full bg-slate-400"></span>' + title + '</span>' +
                '<span class="flex items-center gap-3">' +
                    '<button type="button" class="make-main-btn text-[11px] font-semibold text-blue-700 hover:underline cursor-pointer" onclick="makeMain(this.closest(\'.cam-tile\'))">Jadikan utama</button>' +
                    '<button type="button" class="x-btn-del" title="Hapus kamera ini" onclick="removeExtraCamera(this.closest(\'.cam-tile\'))">&#10005; Hapus</button>' +
                '</span>' +
            '</div>' +
            '<div class="tile-controls px-3 py-2 border-b border-slate-200 flex flex-wrap items-center gap-2">' +
                '<select class="x-select min-w-0 flex-1 bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-2.5 py-1.5 focus:bg-white focus:outline-none focus:border-blue-600 transition" ' +
                        'onchange="if (this.closest(\'.cam-tile\')._stream) startExtraCamera(this.closest(\'.cam-tile\'))"><option value="">Mencari kamera...</option></select>' +
                '<button type="button" class="x-toggle px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition cursor-pointer" ' +
                        'onclick="toggleExtraCamera(this.closest(\'.cam-tile\'))">Nyalakan</button>' +
            '</div>' +
            '<div class="tile-view">' +
                '<video class="cam-video x-video hidden" autoplay playsinline muted></video>' +
                '<div class="x-standby"><div class="x-emoji">&#128249;</div>' +
                    '<div class="text-sm font-bold text-white">Kamera Nonaktif</div>' +
                    '<p class="mt-1">Pilih perangkat kamera lalu klik <strong>Nyalakan</strong>. Bisa USB, webcam lain, atau kamera ponsel.</p></div>' +
            '</div>' +
            '<div class="tile-foot bg-slate-50 px-3 py-2 border-t border-slate-200 text-xs text-slate-500">' +
                '<span class="x-status">Kamera belum aktif</span></div>';

        clusterGrid.appendChild(tile);
        extraTiles.push(tile);
        slotOrder.push(tile);
        applySlots();
        refreshExtraSelect(tile, true);
        if (isFsOn()) fsGoTo(slotOrder.length - 1);
        else tile.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }

    function extraSetStatus(tile, text) { tile.querySelector('.x-status').innerText = text; }

    async function startExtraCamera(tile) {
        const sel = tile.querySelector('.x-select');
        const btn = tile.querySelector('.x-toggle');
        const vid = tile.querySelector('.x-video');
        stopExtraCamera(tile, true);
        btn.disabled = true;
        extraSetStatus(tile, 'Meminta akses kamera...');
        try {
            const video = { width: { ideal: 640 }, height: { ideal: 480 } };
            if (sel.value) video.deviceId = { exact: sel.value };
            const stream = await navigator.mediaDevices.getUserMedia({ video: video, audio: false });
            tile._stream = stream;
            vid.srcObject = stream;
            await vid.play();
            tile.querySelector('.x-standby').classList.add('hidden');
            vid.classList.remove('hidden');
            tile.querySelector('.x-dot').className = 'x-dot w-2 h-2 rounded-full bg-emerald-500 animate-pulse';
            btn.innerText = 'Matikan';
            btn.className = 'x-toggle px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl transition cursor-pointer';
            extraSetStatus(tile, 'Aktif · ' + vid.videoWidth + 'x' + vid.videoHeight);
            stream.getVideoTracks()[0].addEventListener('ended', () => stopExtraCamera(tile, false, 'Kamera terputus'));
            refreshExtraSelect(tile, false);       // label perangkat baru muncul setelah izin diberikan
        } catch (err) {
            console.error('Kamera tambahan gagal:', err);
            tile._stream = null;
            extraSetStatus(tile, 'Gagal: ' + (err.message || err.name) + '. Coba kamera lain atau cek izin.');
        } finally {
            btn.disabled = false;
        }
    }

    function stopExtraCamera(tile, silent, msg) {
        if (tile._stream) { tile._stream.getTracks().forEach(t => t.stop()); tile._stream = null; }
        const vid = tile.querySelector('.x-video');
        vid.srcObject = null;
        vid.classList.add('hidden');
        tile.querySelector('.x-standby').classList.remove('hidden');
        tile.querySelector('.x-dot').className = 'x-dot w-2 h-2 rounded-full bg-slate-400';
        const btn = tile.querySelector('.x-toggle');
        btn.innerText = 'Nyalakan';
        btn.className = 'x-toggle px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition cursor-pointer';
        if (!silent) extraSetStatus(tile, msg || 'Kamera dimatikan');
    }

    function toggleExtraCamera(tile) {
        if (tile._stream) stopExtraCamera(tile); else startExtraCamera(tile);
    }

    function removeExtraCamera(tile) {
        stopExtraCamera(tile, true);
        extraTiles.splice(extraTiles.indexOf(tile), 1);
        slotOrder.splice(slotOrder.indexOf(tile), 1);   // jika ia kamera utama, tile berikutnya naik jadi utama
        tile.remove();
        applySlots();
        if (isFsOn()) fsGoTo(Math.min(fsIndex(), slotOrder.length - 1), true);
    }

    if (navigator.mediaDevices && navigator.mediaDevices.addEventListener) {
        navigator.mediaDevices.addEventListener('devicechange', () => extraTiles.forEach(t => refreshExtraSelect(t, false)));
    }


    // =====================================================================
    //  PENGATURAN (suara, peringatan, kamera, tampilan) - disimpan di localStorage
    // =====================================================================
    const CFG_KEY = 'dsSettings_{{ Auth::id() }}';
    const CUSTOM_SND_KEY = 'dsCustomSound_{{ Auth::id() }}';
    const CFG_DEFAULTS = {
        sound:  { enabled: true, type: 'siren', volume: 0.7 },
        alerts: { banner: true, flash: true, voice: false, vibrate: true },
        events: { KANTUK: true, TIDAK_FOKUS: true, MENGUAP: true, WAJAH_HILANG: true, OBJEK_DEKAT: true },
        camera: { layout: 'side', mainCam: 'driver', fit: 'contain', mirror: false, overlay: true, showDetail: true }
    };
    const settingsModal = document.getElementById('settingsModal');
    const alertFlash = document.getElementById('alertFlash');
    const stVolText = document.getElementById('stVolText');
    const stCustomName = document.getElementById('stCustomName');
    let customName = '';
    let testAlertUntil = 0, testAlertTimer = null;

    function mergeCfg(saved) {
        const out = JSON.parse(JSON.stringify(CFG_DEFAULTS));
        if (saved && typeof saved === 'object') {
            Object.keys(out).forEach(g => Object.keys(out[g]).forEach(k => {
                if (saved[g] && saved[g][k] !== undefined) out[g][k] = saved[g][k];
            }));
        }
        out.theme = window.dsReadTheme ? window.dsReadTheme() : { mode: 'light', accent: 'blue' };
        return out;
    }
    let cfg = (function () {
        let saved = null;
        try { saved = JSON.parse(localStorage.getItem(CFG_KEY) || 'null'); } catch (e) {}
        return mergeCfg(saved);
    })();
    function saveCfg() {
        try { const c = Object.assign({}, cfg); delete c.theme; localStorage.setItem(CFG_KEY, JSON.stringify(c)); } catch (e) {}
    }

    // ----- Suara kustom -----
    (function loadCustomSound() {
        try {
            const o = JSON.parse(localStorage.getItem(CUSTOM_SND_KEY) || 'null');
            if (o && o.data) { customAudio = new Audio(o.data); customName = o.name || 'Suara kustom'; }
        } catch (e) {}
    })();
    function onCustomSoundFile(input) {
        const f = input.files && input.files[0];
        if (!f) return;
        if (f.size > 8 * 1024 * 1024) { stCustomName.innerText = 'File terlalu besar (maks 8 MB).'; return; }
        const rd = new FileReader();
        rd.onload = () => {
            customAudio = new Audio(rd.result);
            customName = f.name;
            try { localStorage.setItem(CUSTOM_SND_KEY, JSON.stringify({ name: f.name, data: rd.result })); }
            catch (e) { customName = f.name + ' (hanya sesi ini, file terlalu besar untuk disimpan)'; }
            syncSettingsUi();
            previewSound();
        };
        rd.readAsDataURL(f);
    }
    function previewSound() { initAudioContext(); playAlarmSound(true); }

    // ----- Saluran peringatan: suara, kilatan, ucapan, getar -----
    let flashTimer = null, lastSpoken = 0, lastSpokenMsg = '', speechOwner = '', lastVibe = 0;
    function showFlash(ms) {
        alertFlash.classList.add('on');
        clearTimeout(flashTimer);
        flashTimer = setTimeout(() => alertFlash.classList.remove('on'), ms || 700);
    }
    function speakAlert(msg, owner) {
        if (!('speechSynthesis' in window) || !msg || speechSynthesis.speaking) return;
        const now = Date.now();
        if (msg === lastSpokenMsg && now - lastSpoken < 8000) return;
        const u = new SpeechSynthesisUtterance(msg.replace(/^(PERINGATAN|BAHAYA|Peringatan)\s*:\s*/, ''));
        u.lang = 'id-ID';
        u.volume = Math.max(0.2, Math.min(1, cfg.sound.volume));
        speechOwner = owner; lastSpoken = now; lastSpokenMsg = msg;
        speechSynthesis.speak(u);
    }
    function vibrateAlert() {
        const now = Date.now();
        if (now - lastVibe < 1500) return;
        lastVibe = now;
        try { if (navigator.vibrate) navigator.vibrate([300, 120, 300]); } catch (e) {}
    }
    // Dipanggil terus-menerus selama alarm aktif; tiap saluran sudah membatasi frekuensinya sendiri
    function fireAlert(type, msg) {
        playAlarmSound();                                            // (cek audio aktif ada di dalamnya)
        if (cfg.alerts.flash) showFlash();
        if (cfg.alerts.voice) speakAlert(msg, type === 'OBJEK_DEKAT' ? 'object' : 'driver');
        if (cfg.alerts.vibrate) vibrateAlert();
    }
    function clearAlert() {          // alarm pengemudi selesai
        if (speechOwner === 'driver' && 'speechSynthesis' in window && speechSynthesis.speaking) speechSynthesis.cancel();
        stopBuzzer();
    }
    function testAlert() {
        const msg = 'Ini adalah tes peringatan. Tetap fokus menatap jalan.';
        testAlertUntil = Date.now() + 3000;
        if (cfg.alerts.banner) {
            alarmTitle.innerText = 'TES PERINGATAN';
            alarmDesc.innerText = msg;
            alarmBanner.classList.remove('hidden');
            clearTimeout(testAlertTimer);
            testAlertTimer = setTimeout(() => alarmBanner.classList.add('hidden'), 3000);
        }
        initAudioContext();
        const iv = setInterval(() => { if (Date.now() >= testAlertUntil) clearInterval(iv); else fireAlert('TES', msg); }, 350);
        fireAlert('TES', msg);
    }

    // ----- Kamera: frame pengemudi (dengan opsi cermin) -----
    function drawDriverFrame() {
        displayCtx.save();
        if (cfg.camera.mirror) { displayCtx.translate(640, 0); displayCtx.scale(-1, 1); }
        displayCtx.drawImage(videoEl, 0, 0, 640, 480);
        displayCtx.restore();
    }

    // ----- Terapkan pengaturan ke tampilan -----
    function applyCameraCfg() {
        clusterGrid.dataset.layout = cfg.camera.layout;
        clusterEl.style.setProperty('--cam-fit', cfg.camera.fit);
        clusterEl.classList.toggle('no-overlay', !cfg.camera.overlay);
        const has = slotOrder.includes(tileDetail);
        if (!cfg.camera.showDetail && has) { slotOrder.splice(slotOrder.indexOf(tileDetail), 1); tileDetail.style.display = 'none'; }
        if (cfg.camera.showDetail && !has) { slotOrder.push(tileDetail); tileDetail.style.display = ''; }
        applySlots();
    }
    function applyMainCam() {
        const t = { driver: tileDriver, object: tileObject, detail: tileDetail }[cfg.camera.mainCam];
        if (t && slotOrder.includes(t)) makeMain(t);
    }
    function applySettings(key) {
        const all = !key;
        if (all || key.indexOf('theme.') === 0) { if (window.dsApplyTheme) window.dsApplyTheme(cfg.theme.mode, cfg.theme.accent, !all); }
        if (all || key === 'sound.enabled') { isAudioEnabled = cfg.sound.enabled; setAudioUi(); }
        if (all || (key.indexOf('camera.') === 0 && key !== 'camera.mainCam')) applyCameraCfg();
        if (all || key === 'camera.mainCam') applyMainCam();
    }

    // ----- Panel pengaturan: binding otomatis lewat atribut data-k -----
    function cfgGet(path) { return path.split('.').reduce((o, k) => (o == null ? o : o[k]), cfg); }
    function cfgSet(path, v) {
        const ks = path.split('.'); let o = cfg;
        for (let i = 0; i < ks.length - 1; i++) o = o[ks[i]];
        o[ks[ks.length - 1]] = v;
    }
    function syncSettingsUi() {
        settingsModal.querySelectorAll('[data-k]').forEach(el => {
            const v = cfgGet(el.dataset.k);
            if (el.type === 'checkbox') el.checked = !!v;
            else if (el.type === 'radio') el.checked = (String(v) === el.value);
            else if (el.type === 'range') el.value = Math.round(v * (+el.dataset.scale || 1));
            else el.value = v;
        });
        document.getElementById('stCustomWrap').style.display = (cfg.sound.type === 'custom') ? 'flex' : 'none';
        stVolText.innerText = Math.round(cfg.sound.volume * 100) + '%';
        stCustomName.innerText = customName || 'Belum ada file dipilih';
        document.getElementById('stThemeNote').style.display = window.__dsThemeFallback ? 'block' : 'none';
    }
    function onSettingEvent(e) {
        const el = e.target.closest ? e.target.closest('[data-k]') : null;
        if (!el) return;
        if (el.type === 'range' ? e.type !== 'input' : e.type !== 'change') return;
        let v;
        if (el.type === 'checkbox') v = el.checked;
        else if (el.type === 'radio') { if (!el.checked) return; v = el.value; }
        else if (el.type === 'range') v = (+el.value) / (+el.dataset.scale || 1);
        else v = el.value;
        cfgSet(el.dataset.k, v);
        saveCfg();
        applySettings(el.dataset.k);
        if (el.type === 'range') stVolText.innerText = Math.round(cfg.sound.volume * 100) + '%'; else syncSettingsUi();
    }
    settingsModal.addEventListener('input', onSettingEvent);
    settingsModal.addEventListener('change', onSettingEvent);

    function stTab(name) {
        document.querySelectorAll('.st-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === name));
        document.querySelectorAll('.st-pane').forEach(p => p.classList.toggle('active', p.dataset.pane === name));
    }
    function openSettings() { syncSettingsUi(); settingsModal.classList.add('open'); }
    function closeSettings() { settingsModal.classList.remove('open'); }
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && settingsModal.classList.contains('open')) { closeSettings(); e.stopImmediatePropagation(); }
    }, true);

    function resetSettings() {
        if (!confirm('Kembalikan semua pengaturan ke default?')) return;
        cfg = mergeCfg(null);
        cfg.theme = { mode: 'light', accent: 'blue' };
        saveCfg();
        try { localStorage.removeItem(CUSTOM_SND_KEY); } catch (e) {}
        customAudio = null; customName = '';
        applySettings();
        syncSettingsUi();
    }

    // ===== Tutorial pop-up bergambar (slider) =====
    const TUT_KEY = 'dsTutorialSeen_{{ Auth::id() }}';
    const TUT_COUNT = 5;
    let tutIndex = 0;
    const tutModal = document.getElementById('tutorialModal');

// Move popup outside the page content containers
if (tutModal) {
    document.body.appendChild(tutModal);

    Object.assign(tutModal.style, {
        position: 'fixed',
        inset: '0',
        width: '100%',
        height: '100%',
        zIndex: '99999',
        alignItems: 'center',
        justifyContent: 'center'
    });
}

const tutTrack = document.getElementById('tutorialTrack');

    function renderTutorial() {
        tutTrack.style.transform = 'translateX(-' + (tutIndex * 100) + '%)';
        document.querySelectorAll('.tut-dot').forEach((d, i) => {
            d.className = 'tut-dot w-2.5 h-2.5 rounded-full cursor-pointer ' + (i === tutIndex ? 'bg-blue-600' : 'bg-slate-300');
        });
        document.getElementById('tutPrev').disabled = (tutIndex === 0);
        document.getElementById('tutNext').innerText = (tutIndex === TUT_COUNT - 1) ? 'Mulai' : 'Berikutnya';
    }
    function openTutorial() {
        if (!tutModal || !tutTrack) return;

        tutIndex = 0;
        renderTutorial();

        tutModal.classList.remove('hidden');
        tutModal.style.display = 'flex';

        document.body.style.overflow = 'hidden';
        document.getElementById('tutNext')?.focus();
    }   
    function closeTutorial() {
        if (!tutModal) return;

        tutModal.classList.add('hidden');
        tutModal.style.display = 'none';

        document.body.style.overflow = '';

        try {
            localStorage.setItem(TUT_KEY, '1');
        } catch (e) {}
    }
    function tutorialNext() {
        if (tutIndex >= TUT_COUNT - 1) { closeTutorial(); return; }
        tutIndex++; renderTutorial();
    }
    function tutorialPrev() { if (tutIndex > 0) { tutIndex--; renderTutorial(); } }
    function tutorialGo(i) { tutIndex = i; renderTutorial(); }
    function initTutorial() {
        let seen = false;
        try { seen = localStorage.getItem(TUT_KEY) === '1'; } catch (e) {}
        if (!seen) openTutorial();
    }
    document.addEventListener('keydown', (e) => {
        if (tutModal.classList.contains('hidden')) return;
        if (e.key === 'Escape') closeTutorial();
        if (e.key === 'ArrowRight') tutorialNext();
        if (e.key === 'ArrowLeft') tutorialPrev();
    });
    let tutTouchX = null;
    tutTrack.addEventListener('touchstart', (e) => { tutTouchX = e.touches[0].clientX; }, { passive: true });
    tutTrack.addEventListener('touchend', (e) => {
        if (tutTouchX === null) return;
        const dx = e.changedTouches[0].clientX - tutTouchX;
        if (dx < -40) tutorialNext(); else if (dx > 40) tutorialPrev();
        tutTouchX = null;
    });

    // ===== Kamera Deteksi Objek (COCO-SSD, berjalan langsung di browser) =====
    const OBJ_LABELS = { person: 'Orang', bicycle: 'Sepeda', car: 'Mobil', motorcycle: 'Motor', bus: 'Bus', truck: 'Truk' };
    const OBJ_NEAR = 0.55;      // tinggi objek > 55% tinggi layar = DEKAT (ubah untuk kalibrasi)
    const OBJ_MID = 0.30;       // 30% - 55% = SEDANG, di bawahnya = JAUH
    const OBJ_MIN_SCORE = 0.5;  // keyakinan minimum deteksi
    const TFJS_URL = 'https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.22.0/dist/tf.min.js';
    const COCO_URL = 'https://cdn.jsdelivr.net/npm/@tensorflow-models/coco-ssd@2.2.3/dist/coco-ssd.min.js';

    const objVideo = document.getElementById('objVideo');
    const objCanvas = document.getElementById('objCanvas');
    const objCtx = objCanvas.getContext('2d');
    const objCamSelect = document.getElementById('objCamSelect');
    const objToggleBtn = document.getElementById('objToggleBtn');
    const objToggleText = document.getElementById('objToggleText');
    const objStandby = document.getElementById('objStandby');
    const objAlert = document.getElementById('objAlert');
    const objAlertText = document.getElementById('objAlertText');
    const objStatus = document.getElementById('objStatus');
    const objSummary = document.getElementById('objSummary');

    let objModel = null, objModelPromise = null, objStream = null, objLoop = null, objBusy = false, objLastBeep = 0;
    const scriptPromises = {};

    function loadScriptOnce(src) {
        if (!scriptPromises[src]) {
            scriptPromises[src] = new Promise((resolve, reject) => {
                const el = document.createElement('script');
                el.src = src;
                el.onload = resolve;
                el.onerror = () => { delete scriptPromises[src]; reject(new Error('Gagal memuat library (cek internet)')); };
                document.head.appendChild(el);
            });
        }
        return scriptPromises[src];
    }

    function ensureObjModel() {
        if (!objModelPromise) {
            objModelPromise = loadScriptOnce(TFJS_URL)
                .then(() => loadScriptOnce(COCO_URL))
                .then(() => cocoSsd.load({ base: 'lite_mobilenet_v2' }))
                .then((m) => { objModel = m; return m; })
                .catch((err) => { objModelPromise = null; throw err; });
        }
        return objModelPromise;
    }

    async function loadObjCameras() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) return;
        try {
            const devices = (await navigator.mediaDevices.enumerateDevices()).filter(d => d.kind === 'videoinput');
            const track = currentStream && currentStream.getVideoTracks()[0];
            const driverId = track ? track.getSettings().deviceId : null;
            objCamSelect.innerHTML = '';
            if (devices.length === 0) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.innerText = 'Tidak ada kamera terdeteksi';
                objCamSelect.appendChild(opt);
                return;
            }
            devices.forEach((d, i) => {
                const opt = document.createElement('option');
                opt.value = d.deviceId;
                opt.innerText = d.label || ('Kamera ' + (i + 1));
                objCamSelect.appendChild(opt);
            });
            const other = devices.find(d => d.deviceId !== driverId);
            if (other) objCamSelect.value = other.deviceId;
        } catch (e) { console.error('Gagal mendata kamera objek:', e); }
    }

    function setObjStatus(text) { objStatus.innerText = text; }

    async function startObjectCamera() {
        initAudioContext();
        objToggleBtn.disabled = true;
        try {
            setObjStatus('Memuat model deteksi objek (pertama kali butuh internet)...');
            await ensureObjModel();
            setObjStatus('Meminta akses kamera objek...');
            const deviceId = objCamSelect.value;
            const video = { width: { ideal: 640 }, height: { ideal: 480 } };
            if (deviceId) video.deviceId = { exact: deviceId };
            objStream = await navigator.mediaDevices.getUserMedia({ video: video, audio: false });
            objVideo.srcObject = objStream;
            await objVideo.play();

            objStandby.classList.add('hidden');
            objCanvas.classList.remove('hidden');
            objToggleText.innerText = 'Matikan Kamera Objek';
            objToggleBtn.className = 'px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer';
            setObjStatus('Kamera objek aktif');
            if (objLoop) clearInterval(objLoop);
            objLoop = setInterval(detectObjects, 200);
        } catch (err) {
            console.error('Kamera objek gagal:', err);
            if (objStream) { objStream.getTracks().forEach(t => t.stop()); objStream = null; }
            setObjStatus('Gagal: ' + (err.message || err.name) + '. Coba kamera lain atau cek izin.');
        } finally {
            objToggleBtn.disabled = false;
        }
    }

    function stopObjectCamera() {
        if (objLoop) { clearInterval(objLoop); objLoop = null; }
        if (objStream) { objStream.getTracks().forEach(t => t.stop()); objStream = null; }
        objVideo.srcObject = null;
        objCtx.clearRect(0, 0, objCanvas.width, objCanvas.height);
        objCanvas.classList.add('hidden');
        objStandby.classList.remove('hidden');
        objAlert.classList.add('hidden');
        objToggleText.innerText = 'Nyalakan Kamera Objek';
        objToggleBtn.className = 'px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer';
        objSummary.innerText = '-';
        setObjStatus('Kamera objek dimatikan');
    }

    function toggleObjectCamera() {
        if (objStream) stopObjectCamera(); else startObjectCamera();
    }

    async function detectObjects() {
        if (!objModel || !objStream || objBusy || objVideo.readyState < 2) return;
        objBusy = true;
        try {
            const w = objVideo.videoWidth, h = objVideo.videoHeight;
            if (!w || !h) return;
            if (objCanvas.width !== w) { objCanvas.width = w; objCanvas.height = h; }
            const preds = await objModel.detect(objVideo);
            objCtx.drawImage(objVideo, 0, 0, w, h);

            const counts = {};
            let nearest = null;
            preds.forEach((p) => {
                const label = OBJ_LABELS[p.class];
                if (!label || p.score < OBJ_MIN_SCORE) return;
                const x = p.bbox[0], y = p.bbox[1], bw = p.bbox[2], bh = p.bbox[3];
                const ratio = bh / h;
                const level = ratio > OBJ_NEAR ? 'DEKAT' : (ratio > OBJ_MID ? 'SEDANG' : 'JAUH');
                const color = level === 'DEKAT' ? '#EF4444' : (level === 'SEDANG' ? '#F59E0B' : '#10B981');

                objCtx.lineWidth = 3;
                objCtx.strokeStyle = color;
                objCtx.strokeRect(x, y, bw, bh);
                const text = label + ' - ' + level;
                objCtx.font = 'bold 14px sans-serif';
                const tw = objCtx.measureText(text).width + 10;
                const ty = Math.max(22, y);
                objCtx.fillStyle = color;
                objCtx.fillRect(x, ty - 22, tw, 22);
                objCtx.fillStyle = level === 'DEKAT' ? '#FFFFFF' : '#0F172A';
                objCtx.fillText(text, x + 5, ty - 6);

                counts[label] = (counts[label] || 0) + 1;
                if (level === 'DEKAT' && (!nearest || ratio > nearest.ratio)) nearest = { label: label, ratio: ratio };
            });

            const parts = Object.keys(counts).map(k => counts[k] + ' ' + k);
            objSummary.innerText = parts.length ? parts.join(', ') : 'Tidak ada objek';

            if (nearest && cfg.events.OBJEK_DEKAT !== false) {
                const nearMsg = 'Objek terlalu dekat: ' + nearest.label + '! Kurangi kecepatan.';
                objAlert.classList.toggle('hidden', !cfg.alerts.banner);
                objAlertText.innerText = nearMsg;
                if (Date.now() - objLastBeep > 1500) { objLastBeep = Date.now(); fireAlert('OBJEK_DEKAT', nearMsg); }
            } else {
                objAlert.classList.add('hidden');
            }
        } catch (e) {
            console.error('Deteksi objek error:', e);
        } finally {
            objBusy = false;
        }
    }

    applySlots();   // inisialisasi posisi kamera
    applySettings();   // terapkan pengaturan tersimpan (tema, tata letak, dll.)
</script>
@endsection
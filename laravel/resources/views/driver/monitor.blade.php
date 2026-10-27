@extends('layouts.driver')
@section('title', 'Monitoring Pengemudi Real-Time - DriveSafe DMS')

@section('content')
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

    <!-- ====== KLASTER KAMERA (kamera utama + kamera kecil + panel detail) ====== -->
    <style>
    /* ===== Klaster kamera ===== */
    #cameraCluster { 
        display: flex; 
        flex-direction: column; 
        gap: 12px; 
        }
    .cluster-grid { 
        display: grid; 
        gap: 16px; 
        grid-template-columns: minmax(0, 1fr); 
        grid-template-areas: "main" "thumb" "details"; 
        }
    @media (min-width: 1024px) {
        .cluster-grid { 
            grid-template-columns: minmax(0, 1fr) 340px; 
            grid-template-rows: auto minmax(0, 1fr); 
            grid-template-areas: "main thumb" "main details"; }
    }
    .cam-tile { 
        display: flex; 
        flex-direction: 
        column; min-width: 0; 
        min-height: 0; 
        border: 1px solid #E2E8F0; 
        border-radius: 16px; 
        overflow: hidden; 
        background: #fff; 
        }
    .cam-tile.is-main { 
        grid-area: main; 
        }
    .cam-tile.is-thumb { 
        grid-area: thumb; 
        }
    .details-pane { 
        grid-area: details; 
        min-height: 0; 
        overflow-y: auto; 
        }
    .tile-view { 
        position: relative; 
        background: #020617; 
        display: flex; 
        align-items: center; 
        justify-content: center;
        overflow: hidden; 
        }
    .is-main .tile-view { 
        flex: 1; 
        min-height: 320px; 
        }
    .is-thumb .tile-view { 
        min-height: 130px; 
        }
    .cam-canvas { 
        width: 100%; 
        height: auto; 
        object-fit: contain; 
        }
    .is-main .cam-canvas { 
        max-height: 70vh; 
        }
    .is-thumb .cam-canvas { 
        max-height: 11rem; 
        }
    .is-main .make-main-btn { 
        display: none; 
        }
    .is-thumb #standbyPlaceholder, .is-thumb #objStandby { 
        padding: 12px; 
        }
    .is-thumb #standbyPlaceholder p, .is-thumb #objStandby p, .is-thumb #permissionDeniedNotice ol { 
        display: none; 
        }
    .is-thumb #standbyPlaceholder .w-20 { 
        width: 3rem; 
        height: 3rem; 
        font-size: 1.5rem; 
        }
    /* Layar penuh: klaster (kamera + panel detail) memenuhi layar, panel detail bisa di-scroll */
    #cameraCluster.fs-on { 
        background: #fff; 
        padding: 12px; 
        height: 100vh; 
        width: 100vw; 
        box-sizing: border-box; 
        border-radius: 0; 
        border: 0; 
        overflow: hidden; 
    }
    #cameraCluster.fs-fallback { 
        position: fixed; 
        inset: 0; 
        z-index: 60; 
    }
    #cameraCluster.fs-on .cluster-grid { 
        flex: 1; 
        min-height: 0; 
        overflow-y: auto; 
    }
    #cameraCluster.fs-on .is-main .cam-canvas { 
        max-height: calc(100vh - 190px); 
    }
    


/* Temporary fix: keep driver camera visible in fullscreen */
#cameraCluster.fs-on .cluster-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 2fr) minmax(260px, 1fr) !important;
    grid-template-rows: minmax(0, 1fr) minmax(0, 1fr) !important;
    grid-template-areas:
        "main thumb"
        "main details" !important;
    flex: 1 1 auto;
    height: 100%;
    min-height: 0;
    overflow: auto;
}

#cameraCluster.fs-on #tileDriver {
    grid-area: main !important;
    min-width: 0;
    min-height: 0;
}

#cameraCluster.fs-on #tileObject {
    grid-area: thumb !important;
    min-width: 0;
    min-height: 0;
}

#cameraCluster.fs-on #detailPane {
    grid-area: details !important;
    min-height: 0;
    overflow-y: auto;
}

#cameraCluster.fs-on #tileDriver .tile-view,
#cameraCluster.fs-on #tileObject .tile-view {
    flex: 1 1 auto;
    min-height: 0;
}

#cameraCluster.fs-on #tileDriver #displayCanvas:not(.hidden),
#cameraCluster.fs-on #tileObject #objCanvas:not(.hidden) {
    display: block;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}


@media (min-width: 1024px) {
    #cameraCluster.fs-on .cluster-grid {
        overflow: hidden;
    }
}



    @media (min-width: 1024px) {
        #cameraCluster.fs-on .cluster-grid { overflow: hidden; }
    }
</style>

    <div id="cameraCluster" class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-3 sm:p-4">

    <!-- Alert Banner Darurat (Muncul otomatis saat kantuk/distraksi terdeteksi) -->
    <div id="alarmBanner" class="hidden p-4 rounded-2xl bg-red-600 text-white shadow-xl alarm-pulsing flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl font-bold">
                ⚠️
            </div>
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

        <div class="cluster-grid">

            <!-- Kamera Pengemudi -->
            <section id="tileDriver" class="cam-tile is-main" aria-label="Kamera Pengemudi">
                <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                        <span id="streamIndicator" class="w-2 h-2 rounded-full bg-slate-400"></span> Kamera Pengemudi
                    </span>
                    <span class="flex items-center gap-2">
                        <span id="modelBadge" class="text-[11px] px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-mono font-medium">Model: Memuat...</span>
                        <button type="button" onclick="swapCameras()" class="make-main-btn text-[11px] font-semibold text-blue-700 hover:underline cursor-pointer">Jadikan utama</button>
                    </span>
                </div>
                <div class="tile-view">
                    <video id="webcamVideo" autoplay playsinline muted class="hidden"></video>
                    <canvas id="captureCanvas" width="640" height="480" class="hidden"></canvas>
                    <canvas id="displayCanvas" width="640" height="480" class="cam-canvas hidden"></canvas>

                <!-- Placeholder Standby Sebelum Izin Diberikan / Saat Kamera Mati -->
                <div id="standbyPlaceholder" class="p-8 text-center flex flex-col items-center justify-center space-y-4">
                    <div class="w-20 h-20 rounded-full bg-slate-800 text-slate-300 flex items-center justify-center text-4xl shadow-inner border border-slate-700">
                        📹
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white font-heading">Kamera Sedang Nonaktif</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                            Klik tombol biru <strong>"Nyalakan Kamera"</strong> untuk memulai streaming dan deteksi AI secara real-time.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-3">
                        <button onclick="requestAndStartCamera()"
                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                            <span>📹</span> Nyalakan Kamera & Izinkan Akses
                        </button>
                    </div>
                </div>

                <!-- Petunjuk jika izin ditolak browser -->
                <div id="permissionDeniedNotice" class="hidden p-6 text-center bg-red-950/80 border border-red-800 rounded-2xl m-4 max-w-md">
                    <span class="text-3xl block mb-2">🚫</span>
                    <h4 class="text-base font-bold text-red-300">Akses Kamera Diblokir Browser</h4>
                    <p class="text-xs text-slate-300 mt-2 leading-relaxed">
                        Browser Anda memblokir akses ke webcam. Untuk mengizinkan:
                    </p>
                    <ol class="text-xs text-slate-400 text-left mt-2 space-y-1 list-decimal list-inside bg-slate-900/60 p-3 rounded-lg">
                        <li>Klik ikon <strong>Gembok 🔒</strong> di samping URL browser.</li>
                        <li>Ubah izin Kamera menjadi <strong>Izinkan (Allow)</strong>.</li>
                        <li>Klik tombol coba izin lagi di bawah.</li>
                    </ol>
                    <div class="mt-4">
                        <button onclick="requestAndStartCamera()"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow transition">
                            🔄 Coba Minta Izin Ulang
                        </button>
                    </div>
                </div>
            </div>
                <!-- Overlay: status dan arah pandangan (tetap terlihat saat layar penuh) -->
                <div id="driverOverlay" class="hidden absolute left-2 top-2 flex flex-col items-start gap-1 pointer-events-none">
                    <span id="ovStatus" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-700 text-white">STANDBY</span>
                    <span id="ovPose" class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-black/70 text-white">Arah: -</span>
                </div>
                </div>
                <div class="bg-slate-50 px-4 py-2 border-t border-slate-200 text-xs text-slate-500">
                    <span id="fpsCounter" class="font-mono">FPS: 0 &middot; Resolusi: 640x480</span>
                </div>
            </section>

            <!-- Kamera Objek -->
            <section id="tileObject" class="cam-tile is-thumb" aria-label="Kamera Objek">
                <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kamera Objek (Jalan)</span>
                    <button type="button" onclick="swapCameras()" class="make-main-btn text-[11px] font-semibold text-blue-700 hover:underline cursor-pointer">Jadikan utama</button>
                </div>
                <div class="px-3 py-2 border-b border-slate-200 flex flex-wrap items-center gap-2">
                    <label for="objCamSelect" class="sr-only">Pilih kamera objek</label>
                    <select id="objCamSelect" class="min-w-0 flex-1 bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-2.5 py-1.5 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100 transition">
                        <option value="">Mencari kamera...</option>
                    </select>
                    <button id="objToggleBtn" type="button" onclick="toggleObjectCamera()"
                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer">
                        <span id="objToggleText">Nyalakan Kamera Objek</span>
                    </button>
                </div>
                <div id="objAlert" class="hidden px-3 py-2 bg-red-600 text-white text-xs font-bold flex items-center gap-2">
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
                <div class="bg-slate-50 px-3 py-2 border-t border-slate-200 text-xs text-slate-500 flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                    <span id="objStatus">Kamera objek belum aktif</span>
                    <span class="flex items-center gap-2.5">
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>Jauh</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>Sedang</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>Dekat</span>
                    </span>
                    <span id="objSummary" class="font-mono text-slate-600">-</span>
                </div>
            </section>

            <!-- Panel Detail (bisa di-scroll saat layar penuh) -->
            <aside id="detailPane" class="details-pane space-y-4" aria-label="Detail deteksi">
            <!-- Kartu Status Utama (White Card) -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Status Kewaspadaan Saat Ini</h3>
                <div class="flex items-center justify-between">
                    <div>
                        <div id="statusText" class="text-2xl font-extrabold text-slate-800 font-heading">STANDBY</div>
                        <p id="statusSub" class="text-xs text-slate-500 mt-0.5">Menunggu kamera aktif</p>
                    </div>
                    <div id="statusIcon" class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-3xl">
                        👤
                    </div>
                </div>

                <!-- Drowsiness Score Progress Bar -->
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-slate-600 font-medium">Skor Tingkat Kantuk:</span>
                        <span id="drowsyScoreText" class="font-bold text-slate-800">0%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div id="drowsyScoreBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Distraction Score Progress Bar -->
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

            <!-- Kartu Metrik Geometris Wajah (White Card) -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-4">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Metrik Landmark MediaPipe</h3>

                <!-- EAR (Eye Aspect Ratio) -->
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

                <!-- MAR (Mouth Aspect Ratio) -->
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

                <!-- Head Pose Yaw & Pitch -->
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
            </aside>
        </div>
    </div>
    </div>
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

    function playAlarmSound() {
        if (!isAudioEnabled) return;
        initAudioContext();
        if (isBeeping) return;

        isBeeping = true;
        try {
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();

            // Sirine nada ganda tajam (920Hz -> 680Hz -> 920Hz)
            osc.type = 'sawtooth';
            const now = audioCtx.currentTime;
            osc.frequency.setValueAtTime(920, now);
            osc.frequency.linearRampToValueAtTime(680, now + 0.15);
            osc.frequency.linearRampToValueAtTime(920, now + 0.3);

            gain.gain.setValueAtTime(0.4, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);

            osc.connect(gain);
            gain.connect(audioCtx.destination);

            osc.start(now);
            osc.stop(now + 0.36);

            setTimeout(() => {
                isBeeping = false;
            }, 380);
        } catch (e) {
            console.error("Audio error:", e);
            isBeeping = false;
        }
    }

    function stopBuzzer() {
        isBeeping = false;
    }

    function testAlarm() {
        initAudioContext();
        isAudioEnabled = true;
        const icon = document.getElementById('audioIcon');
        const text = document.getElementById('audioText');
        const btn = document.getElementById('toggleAudioBtn');
        if (icon) icon.innerText = '🔊';
        if (text) text.innerText = 'Audio Alarm: AKTIF';
        if (btn) btn.className = "px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl border border-slate-700 flex items-center gap-2 transition";
        playAlarmSound();
    }

    function toggleAudio() {
        isAudioEnabled = !isAudioEnabled;
        const icon = document.getElementById('audioIcon');
        const text = document.getElementById('audioText');
        const btn = document.getElementById('toggleAudioBtn');

        if (isAudioEnabled) {
            initAudioContext();
            icon.innerText = '🔊';
            text.innerText = 'Audio Alarm: AKTIF';
            btn.className = "px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl border border-slate-700 flex items-center gap-2 transition";
        } else {
            icon.innerText = '🔇';
            text.innerText = 'Audio Alarm: SENYAP';
            btn.className = "px-3.5 py-2 bg-red-900/40 hover:bg-red-900/60 text-red-300 text-xs font-semibold rounded-xl border border-red-700/60 flex items-center gap-2 transition";
        }
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
            displayCtx.drawImage(videoEl, 0, 0, 640, 480);

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
            displayCtx.drawImage(videoEl, 0, 0, 640, 480);

            // Anotasi Landmark & Bounding Box Wajah
            if (data.face_detected) {
                consecNoFaceFrames = 0;
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
            if (data.is_alarm_active) {
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
        if (data.is_alarm_active) {
            alarmBanner.classList.remove('hidden');
            alarmTitle.innerText = "PERINGATAN: " + (data.alarm_type || "BAHAYA");
            alarmDesc.innerText = data.alarm_message || "Kondisi berbahaya terdeteksi!";
            statusText.innerText = data.alarm_type || "BAHAYA";
            statusText.className = "text-2xl font-black text-red-500";
            statusIcon.className = "w-14 h-14 rounded-2xl bg-red-500/20 border border-red-500/40 flex items-center justify-center text-3xl";
            statusIcon.innerText = "🚨";
            statusSub.innerText = data.alarm_message;

            playAlarmSound();
        } else {
            alarmBanner.classList.add('hidden');
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
    const fsText = document.getElementById('fsText');
    const fsBtn = document.getElementById('fsBtn');
    const driverOverlay = document.getElementById('driverOverlay');
    const ovStatus = document.getElementById('ovStatus');
    const ovPose = document.getElementById('ovPose');

    function swapCameras() {
        const driverIsMain = tileDriver.classList.contains('is-main');
        tileDriver.classList.toggle('is-main', !driverIsMain);
        tileDriver.classList.toggle('is-thumb', driverIsMain);
        tileObject.classList.toggle('is-main', driverIsMain);
        tileObject.classList.toggle('is-thumb', !driverIsMain);
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

    function setFsUi(on) {
        clusterEl.classList.toggle('fs-on', on);
        fsText.innerText = on ? 'Keluar Layar Penuh' : 'Layar Penuh';
        fsBtn.setAttribute('aria-pressed', on ? 'true' : 'false');
    }
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

            if (nearest) {
                objAlert.classList.remove('hidden');
                objAlertText.innerText = 'Objek terlalu dekat: ' + nearest.label + '! Kurangi kecepatan.';
                if (Date.now() - objLastBeep > 1500) { objLastBeep = Date.now(); playAlarmSound(); }
            } else {
                objAlert.classList.add('hidden');
            }
        } catch (e) {
            console.error('Deteksi objek error:', e);
        } finally {
            objBusy = false;
        }
    }
</script>
@endsection
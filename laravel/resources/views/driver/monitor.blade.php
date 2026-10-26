<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Kabin Pengemudi - DriveSafe</title>
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
<body class="bg-slate-950 text-white min-h-screen font-sans antialiased flex flex-col justify-between selection:bg-slate-800 selection:text-white">

    <!-- Topbar Kabin -->
    <header class="h-14 border-b border-slate-900 px-6 flex items-center justify-between bg-slate-950/80 sticky top-0 z-30">
        <div class="flex items-center gap-3">
            <a href="{{ route('driver.dashboard') }}" class="text-slate-400 hover:text-white transition-colors text-xs flex items-center gap-1.5">
                <span>&larr;</span>
                <span>Kembali</span>
            </a>
            <span class="text-slate-700">|</span>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold text-white tracking-wide">KABIN MONITORING AKTIF</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" id="toggleAudioBtn" onclick="toggleAudio()" 
                    class="h-7.5 px-3 bg-slate-900 hover:bg-slate-800 text-slate-300 rounded border border-slate-800 text-xs font-mono transition-colors flex items-center gap-1.5 cursor-pointer">
                <span id="audioStatusText">Buzzer: AKTIF</span>
            </button>
            <button type="button" onclick="testAudioAlarm()" 
                    class="h-7.5 px-3 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 rounded border border-amber-500/20 text-xs font-mono transition-colors cursor-pointer">
                Tes Alarm
            </button>
            <span class="text-xs text-slate-400 font-mono hidden sm:inline">{{ $user->full_name }}</span>
        </div>
    </header>

    <!-- Main Viewport Kamera -->
    <main class="flex-1 flex flex-col items-center justify-center p-4 sm:p-6 max-w-5xl mx-auto w-full">
        <div class="w-full bg-slate-900 border border-slate-800 rounded-lg overflow-hidden relative shadow-2xl flex flex-col items-center justify-center min-h-[420px]">
            <!-- Video Element -->
            <video id="webcamVideo" autoplay playsinline muted class="w-full h-auto max-h-[500px] object-contain bg-black"></video>

            <!-- Status Overlay Bar -->
            <div class="absolute top-4 left-4 right-4 flex items-center justify-between pointer-events-none">
                <div class="bg-slate-950/80 backdrop-blur-md px-3 py-1.5 rounded border border-slate-800 text-xs font-mono flex items-center gap-2">
                    <span class="text-slate-400">STATUS:</span>
                    <span id="detectionLabel" class="text-emerald-400 font-bold">TERJAGA / NORMAL</span>
                </div>

                <div class="bg-slate-950/80 backdrop-blur-md px-3 py-1.5 rounded border border-slate-800 text-xs font-mono flex items-center gap-3 tabular-nums">
                    <span>EAR: <strong id="earValue" class="text-white">0.32</strong></span>
                    <span>MAR: <strong id="marValue" class="text-white">0.12</strong></span>
                </div>
            </div>

            <!-- Fullscreen Critical Alarm Flash Overlay -->
            <div id="alarmOverlay" class="absolute inset-0 bg-rose-950/70 border-4 border-rose-600 hidden flex items-center justify-center pointer-events-none transition-all">
                <div class="text-center space-y-2 p-6 bg-slate-950/90 rounded-lg border border-rose-500/50">
                    <div class="text-rose-500 text-4xl animate-bounce font-bold">&#9888;</div>
                    <h2 class="text-xl font-bold text-white tracking-wider uppercase" id="alarmTitle">PERINGATAN BAHAYA</h2>
                    <p class="text-xs text-rose-300 font-mono" id="alarmMsg">MATA TERPEJAM LEBIH DARI 1.2 DETIK!</p>
                </div>
            </div>

            <!-- Standby Placeholder if Camera Not Started -->
            <div id="cameraStandby" class="absolute inset-0 bg-slate-950 flex flex-col items-center justify-center p-6 text-center space-y-4">
                <div class="w-12 h-12 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 text-xl font-bold">
                    &#128247;
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Nyalakan Kamera Kabin</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm">Izinkan browser mengakses webcam pengemudi untuk memulai tracking MediaPipe & AI.</p>
                </div>
                <button type="button" onclick="startCamera()" 
                        class="px-5 py-2 bg-white text-slate-950 hover:bg-slate-200 font-medium text-xs rounded transition-colors cursor-pointer">
                    Mulai Kamera Sekarang
                </button>
            </div>
        </div>
    </main>

    <!-- Footer Status Telemetri -->
    <footer class="h-10 border-t border-slate-900 px-6 flex items-center justify-between text-[11px] font-mono text-slate-500 bg-slate-950">
        <div>Engine AI: Random Forest Classifier (Python Service Active)</div>
        <div>Throughput: 30 FPS &bull; Latensi &lt; 15ms</div>
    </footer>

    <script>
        let isAudioActive = true;
        let audioCtx = null;

        function getAudioContext() {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            return audioCtx;
        }

        function playBeep(freq = 880, duration = 0.25) {
            if (!isAudioActive) return;
            try {
                const ctx = getAudioContext();
                if (ctx.state === 'suspended') ctx.resume();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'square';
                osc.frequency.setValueAtTime(freq, ctx.currentTime);
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + duration);
            } catch (e) {
                console.error("Audio error", e);
            }
        }

        function toggleAudio() {
            isAudioActive = !isAudioActive;
            document.getElementById('audioStatusText').textContent = isAudioActive ? 'Buzzer: AKTIF' : 'Buzzer: SENYAP';
        }

        function testAudioAlarm() {
            playBeep(920, 0.4);
            const overlay = document.getElementById('alarmOverlay');
            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 800);
        }

        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { width: { ideal: 640 }, height: { ideal: 480 } }, 
                    audio: false 
                });
                const video = document.getElementById('webcamVideo');
                video.srcObject = stream;
                document.getElementById('cameraStandby').classList.add('hidden');
            } catch (err) {
                alert("Gagal mengakses webcam: " + err.message);
            }
        }
    </script>
</body>
</html>


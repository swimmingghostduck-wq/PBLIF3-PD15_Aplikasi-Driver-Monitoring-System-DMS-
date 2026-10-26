<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <span>Dashboard</span>
            </a>
            <span class="text-slate-700">|</span>
            <div class="flex items-center gap-2">
                <span id="liveDot" class="w-2 h-2 rounded-full bg-slate-600"></span>
                <span id="liveStatusText" class="text-xs font-semibold text-slate-300 tracking-wide font-mono">STANDBY</span>
            </div>
            <span class="text-slate-700 hidden sm:inline">|</span>
            <div class="hidden sm:flex items-center gap-1.5 text-[11px] font-mono">
                <span class="text-slate-500">AI Service (5000):</span>
                <span id="aiServiceStatus" class="text-amber-400 font-bold">MENGECEK...</span>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button" id="toggleAudioBtn" onclick="toggleAudio()" 
                    class="h-7.5 px-2.5 bg-slate-900 hover:bg-slate-800 text-slate-300 rounded border border-slate-800 text-xs font-mono transition-colors flex items-center gap-1.5 cursor-pointer">
                <span id="audioStatusText">Buzzer: AKTIF</span>
            </button>
            <button type="button" onclick="testAudioAlarm()" 
                    class="h-7.5 px-2.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 rounded border border-amber-500/20 text-xs font-mono transition-colors cursor-pointer">
                Tes Alarm
            </button>
            <span class="text-xs text-slate-400 font-mono hidden md:inline ml-2">{{ $user->full_name }}</span>
        </div>
    </header>

    <!-- Main Viewport Kamera & Anotasi -->
    <main class="flex-1 flex flex-col items-center justify-center p-3 sm:p-6 max-w-5xl mx-auto w-full">
        <div class="w-full bg-slate-900 border border-slate-800 rounded-lg overflow-hidden relative shadow-2xl flex flex-col items-center justify-center min-h-[440px]">
            
            <!-- Hidden Video Stream dari Browser -->
            <video id="webcamVideo" autoplay playsinline muted class="hidden"></video>
            <!-- Hidden Offscreen Canvas untuk Sampling Frame -->
            <canvas id="captureCanvas" width="640" height="480" class="hidden"></canvas>
            
            <!-- Canvas Tampilan Utama dengan Visual Landmark Real-Time -->
            <canvas id="displayCanvas" width="640" height="480" class="w-full h-auto max-h-[500px] object-contain bg-black hidden"></canvas>

            <!-- Status Overlay Bar Atas -->
            <div id="hudOverlay" class="absolute top-3 left-3 right-3 flex items-center justify-between pointer-events-none hidden">
                <div class="bg-slate-950/85 backdrop-blur-md px-3 py-1.5 rounded border border-slate-800 text-xs font-mono flex items-center gap-2">
                    <span class="text-slate-400">STATUS:</span>
                    <span id="detectionLabel" class="text-emerald-400 font-bold uppercase">TERJAGA / NORMAL</span>
                </div>

                <div class="bg-slate-950/85 backdrop-blur-md px-3 py-1.5 rounded border border-slate-800 text-xs font-mono flex items-center gap-3 tabular-nums">
                    <span>EAR: <strong id="earValue" class="text-white">0.00</strong></span>
                    <span>MAR: <strong id="marValue" class="text-white">0.00</strong></span>
                    <span class="hidden sm:inline">YAW: <strong id="yawValue" class="text-white">0.0&deg;</strong></span>
                </div>
            </div>

            <!-- Fullscreen Critical Alarm Flash Overlay -->
            <div id="alarmOverlay" class="absolute inset-0 bg-rose-950/85 border-4 border-rose-600 hidden flex items-center justify-center pointer-events-none transition-all z-20">
                <div class="text-center space-y-2 p-6 bg-slate-950/95 rounded-lg border border-rose-500/60 shadow-2xl max-w-sm mx-4">
                    <div class="text-rose-500 text-4xl animate-bounce font-bold">&#9888;</div>
                    <h2 class="text-lg font-bold text-white tracking-wider uppercase" id="alarmTitle">PERINGATAN BAHAYA</h2>
                    <p class="text-xs text-rose-300 font-mono" id="alarmMsg">MATA TERPEJAM MELEBIHI AMBANG BATAS!</p>
                </div>
            </div>

            <!-- Standby Placeholder Sebelum Kamera Dimulai -->
            <div id="cameraStandby" class="absolute inset-0 bg-slate-950 flex flex-col items-center justify-center p-6 text-center space-y-4">
                <div class="w-12 h-12 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 text-xl font-bold">
                    &#128247;
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Mulai Monitoring Kabin</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm">
                        Aplikasi akan mengakses webcam dan menghubungkannya dengan Microservice AI Python (MediaPipe & Random Forest) secara real-time.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" id="startCamBtn" onclick="startCamera()" 
                            class="px-5 py-2.5 bg-white text-slate-950 hover:bg-slate-200 font-medium text-xs rounded transition-colors cursor-pointer flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Nyalakan Kamera Kabin</span>
                    </button>
                </div>
                <p id="camErrorNotice" class="text-xs text-rose-400 font-mono hidden"></p>
            </div>
        </div>

        <!-- Tombol Kontrol Bawah Kamera -->
        <div id="cameraControls" class="mt-4 flex items-center justify-between w-full hidden">
            <button type="button" onclick="stopCamera()" 
                    class="h-8 px-3 bg-slate-900 hover:bg-slate-800 text-rose-400 border border-slate-800 rounded text-xs font-mono transition-colors cursor-pointer flex items-center gap-1.5">
                <span>&times;</span>
                <span>Hentikan Kamera</span>
            </button>
            <div class="text-xs font-mono text-slate-400 flex items-center gap-3 tabular-nums">
                <span id="fpsCounter">FPS: 0</span>
                <span class="text-slate-700">|</span>
                <span id="consecDrowsyText">Durasi Bahaya: 0.0s</span>
            </div>
        </div>
    </main>

    <!-- Footer Status Telemetri -->
    <footer class="h-10 border-t border-slate-900 px-6 flex items-center justify-between text-[11px] font-mono text-slate-500 bg-slate-950">
        <div>Engine AI: <span id="modelEngineLabel" class="text-slate-400">Random Forest Classifier</span></div>
        <div class="flex items-center gap-2">
            <span>Server AI:</span>
            <span class="text-slate-400">http://127.0.0.1:5000</span>
        </div>
    </footer>

    <script>
        const AI_API_URL = "http://127.0.0.1:5000/driver/process_frame";
        const LARAVEL_LOG_URL = "{{ route('driver.log_alert') }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const DRIVER_ID = {{ $user->id }};

        let currentStream = null;
        let isProcessing = false;
        let frameInterval = null;
        let isAudioActive = true;
        let audioCtx = null;
        let alarmBeepInterval = null;

        let fpsFrames = 0;
        let lastFpsTime = Date.now();

        const videoEl = document.getElementById('webcamVideo');
        const captureCanvas = document.getElementById('captureCanvas');
        const captureCtx = captureCanvas.getContext('2d');
        const displayCanvas = document.getElementById('displayCanvas');
        const displayCtx = displayCanvas.getContext('2d');

        // Cek status service Python saat halaman dibuka
        async function checkAIService() {
            const statusEl = document.getElementById('aiServiceStatus');
            try {
                const res = await fetch("http://127.0.0.1:5000/driver/status_feed", {
                    method: "GET",
                    headers: { "Accept": "application/json" }
                });
                if (res.ok) {
                    statusEl.textContent = "ONLINE (TERHUBUNG)";
                    statusEl.className = "text-emerald-400 font-bold";
                } else {
                    statusEl.textContent = "TIDAK RESPON";
                    statusEl.className = "text-rose-400 font-bold";
                }
            } catch (e) {
                statusEl.textContent = "OFFLINE (Jalankan: python run.py)";
                statusEl.className = "text-rose-400 font-bold";
            }
        }
        checkAIService();

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
            const errEl = document.getElementById('camErrorNotice');
            errEl.classList.add('hidden');

            try {
                currentStream = await navigator.mediaDevices.getUserMedia({ 
                    video: { width: { ideal: 640 }, height: { ideal: 480 } }, 
                    audio: false 
                });
                videoEl.srcObject = currentStream;
                await videoEl.play();

                document.getElementById('cameraStandby').classList.add('hidden');
                document.getElementById('cameraControls').classList.remove('hidden');
                document.getElementById('hudOverlay').classList.remove('hidden');
                displayCanvas.classList.remove('hidden');

                document.getElementById('liveDot').className = "w-2 h-2 rounded-full bg-emerald-500 animate-pulse";
                document.getElementById('liveStatusText').textContent = "MONITORING AKTIF";
                document.getElementById('liveStatusText').className = "text-xs font-semibold text-emerald-400 tracking-wide font-mono";

                // Mulai loop inferensi frame ke Python AI (~12 FPS / 80ms)
                if (frameInterval) clearInterval(frameInterval);
                frameInterval = setInterval(sendFrameToAI, 85);
            } catch (err) {
                console.error("Webcam Error:", err);
                errEl.textContent = "Gagal mengakses webcam: " + err.message + ". Pastikan izin kamera telah diberikan.";
                errEl.classList.remove('hidden');
            }
        }

        function stopCamera() {
            if (frameInterval) {
                clearInterval(frameInterval);
                frameInterval = null;
            }
            if (currentStream) {
                currentStream.getTracks().forEach(t => t.stop());
                currentStream = null;
            }
            videoEl.srcObject = null;

            displayCtx.clearRect(0, 0, displayCanvas.width, displayCanvas.height);
            displayCanvas.classList.add('hidden');
            document.getElementById('hudOverlay').classList.add('hidden');
            document.getElementById('cameraControls').classList.add('hidden');
            document.getElementById('cameraStandby').classList.remove('hidden');
            document.getElementById('alarmOverlay').classList.add('hidden');

            document.getElementById('liveDot').className = "w-2 h-2 rounded-full bg-slate-600";
            document.getElementById('liveStatusText').textContent = "STANDBY";
            document.getElementById('liveStatusText').className = "text-xs font-semibold text-slate-300 tracking-wide font-mono";

            if (alarmBeepInterval) {
                clearInterval(alarmBeepInterval);
                alarmBeepInterval = null;
            }
        }

        async function sendFrameToAI() {
            if (!currentStream || isProcessing || videoEl.paused || videoEl.ended) return;

            isProcessing = true;
            try {
                captureCtx.drawImage(videoEl, 0, 0, 640, 480);
                const dataUrl = captureCanvas.toDataURL('image/jpeg', 0.65);

                // Hitung FPS
                fpsFrames++;
                const now = Date.now();
                if (now - lastFpsTime >= 1000) {
                    document.getElementById('fpsCounter').textContent = `FPS: ${fpsFrames}`;
                    fpsFrames = 0;
                    lastFpsTime = now;
                }

                // Kirim frame ke Microservice Python di port 5000
                const res = await fetch(AI_API_URL, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ 
                        image: dataUrl,
                        driver_id: DRIVER_ID
                    })
                });

                if (!res.ok) {
                    throw new Error(`AI Service HTTP ${res.status}`);
                }

                const data = await res.json();

                // Gambar video asli ke canvas tampilan
                displayCtx.drawImage(videoEl, 0, 0, 640, 480);

                // Gambar Landmark & Bounding Box jika wajah terdeteksi
                if (data.face_detected) {
                    if (data.bbox) {
                        displayCtx.strokeStyle = data.is_alarm_active ? '#e11d48' : '#10b981';
                        displayCtx.lineWidth = 2;
                        displayCtx.strokeRect(data.bbox[0], data.bbox[1], data.bbox[2], data.bbox[3]);
                    }

                    // Gambar titik landmark mata kiri & kanan
                    displayCtx.fillStyle = data.is_alarm_active ? '#f43f5e' : '#38bdf8';
                    if (data.left_eye_pts) {
                        data.left_eye_pts.forEach(pt => {
                            displayCtx.beginPath();
                            displayCtx.arc(pt[0], pt[1], 1.5, 0, 2 * Math.PI);
                            displayCtx.fill();
                        });
                    }
                    if (data.right_eye_pts) {
                        data.right_eye_pts.forEach(pt => {
                            displayCtx.beginPath();
                            displayCtx.arc(pt[0], pt[1], 1.5, 0, 2 * Math.PI);
                            displayCtx.fill();
                        });
                    }
                }

                // Update Nilai HUD
                document.getElementById('earValue').textContent = Number(data.ear_avg || 0).toFixed(2);
                document.getElementById('marValue').textContent = Number(data.mar || 0).toFixed(2);
                document.getElementById('yawValue').textContent = `${Number(data.yaw || 0).toFixed(1)}°`;

                // Update Status Label
                const labelEl = document.getElementById('detectionLabel');
                const overlay = document.getElementById('alarmOverlay');

                if (data.is_alarm_active) {
                    labelEl.textContent = data.alarm_type === 'kantuk' ? 'BAHAYA: MENGANTUK' : 'PERINGATAN: TIDAK FOKUS';
                    labelEl.className = "text-rose-400 font-bold uppercase animate-pulse";

                    document.getElementById('alarmTitle').textContent = data.alarm_type === 'kantuk' ? 'PERINGATAN KANTUK' : 'PERINGATAN DISTRAKSI';
                    document.getElementById('alarmMsg').textContent = data.alarm_message || 'FOKUSKAN PANDANGAN KE DEPAN!';
                    overlay.classList.remove('hidden');

                    // Mainkan buzzer berulang
                    if (!alarmBeepInterval) {
                        playBeep(940, 0.3);
                        alarmBeepInterval = setInterval(() => playBeep(940, 0.3), 500);
                    }

                    // Jika ini awal mula insiden (event_phase start), kirim log ke database Laravel
                    if (data.event_phase === 'start' && data.alarm_type) {
                        logIncidentToLaravel(data.alarm_type, data.alarm_message, data.snapshot_file);
                    }

                } else {
                    labelEl.textContent = data.face_detected ? (data.status_label || 'TERJAGA / NORMAL') : 'WAJAH TIDAK TERLIHAT';
                    labelEl.className = data.face_detected ? 'text-emerald-400 font-bold uppercase' : 'text-amber-400 font-bold uppercase';
                    overlay.classList.add('hidden');

                    if (alarmBeepInterval) {
                        clearInterval(alarmBeepInterval);
                        alarmBeepInterval = null;
                    }
                }

                if (data.method) {
                    document.getElementById('modelEngineLabel').textContent = data.method;
                }

            } catch (err) {
                console.error("AI inference error:", err);
                document.getElementById('aiServiceStatus').textContent = "TERPUTUS / ERROR";
                document.getElementById('aiServiceStatus').className = "text-rose-400 font-bold";
            } finally {
                isProcessing = false;
            }
        }

        async function logIncidentToLaravel(alertType, notes, snapshotFile) {
            try {
                await fetch(LARAVEL_LOG_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        alert_type: alertType,
                        severity: alertType === 'kantuk' ? 'BAHAYA' : 'PERINGATAN',
                        duration_seconds: 2.0,
                        snapshot_path: snapshotFile || null,
                        notes: notes || 'Insiden terdeteksi oleh modul kamera real-time'
                    })
                });
            } catch (e) {
                console.error("Gagal menyimpan log insiden ke Laravel:", e);
            }
        }
    </script>
</body>
</html>

# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Pengemudi Kendaraan (Primary)**: Mengoperasikan kendaraan sambil memantau status kewaspadaan secara langsung di kabin melalui layar dan peringatan audio real-time tanpa terganggu oleh alarm palsu saat aktivitas wajar (melirik spion, menggaruk kepala).
- **Admin / Manajer Armada (Secondary)**: Mengawasi rekaman log keselamatan, meninjau snapshot bukti insiden bahaya, serta mengelola akun dan sesi pengemudi.
- **Penguji / Dosen PBL (Stakeholder)**: Mengevaluasi implementasi Computer Vision, akurasi klasifikasi AI, serta kepatuhan sistem terhadap spesifikasi fungsional PBL IF314.

## Product Purpose

Memberikan sistem pemantauan pengemudi (Driver Monitoring System) berbasis web lokal yang andal, cepat, dan terjangkau untuk menekan angka kecelakaan lalu lintas akibat kelelahan dan kelalaian mengemudi secara real-time.

## Positioning

Sistem DMS cerdas berbasis browser lokal yang mengombinasikan pelacakan landmark MediaPipe (Iris Gaze Tracking + Head Pose 3D via solvePnP) dan Machine Learning (Random Forest) dengan penanganan toleransi temporal alami, membedakan kedipan mata normal vs kantuk serta melirik spion / menggaruk kepala vs distraksi nyata tanpa memerlukan sensor perangkat keras mahal.

## Operating Context

- Digunakan di dalam kabin kendaraan atau workstation simulasi pengujian.
- Lingkungan pencahayaan bervariasi dengan kamera webcam standar laptop/dashboard.
- Tampilan antarmuka berorientasi *Operate*: kontras tinggi, tema gelap (dark mode) bernuansa *cockpit telematics* yang nyaman di mata pengemudi, metrik telemetri yang terbaca jelas dalam sekilas pandang, serta peringatan suara tajam melalui Web Audio API.

## Capabilities and Constraints

- **Deteksi Kantuk (EAR)**: Eye Aspect Ratio untuk mengidentifikasi mata terpejam $\ge 1.2$ detik (kedipan normal 1–4 frame diabaikan).
- **Deteksi Menguap (MAR)**: Mouth Aspect Ratio untuk mengidentifikasi bukaan mulut indikasi kelelahan ($\ge 0.65$).
- **Deteksi Distraksi (Head Pose & Iris Gaze)**: Mengombinasikan rotasi sudut kepala ($Yaw \ge 25^\circ$, $Pitch \ge 22^\circ$) dengan arah bola mata (Iris Gaze) dengan batas toleransi 3 detik untuk mencegah alarm palsu.
- **Logging & Snapshot Bukti**: Penyimpanan tangkapan layar otomatis ke database saat terjadi event kantuk atau distraksi bahaya.
- **Fallback Database**: Otomatis beralih ke SQLite lokal jika server MySQL Laragon/XAMPP tidak aktif.
- **Batasan Teknis**: Berjalan lokal pada browser modern (Chrome/Edge) dengan izin akses webcam (HTTPS atau localhost).

## Brand Commitments

- Nama: Driver Monitoring System (DMS) - PBL IF314 PD-15.
- Nuansa: Profesional, fokus keselamatan berkendara (Safety-first), modern, berteknologi tinggi (*cockpit telematics*), bersih, dan tidak mengalihkan perhatian pengemudi saat kondisi aman.

## Evidence on Hand

- Dataset fitur fisiologis wajah (`dataset/dataset_features.csv`) dengan 1.447 sampel tervalidasi.
- Model terlatih Random Forest (`models/trained_model.joblib`) dengan akurasi 99.66%.
- Pipeline pengujian otomatis (`test_system.py`) dengan 11 rangkaian uji validasi sistem.
- Laporan PBL Bab 3 & Bab 4 terstruktur (`Laporan_PBL_IF314_DMS_Bab3_Bab4.docx`).

## Product Principles

1. **Safety First, Zero False Alarm**: Sistem harus membedakan gerak alami manusia (kedipan mata, menggaruk kepala, melirik spion) dari bahaya nyata agar pengemudi percaya dan tidak mematikan sistem.
2. **Instant Clarity (Glanceable Telemetry)**: Informasi status (Aman, Waspada, Bahaya) harus dapat dipahami dalam waktu kurang dari 0.5 detik melalui kode warna dan ikonografi yang tegas.
3. **Local & Resilient Execution**: Pemrosesan inferensi dan fallback basis data berjalan sepenuhnya di perangkat lokal pengemudi tanpa ketergantungan koneksi internet eksternal saat berkendara.
4. **Actionable Accountability**: Setiap insiden bahaya terdokumentasi lengkap dengan timestamp, snapshot foto, dan metrik fisiologis untuk keperluan audit keselamatan.


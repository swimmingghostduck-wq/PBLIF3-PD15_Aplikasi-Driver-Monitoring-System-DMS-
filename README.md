# Driver Monitoring System (DMS) & Model Training Suite
**Proyek PBL IF3-PD15 — Teknik Informatika, Politeknik Negeri Batam**

Sistem Pemantauan Pengemudi (*Driver Monitoring System*) berbasis AI dan Computer Vision real-time untuk mendeteksi kondisi kantuk (*drowsiness*) dan ketidakfokusan pengemudi menggunakan kamera/webcam. 

Aplikasi ini menggunakan arsitektur hybrid modern:
- **Fullstack Web & Dashboard Admin (Laravel 12 / PHP 8.5+ & Blade)**: Menangani antarmuka operasional utilitarian bergaya Linear/Vercel (*anti-AI slop*), autentikasi, otorisasi peran, manajemen data pengemudi, log insiden, dan audit forensik.
- **AI & Computer Vision Microservice (Python & MediaPipe)**: Menangani deteksi wajah real-time (468 landmark), kalkulasi EAR/MAR, estimasi Head Pose (Yaw/Pitch), klasifikasi Random Forest kustom, serta streaming video feed.
- **Training Suite Mandiri**: Alat mandiri untuk merekam dataset wajah dan melatih ulang model AI kustom.

---

## 👥 Tim Pengembang
- **Ketua Tim**: Apri Catur Pramudiansyah (NIM: 3312501067)
- **Anggota Tim**: Nayla Azkyah Azra (NIM: 3312501061)
- **Manajer Proyek / Klien**: Ari Wibowo, S.T., M.T

---

## 📌 Keselarasan Kebutuhan Proyek (SRS / Use Case)
Sistem ini mengimplementasikan seluruh kebutuhan fungsional (FR) dan non-fungsional (NFR) dari dokumen **Praktikum 3 Use Case Diagram** dan **RPP PBL IF3-PD15**:

| Kode | Deskripsi Kebutuhan | Status Implementasi |
|---|---|---|
| **FR-01** | Driver dapat membuat akun baru | Selesai (`/register`) |
| **FR-02** | Pengguna dapat melakukan Login | Selesai (`/login`) |
| **FR-03** | Akses kamera/webcam sebagai input video | Selesai (Kamera Browser WebRTC & Fallback OpenCV) |
| **FR-04** | Deteksi wajah dan landmark mata real-time | Selesai (MediaPipe Face Mesh 468 titik) |
| **FR-05** | Deteksi kondisi pengemudi yang mengantuk | Selesai (Model AI Random Forest & kalkulasi EAR) |
| **FR-06** | Deteksi arah pandangan/kepala (menoleh) | Selesai (Estimasi Head Pose `solvePnP` Yaw/Pitch) |
| **FR-09** | Pemberian peringatan saat kondisi bahaya | Selesai (`dms_core/tracker.py`) |
| **FR-10** | Peringatan suara (Web Audio) & visual | Selesai (Synthesizer Web Audio API + HUD Alert) |
| **FR-11** | Pengguna dapat melakukan Logout | Selesai (`/logout`) |
| **FR-12** | Admin dapat mengelola data Driver (CRUD) | Selesai (`/admin/drivers`) |
| **FR-13** | Admin memantau hasil monitoring driver | Selesai (`/admin/dashboard` & `/admin/alerts`) |
| **FR-14** | Driver melihat riwayat peringatan pribadi | Selesai (`/driver/dashboard`) |
| **NFR-01** | Antarmuka & dokumentasi Bahasa Indonesia | Selesai (100% Bahasa Indonesia) |
| **NFR-03** | Peringatan dalam waktu $\le$ 3 detik | Selesai (Deteksi real-time responsif) |
| **NFR-05** | Akurasi deteksi model $\ge 80\%$ | Selesai (Evaluasi test set mencapai $\ge 95\%$) |

---

## 📂 Struktur Direktori Proyek

```
PBLIF3-PD15_Aplikasi-Driver-Monitoring-System-DMS-/
├── laravel/                       # Aplikasi Web Utama (Laravel Framework)
│   ├── app/
│   │   ├── Http/Controllers/     # AuthController, AdminController, DriverController
│   │   ├── Http/Middleware/      # EnsureAdmin (Otorisasi Akses Admin)
│   │   └── Models/               # User, MonitoringSession, AlertLog (Eloquent ORM)
│   ├── database/
│   │   ├── migrations/           # Skema tabel users, monitoring_sessions, alert_logs
│   │   └── seeders/              # DatabaseSeeder (Akun default Admin & Driver)
│   ├── resources/views/          # Blade Templates Utilitarian (Linear/Vercel style)
│   │   ├── admin/                # dashboard, alerts, drivers, models, profile
│   │   ├── auth/                 # login, register
│   │   ├── driver/               # dashboard, monitor (Webcam HUD)
│   │   └── layouts/              # admin.blade.php (Hairstyle hairline borders)
│   ├── routes/web.php            # 25 Web Routes aplikasi
│   ├── public/snapshots/         # Foto bukti rekaman insiden kamera
│   └── tests/Feature/            # Automated Feature & Web Tests (PHPUnit)
├── dms_core/                      # Core Engine Computer Vision & AI
│   ├── detector.py                # MediaPipe Face Mesh, EAR, MAR, Head Pose (solvePnP)
│   ├── classifier.py              # Inferensi Model Custom (Scikit-Learn/PyTorch)
│   ├── tracker.py                 # Pelacak durasi frame kantuk/distraksi & pemicu alarm
│   └── alert_manager.py           # Manajemen snapshot kamera & pencatatan log
├── training_suite/                # Suite Pelatihan Model AI Mandiri
│   ├── data_collector.py          # GUI Webcam untuk merekam sampel dataset wajah
│   ├── dataset_loader.py          # Pemuat dataset & stratified split train-test
│   ├── train_sklearn.py           # Pelatihan model (Random Forest / SVM / GBDT)
│   ├── train_pytorch.py           # Pelatihan Deep Learning PyTorch MLP
│   └── evaluate.py                # Evaluasi akurasi & generator Confusion Matrix PNG
├── dataset/                       # File dataset CSV dan data rekaman fitur
├── models/                        # Bobot model terlatih (.joblib) & gambar confusion matrix
├── run.py                         # Skrip peluncur Service API & Deteksi AI (Python Flask)
└── requirements.txt               # Dependensi pustaka Python
```

---

## 🚀 Panduan Menjalankan Aplikasi

Sistem ini terdiri dari dua komponen yang saling terhubung:
1. **Web Dashboard & Antarmuka Pengguna (Laravel)**
2. **Service Deteksi AI & Kamera (Python)**

---

### Langkah 1: Persiapan Lingkungan

Pastikan laptop Anda telah terpasang:
- **Laragon / PHP 8.2+** (dengan Composer)
- **Python 3.10 atau 3.11** (rekomendasi terbaik untuk MediaPipe)
- **Webcam** (kamera bawaan laptop atau USB webcam)

---

### Langkah 2: Setup Service AI Python

Buka terminal PowerShell di folder root proyek:

```powershell
# 1. Buat virtual environment bernama 'venv' (jika belum ada)
python -m venv venv

# 2. Aktifkan virtual environment
.\venv\Scripts\Activate.ps1

# 3. Pasang dependensi Computer Vision & AI
pip install torch --index-url https://download.pytorch.org/whl/cpu
pip install -r requirements.txt
```

> **💡 Catatan PowerShell:** Jika muncul error *Script Execution Policy*, jalankan perintah:
> `Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass` lalu ulangi perintah aktivasi.

---

### Langkah 3: Setup Web Laravel & Database

Buka terminal PowerShell baru dan masuk ke folder `laravel`:

```powershell
cd laravel

# 1. Pasang dependensi PHP (jika belum terpasang)
composer install

# 2. Buat file .env dan generate Application Key (jika baru pertama kali clone)
copy .env.example .env
php artisan key:generate

# 3. Jalankan migrasi database dan masukkan data bawaan (seeder)
php artisan migrate --seed
```

---

### Langkah 4: Menjalankan Aplikasi

Jalankan kedua terminal secara berdampingan:

#### Terminal 1 — Menjalankan Web Laravel:
```powershell
cd laravel
php artisan serve
```
Aplikasi Web utama dapat diakses di:
👉 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

#### Terminal 2 — Menjalankan Service AI & Deteksi Kamera:
```powershell
# Pastikan berada di root direktori proyek
python run.py
```
Service Deteksi AI aktif di:
👉 **`http://127.0.0.1:5000`** *(API & Kamera Microservice)*

---

## 🔑 Akun Pengujian Bawaan (Default Login)

Database telah dilengkapi akun bawaan untuk pengujian langsung:

| Peran | Username | Password | Deskripsi |
|---|---|---|---|
| **Administrator** | `admin` | `admin123` | Akses penuh dashboard analitik, kelola pengemudi, riwayat peringatan, dan evaluasi model AI |
| **Driver Utama** | `apri` *(atau `driver`)* | `driver123` | Akses monitoring kabin real-time, HUD webcam, dan riwayat berkendara |
| **Driver Rekan** | `nayla` | `driver123` | Akun pengemudi kedua untuk pengujian multi-driver |

---

## 🧪 Pengujian Otomatis (Automated Testing)

Untuk memverifikasi keandalan seluruh rute, otentikasi, middleware otorisasi, dan basis data:

```powershell
cd laravel
php artisan test
```

Semua 10 skenario pengujian fitur (`DmsWebTest` & `ExampleTest`) akan berjalan dan menghasilkan status **PASSED (100%)**.

---

## 🧠 Pelatihan Model AI Mandiri (Training Suite)

Anda dapat melatih ulang model deteksi kantuk menggunakan wajah pengemudi:

### 1. Rekam Sampel Wajah via Webcam
```powershell
python training_suite/data_collector.py
```
- Tekan **`[1]`** untuk sampel kelas **TERJAGA** (mata terbuka normal).
- Tekan **`[2]`** untuk sampel kelas **MENGANTUK** (mata terpejam/sayu).
- Tekan **`[3]`** untuk sampel kelas **MENGUAP** (mulut terbuka lebar).
- Tekan **`[SPASI]`** untuk merekam kontinu 10 detik.
- Tekan **`[Q]`** atau **`[ESC]`** untuk selesai.

### 2. Latih Model Random Forest
```powershell
python training_suite/train_sklearn.py --model rf
```
Bobot model baru akan tersimpan di `models/trained_model.joblib`.

### 3. Evaluasi & Buat Confusion Matrix
```powershell
python training_suite/evaluate.py
```
Grafik matriks konfusi HD tersimpan di `models/confusion_matrix.png` dan dapat langsung diinspeksi melalui menu **Model AI & Evaluasi** di Web Admin Laravel.

---

## ❓ Solusi Masalah Teknis (Troubleshooting)

1. **Webcam tidak menyala / bentrok aplikasi lain:**
   - Tutup Zoom, Teams, Meet, atau aplikasi lain yang menggunakan webcam.
   - Jika proses Python lama menggantung, hentikan dengan:
     ```powershell
     Stop-Process -Name python, pythonw -Force -ErrorAction SilentlyContinue
     ```
2. **Koneksi Database:**
   - Secara default Laravel menggunakan SQLite (`database/database.sqlite`) yang sudah siap pakai tanpa konfigurasi tambahan.
   - Jika ingin menggunakan MySQL di Laragon: cukup ubah `DB_CONNECTION=mysql` di file `laravel/.env`, nyalakan MySQL di Laragon, lalu jalankan `php artisan migrate --seed`.

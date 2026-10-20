# Driver Monitoring System (DMS) & Model Training Suite
**Proyek PBL IF3-PD15 — Teknik Informatika, Politeknik Negeri Batam**

Sistem Pemantauan Pengemudi (*Driver Monitoring System*) berbasis AI dan Computer Vision real-time untuk mendeteksi kondisi kantuk (*drowsiness*) dan ketidakfokusan pengemudi menggunakan kamera/webcam. Dilengkapi dengan **Training Suite Mandiri** untuk merekam dataset dan melatih model AI kustom.

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
| **FR-01** | Driver dapat membuat akun baru | Selesai (`/auth/register`) |
| **FR-02** | Pengguna dapat melakukan Login | Selesai (`/auth/login`) |
| **FR-03** | Akses kamera/webcam sebagai input video | Selesai (`dms_core/detector.py`, `/driver/video_feed`) |
| **FR-04** | Deteksi wajah dan landmark mata real-time | Selesai (MediaPipe Face Mesh 468 titik) |
| **FR-05** | Deteksi kondisi pengemudi yang mengantuk | Selesai (Model AI kustom & fallback EAR/MAR) |
| **FR-06** | Deteksi arah pandangan/kepala (menoleh) | Selesai (Estimasi Head Pose `cv2.solvePnP` Yaw/Pitch) |
| **FR-09** | Pemberian peringatan saat kondisi bahaya | Selesai (`dms_core/tracker.py`) |
| **FR-10** | Peringatan suara (Web Audio) & visual | Selesai (Synthesizer Web Audio API + HUD Banner) |
| **FR-11** | Pengguna dapat melakukan Logout | Selesai (`/auth/logout`) |
| **FR-12** | Admin dapat mengelola data Driver (CRUD) | Selesai (`/admin/drivers`) |
| **FR-13** | Admin memantau hasil monitoring driver | Selesai (`/admin/dashboard`) |
| **FR-14** | Driver melihat riwayat peringatan pribadi | Selesai (`/driver/history`) |
| **NFR-01** | Antarmuka & dokumentasi Bahasa Indonesia | Selesai (100% Bahasa Indonesia) |
| **NFR-03** | Peringatan dalam waktu $\le$ 3 detik | Selesai (Deteksi real-time ~25-30 FPS) |
| **NFR-05** | Akurasi deteksi model $\ge 80\%$ | Selesai (Evaluasi test set mencapai $\ge 95\%$) |

---

## 📂 Struktur Direktori Proyek

```
dmsgacorsekali/
├── app/                           # Aplikasi Web Flask
│   ├── routes/
│   │   ├── auth.py                # Login, Register, Logout
│   │   ├── driver.py              # Monitoring Webcam, Telemetri, Riwayat Insiden
│   │   └── admin.py               # Dashboard Admin, Kelola Driver, Manajemen Model
│   ├── templates/                 # Antarmuka HTML5 Tailwind CSS Responsive
│   ├── static/snapshots/          # Foto bukti insiden otomatis
│   ├── models.py                  # Model Database SQLAlchemy (User, AlertLog, Session)
│   └── __init__.py                # Factory Flask & Auto-Fallback Database (MySQL/SQLite)
├── dms_core/                      # Core Engine Computer Vision
│   ├── detector.py                # MediaPipe Face Mesh, EAR, MAR, Head Pose (solvePnP)
│   ├── classifier.py              # Inferensi Model Custom (Scikit-Learn/PyTorch) & Proteksi Rambut
│   ├── tracker.py                 # Pelacak durasi frame kantuk/distraksi & pemicu alarm
│   └── alert_manager.py           # Penyimpan snapshot & log insiden
├── training_suite/                # Suite Pelatihan Model Mandiri
│   ├── data_collector.py          # GUI Webcam untuk merekam sampel dataset wajah
│   ├── dataset_loader.py          # Pemuat dataset, stratified train-test, & bootstrap sintetis
│   ├── train_sklearn.py           # Pelatihan Random Forest / SVM / Gradient Boosting
│   ├── train_pytorch.py           # Pelatihan Deep Learning PyTorch MLP
│   └── evaluate.py                # Evaluasi akurasi & pembuatan grafik Confusion Matrix
├── dataset/                       # Folder dataset CSV dan data rekaman
├── models/                        # Folder file bobot model (.joblib, .pt) dan grafik PNG
├── config.py                      # Konfigurasi aplikasi dan koneksi MySQL/SQLite
├── init_db.py                     # Skrip inisialisasi tabel dan akun default
├── run.py                         # Skrip peluncur server web Flask
├── test_system.py                 # Unit test otomatis komprehensif (9 Test Cases)
└── requirements.txt               # Daftar dependensi Python
```

---

## 💻 Panduan Instalasi & Menjalankan di Laptop Baru

Ikuti langkah-langkah berikut untuk menjalankan sistem di laptop:

### 1. Prasyarat Sistem
- **Python**: Disarankan menggunakan **Python 3.10** atau **Python 3.11** *(Hindari Python 3.14 karena beberapa pustaka Computer Vision seperti MediaPipe & PyTorch belum menyediakan prebuilt wheel stabil untuk Windows di Python 3.14)*.
- **Git** terpasang di laptop.
- Kamera/Webcam laptop berfungsi normal.
- *(Opsional)* **MySQL (XAMPP / Laragon)**. Jika tidak menyalakan MySQL, sistem otomatis menggunakan **SQLite lokal (`dms.db`)** tanpa konfigurasi tambahan!

### 2. Clone Repositori & Buat Virtual Environment
Buka PowerShell atau Terminal di laptop Anda:
```powershell
# 1. Masuk ke folder proyek
cd dmsgacorsekali

# 2. Buat virtual environment bernama 'venv'
python -m venv venv

# 3. Aktifkan virtual environment
.\venv\Scripts\Activate.ps1
```
> **💡 Catatan PowerShell:** Jika muncul pesan `cannot be loaded because running scripts is disabled on this system`, jalankan perintah berikut sekali saja lalu ulangi aktivasi:
> ```powershell
> Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
> .\venv\Scripts\Activate.ps1
> ```

### 3. Instalasi Dependensi Pustaka
Untuk menghemat kuota dan mempercepat proses download (versi CPU ~180MB vs versi CUDA ~2.5GB):
```powershell
# Pasang PyTorch versi CPU yang hemat penyimpanan
pip install torch --index-url https://download.pytorch.org/whl/cpu

# Pasang seluruh dependensi proyek
pip install -r requirements.txt
```

> **✨ Fitur Auto-Switch Venv:** Seluruh skrip (`run.py`, `init_db.py`, `test_system.py`, dan seluruh skrip di folder `training_suite/`) telah dilengkapi pendeteksi otomatis. Jika Anda lupa mengaktifkan venv dan mengetik `python <skrip>.py`, skrip akan otomatis dialihkan untuk dieksekusi di dalam virtual environment proyek.

### 4. Inisialisasi Database
Jalankan skrip inisialisasi untuk membuat seluruh tabel dan akun pengguna bawaan:
```powershell
python init_db.py
```
**Akun Bawaan (Default) yang Tersedia:**
- **Administrator**:
  - Username: `admin`
  - Password: `admin123`
- **Driver Utama (Apri Catur)**:
  - Username: `driver`
  - Password: `driver123`
- **Driver Rekan (Nayla Azkyah)**:
  - Username: `nayla`
  - Password: `driver123`

### 5. Verifikasi Pengujian Sistem (Testing)
Pastikan semua modul berjalan sempurna 100%:
```powershell
python test_system.py
```
*(Hasil yang diharapkan: `Ran 9 tests ... OK`)*.

### 6. Menjalankan Aplikasi Web DMS
```powershell
python run.py
```
Buka browser favorit Anda (Google Chrome / Microsoft Edge) dan akses alamat:
👉 **[http://localhost:5000](http://localhost:5000)** atau **[http://127.0.0.1:5000](http://127.0.0.1:5000)**

---

## 🔒 Keamanan Sesi & Akun (Session Security)
Sistem dilengkapi fitur keamanan sesi:
- **Proteksi Sesi Kuat (*Strong Session Protection*)**: Mencegah serangan *Session Hijacking* dan *Session Fixation*.
- **Batas Waktu Inaktivitas Otomatis (*Session Inactivity Timeout*)**: Sesi pengguna otomatis berakhir setelah 30 menit tidak aktif demi keamanan.
- **Cookie Aman**: Menggunakan atribut `HttpOnly` dan `SameSite=Lax` untuk mencegah pencurian cookie via XSS dan serangan CSRF.
- **Validasi Pengalihan URL**: Melindungi sistem dari kerentanan *Open Redirect*.

---

## 🧠 Panduan Mengumpulkan Data & Melatih Model AI Mandiri

Anda dapat melatih model AI Anda sendiri menggunakan wajah Anda atau rekan tim:

### Langkah 1: Kumpulkan Data Wajah via Webcam
Pastikan aplikasi web sedang tidak menggunakan kamera, lalu jalankan:
```powershell
python training_suite/data_collector.py
```
Jendela pop-up kamera akan muncul di layar monitor Anda:
- Tekan **`[1]`** untuk mengatur label kelas **TERJAGA** (mata terbuka normal).
- Tekan **`[2]`** untuk mengatur label kelas **MENGANTUK** (mata terpejam/sayu).
- Tekan **`[3]`** untuk mengatur label kelas **MENGUAP** (mulut terbuka lebar).
- Tekan **`[SPASI]`** untuk merekam kontinu selama 10 detik.
- Tekan **`[Q]`** atau **`[ESC]`** untuk selesai. Data tersimpan di `dataset/dataset_features.csv`.

### Langkah 2: Latih Model AI (Scikit-Learn atau PyTorch)
Untuk melatih model Random Forest (**Direkomendasikan** — akurasi $\ge 95\%$):
```powershell
python training_suite/train_sklearn.py --model rf
```
Atau melatih model Deep Learning PyTorch MLP:
```powershell
python training_suite/train_pytorch.py --epochs 25
```
Bobot model akan otomatis tersimpan di folder `models/trained_model.joblib` atau `models/trained_model.pt`.

### Langkah 3: Evaluasi Akurasi & Buat Grafik Confusion Matrix
```powershell
python training_suite/evaluate.py
```
Grafik akurasi dan matriks konfusi tersimpan di `models/confusion_matrix.png`, siap digunakan untuk laporan, poster, dan presentasi PBL Anda!

### Langkah 4: Aktifkan Model pada Aplikasi Web
Buka menu **Admin -> Manajemen Model** di aplikasi web (`http://localhost:5000/admin/models`), lalu klik tombol **"Muat Ulang Model ke Memory"**. Aplikasi akan langsung menggunakan model baru Anda untuk inferensi real-time!

---

## ❓ FAQ & Solusi Masalah Teknis (Troubleshooting)

1. **Webcam terdeteksi sibuk / lampu kamera laptop menyala terus:**
   - Tutup aplikasi lain yang mungkin memakai kamera (seperti Zoom, Google Meet, Skype, atau tab browser lain).
   - Jika proses Python lama masih menggantung di latar belakang, buka PowerShell dan jalankan:
     ```powershell
     Stop-Process -Name python, pythonw -Force -ErrorAction SilentlyContinue
     ```
2. **Port 5000 sudah digunakan aplikasi lain:**
   - Anda dapat mengubah port di baris terakhir `run.py` menjadi port lain, misalnya `port=5001`.
3. **Database MySQL tidak terhubung:**
   - Tidak perlu khawatir! Sistem telah dilengkapi **Auto-Fallback**. Jika MySQL tidak menyala, sistem otomatis beralih ke database lokal SQLite (`sqlite:///dms.db`) sehingga aplikasi tetap dapat berjalan normal 100%.

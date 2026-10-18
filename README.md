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
c:\GIT\apri\
├── app\                           # Aplikasi Web Flask
│   ├── routes\
│   │   ├── auth.py                # Login, Register, Logout
│   │   ├── driver.py              # Video Streaming, Status API, Riwayat Driver
│   │   └── admin.py               # Dashboard Admin, Kelola Driver, Manajemen Model
│   ├── templates\                 # Antarmuka HTML5 Tailwind CSS
│   ├── static\snapshots\          # Foto bukti insiden otomatis
│   ├── models.py                  # Model Database SQLAlchemy (User, AlertLog, Session)
│   └── __init__.py                # Factory Flask & Auto-Fallback Database
├── dms_core\                      # Core Engine Computer Vision
│   ├── detector.py                # MediaPipe Face Mesh, EAR, MAR, Head Pose (solvePnP)
│   ├── classifier.py              # Inferensi Model Custom (Scikit-Learn/PyTorch) & Baseline
│   ├── tracker.py                 # Pelacak frame kantuk/distraksi & pemicu alarm
│   └── alert_manager.py           # Penyimpan snapshot & log insiden
├── training_suite\                # Suite Pelatihan Model Mandiri
│   ├── data_collector.py          # Tool webcam untuk merekam sampel dataset
│   ├── dataset_loader.py          # Pemuat dataset, split train-test, & bootstrap
│   ├── train_sklearn.py           # Pelatihan Random Forest / SVM / Gradient Boosting
│   ├── train_pytorch.py           # Pelatihan Deep Learning PyTorch MLP
│   └── evaluate.py                # Evaluasi akurasi & pembuatan grafik Confusion Matrix
├── dataset\                       # Folder dataset CSV dan gambar
├── models\                        # Folder file bobot model (.joblib, .pt) dan grafik
├── config.py                      # Konfigurasi aplikasi dan koneksi MySQL/SQLite
├── init_db.py                     # Skrip inisialisasi tabel dan akun default
├── run.py                         # Skrip peluncur server web
├── test_system.py                 # Unit test otomatis
└── requirements.txt               # Daftar dependensi Python
```

---

## 🚀 Panduan Instalasi & Menjalankan Sistem

### 1. Persiapan Lingkungan (Environment)
Pastikan Python telah terpasang. Pasang seluruh dependensi:
```powershell
python -m pip install -r requirements.txt
```

### 2. Inisialisasi Database
Jalankan skrip inisialisasi database (otomatis mendeteksi MySQL XAMPP atau membuat SQLite lokal):
```powershell
python init_db.py
```
**Akun Bawaan (Default):**
- **Admin**: Username: `admin` | Password: `admin123`
- **Driver**: Username: `driver` | Password: `driver123`

### 3. Menjalankan Aplikasi Web DMS
```powershell
python run.py
```
Buka browser pada: [http://localhost:5000](http://localhost:5000)

---

## 🧠 Cara Mengumpulkan Data & Melatih Model AI Sendiri

Anda dapat melatih model AI Anda sendiri menggunakan langkah-langkah berikut:

### Langkah 1: Kumpulkan Data Pengemudi via Webcam
```powershell
python training_suite/data_collector.py
```
- Tekan `[1]` untuk mengatur label kelas **TERJAGA** (mata terbuka normal).
- Tekan `[2]` untuk mengatur label kelas **MENGANTUK** (mata terpejam/sayu).
- Tekan `[3]` untuk mengatur label kelas **MENGUAP** (mulut terbuka lebar).
- Tekan `[SPASI]` untuk merekam otomatis selama 10 detik.
- Tekan `[Q]` untuk selesai. Data tersimpan di `dataset/dataset_features.csv`.

### Langkah 2: Latih Model AI (Scikit-Learn atau PyTorch)
Untuk melatih model Random Forest (Direkomendasikan):
```powershell
python training_suite/train_sklearn.py --model rf
```
Atau untuk melatih model Deep Learning PyTorch:
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
Buka menu **Admin -> Model AI** di aplikasi web, lalu klik tombol **"Muat Ulang Model ke Memory"**. Aplikasi akan langsung menggunakan model baru Anda untuk inferensi real-time!

---

## 🧪 Menjalankan Pengujian Otomatis (Testing)
Untuk memastikan seluruh modul berfungsi 100%:
```powershell
python test_system.py
```


# PBLIF3-PD15 — Aplikasi Driver Monitoring System (DMS)

Aplikasi untuk memantau kondisi dan perilaku pengemudi secara real-time guna
meningkatkan keselamatan berkendara. Sistem mencari tanda-tanda kelelahan dan
perilaku berisiko menggunakan pengenalan wajah, analisis gerakan mata, dan
deteksi perilaku.

**Kode Tim PBL:** IF3-PD15 · **Program Studi:** Teknik Informatika

| | |
|---|---|
| **Ketua / Anggota** | Apri Catur Pramudiansyah (3312501067) · Nayla Azkyah Azra (3312501061) |
| **Manajer Proyek** | Ari Wibowo, S.T., M.T. |
| **Metode** | Agile |

## Ruang Lingkup

Pengembangan dilakukan pada **lingkungan simulasi** menggunakan webcam/kamera
laptop sebagai input — **tidak** dipasang pada kendaraan nyata. Fokus proyek
adalah logika sistem, penerapan AI, dan mekanisme monitoring data.

## Arsitektur

```
┌──────────────────────┐        HTTP/JSON        ┌────────────────────────┐
│  Laravel (web app)   │ ──────────────────────► │  AI Service (FastAPI)  │
│  - Login & hak akses │                         │  - MediaPipe FaceMesh  │
│  - Dashboard admin   │ ◄────────────────────── │  - Deteksi kantuk      │
│  - Riwayat peringatan│    hasil deteksi        │  - Pose kepala         │
│  - MySQL             │                         │  - Deteksi objek (ops) │
└──────────────────────┘                         └────────────────────────┘
        ▲                                                     ▲
        │                                                     │
        └──────────────── webcam / kamera ────────────────────┘
```

| Folder | Isi | Teknologi |
|---|---|---|
| `/` | Aplikasi web utama | Laravel 13, MySQL 8, Tailwind/Vite |
| `/ai-service` | Microservice computer vision | Python 3.13, FastAPI, MediaPipe |

## Kebutuhan Perangkat

- PHP **8.3+** dan Composer 2
- Node.js **20+** dan npm
- MySQL **8**
- Python **3.13** (MediaPipe 1.x belum mendukung Python 3.14)

## Setup

### 1. Aplikasi Laravel

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate

# Buat database `pbl_dms` di MySQL, lalu:
php artisan migrate

npm install
```

### 2. AI Service

```powershell
cd ai-service
py -3.13 -m venv .venv
.venv\Scripts\python.exe -m pip install -r requirements.txt
.venv\Scripts\python.exe scripts\download_models.py
Copy-Item .env.example .env
```

## Menjalankan (butuh 2 terminal)

```powershell
# Terminal 1 — AI Service  -> http://127.0.0.1:8001  (dokumentasi: /docs)
cd ai-service
.venv\Scripts\python.exe run.py

# Terminal 2 — Aplikasi Laravel -> http://localhost:8000
php artisan serve
```

## Kebutuhan Fungsional

| Kode | Fungsi | Modul |
|---|---|---|
| FR-01 | Driver dapat membuat akun | Laravel |
| FR-02 | Pengguna dapat login | Laravel |
| FR-03 | Sistem mengakses kamera/webcam | Laravel + AI |
| FR-04 | Deteksi wajah dan mata real-time | AI Service |
| FR-05 | Deteksi kondisi mengantuk | AI Service |
| FR-06 | Deteksi arah pandangan/kepala | AI Service |
| FR-07 | Deteksi objek di sekitar kendaraan | AI Service (opsional) |
| FR-08 | Estimasi jarak relatif objek | AI Service (opsional) |
| FR-09 | Peringatan kondisi berbahaya | Laravel + AI |
| FR-10 | Peringatan suara/visual | Laravel |
| FR-11 | Pengguna dapat logout | Laravel |
| FR-12 | Admin mengelola data Driver | Laravel |
| FR-13 | Admin mengelola data Monitoring Driver | Laravel |
| FR-14 | Driver melihat riwayat peringatan | Laravel |

## Catatan Teknis

- MediaPipe **1.x menghapus API lama** `mp.solutions`. Kode memakai **Tasks API**
  dan memerlukan `ai-service/models/face_landmarker.task`.
- Model bobot **tidak di-commit** ke Git. Unduh dengan
  `ai-service/scripts/download_models.py`.
- Estimasi pose kepala memakai matriks transformasi wajah MediaPipe dan sudah
  divalidasi (rotasi gambar -25° mengubah `roll` sebesar -25.18°).
- Deteksi objek (FR-07/FR-08) **non-aktif secara default** sampai bobot model
  YOLO tersedia. Aktifkan lewat `DMS_YOLO_ENABLED=true`.

## Dokumentasi Terkait

- `../RPP PBL IF3PD-15 (1).pdf` — Rencana Pelaksanaan Proyek
- `../Praktikum 3 Use Case Diagram.docx.pdf` — Kebutuhan fungsional & use case

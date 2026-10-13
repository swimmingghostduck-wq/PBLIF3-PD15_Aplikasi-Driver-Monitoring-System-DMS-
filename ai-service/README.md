# AI Service — Driver Monitoring System (DMS)

Microservice AI untuk **Aplikasi Driver Monitoring System (DMS)**.
Bagian dari proyek PBL **IF3-PD15**, Program Studi Teknik Informatika.

Service ini menangani seluruh proses *computer vision*, terpisah dari aplikasi
web Laravel (`../`).

## Peran dalam Sistem

| Kebutuhan | Endpoint | Deskripsi |
|---|---|---|
| FR-04 | `POST /api/v1/detect/driver` | Deteksi wajah & mata (landmark) |
| FR-05 | `POST /api/v1/detect/driver` | Deteksi kondisi mengantuk (EAR) |
| FR-06 | `POST /api/v1/detect/driver` | Deteksi arah pandangan/kepala (yaw/pitch) |
| FR-07 | `POST /api/v1/detect/objects` | Deteksi objek di sekitar kendaraan |
| FR-08 | `POST /api/v1/detect/objects` | Estimasi jarak relatif objek |
| — | `GET /health` | Status service & kesiapan model |

Aplikasi Laravel memanggil service ini, lalu menyimpan hasilnya sebagai
riwayat monitoring (FR-13) dan meneruskan peringatan ke driver (FR-09, FR-10).

## Teknologi

- **Python 3.13** — MediaPipe 1.1.0 belum menyediakan wheel untuk Python 3.14.
- **FastAPI** — REST API + dokumentasi otomatis (Swagger).
- **MediaPipe Tasks API** — `FaceLandmarker` untuk 478 landmark wajah.
- **OpenCV** — pembacaan frame & estimasi pose kepala (`solvePnP`).
- **YOLO/Ultralytics** — opsional, untuk deteksi objek (FR-07/FR-08).

> Catatan penting: MediaPipe 1.x **sudah menghapus** API lama `mp.solutions`.
> Kode di sini memakai **Tasks API** dan membutuhkan file model
> `models/face_landmarker.task`.

## Setup

```powershell
# 1. Buat virtual environment (wajib Python 3.13)
py -3.13 -m venv .venv

# 2. Pasang dependensi
.venv\Scripts\python.exe -m pip install -r requirements.txt

# 3. Unduh model MediaPipe
.venv\Scripts\python.exe scripts\download_models.py

# 4. Salin konfigurasi
Copy-Item .env.example .env
```

## Menjalankan

```powershell
.venv\Scripts\python.exe run.py
```

Service berjalan di `http://127.0.0.1:8001`.
Dokumentasi interaktif: `http://127.0.0.1:8001/docs`.

## Contoh Pemakaian

```powershell
curl.exe -X POST "http://127.0.0.1:8001/api/v1/detect/driver" -F "file=@frame.jpg"
```

Contoh respons:

```json
{
  "face_detected": true,
  "eyes_detected": true,
  "status": "alert",
  "ear": 0.2831,
  "mar": 0.1423,
  "yaw": -4.21,
  "pitch": 2.87,
  "roll": 1.11,
  "drowsy_frames": 0,
  "distracted_frames": 0,
  "should_alert": false,
  "message": "Driver dalam kondisi waspada",
  "landmarks": [[0.51234, 0.39876]]
}
```

## Kalibrasi (FR-05, FR-06)

Ambang batas ada di `.env` dan dapat disesuaikan saat pengujian (lihat RPP
bagian 6, fase *Test*: "kalibrasi threshold"):

| Variabel | Default | Fungsi |
|---|---|---|
| `DMS_EAR_THRESHOLD` | 0.21 | Mata dianggap tertutup bila EAR di bawah nilai ini |
| `DMS_DROWSY_FRAME_THRESHOLD` | 20 | Jumlah frame berturut-turut sebelum dinyatakan mengantuk |
| `DMS_MAR_THRESHOLD` | 0.60 | Ambang deteksi menguap |
| `DMS_YAW_THRESHOLD_DEG` | 30.0 | Ambang menoleh kiri/kanan |
| `DMS_PITCH_THRESHOLD_DEG` | 25.0 | Ambang menunduk/mendongak |

## Struktur

```
ai-service/
├── app/
│   ├── config.py              # Konfigurasi (prefix DMS_)
│   ├── main.py                # Aplikasi FastAPI
│   ├── schemas.py             # Skema request/response
│   ├── routers/
│   │   ├── health.py          # GET /health
│   │   └── detection.py       # POST /api/v1/detect/*
│   ├── services/
│   │   ├── face_analyzer.py   # FR-04..FR-06
│   │   └── object_detector.py # FR-07..FR-08
│   └── utils/image.py         # Decode upload -> frame OpenCV
├── models/                    # Bobot model (tidak di-commit)
├── scripts/download_models.py
├── requirements.txt
└── run.py
```

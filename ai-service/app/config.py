"""Konfigurasi AI Service (dibaca dari environment variable ber-prefix DMS_)."""

from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    model_config = SettingsConfigDict(
        env_file=".env",
        env_prefix="DMS_",
        extra="ignore",
        case_sensitive=False,
    )

    # --- Identitas service ---
    app_name: str = "DMS AI Service"
    version: str = "0.1.0"
    host: str = "127.0.0.1"
    port: int = 8001
    debug: bool = True

    # --- CORS (alamat aplikasi Laravel) ---
    cors_origins: list[str] = [
        "http://pbildms.test",
        "http://localhost:8000",
        "http://127.0.0.1:8000",
    ]

    # --- NFR-05: akurasi deteksi kantuk minimal 80% ---
    # Path model FaceLandmarker (MediaPipe Tasks API).
    face_landmarker_model: str = "models/face_landmarker.task"
    # Eye Aspect Ratio di bawah nilai ini dianggap mata tertutup.
    ear_threshold: float = 0.21
    # Jumlah frame berturut-turut mata tertutup sebelum dinyatakan mengantuk.
    drowsy_frame_threshold: int = 20
    # Mouth Aspect Ratio di atas nilai ini dianggap menguap.
    mar_threshold: float = 0.60

    # --- FR-06: deteksi arah pandangan/kepala ---
    yaw_threshold_deg: float = 30.0
    pitch_threshold_deg: float = 25.0
    distracted_frame_threshold: int = 15

    # --- FR-07/FR-08: deteksi objek & estimasi jarak ---
    # Aktifkan setelah bobot model YOLO tersedia di folder models/.
    yolo_enabled: bool = False
    yolo_weights: str = "models/yolov8n.pt"
    # Ambang jarak relatif (meter) yang dianggap berbahaya.
    unsafe_distance_m: float = 5.0


settings = Settings()

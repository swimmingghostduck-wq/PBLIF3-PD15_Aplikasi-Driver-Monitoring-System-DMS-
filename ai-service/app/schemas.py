"""Skema request/response AI Service."""

from enum import Enum

from pydantic import BaseModel, Field


class DriverStatus(str, Enum):
    """Kondisi driver hasil analisis."""

    ALERT = "alert"            # FR-04: waspada
    DROWSY = "drowsy"          # FR-05: mengantuk
    DISTRACTED = "distracted"  # FR-06: tidak fokus (menoleh)
    NO_FACE = "no_face"        # wajah tidak terdeteksi


class DetectionResult(BaseModel):
    """Hasil deteksi wajah, mata, dan kondisi driver."""

    face_detected: bool = Field(description="FR-04: wajah terdeteksi atau tidak")
    eyes_detected: bool = Field(description="FR-04: mata dapat teridentifikasi")
    status: DriverStatus = Field(description="Kondisi driver saat ini")

    ear: float | None = Field(default=None, description="Eye Aspect Ratio (rasio bukaan mata)")
    mar: float | None = Field(default=None, description="Mouth Aspect Ratio (rasio bukaan mulut)")
    yaw: float | None = Field(default=None, description="Sudut menoleh kiri/kanan (derajat)")
    pitch: float | None = Field(default=None, description="Sudut menunduk/mendongak (derajat)")
    roll: float | None = Field(default=None, description="Sudut miring kepala (derajat)")

    drowsy_frames: int = Field(default=0, description="Jumlah frame berturut-turut mata tertutup")
    distracted_frames: int = Field(default=0, description="Jumlah frame berturut-turut menoleh")

    should_alert: bool = Field(default=False, description="FR-09: perlu peringatan atau tidak")
    message: str = Field(default="", description="FR-10: pesan peringatan untuk driver")

    landmarks: list[list[float]] = Field(
        default_factory=list,
        description="FR-04: titik landmark wajah terpilih (x, y) ternormalisasi",
    )


class ObjectItem(BaseModel):
    """Satu objek yang terdeteksi."""

    label: str
    confidence: float
    bbox: list[float] = Field(description="[x1, y1, x2, y2] dalam piksel")
    distance_m: float | None = Field(default=None, description="FR-08: estimasi jarak relatif (meter)")
    risky: bool = Field(default=False, description="Objek dianggap berisiko/terlalu dekat")


class ObjectDetectionResult(BaseModel):
    """FR-07/FR-08: hasil deteksi objek di sekitar kendaraan."""

    enabled: bool = Field(description="Model deteksi objek aktif atau tidak")
    objects: list[ObjectItem] = Field(default_factory=list)
    nearest_distance_m: float | None = None
    should_alert: bool = Field(default=False)
    message: str = ""


class HealthResponse(BaseModel):
    status: str
    service: str
    version: str
    face_mesh_ready: bool
    object_detector_ready: bool

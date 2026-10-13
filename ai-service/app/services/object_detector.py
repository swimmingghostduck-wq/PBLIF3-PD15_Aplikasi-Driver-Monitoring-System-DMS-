"""FR-07 & FR-08: deteksi objek di sekitar kendaraan + estimasi jarak relatif.

Catatan penting (lihat RPP bagian 1 "Ruang lingkup"):
Deteksi objek bersifat OPSIONAL dan masih dalam tahap sederhana. Modul ini
sengaja dibuat non-aktif secara default (`DMS_YOLO_ENABLED=false`) supaya
service tetap berjalan tanpa bobot model. Aktifkan setelah file bobot
(contoh `models/yolov8n.pt`) tersedia.

Estimasi jarak di sini adalah "jarak relatif" berbasis ukuran bounding box,
bukan pengukuran metrik presisi — sesuai batasan proyek yang hanya memakai
kamera/webcam tanpa sensor jarak.
"""

from __future__ import annotations

from pathlib import Path

from app.config import settings
from app.schemas import ObjectDetectionResult, ObjectItem

# Tinggi objek nyata (meter) sebagai asumsi untuk estimasi jarak relatif.
# Nilai default mewakili kendaraan/rata-rata objek di jalan.
ASSUMED_OBJECT_HEIGHT_M = 1.5
# Panjang fokus kamera dalam piksel (kira-kira, untuk webcam standar).
ASSUMED_FOCAL_LENGTH_PX = 700.0


class ObjectDetector:
    """Pembungkus model deteksi objek (YOLO/Ultralytics) yang bersifat opsional."""

    def __init__(self) -> None:
        self._model = None
        self._ready = False

    @property
    def ready(self) -> bool:
        return self._ready

    def load(self) -> None:
        """Muat bobot model bila diaktifkan. Tidak melempar error bila gagal."""
        if not settings.yolo_enabled:
            self._ready = False
            return

        weights = Path(settings.yolo_weights)
        if not weights.exists():
            self._ready = False
            return

        try:
            from ultralytics import YOLO  # type: ignore[import-not-found]

            self._model = YOLO(str(weights))
            self._ready = True
        except Exception:
            # Model opsional: kegagalan muat tidak boleh mematikan service.
            self._model = None
            self._ready = False

    @staticmethod
    def estimate_distance(bbox: list[float]) -> float | None:
        """FR-08: estimasi jarak relatif dari tinggi bounding box (metode pinhole)."""
        height_px = bbox[3] - bbox[1]
        if height_px <= 1:
            return None
        return round(
            (ASSUMED_OBJECT_HEIGHT_M * ASSUMED_FOCAL_LENGTH_PX) / height_px, 2
        )

    def detect(self, frame_bgr) -> ObjectDetectionResult:
        """Jalankan deteksi objek pada satu frame."""
        if not self._ready or self._model is None:
            return ObjectDetectionResult(
                enabled=False,
                objects=[],
                message="Modul deteksi objek belum aktif (bobot model belum tersedia).",
            )

        results = self._model.predict(frame_bgr, verbose=False)
        objects: list[ObjectItem] = []
        nearest: float | None = None

        for result in results:
            names = getattr(result, "names", {}) or {}
            for box in result.boxes:
                xyxy = [float(v) for v in box.xyxy[0].tolist()]
                confidence = float(box.conf[0])
                label = str(names.get(int(box.cls[0]), "objek"))

                distance = self.estimate_distance(xyxy)
                risky = distance is not None and distance <= settings.unsafe_distance_m
                if distance is not None and (nearest is None or distance < nearest):
                    nearest = distance

                objects.append(
                    ObjectItem(
                        label=label,
                        confidence=round(confidence, 4),
                        bbox=[round(v, 1) for v in xyxy],
                        distance_m=distance,
                        risky=risky,
                    )
                )

        should_alert = any(obj.risky for obj in objects)
        message = ""
        if should_alert:
            message = (
                f"Perhatian! Objek terdeteksi pada jarak sekitar "
                f"{nearest} m di sekitar kendaraan."
            )

        return ObjectDetectionResult(
            enabled=True,
            objects=objects,
            nearest_distance_m=nearest,
            should_alert=should_alert,
            message=message,
        )


object_detector = ObjectDetector()

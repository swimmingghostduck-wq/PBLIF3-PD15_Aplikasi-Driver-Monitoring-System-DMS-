"""FR-04, FR-05, FR-06: deteksi wajah & mata, kantuk, dan arah pandangan/kepala.

Implementasi memakai MediaPipe **Tasks API** (FaceLandmarker).
Catatan: MediaPipe 1.x sudah menghapus API lama `mp.solutions`, sehingga modul ini
memerlukan file model `models/face_landmarker.task`
(lihat `scripts/download_models.py`).
"""

from __future__ import annotations

import math
import threading
import time
from pathlib import Path

import cv2
import numpy as np

from app.config import settings
from app.schemas import DetectionResult, DriverStatus

# --- Indeks landmark MediaPipe (FaceMesh 478 titik) ---
# Mata kiri & kanan menurut koordinat gambar (dipakai untuk menghitung EAR).
LEFT_EYE = [33, 160, 158, 133, 153, 144]
RIGHT_EYE = [362, 385, 387, 263, 373, 380]

# Mulut (untuk MAR / deteksi menguap).
MOUTH_TOP, MOUTH_BOTTOM = 13, 14
MOUTH_LEFT, MOUTH_RIGHT = 61, 291

# Titik yang dikirim balik ke Laravel untuk menggambar overlay landmark.
POSE_LANDMARKS = {
    "nose_tip": 1,
    "chin": 152,
    "left_eye_outer": 33,
    "right_eye_outer": 263,
    "mouth_left": 61,
    "mouth_right": 291,
}

# Landmark yang dikirim balik ke Laravel (cukup untuk menggambar overlay).
RETURNED_LANDMARKS = sorted(
    set(LEFT_EYE + RIGHT_EYE + [MOUTH_TOP, MOUTH_BOTTOM, MOUTH_LEFT, MOUTH_RIGHT])
    | set(POSE_LANDMARKS.values())
)


class FaceAnalyzer:
    """Analisis kondisi driver dari frame kamera (stateful, satu driver per sesi)."""

    def __init__(self) -> None:
        self._landmarker = None
        self._ready = False
        self._lock = threading.Lock()
        self._last_timestamp_ms = 0
        self._drowsy_frames = 0
        self._distracted_frames = 0

    # ------------------------------------------------------------------ #
    # Siklus hidup
    # ------------------------------------------------------------------ #
    @property
    def ready(self) -> bool:
        return self._ready

    def load(self) -> None:
        """Muat model FaceLandmarker. Tidak melempar error bila gagal."""
        model_path = Path(settings.face_landmarker_model)
        if not model_path.exists():
            self._ready = False
            return

        try:
            from mediapipe.tasks import python as mp_python
            from mediapipe.tasks.python import vision

            options = vision.FaceLandmarkerOptions(
                base_options=mp_python.BaseOptions(model_asset_path=str(model_path)),
                running_mode=vision.RunningMode.VIDEO,
                num_faces=1,
                min_face_detection_confidence=0.5,
                min_face_presence_confidence=0.5,
                min_tracking_confidence=0.5,
                output_face_blendshapes=False,
                output_facial_transformation_matrixes=True,
            )
            self._landmarker = vision.FaceLandmarker.create_from_options(options)
            self._ready = True
        except Exception:
            self._landmarker = None
            self._ready = False

    def close(self) -> None:
        if self._landmarker is not None:
            try:
                self._landmarker.close()
            except Exception:
                pass
        self._landmarker = None
        self._ready = False

    def reset(self) -> None:
        """Reset akumulasi frame (dipakai saat sesi monitoring dimulai ulang)."""
        with self._lock:
            self._drowsy_frames = 0
            self._distracted_frames = 0

    # ------------------------------------------------------------------ #
    # Perhitungan
    # ------------------------------------------------------------------ #
    @staticmethod
    def _points(landmarks, indices, width: int, height: int) -> np.ndarray:
        return np.array(
            [[landmarks[i].x * width, landmarks[i].y * height] for i in indices],
            dtype=np.float64,
        )

    @staticmethod
    def _ear(eye_points: np.ndarray) -> float:
        """Eye Aspect Ratio: (|p2-p6| + |p3-p5|) / (2 * |p1-p4|)."""
        vertical_1 = np.linalg.norm(eye_points[1] - eye_points[5])
        vertical_2 = np.linalg.norm(eye_points[2] - eye_points[4])
        horizontal = np.linalg.norm(eye_points[0] - eye_points[3])
        if horizontal == 0:
            return 0.0
        return float((vertical_1 + vertical_2) / (2.0 * horizontal))

    @staticmethod
    def _mar(mouth_points: np.ndarray) -> float:
        """Mouth Aspect Ratio sederhana: tinggi / lebar mulut."""
        vertical = np.linalg.norm(mouth_points[0] - mouth_points[1])
        horizontal = np.linalg.norm(mouth_points[2] - mouth_points[3])
        if horizontal == 0:
            return 0.0
        return float(vertical / horizontal)

    @staticmethod
    def _head_pose(transformation_matrix) -> tuple[float, float, float]:
        """Estimasi sudut (yaw, pitch, roll) dalam derajat.

        Memakai matriks transformasi wajah dari MediaPipe, yang memetakan model
        wajah kanonik (frontal) ke wajah terdeteksi. Sudut dihitung dengan
        dekomposisi Tait-Bryan.

        Sudah divalidasi: memutar gambar -25 derajat mengubah `roll` sebesar
        -25.18 derajat (lihat pengujian fase Test).
        """
        rotation = np.asarray(transformation_matrix, dtype=np.float64)[:3, :3]
        sy = math.sqrt(rotation[0, 0] ** 2 + rotation[1, 0] ** 2)

        if sy > 1e-6:
            pitch = math.degrees(math.atan2(rotation[2, 1], rotation[2, 2]))
            yaw = math.degrees(math.atan2(-rotation[2, 0], sy))
            roll = math.degrees(math.atan2(rotation[1, 0], rotation[0, 0]))
        else:
            pitch = math.degrees(math.atan2(-rotation[1, 2], rotation[1, 1]))
            yaw = math.degrees(math.atan2(-rotation[2, 0], sy))
            roll = 0.0

        return float(yaw), float(pitch), float(roll)

    # ------------------------------------------------------------------ #
    # Analisis utama
    # ------------------------------------------------------------------ #
    def analyze(self, frame_bgr: np.ndarray) -> DetectionResult:
        """Analisis satu frame dan perbarui status kewaspadaan driver."""
        if not self._ready or self._landmarker is None:
            return DetectionResult(
                face_detected=False,
                eyes_detected=False,
                status=DriverStatus.NO_FACE,
                message="Modul deteksi wajah belum siap (model belum dimuat).",
            )

        import mediapipe as mp

        height, width = frame_bgr.shape[:2]
        rgb = cv2.cvtColor(frame_bgr, cv2.COLOR_BGR2RGB)
        mp_image = mp.Image(image_format=mp.ImageFormat.SRGB, data=rgb)

        with self._lock:
            timestamp_ms = max(int(time.monotonic() * 1000), self._last_timestamp_ms + 1)
            self._last_timestamp_ms = timestamp_ms
            result = self._landmarker.detect_for_video(mp_image, timestamp_ms)

            if not result.face_landmarks:
                self._drowsy_frames = 0
                self._distracted_frames = 0
                return DetectionResult(
                    face_detected=False,
                    eyes_detected=False,
                    status=DriverStatus.NO_FACE,
                    message="Wajah tidak terdeteksi",
                )

            landmarks = result.face_landmarks[0]

            left_eye = self._points(landmarks, LEFT_EYE, width, height)
            right_eye = self._points(landmarks, RIGHT_EYE, width, height)
            mouth = self._points(
                landmarks, [MOUTH_TOP, MOUTH_BOTTOM, MOUTH_LEFT, MOUTH_RIGHT], width, height
            )

            left_ear = self._ear(left_eye)
            right_ear = self._ear(right_eye)
            ear = float((left_ear + right_ear) / 2.0)
            mar = self._mar(mouth)

            if result.facial_transformation_matrixes:
                yaw, pitch, roll = self._head_pose(result.facial_transformation_matrixes[0])
            else:
                yaw = pitch = roll = 0.0

            # --- FR-05: akumulasi frame mata tertutup ---
            eyes_closed = ear < settings.ear_threshold
            self._drowsy_frames = self._drowsy_frames + 1 if eyes_closed else 0

            # --- FR-06: akumulasi frame menoleh / menunduk ---
            looking_away = (
                abs(yaw) > settings.yaw_threshold_deg
                or abs(pitch) > settings.pitch_threshold_deg
            )
            self._distracted_frames = (
                self._distracted_frames + 1 if looking_away else 0
            )

            # --- Tentukan status (prioritas: kantuk > tidak fokus) ---
            if self._drowsy_frames >= settings.drowsy_frame_threshold:
                status = DriverStatus.DROWSY
                should_alert = True
                message = "PERINGATAN: Driver terdeteksi mengantuk! Segera berhenti dan istirahat."
            elif self._distracted_frames >= settings.distracted_frame_threshold:
                status = DriverStatus.DISTRACTED
                should_alert = True
                message = "PERINGATAN: Driver terdeteksi tidak fokus, kembalikan pandangan ke depan."
            elif mar >= settings.mar_threshold:
                status = DriverStatus.ALERT
                should_alert = True
                message = "Perhatian: Driver terdeteksi menguap, disarankan beristirahat."
            else:
                status = DriverStatus.ALERT
                should_alert = False
                message = "Driver dalam kondisi waspada"

            returned = [
                [round(landmarks[i].x, 5), round(landmarks[i].y, 5)]
                for i in RETURNED_LANDMARKS
            ]

            return DetectionResult(
                face_detected=True,
                eyes_detected=True,
                status=status,
                ear=round(ear, 4),
                mar=round(mar, 4),
                yaw=round(yaw, 2),
                pitch=round(pitch, 2),
                roll=round(roll, 2),
                drowsy_frames=self._drowsy_frames,
                distracted_frames=self._distracted_frames,
                should_alert=should_alert,
                message=message,
                landmarks=returned,
            )


face_analyzer = FaceAnalyzer()

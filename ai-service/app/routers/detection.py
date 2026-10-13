"""Endpoint deteksi: wajah/mata (FR-04..FR-06) dan objek (FR-07..FR-08)."""

from fastapi import APIRouter, File, HTTPException, UploadFile

from app.schemas import DetectionResult, ObjectDetectionResult
from app.services.face_analyzer import face_analyzer
from app.services.object_detector import object_detector
from app.utils.image import decode_upload

router = APIRouter(prefix="/api/v1/detect", tags=["detection"])

MAX_UPLOAD_BYTES = 8 * 1024 * 1024  # 8 MB per frame


@router.post(
    "/driver",
    response_model=DetectionResult,
    summary="FR-04..FR-06: deteksi wajah, mata, kantuk, dan arah pandangan",
)
async def detect_driver(file: UploadFile = File(..., description="Frame gambar (jpg/png)")) -> DetectionResult:
    """Analisis satu frame: status kewaspadaan driver."""
    if not face_analyzer.ready:
        raise HTTPException(status_code=503, detail="Modul deteksi wajah belum siap.")

    frame = await decode_upload(file, MAX_UPLOAD_BYTES)
    return face_analyzer.analyze(frame)


@router.post(
    "/objects",
    response_model=ObjectDetectionResult,
    summary="FR-07 & FR-08: deteksi objek sekitar kendaraan + estimasi jarak",
)
async def detect_objects(file: UploadFile = File(..., description="Frame gambar (jpg/png)")) -> ObjectDetectionResult:
    """Deteksi objek pada satu frame. Non-aktif bila bobot model belum tersedia."""
    frame = await decode_upload(file, MAX_UPLOAD_BYTES)
    return object_detector.detect(frame)

"""Health check AI Service."""

from fastapi import APIRouter

from app.config import settings
from app.schemas import HealthResponse
from app.services.face_analyzer import face_analyzer
from app.services.object_detector import object_detector

router = APIRouter(tags=["health"])


@router.get("/health", response_model=HealthResponse, summary="Status service")
def health() -> HealthResponse:
    """Dipakai Laravel / tim untuk memastikan service AI hidup."""
    return HealthResponse(
        status="ok",
        service=settings.app_name,
        version=settings.version,
        face_mesh_ready=face_analyzer.ready,
        object_detector_ready=object_detector.ready,
    )

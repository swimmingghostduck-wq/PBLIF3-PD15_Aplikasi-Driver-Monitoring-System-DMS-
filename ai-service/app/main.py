"""Entry point AI Service - Driver Monitoring System (DMS).

Jalankan:
    .venv\\Scripts\\python.exe run.py
atau:
    .venv\\Scripts\\python.exe -m uvicorn app.main:app --reload --port 8001
"""

from contextlib import asynccontextmanager

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

from app.config import settings
from app.routers import detection, health
from app.services.face_analyzer import face_analyzer
from app.services.object_detector import object_detector


@asynccontextmanager
async def lifespan(_: FastAPI):
    """Muat model sekali saat service start (bukan setiap request)."""
    face_analyzer.load()
    object_detector.load()
    yield
    face_analyzer.close()


app = FastAPI(
    title=settings.app_name,
    version=settings.version,
    description=(
        "Microservice AI untuk Aplikasi Driver Monitoring System (DMS).\n\n"
        "Kode Tim PBL: **IF3-PD15**"
    ),
    lifespan=lifespan,
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=settings.cors_origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(health.router)
app.include_router(detection.router)


@app.get("/", tags=["root"], summary="Info service")
def root() -> dict:
    return {
        "service": settings.app_name,
        "version": settings.version,
        "docs": "/docs",
        "health": "/health",
    }

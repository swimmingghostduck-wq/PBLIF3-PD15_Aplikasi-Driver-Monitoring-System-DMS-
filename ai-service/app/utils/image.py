"""Utilitas pengolahan gambar."""

from __future__ import annotations

import cv2
import numpy as np
from fastapi import HTTPException, UploadFile


async def decode_upload(file: UploadFile, max_bytes: int = 8 * 1024 * 1024) -> np.ndarray:
    """Baca UploadFile menjadi frame BGR (format OpenCV)."""
    raw = await file.read()

    if not raw:
        raise HTTPException(status_code=400, detail="File gambar kosong.")
    if len(raw) > max_bytes:
        raise HTTPException(
            status_code=413,
            detail=f"Ukuran frame melebihi batas {max_bytes // (1024 * 1024)} MB.",
        )

    buffer = np.frombuffer(raw, dtype=np.uint8)
    frame = cv2.imdecode(buffer, cv2.IMREAD_COLOR)

    if frame is None:
        raise HTTPException(status_code=400, detail="Format gambar tidak dikenali.")

    return frame

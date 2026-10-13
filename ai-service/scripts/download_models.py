"""Unduh model MediaPipe yang dibutuhkan AI Service.

Jalankan dari folder ai-service:
    .venv\\Scripts\\python.exe scripts\\download_models.py
"""

from __future__ import annotations

import sys
import urllib.request
from pathlib import Path

MODELS = {
    "face_landmarker.task": (
        "https://storage.googleapis.com/mediapipe-models/face_landmarker/"
        "face_landmarker/float16/1/face_landmarker.task"
    ),
}

MODELS_DIR = Path(__file__).resolve().parent.parent / "models"


def download(name: str, url: str) -> bool:
    target = MODELS_DIR / name
    if target.exists():
        print(f"[SKIP] {name} sudah ada ({target.stat().st_size / 1_048_576:.2f} MB)")
        return True

    print(f"[GET ] {name} <- {url}")
    try:
        urllib.request.urlretrieve(url, target)  # noqa: S310 (URL internal Google)
    except Exception as exc:  # pragma: no cover
        print(f"[FAIL] {name}: {exc}")
        target.unlink(missing_ok=True)
        return False

    print(f"[OK  ] {name} ({target.stat().st_size / 1_048_576:.2f} MB)")
    return True


def main() -> int:
    MODELS_DIR.mkdir(parents=True, exist_ok=True)
    results = [download(name, url) for name, url in MODELS.items()]
    return 0 if all(results) else 1


if __name__ == "__main__":
    sys.exit(main())

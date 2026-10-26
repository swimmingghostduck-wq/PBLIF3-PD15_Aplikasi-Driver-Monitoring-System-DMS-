import os
import sys

# Auto-switch ke virtual environment jika dijalankan langsung dengan Python global
ROOT_DIR = os.path.abspath(os.path.dirname(__file__))
_VENV_PY = os.path.join(ROOT_DIR, 'venv', 'Scripts', 'python.exe')
if not os.path.exists(_VENV_PY):
    _VENV_PY = os.path.join(ROOT_DIR, 'venv', 'bin', 'python')
if os.path.exists(_VENV_PY) and os.path.abspath(sys.executable).lower() != os.path.abspath(_VENV_PY).lower():
    import subprocess
    sys.exit(subprocess.run([_VENV_PY] + sys.argv).returncode)

if ROOT_DIR not in sys.path:
    sys.path.insert(0, ROOT_DIR)

from app import create_app

app = create_app()

if __name__ == '__main__':
    print("=" * 65)
    print("   DRIVER MONITORING SYSTEM (DMS) - AI & CAMERA DETECTION SERVICE")
    print("=" * 65)
    print("Service Deteksi AI berjalan di:")
    print("  -> http://127.0.0.1:5000 (API & Camera Stream)")
    print("\nDashboard Web & Admin Laravel berjalan di:")
    print("  -> http://127.0.0.1:8000")
    print("\nTekan [Ctrl+C] di terminal ini untuk menghentikan server AI.")
    print("=" * 65)
    app.run(host='0.0.0.0', port=5000, debug=False, threaded=True)


import os
import sys

ROOT_DIR = os.path.abspath(os.path.dirname(__file__))
if ROOT_DIR not in sys.path:
    sys.path.insert(0, ROOT_DIR)

from app import create_app

app = create_app()

if __name__ == '__main__':
    print("=" * 65)
    print("      DRIVER MONITORING SYSTEM (DMS) - POLIBATAM PBL IF3-PD15")
    print("=" * 65)
    print("Aplikasi Web berjalan di:")
    print("  -> http://127.0.0.1:5000")
    print("  -> http://localhost:5000")
    print("\nTekan [Ctrl+C] di terminal ini untuk menghentikan server.")
    print("=" * 65)
    app.run(host='0.0.0.0', port=5000, debug=False, threaded=True)


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
from app.models import db, User, AlertLog, MonitoringSession

def init_database():
    """Inisialisasi tabel database dan akun default admin serta driver."""
    app = create_app()
    with app.app_context():
        print("[*] Membuat seluruh tabel database...")
        db.create_all()

        # 1. Buat Akun Administrator Default
        admin_user = User.query.filter_by(username='admin').first()
        if not admin_user:
            admin_user = User(
                username='admin',
                full_name='Administrator DMS',
                role='admin',
                nim_nik='ADMIN-001'
            )
            admin_user.set_password('admin123')
            db.session.add(admin_user)
            print("[+] Akun Admin berhasil dibuat: admin / admin123")
        else:
            print("[i] Akun Admin sudah ada.")

        # 2. Buat Akun Driver Default (Apri Catur)
        driver_user = User.query.filter_by(username='driver').first()
        if not driver_user:
            driver_user = User(
                username='driver',
                full_name='Apri Catur Pramudiansyah',
                role='driver',
                nim_nik='3312501067'
            )
            driver_user.set_password('driver123')
            db.session.add(driver_user)
            print("[+] Akun Driver berhasil dibuat: driver / driver123 (Apri Catur Pramudiansyah)")
        else:
            print("[i] Akun Driver sudah ada.")

        # 3. Buat Akun Driver Kedua (Nayla Azkyah)
        nayla_user = User.query.filter_by(username='nayla').first()
        if not nayla_user:
            nayla_user = User(
                username='nayla',
                full_name='Nayla Azkyah Azra',
                role='driver',
                nim_nik='3312501061'
            )
            nayla_user.set_password('driver123')
            db.session.add(nayla_user)
            print("[+] Akun Driver berhasil dibuat: nayla / driver123 (Nayla Azkyah Azra)")

        db.session.commit()
        print("\n" + "=" * 60)
        print("     INISIALISASI DATABASE SELESAI")
        print("=" * 60)
        print("Akun yang tersedia untuk login:")
        print("  1. Administrator -> Username: admin  | Password: admin123")
        print("  2. Driver Utama  -> Username: driver | Password: driver123")
        print("  3. Driver Rekan  -> Username: nayla  | Password: driver123")
        print("=" * 60)

if __name__ == '__main__':
    init_database()


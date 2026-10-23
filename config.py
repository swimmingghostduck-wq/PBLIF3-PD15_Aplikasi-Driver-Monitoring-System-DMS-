import os
from datetime import timedelta

class Config:
    """Konfigurasi Aplikasi Driver Monitoring System (DMS)"""
    SECRET_KEY = os.environ.get('SECRET_KEY', 'dms-secret-key-pbl-if3pd15-2026')

    # Keamanan Cookie & Sesi (Session Security)
    SESSION_COOKIE_HTTPONLY = True
    SESSION_COOKIE_SAMESITE = 'Lax'
    SESSION_COOKIE_SECURE = False  # Set True jika menggunakan HTTPS pada production
    PERMANENT_SESSION_LIFETIME = timedelta(minutes=60)
    
    # Keamanan Remember-Me Cookie (Flask-Login)
    REMEMBER_COOKIE_HTTPONLY = True
    REMEMBER_COOKIE_SAMESITE = 'Lax'
    REMEMBER_COOKIE_DURATION = timedelta(days=1)
    
    # Batas Waktu Inaktivitas Sesi Pengguna (detik)
    SESSION_INACTIVITY_TIMEOUT = 1800  # 30 menit inaktivitas otomatis logout
    
    # Konfigurasi Database: Prioritas MySQL (XAMPP default: root tanpa password)
    # Jika koneksi MySQL gagal, aplikasi otomatis beralih ke SQLite lokal.
    MYSQL_HOST = os.environ.get('MYSQL_HOST', 'localhost')
    MYSQL_PORT = int(os.environ.get('MYSQL_PORT', 3306))
    MYSQL_USER = os.environ.get('MYSQL_USER', 'root')
    MYSQL_PASSWORD = os.environ.get('MYSQL_PASSWORD', '')
    MYSQL_DB = os.environ.get('MYSQL_DB', 'dms_db')
    
    MYSQL_URI = f"mysql+pymysql://{MYSQL_USER}:{MYSQL_PASSWORD}@{MYSQL_HOST}:{MYSQL_PORT}/{MYSQL_DB}"
    SQLITE_URI = "sqlite:///dms.db"
    
    SQLALCHEMY_DATABASE_URI = MYSQL_URI
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    
    # Path Penyimpanan Model & Dataset
    BASE_DIR = os.path.abspath(os.path.dirname(__file__))
    MODELS_DIR = os.path.join(BASE_DIR, 'models')
    DATASET_DIR = os.path.join(BASE_DIR, 'dataset')
    SNAPSHOTS_DIR = os.path.join(BASE_DIR, 'app', 'static', 'snapshots')
    
    # Ambang Batas Logika Kantuk & Distraksi (Fallback / Heuristik)
    EAR_THRESHOLD = 0.22         # Eye Aspect Ratio di bawah ini dihitung mata tertutup
    MAR_THRESHOLD = 0.65         # Mouth Aspect Ratio di atas ini dihitung menguap
    DROWSY_CONSEC_FRAMES = 12    # ~1.2 detik berturut-turut untuk alarm kantuk
    DISTRACTION_CONSEC_FRAMES = 8 # ~0.8 detik menoleh/menunduk untuk alarm tidak fokus
    YAW_THRESHOLD = 12.0         # Derajat sudut menoleh ke samping (kiri / kanan)
    PITCH_THRESHOLD = 12.0       # Derajat sudut menunduk (bawah)

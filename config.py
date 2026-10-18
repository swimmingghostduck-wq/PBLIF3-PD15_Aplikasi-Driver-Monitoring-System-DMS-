import os

class Config:
    """Konfigurasi Aplikasi Driver Monitoring System (DMS)"""
    SECRET_KEY = os.environ.get('SECRET_KEY', 'dms-secret-key-pbl-if3pd15-2026')
    
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
    DROWSY_CONSEC_FRAMES = 20    # ~1.5 - 2 detik berturut-turut untuk alarm kantuk
    DISTRACTION_CONSEC_FRAMES = 35 # ~2.5 - 3 detik menoleh samping untuk alarm tidak fokus

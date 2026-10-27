import os
import time
from flask import Flask, redirect, url_for, session, flash, request, render_template
from flask_login import LoginManager, current_user, logout_user
from config import Config
from app.models import db, User

login_manager = LoginManager()
login_manager.login_view = 'auth.login'
login_manager.login_message = 'Silakan login terlebih dahulu untuk mengakses sistem.'
login_manager.login_message_category = 'warning'
login_manager.session_protection = 'strong'

@login_manager.user_loader
def load_user(user_id):
    try:
        return db.session.get(User, int(user_id))
    except Exception:
        return None

def create_app(config_class=Config):
    """Factory function untuk membuat instansiasi aplikasi Flask."""
    app = Flask(__name__)
    app.config.from_object(config_class)

    # Inisialisasi Database dengan uji koneksi MySQL -> Fallback SQLite
    try:
        # Coba inisialisasi koneksi MySQL
        import pymysql
        conn = pymysql.connect(
            host=app.config['MYSQL_HOST'],
            user=app.config['MYSQL_USER'],
            password=app.config['MYSQL_PASSWORD'],
            port=app.config['MYSQL_PORT'],
            connect_timeout=2
        )
        with conn.cursor() as cursor:
            cursor.execute(f"CREATE DATABASE IF NOT EXISTS `{app.config['MYSQL_DB']}` CHARACTER SET utf8mb4;")
        conn.close()
        print(f"[Database] Terhubung ke MySQL server ({app.config['MYSQL_HOST']}:{app.config['MYSQL_PORT']}). Database: {app.config['MYSQL_DB']}")
    except Exception as e:
        print(f"[Database] MySQL server tidak terdeteksi ({e}).")
        print("[Database] Mengalihkan ke SQLite lokal (sqlite:///dms.db) demi kelancaran aplikasi...")
        app.config['SQLALCHEMY_DATABASE_URI'] = app.config['SQLITE_URI']

    db.init_app(app)
    login_manager.init_app(app)

    # Pastikan folder snapshot tersedia
    os.makedirs(app.config['SNAPSHOTS_DIR'], exist_ok=True)
    os.makedirs(app.config['MODELS_DIR'], exist_ok=True)
    os.makedirs(app.config['DATASET_DIR'], exist_ok=True)

    # Daftarkan Blueprint
    from app.routes.auth import auth_bp
    from app.routes.driver import driver_bp
    from app.routes.admin import admin_bp

    app.register_blueprint(auth_bp, url_prefix='/auth')
    app.register_blueprint(driver_bp, url_prefix='/driver')
    app.register_blueprint(admin_bp, url_prefix='/admin')

    # Pre-warm AI Engine saat booting agar siap melayani frame webcam secara instan
    with app.app_context():
        try:
            from app.routes.driver import init_dms_engine, warmup_dms_engine
            init_dms_engine(app)
            warmup_dms_engine()
        except Exception as e:
            print(f"[AI Engine] Notice saat pre-warming: {e}")

    @app.before_request
    def handle_incoming_request():
        # Izinkan preflight CORS
        if request.method == 'OPTIONS':
            return ('', 204)

        # Lewatkan request API dan file statis
        if request.path.startswith('/driver/process_frame') or \
           request.path.startswith('/driver/video_feed') or \
           request.path.startswith('/driver/status') or \
           request.path.startswith('/static') or \
           request.is_json:
            return None

        # Segala request halaman web dialihkan langsung ke Laravel di port 8000
        path = request.path
        path_mapping = {
            '/': '/',
            '/splash': '/',
            '/auth/login': '/login',
            '/auth/register': '/register',
            '/auth/logout': '/logout',
            '/driver/monitor': '/driver/monitor',
            '/driver/history': '/driver/dashboard',
            '/driver/dashboard': '/driver/dashboard',
            '/driver/profile': '/driver/profile',
            '/admin/dashboard': '/admin/dashboard',
            '/admin/alerts': '/admin/alerts',
            '/admin/drivers': '/admin/drivers',
            '/admin/models': '/admin/models',
            '/admin/profile': '/admin/profile',
        }
        target_path = path_mapping.get(path, path)
        return redirect(f'http://127.0.0.1:8000{target_path}')

    @app.after_request
    def add_cors_headers(response):
        response.headers['Access-Control-Allow-Origin'] = '*'
        response.headers['Access-Control-Allow-Headers'] = 'Content-Type, Authorization, X-Requested-With'
        response.headers['Access-Control-Allow-Methods'] = 'GET, POST, PUT, DELETE, OPTIONS'
        return response

    @app.errorhandler(500)
    @app.errorhandler(404)
    def handle_global_errors(e):
        if not request.is_json and not request.path.startswith('/driver/process_frame'):
            return redirect('http://127.0.0.1:8000' + request.path)
        return {'error': str(e), 'service': 'DMS AI Microservice'}, (getattr(e, 'code', 500))

    @app.route('/')
    def index():
        return redirect('http://127.0.0.1:8000')

    return app


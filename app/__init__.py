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
    return db.session.get(User, int(user_id))

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

    @app.before_request
    def enforce_session_security():
        """Memeriksa batas waktu inaktivitas sesi pengguna untuk keamanan sistem."""
        if current_user.is_authenticated:
            # Kecualikan file statis dari update waktu aktivitas
            if request.endpoint and request.endpoint.startswith('static'):
                return

            now = time.time()
            last_active = session.get('_last_active')
            timeout = app.config.get('SESSION_INACTIVITY_TIMEOUT', 1800)

            if last_active and (now - last_active > timeout):
                logout_user()
                session.clear()
                flash('Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan masuk kembali.', 'warning')
                return redirect(url_for('auth.login', next=request.path if request.method == 'GET' else None))

            session['_last_active'] = now

    @app.route('/')
    def index():
        if current_user.is_authenticated:
            if current_user.is_admin():
                return redirect(url_for('admin.dashboard'))
            return redirect(url_for('driver.monitor'))
        return render_template('splash.html')

    return app


import os
from flask import Flask, redirect, url_for
from flask_login import LoginManager, current_user
from config import Config
from app.models import db, User

login_manager = LoginManager()
login_manager.login_view = 'auth.login'
login_manager.login_message = 'Silakan login terlebih dahulu untuk mengakses sistem.'
login_manager.login_message_category = 'warning'

@login_manager.user_loader
def load_user(user_id):
    return User.query.get(int(user_id))

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

    @app.route('/')
    def index():
        if current_user.is_authenticated:
            if current_user.is_admin():
                return redirect(url_for('admin.dashboard'))
            return redirect(url_for('driver.monitor'))
        return redirect(url_for('auth.login'))

    return app


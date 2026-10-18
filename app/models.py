from datetime import datetime
from flask_sqlalchemy import SQLAlchemy
from flask_login import UserMixin
from werkzeug.security import generate_password_hash, check_password_hash

db = SQLAlchemy()

class User(UserMixin, db.Model):
    """
    Model Akun Pengguna Sistem Driver Monitoring System (DMS).
    Mendukung dua peran: 'driver' dan 'admin'.
    """
    __tablename__ = 'users'

    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(50), unique=True, nullable=False, index=True)
    password_hash = db.Column(db.String(255), nullable=False)
    full_name = db.Column(db.String(100), nullable=False)
    role = db.Column(db.String(20), nullable=False, default='driver') # 'driver' atau 'admin'
    nim_nik = db.Column(db.String(30), nullable=True)
    is_active = db.Column(db.Boolean, default=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

    # Relasi
    sessions = db.relationship('MonitoringSession', backref='driver', lazy=True, cascade='all, delete-orphan')
    alerts = db.relationship('AlertLog', backref='driver', lazy=True, cascade='all, delete-orphan')

    def set_password(self, password):
        self.password_hash = generate_password_hash(password)

    def check_password(self, password):
        return check_password_hash(self.password_hash, password)

    def is_admin(self):
        return self.role == 'admin'

    def to_dict(self):
        return {
            'id': self.id,
            'username': self.username,
            'full_name': self.full_name,
            'role': self.role,
            'nim_nik': self.nim_nik,
            'created_at': self.created_at.strftime('%Y-%m-%d %H:%M:%S')
        }

class MonitoringSession(db.Model):
    """
    Model Sesi Pemantauan Driver.
    Mencatat awal dan akhir waktu berkendara/monitoring serta total insiden.
    """
    __tablename__ = 'monitoring_sessions'

    id = db.Column(db.Integer, primary_key=True)
    driver_id = db.Column(db.Integer, db.ForeignKey('users.id'), nullable=False, index=True)
    start_time = db.Column(db.DateTime, default=datetime.utcnow, nullable=False)
    end_time = db.Column(db.DateTime, nullable=True)
    total_drowsy = db.Column(db.Integer, default=0)
    total_distraction = db.Column(db.Integer, default=0)
    total_yawn = db.Column(db.Integer, default=0)
    is_active = db.Column(db.Boolean, default=True)

    alerts = db.relationship('AlertLog', backref='session', lazy=True, cascade='all, delete-orphan')

class AlertLog(db.Model):
    """
    Model Catatan Riwayat Insiden Peringatan Driver.
    Menyimpan jenis insiden, tingkat bahaya, durasi, dan file tangkapan layar.
    """
    __tablename__ = 'alert_logs'

    id = db.Column(db.Integer, primary_key=True)
    session_id = db.Column(db.Integer, db.ForeignKey('monitoring_sessions.id'), nullable=True)
    driver_id = db.Column(db.Integer, db.ForeignKey('users.id'), nullable=False, index=True)
    alert_type = db.Column(db.String(50), nullable=False) # 'kantuk', 'tidak_fokus', 'menguap', 'wajah_hilang'
    severity = db.Column(db.String(20), default='BAHAYA')   # 'BAHAYA', 'PERINGATAN'
    duration_seconds = db.Column(db.Float, default=1.5)
    snapshot_path = db.Column(db.String(255), nullable=True)
    timestamp = db.Column(db.DateTime, default=datetime.utcnow, nullable=False, index=True)
    notes = db.Column(db.Text, nullable=True)

    def to_dict(self):
        return {
            'id': self.id,
            'driver_id': self.driver_id,
            'driver_name': self.driver.full_name if self.driver else 'Driver',
            'alert_type': self.alert_type,
            'severity': self.severity,
            'duration_seconds': self.duration_seconds,
            'snapshot_path': self.snapshot_path,
            'timestamp': self.timestamp.strftime('%Y-%m-%d %H:%M:%S'),
            'notes': self.notes
        }


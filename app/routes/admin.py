import os
import joblib
from flask import Blueprint, render_template, request, redirect, url_for, flash, jsonify, current_app
from flask_login import login_required, current_user
from app.models import db, User, AlertLog, MonitoringSession

admin_bp = Blueprint('admin', __name__)

@admin_bp.before_request
def ensure_admin():
    """Memastikan hanya user dengan role 'admin' yang dapat mengakses modul ini."""
    if not current_user.is_authenticated:
        return redirect(url_for('auth.login', next=request.path))
    if not current_user.is_admin():
        flash('Akses ditolak. Halaman ini khusus Administrator.', 'danger')
        return redirect(url_for('driver.monitor'))

@admin_bp.route('/dashboard')
def dashboard():
    """FR-13: Admin dapat memantau data hasil monitoring dan statistik insiden."""
    total_drivers = User.query.filter_by(role='driver').count()
    total_alerts = AlertLog.query.count()
    total_drowsy = AlertLog.query.filter_by(alert_type='kantuk').count()
    total_distraction = AlertLog.query.filter_by(alert_type='tidak_fokus').count()
    total_yawn = AlertLog.query.filter_by(alert_type='menguap').count()

    # Ambil 20 insiden peringatan terbaru
    recent_alerts = AlertLog.query.order_by(AlertLog.timestamp.desc()).limit(20).all()

    # Periksa status file model yang aktif
    models_dir = current_app.config['MODELS_DIR']
    sklearn_model = os.path.join(models_dir, 'trained_model.joblib')
    pytorch_model = os.path.join(models_dir, 'trained_model.pt')
    
    active_model_info = {
        'status': 'Baseline Geometris (EAR/MAR)',
        'accuracy': 'Standar Heuristik',
        'type': 'Rule-Based'
    }

    if os.path.exists(sklearn_model):
        try:
            bundle = joblib.load(sklearn_model)
            active_model_info = {
                'status': bundle.get('name', 'Scikit-Learn Model'),
                'accuracy': f"{bundle.get('accuracy', 0.0):.2f}%",
                'type': 'Machine Learning (Joblib)'
            }
        except Exception:
            pass
    elif os.path.exists(pytorch_model):
        active_model_info = {
            'status': 'PyTorch Deep Learning Model',
            'accuracy': 'Terlatih',
            'type': 'Neural Network (.pt)'
        }

    return render_template(
        'admin_dashboard.html',
        total_drivers=total_drivers,
        total_alerts=total_alerts,
        total_drowsy=total_drowsy,
        total_distraction=total_distraction,
        total_yawn=total_yawn,
        recent_alerts=recent_alerts,
        active_model_info=active_model_info,
        user=current_user
    )

@admin_bp.route('/drivers')
def manage_drivers():
    """FR-12: Admin dapat mengelola data Driver."""
    drivers = User.query.filter_by(role='driver').order_by(User.created_at.desc()).all()
    return render_template('admin_drivers.html', drivers=drivers, user=current_user)

@admin_bp.route('/drivers/create', methods=['POST'])
def create_driver():
    """FR-12: Tambah driver baru dari panel admin."""
    username = request.form.get('username', '').strip()
    full_name = request.form.get('full_name', '').strip()
    nim_nik = request.form.get('nim_nik', '').strip()
    password = request.form.get('password', '')

    if not username or not full_name or not password:
        flash('Username, Nama Lengkap, dan Password wajib diisi.', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    if len(password) < 6:
        flash('Password minimal harus terdiri dari 6 karakter.', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    if User.query.filter_by(username=username).first():
        flash('Username sudah digunakan.', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    new_driver = User(username=username, full_name=full_name, nim_nik=nim_nik, role='driver')
    new_driver.set_password(password)
    db.session.add(new_driver)
    db.session.commit()

    flash(f'Driver {full_name} berhasil ditambahkan.', 'success')
    return redirect(url_for('admin.manage_drivers'))

@admin_bp.route('/drivers/delete/<int:driver_id>', methods=['POST'])
def delete_driver(driver_id):
    """FR-12: Hapus data driver."""
    driver = db.session.get(User, driver_id)
    if not driver:
        flash('Data driver tidak ditemukan.', 'warning')
        return redirect(url_for('admin.manage_drivers'))

    if driver.role == 'admin' or driver.id == current_user.id:
        flash('Akun Administrator tidak dapat dihapus.', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    name = driver.full_name
    db.session.delete(driver)
    db.session.commit()
    flash(f'Data driver {name} berhasil dihapus.', 'info')
    return redirect(url_for('admin.manage_drivers'))

@admin_bp.route('/models')
def manage_models():
    """Halaman Manajemen & Evaluasi Model AI."""
    models_dir = current_app.config['MODELS_DIR']
    models_list = []
    
    for filename in os.listdir(models_dir):
        if filename.endswith(('.joblib', '.pt', '.png')):
            fpath = os.path.join(models_dir, filename)
            models_list.append({
                'filename': filename,
                'size_kb': round(os.path.getsize(fpath) / 1024, 2),
                'modified': os.path.getmtime(fpath)
            })

    cm_exists = os.path.exists(os.path.join(models_dir, 'confusion_matrix.png'))
    return render_template('admin_models.html', models_list=models_list, cm_exists=cm_exists, user=current_user)

@admin_bp.route('/models/confusion_matrix')
def view_confusion_matrix():
    """Menampilkan grafik evaluasi model (Confusion Matrix) secara aman."""
    from flask import send_from_directory
    models_dir = current_app.config['MODELS_DIR']
    return send_from_directory(models_dir, 'confusion_matrix.png')

@admin_bp.route('/models/reload', methods=['POST'])
def reload_model():
    """Reload model di memori DMS Engine."""
    from app.routes.driver import classifier
    models_dir = current_app.config['MODELS_DIR']
    sklearn_model = os.path.join(models_dir, 'trained_model.joblib')
    pytorch_model = os.path.join(models_dir, 'trained_model.pt')
    
    target = sklearn_model if os.path.exists(sklearn_model) else (pytorch_model if os.path.exists(pytorch_model) else None)
    
    if classifier and target:
        success = classifier.load_model(target)
        if success:
            flash(f'Model AI berhasil dimuat ulang: {classifier.model_name}', 'success')
        else:
            flash('Gagal memuat model. Kembali ke heuristik baseline.', 'warning')
    else:
        flash('Belum ada file model terlatih di direktori models/. Silakan jalankan script training_suite.', 'info')

    return redirect(url_for('admin.manage_models'))


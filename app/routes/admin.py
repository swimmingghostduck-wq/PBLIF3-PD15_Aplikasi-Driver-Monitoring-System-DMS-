import os
import time
import io
import csv
import joblib
from flask import Blueprint, render_template, request, redirect, url_for, flash, jsonify, current_app, Response
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
    """FR-13: Admin dapat memantau data hasil monitoring, memfilter data, dan melihat statistik insiden."""
    # Statistik Global Keseluruhan
    total_drivers = User.query.filter_by(role='driver').count()
    total_alerts = AlertLog.query.count()
    total_drowsy = AlertLog.query.filter_by(alert_type='kantuk').count()
    total_distraction = AlertLog.query.filter_by(alert_type='tidak_fokus').count()
    total_yawn = AlertLog.query.filter_by(alert_type='menguap').count()

    # Parameter Filter & Pencarian
    selected_driver_id = request.args.get('driver_id', type=int)
    selected_alert_type = request.args.get('alert_type', type=str, default='').strip()
    search_query = request.args.get('q', type=str, default='').strip()

    query = AlertLog.query
    if selected_driver_id:
        query = query.filter(AlertLog.driver_id == selected_driver_id)
    if selected_alert_type:
        query = query.filter(AlertLog.alert_type == selected_alert_type)
    if search_query:
        query = query.join(User, AlertLog.driver_id == User.id).filter(
            db.or_(
                User.full_name.ilike(f'%{search_query}%'),
                User.username.ilike(f'%{search_query}%'),
                AlertLog.notes.ilike(f'%{search_query}%')
            )
        )

    # Ambil hingga 100 insiden terbaru sesuai filter
    filtered_alerts = query.order_by(AlertLog.timestamp.desc()).limit(100).all()
    all_drivers = User.query.filter_by(role='driver').order_by(User.full_name.asc()).all()

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
        recent_alerts=filtered_alerts,
        all_drivers=all_drivers,
        selected_driver_id=selected_driver_id,
        selected_alert_type=selected_alert_type,
        search_query=search_query,
        active_model_info=active_model_info,
        user=current_user
    )

@admin_bp.route('/alerts/export/csv')
def export_alerts_csv():
    """FR-13: Ekspor log insiden monitoring driver ke file CSV untuk bukti laporan PBL."""
    selected_driver_id = request.args.get('driver_id', type=int)
    selected_alert_type = request.args.get('alert_type', type=str, default='').strip()
    search_query = request.args.get('q', type=str, default='').strip()

    query = AlertLog.query
    if selected_driver_id:
        query = query.filter(AlertLog.driver_id == selected_driver_id)
    if selected_alert_type:
        query = query.filter(AlertLog.alert_type == selected_alert_type)
    if search_query:
        query = query.join(User, AlertLog.driver_id == User.id).filter(
            db.or_(
                User.full_name.ilike(f'%{search_query}%'),
                User.username.ilike(f'%{search_query}%'),
                AlertLog.notes.ilike(f'%{search_query}%')
            )
        )

    alerts = query.order_by(AlertLog.timestamp.desc()).all()

    output = io.StringIO()
    writer = csv.writer(output)
    writer.writerow([
        'ID Log',
        'Waktu Kejadian (UTC)',
        'Nama Driver',
        'Username',
        'NIM/NIK',
        'Jenis Peringatan',
        'Tingkat Bahaya',
        'Durasi (Detik)',
        'Catatan Telemetri (EAR/MAR/Pose/Gaze)',
        'File Snapshot Bukti'
    ])

    for a in alerts:
        writer.writerow([
            a.id,
            a.timestamp.strftime('%Y-%m-%d %H:%M:%S'),
            a.driver.full_name if a.driver else f'Driver #{a.driver_id}',
            a.driver.username if a.driver else '-',
            a.driver.nim_nik if (a.driver and a.driver.nim_nik) else '-',
            a.alert_type,
            a.severity,
            a.duration_seconds,
            a.notes or '-',
            a.snapshot_path or '-'
        ])

    output.seek(0)
    filename = f"dms_log_insiden_{time.strftime('%Y%m%d_%H%M%S')}.csv"
    return Response(
        output.getvalue(),
        mimetype="text/csv",
        headers={"Content-Disposition": f"attachment; filename={filename}"}
    )

@admin_bp.route('/alerts/delete/<int:alert_id>', methods=['POST'])
def delete_alert(alert_id):
    """FR-13: Hapus catatan insiden individual dan file snapshot terkait."""
    alert = db.session.get(AlertLog, alert_id)
    if not alert:
        flash('Data insiden tidak ditemukan.', 'warning')
        return redirect(url_for('admin.dashboard'))

    # Hapus file snapshot jika tersimpan di disk
    if alert.snapshot_path:
        try:
            snap_file = os.path.join(current_app.config['SNAPSHOTS_DIR'], alert.snapshot_path)
            if os.path.exists(snap_file):
                os.remove(snap_file)
        except Exception:
            pass

    db.session.delete(alert)
    db.session.commit()
    flash(f'Catatan insiden #{alert_id} berhasil dihapus.', 'info')
    return redirect(url_for('admin.dashboard'))

@admin_bp.route('/alerts/clear', methods=['POST'])
def clear_alerts():
    """FR-13: Bersihkan seluruh data log insiden uji coba dari database."""
    total = AlertLog.query.count()
    AlertLog.query.delete()
    db.session.commit()
    flash(f'Seluruh data log uji coba ({total} insiden) berhasil dibersihkan dari database.', 'info')
    return redirect(url_for('admin.dashboard'))

@admin_bp.route('/drivers')
def manage_drivers():
    """FR-12: Admin dapat mengelola data Driver (CRUD)."""
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

@admin_bp.route('/drivers/edit/<int:driver_id>', methods=['POST'])
def edit_driver(driver_id):
    """FR-12: Perbarui data profil driver atau reset kata sandi."""
    driver = db.session.get(User, driver_id)
    if not driver:
        flash('Data driver tidak ditemukan.', 'warning')
        return redirect(url_for('admin.manage_drivers'))

    if driver.role == 'admin':
        flash('Akun Administrator tidak dapat diubah melalui menu ini.', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    full_name = request.form.get('full_name', '').strip()
    nim_nik = request.form.get('nim_nik', '').strip()
    username = request.form.get('username', '').strip()
    new_password = request.form.get('new_password', '').strip()

    if not full_name or not username:
        flash('Nama Lengkap dan Username tidak boleh kosong.', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    # Validasi username unik (kecuali milik diri sendiri)
    duplicate_user = User.query.filter(User.username == username, User.id != driver_id).first()
    if duplicate_user:
        flash(f'Username "{username}" sudah digunakan akun lain.', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    driver.full_name = full_name
    driver.nim_nik = nim_nik
    driver.username = username

    if new_password:
        if len(new_password) < 6:
            flash('Password baru minimal harus 6 karakter.', 'danger')
            return redirect(url_for('admin.manage_drivers'))
        driver.set_password(new_password)
        flash(f'Profil dan password driver {full_name} berhasil diperbarui.', 'success')
    else:
        flash(f'Profil driver {full_name} berhasil diperbarui.', 'success')

    db.session.commit()
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
    
    if os.path.exists(models_dir):
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
    """Perbaikan Bug: Reload model di memori server DMS Engine dengan inisialisasi yang terjamin."""
    import app.routes.driver as driver_mod
    
    # Pastikan DMS Engine sudah terinisialisasi
    driver_mod.init_dms_engine(current_app)
    
    models_dir = current_app.config['MODELS_DIR']
    sklearn_model = os.path.join(models_dir, 'trained_model.joblib')
    pytorch_model = os.path.join(models_dir, 'trained_model.pt')
    
    target = sklearn_model if os.path.exists(sklearn_model) else (pytorch_model if os.path.exists(pytorch_model) else None)
    
    if driver_mod.classifier and target:
        success = driver_mod.classifier.load_model(target)
        if success:
            flash(f'Model AI berhasil dimuat ulang ke memori engine: {driver_mod.classifier.model_name}', 'success')
        else:
            flash('Gagal memuat model. Kembali ke heuristik baseline.', 'warning')
    elif not target:
        flash('Belum ada file model terlatih di direktori models/. Silakan jalankan script training_suite.', 'info')
    else:
        flash('Engine klasifikasi belum aktif.', 'warning')

    return redirect(url_for('admin.manage_models'))

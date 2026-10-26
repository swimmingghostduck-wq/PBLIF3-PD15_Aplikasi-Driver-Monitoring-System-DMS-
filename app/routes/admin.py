import os
import re
import time
import io
import csv
import hashlib
import joblib
from flask import Blueprint, render_template, request, redirect, url_for, flash, jsonify, current_app, Response, session
from flask_login import login_required, current_user, login_user
from app.models import db, User, AlertLog, MonitoringSession

admin_bp = Blueprint('admin', __name__)

@admin_bp.app_template_filter('timeago')
def timeago_filter(dt):
    """Mengubah objek datetime menjadi format waktu relatif yang mudah dipahami manusia."""
    if not dt:
        return ""
    from datetime import datetime
    now = datetime.now()
    diff = now - dt
    total_seconds = int(diff.total_seconds())
    if total_seconds < 0:
        return "Baru saja"
    if total_seconds < 60:
        return f"{total_seconds} detik yang lalu" if total_seconds > 5 else "Baru saja"
    minutes = total_seconds // 60
    if minutes < 60:
        return f"{minutes} menit yang lalu"
    hours = minutes // 60
    if hours < 24:
        return f"{hours} jam yang lalu"
    days = hours // 24
    if days < 7:
        return f"{days} hari yang lalu"
    return dt.strftime('%d/%m/%Y')

@admin_bp.app_template_filter('clean_operational_note')
def clean_operational_note_filter(notes, alert_type=None):
    """
    Menghilangkan jargon teknis machine learning (seperti 'Metode: Random Forest Classifier', 'EAR:', 'MAR:', dll.)
    dan menggantinya dengan konteks operasional yang ramah bagi administrator armada.
    """
    if not notes or not isinstance(notes, str) or notes.strip() == '' or notes.strip() == '-':
        if alert_type == 'kantuk':
            return "Mata terpejam saat berkendara"
        elif alert_type == 'tidak_fokus':
            return "Pandangan teralihkan dari arah jalan"
        elif alert_type == 'menguap':
            return "Kelelahan terdeteksi"
        return "Insiden kewaspadaan terdeteksi"

    # Filter out ML technical jargon
    cleaned = re.sub(r'Metode:\s*[^|;]+[|;]?', '', notes, flags=re.IGNORECASE)
    cleaned = re.sub(r'\(Nilai EAR:[^)]+\)', '', cleaned, flags=re.IGNORECASE)
    cleaned = re.sub(r'\(Nilai MAR:[^)]+\)', '', cleaned, flags=re.IGNORECASE)
    cleaned = re.sub(r'\(Sudut Pitch:[^)]+\)', '', cleaned, flags=re.IGNORECASE)
    cleaned = re.sub(r'\(Sudut Yaw:[^)]+\)', '', cleaned, flags=re.IGNORECASE)
    cleaned = re.sub(r'EAR:\s*[\d.]+', '', cleaned, flags=re.IGNORECASE)
    cleaned = re.sub(r'MAR:\s*[\d.]+', '', cleaned, flags=re.IGNORECASE)
    cleaned = re.sub(r'Yaw:\s*[^|;]+', '', cleaned, flags=re.IGNORECASE)
    cleaned = re.sub(r'Pitch:\s*[^|;]+', '', cleaned, flags=re.IGNORECASE)
    cleaned = cleaned.replace('|', '').replace(';', '').strip()

    if not cleaned:
        if alert_type == 'kantuk':
            return "Mata terpejam saat berkendara"
        elif alert_type == 'tidak_fokus':
            return "Pandangan teralihkan dari arah jalan"
        elif alert_type == 'menguap':
            return "Kelelahan terdeteksi"
        return "Insiden kewaspadaan terdeteksi"

    return cleaned

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
    """FR-13: Executive Telematics Dashboard (Overview kesehatan armada, distribusi risiko, & feed aktivitas)."""
    # Statistik Global Keseluruhan
    total_drivers = User.query.filter_by(role='driver').count()
    total_alerts = AlertLog.query.count()
    total_drowsy = AlertLog.query.filter_by(alert_type='kantuk').count()
    total_distraction = AlertLog.query.filter_by(alert_type='tidak_fokus').count()
    total_yawn = AlertLog.query.filter_by(alert_type='menguap').count()
    total_sessions = MonitoringSession.query.count()

    # Indeks Keselamatan Armada (0 - 100%)
    if total_alerts == 0:
        fleet_safety_score = 100.0
    else:
        fleet_safety_score = round(max(60.0, 100.0 - (total_alerts * 2.5)), 1)

    # Distribusi Temporal Kejadian (Pola Waktu Insiden)
    all_alert_logs = AlertLog.query.all()
    time_bins = {
        'Pagi (06-12)': sum(1 for a in all_alert_logs if 6 <= a.timestamp.hour < 12),
        'Siang (12-18)': sum(1 for a in all_alert_logs if 12 <= a.timestamp.hour < 18),
        'Malam (18-24)': sum(1 for a in all_alert_logs if 18 <= a.timestamp.hour < 24),
        'Dini Hari (00-06)': sum(1 for a in all_alert_logs if 0 <= a.timestamp.hour < 6),
    }

    # Parameter Filter & Pencarian (untuk feed aktivitas terkini)
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

    # Feed 6 insiden terbaru untuk activity stream di dashboard
    recent_alerts = query.order_by(AlertLog.timestamp.desc()).limit(6).all()
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

    # Roster pengemudi & skor keselamatan
    driver_roster = []
    for d in all_drivers:
        drowsy_cnt = sum(1 for a in d.alerts if a.alert_type == 'kantuk')
        distract_cnt = sum(1 for a in d.alerts if a.alert_type == 'tidak_fokus')
        yawn_cnt = sum(1 for a in d.alerts if a.alert_type == 'menguap')
        total_cnt = len(d.alerts)
        score = max(50, 100 - (total_cnt * 8))
        tier = 'Tier 1 Prima' if total_cnt == 0 else ('Tier 2 Standar' if total_cnt < 5 else 'Tier 3 Perhatian')
        driver_roster.append({
            'driver': d,
            'total_alerts': total_cnt,
            'safety_score': score,
            'tier': tier,
            'drowsy': drowsy_cnt,
            'distract': distract_cnt,
            'yawn': yawn_cnt
        })
    driver_roster.sort(key=lambda x: x['total_alerts'], reverse=True)

    return render_template(
        'admin_dashboard.html',
        total_drivers=total_drivers,
        total_alerts=total_alerts,
        total_drowsy=total_drowsy,
        total_distraction=total_distraction,
        total_yawn=total_yawn,
        total_sessions=total_sessions,
        fleet_safety_score=fleet_safety_score,
        time_bins=time_bins,
        recent_alerts=recent_alerts,
        driver_roster=driver_roster,
        all_drivers=all_drivers,
        selected_driver_id=selected_driver_id,
        selected_alert_type=selected_alert_type,
        search_query=search_query,
        active_model_info=active_model_info,
        user=current_user
    )

@admin_bp.route('/alerts')
def alerts():
    """FR-13: Halaman Ringkasan Insiden (Tabel audit riwayat insiden pengemudi lengkap dengan filter & ekspor)."""
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

    return render_template(
        'admin_alerts.html',
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

    def sanitize_csv_field(val):
        """Mencegah CSV Formula Injection (DDE) jika data dibuka di Microsoft Excel atau LibreOffice."""
        if val is None:
            return ''
        s = str(val).strip()
        if s and s[0] in ('=', '+', '-', '@', '\t', '\r'):
            return f"'{s}"
        return s

    for a in alerts:
        writer.writerow([
            a.id,
            a.timestamp.strftime('%Y-%m-%d %H:%M:%S'),
            sanitize_csv_field(a.driver.full_name if a.driver else f'Driver #{a.driver_id}'),
            sanitize_csv_field(a.driver.username if a.driver else '-'),
            sanitize_csv_field(a.driver.nim_nik if (a.driver and a.driver.nim_nik) else '-'),
            sanitize_csv_field(a.alert_type),
            sanitize_csv_field(a.severity),
            a.duration_seconds,
            sanitize_csv_field(a.notes or '-'),
            sanitize_csv_field(a.snapshot_path or '-')
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

    # Hapus file snapshot jika tersimpan di disk (dengan pencegahan path traversal)
    if alert.snapshot_path:
        try:
            safe_basename = os.path.basename(alert.snapshot_path)
            snap_file = os.path.join(current_app.config['SNAPSHOTS_DIR'], safe_basename)
            if os.path.exists(snap_file):
                os.remove(snap_file)
        except Exception:
            pass

    db.session.delete(alert)
    db.session.commit()
    flash(f'Catatan insiden #{alert_id} berhasil dihapus.', 'info')
    return redirect(request.referrer or url_for('admin.alerts'))

@admin_bp.route('/alerts/clear', methods=['POST'])
def clear_alerts():
    """FR-13: Bersihkan seluruh data log insiden uji coba dari database."""
    total = AlertLog.query.count()
    AlertLog.query.delete()
    db.session.commit()
    flash(f'Seluruh data log uji coba ({total} insiden) berhasil dibersihkan dari database.', 'info')
    return redirect(request.referrer or url_for('admin.alerts'))

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

    if len(full_name) > 100:
        flash('Nama Lengkap tidak boleh lebih dari 100 karakter.', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    if len(username) < 3 or len(username) > 50:
        flash('Username harus berdurasi antara 3 hingga 50 karakter.', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    if not re.match(r'^[a-zA-Z0-9_.-]+$', username):
        flash('Username hanya boleh memuat huruf, angka, garis bawah (_), titik (.), dan strip (-).', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    if nim_nik and len(nim_nik) > 30:
        flash('NIM/NIK tidak boleh lebih dari 30 karakter.', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    if len(password) < 6:
        flash('Password minimal harus terdiri dari 6 karakter.', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    if len(password) > 128:
        flash('Password tidak boleh lebih dari 128 karakter.', 'danger')
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

    if len(full_name) > 100:
        flash('Nama Lengkap tidak boleh lebih dari 100 karakter.', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    if len(username) < 3 or len(username) > 50:
        flash('Username harus berdurasi antara 3 hingga 50 karakter.', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    if not re.match(r'^[a-zA-Z0-9_.-]+$', username):
        flash('Username hanya boleh memuat huruf, angka, garis bawah (_), titik (.), dan strip (-).', 'danger')
        return redirect(url_for('admin.manage_drivers'))

    if nim_nik and len(nim_nik) > 30:
        flash('NIM/NIK tidak boleh lebih dari 30 karakter.', 'danger')
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
        if len(new_password) > 128:
            flash('Password baru tidak boleh lebih dari 128 karakter.', 'danger')
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

@admin_bp.route('/profile', methods=['GET', 'POST'])
def profile():
    """Halaman Pengaturan Profil & Data Diri Administrator."""
    admin_user = db.session.get(User, current_user.id)
    if not admin_user:
        flash('Data akun pengguna tidak ditemukan.', 'danger')
        return redirect(url_for('admin.dashboard'))

    # Format ID Unik Administrator
    hash_seed = f"{admin_user.id}-{admin_user.username}-{admin_user.created_at or ''}"
    short_hash = hashlib.sha256(hash_seed.encode()).hexdigest()[:6].upper()
    unique_admin_id = f"DS-ADM-{admin_user.id:04d}-{short_hash}"

    if request.method == 'POST':
        full_name = request.form.get('full_name', '').strip()
        username = request.form.get('username', '').strip()
        nim_nik = request.form.get('nim_nik', '').strip()
        current_password = request.form.get('current_password', '')
        new_password = request.form.get('new_password', '').strip()
        confirm_password = request.form.get('confirm_password', '').strip()

        if not full_name or not username:
            flash('Nama Lengkap dan Username wajib diisi.', 'danger')
            return render_template('admin_profile.html', user=admin_user, unique_admin_id=unique_admin_id)

        # Validasi keamanan input (panjang & karakter)
        if len(full_name) > 100:
            flash('Nama Lengkap tidak boleh lebih dari 100 karakter.', 'danger')
            return render_template('admin_profile.html', user=admin_user, unique_admin_id=unique_admin_id)

        if len(username) < 3 or len(username) > 50:
            flash('Username harus berdurasi antara 3 hingga 50 karakter.', 'danger')
            return render_template('admin_profile.html', user=admin_user, unique_admin_id=unique_admin_id)

        if not re.match(r'^[a-zA-Z0-9_.-]+$', username):
            flash('Username hanya boleh memuat huruf, angka, garis bawah (_), titik (.), dan strip (-).', 'danger')
            return render_template('admin_profile.html', user=admin_user, unique_admin_id=unique_admin_id)

        if nim_nik and len(nim_nik) > 30:
            flash('NIM/NIK tidak boleh lebih dari 30 karakter.', 'danger')
            return render_template('admin_profile.html', user=admin_user, unique_admin_id=unique_admin_id)

        # Cek jika username baru bentrok dengan user lain
        existing = User.query.filter(User.username == username, User.id != admin_user.id).first()
        if existing:
            flash(f'Username "@{username}" sudah digunakan akun lain. Silakan pilih username lain.', 'danger')
            return render_template('admin_profile.html', user=admin_user, unique_admin_id=unique_admin_id)

        admin_user.full_name = full_name
        admin_user.username = username
        admin_user.nim_nik = nim_nik

        # Penanganan ganti password baru (Re-Authentication & Integrity)
        if new_password:
            if not current_password:
                flash('Masukkan kata sandi saat ini untuk memverifikasi penggantian kata sandi.', 'warning')
                return render_template('admin_profile.html', user=admin_user, unique_admin_id=unique_admin_id)
            if not admin_user.check_password(current_password):
                flash('Kata sandi saat ini yang Anda masukkan salah. Perubahan kata sandi dibatalkan.', 'danger')
                return render_template('admin_profile.html', user=admin_user, unique_admin_id=unique_admin_id)
            if len(new_password) < 6:
                flash('Kata sandi baru minimal harus terdiri dari 6 karakter.', 'danger')
                return render_template('admin_profile.html', user=admin_user, unique_admin_id=unique_admin_id)
            if len(new_password) > 128:
                flash('Kata sandi baru tidak boleh melebihi 128 karakter demi keamanan komputasi.', 'danger')
                return render_template('admin_profile.html', user=admin_user, unique_admin_id=unique_admin_id)
            if new_password != confirm_password:
                flash('Konfirmasi kata sandi baru tidak cocok.', 'danger')
                return render_template('admin_profile.html', user=admin_user, unique_admin_id=unique_admin_id)

            admin_user.set_password(new_password)
            db.session.commit()

            # Refresh sesi login untuk menjaga integritas kredensial aktif
            login_user(admin_user)
            session['_last_active'] = time.time()

            flash('Profil data diri dan kata sandi baru Anda berhasil diperbarui!', 'success')
        else:
            db.session.commit()
            flash('Profil data diri berhasil diperbarui!', 'success')

        return redirect(url_for('admin.profile'))

    return render_template('admin_profile.html', user=admin_user, unique_admin_id=unique_admin_id)

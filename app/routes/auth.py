import time
from flask import Blueprint, render_template, redirect, url_for, flash, request, session
from flask_login import login_user, logout_user, login_required, current_user
from app.models import db, User

auth_bp = Blueprint('auth', __name__)

@auth_bp.route('/login', methods=['GET', 'POST'])
def login():
    """FR-02: Pengguna (Driver / Admin) dapat melakukan login."""
    if current_user.is_authenticated:
        if current_user.is_admin():
            return redirect(url_for('admin.dashboard'))
        return redirect(url_for('driver.monitor'))

    if request.method == 'POST':
        username = request.form.get('username', '').strip()
        password = request.form.get('password', '')
        remember = True if request.form.get('remember') else False

        user = User.query.filter_by(username=username).first()
        if not user or not user.check_password(password):
            flash('Username atau password yang Anda masukkan salah.', 'danger')
            return render_template('login.html', username=username)

        # Bersihkan sesi lama untuk mencegah Session Fixation Attack
        session.clear()
        login_user(user, remember=remember)
        session.permanent = remember
        session['_last_active'] = time.time()

        flash(f'Selamat datang, {user.full_name}!', 'success')

        # Tangani pengalihan aman (mencegah Open Redirect)
        next_page = request.args.get('next')
        if next_page and next_page.startswith('/') and not next_page.startswith('//'):
            if not user.is_admin() and next_page.startswith('/admin'):
                next_page = url_for('driver.monitor')
            return redirect(next_page)

        if user.is_admin():
            return redirect(url_for('admin.dashboard'))
        return redirect(url_for('driver.monitor'))

    return render_template('login.html')

@auth_bp.route('/register', methods=['GET', 'POST'])
def register():
    """FR-01: Driver dapat membuat akun baru."""
    if current_user.is_authenticated:
        if current_user.is_admin():
            return redirect(url_for('admin.dashboard'))
        return redirect(url_for('driver.monitor'))

    if request.method == 'POST':
        username = request.form.get('username', '').strip()
        full_name = request.form.get('full_name', '').strip()
        nim_nik = request.form.get('nim_nik', '').strip()
        password = request.form.get('password', '')
        confirm_password = request.form.get('confirm_password', '')

        if not username or not full_name or not password:
            flash('Semua kolom bertanda bintang wajib diisi.', 'danger')
            return render_template('register.html')

        if len(username) < 3:
            flash('Username minimal harus terdiri dari 3 karakter.', 'danger')
            return render_template('register.html')

        if len(password) < 6:
            flash('Password minimal harus terdiri dari 6 karakter.', 'danger')
            return render_template('register.html')

        if password != confirm_password:
            flash('Konfirmasi password tidak cocok.', 'danger')
            return render_template('register.html')

        if User.query.filter_by(username=username).first():
            flash('Username sudah digunakan, silakan pilih username lain.', 'danger')
            return render_template('register.html')

        new_driver = User(
            username=username,
            full_name=full_name,
            nim_nik=nim_nik,
            role='driver'
        )
        new_driver.set_password(password)
        db.session.add(new_driver)
        db.session.commit()

        flash('Pendaftaran akun Driver berhasil! Silakan login.', 'success')
        return redirect(url_for('auth.login'))

    return render_template('register.html')

@auth_bp.route('/logout')
@login_required
def logout():
    """FR-11: Pengguna dapat melakukan Logout pada aplikasi."""
    logout_user()
    session.clear()
    flash('Anda telah berhasil keluar dari sistem.', 'info')
    return redirect(url_for('auth.login'))


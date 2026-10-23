import os
import sys

# Auto-switch ke virtual environment jika dijalankan langsung dengan Python global
ROOT_DIR = os.path.abspath(os.path.dirname(__file__))
_VENV_PY = os.path.join(ROOT_DIR, 'venv', 'Scripts', 'python.exe')
if not os.path.exists(_VENV_PY):
    _VENV_PY = os.path.join(ROOT_DIR, 'venv', 'bin', 'python')
if os.path.exists(_VENV_PY) and os.path.abspath(sys.executable).lower() != os.path.abspath(_VENV_PY).lower():
    import subprocess
    sys.exit(subprocess.run([_VENV_PY] + sys.argv).returncode)

if ROOT_DIR not in sys.path:
    sys.path.insert(0, ROOT_DIR)

import unittest
import base64
import numpy as np
import cv2

class TestDMSSystem(unittest.TestCase):
    """Pengujian Unit dan Integrasi Otomatis untuk Seluruh Modul DMS."""

    def test_01_imports(self):
        """Memastikan semua pustaka dan modul kustom dapat diimpor tanpa kendala."""
        import cv2
        import mediapipe
        import sklearn
        import torch
        import flask
        import pymysql
        from dms_core.detector import FaceLandmarkDetector
        from dms_core.classifier import DrowsinessClassifier
        from dms_core.tracker import DrowsinessTracker
        from training_suite.dataset_loader import DatasetLoader
        from training_suite.train_sklearn import train_model
        from app import create_app
        self.assertTrue(True)

    def test_02_database_init(self):
        """Memastikan inisialisasi tabel dan akun default berhasil."""
        from init_db import init_database
        from app import create_app
        from app.models import User
        init_database()
        app = create_app()
        with app.app_context():
            admin = User.query.filter_by(username='admin').first()
            driver = User.query.filter_by(username='driver').first()
            self.assertIsNotNone(admin)
            self.assertIsNotNone(driver)
            self.assertTrue(admin.check_password('admin123'))
            self.assertTrue(driver.check_password('driver123'))

    def test_03_detector_synthetic_frame(self):
        """Memastikan FaceLandmarkDetector menangani frame kosong & frame gambar dengan aman."""
        from dms_core.detector import FaceLandmarkDetector
        detector = FaceLandmarkDetector()
        
        empty_res = detector.process_frame(None)
        self.assertFalse(empty_res['face_detected'])

        black_frame = np.zeros((480, 640, 3), dtype=np.uint8)
        res = detector.process_frame(black_frame)
        self.assertFalse(res['face_detected'])
        self.assertEqual(res['ear_avg'], 0.0)

    def test_04_dataset_loader(self):
        """Memastikan DatasetLoader mampu menghasilkan dataset fisiologis dan normalisasi."""
        from training_suite.dataset_loader import DatasetLoader
        loader = DatasetLoader()
        df = loader.generate_synthetic_baseline_dataset(samples_per_class=50)
        self.assertEqual(len(df), 125)
        self.assertIn('label', df.columns)

        X_train, X_test, y_train, y_test, scaler = loader.load_data()
        self.assertTrue(X_train.shape[0] > 0)
        self.assertEqual(X_train.shape[1], 10)

    def test_05_train_and_evaluate_model(self):
        """Memastikan pipeline training Machine Learning menghasilkan akurasi >= 80% (NFR-05)."""
        from training_suite.train_sklearn import train_model
        from training_suite.evaluate import evaluate_model
        model_path, acc = train_model(model_type='rf')
        self.assertTrue(os.path.exists(model_path))
        self.assertGreaterEqual(acc, 80.0, f"Akurasi ({acc}%) harus memenuhi standar NFR-05 (>= 80%)")

        evaluate_model()
        cm_path = os.path.join(ROOT_DIR, 'models', 'confusion_matrix.png')
        self.assertTrue(os.path.exists(cm_path))

    def test_06_classifier_inference(self):
        """Memastikan classifier memuat model hasil training dan memprediksi status dengan tepat."""
        from dms_core.classifier import DrowsinessClassifier
        model_path = os.path.join(ROOT_DIR, 'models', 'trained_model.joblib')
        clf = DrowsinessClassifier(model_path=model_path)
        self.assertEqual(clf.model_type, 'sklearn')

        mock_drowsy_features = {
            'face_detected': True,
            'ear_avg': 0.12,
            'mar': 0.30,
            'features_vector': [0.12, 0.12, 0.12, 0.30, 0.0, 0.0, 0.0, 0.0, 0.4, 0.0]
        }
        pred = clf.predict(mock_drowsy_features)
        self.assertTrue(pred['is_drowsy'])

    def test_07_tracker_consecutive_frames(self):
        """Memastikan DrowsinessTracker memicu alarm jika mata tertutup melampaui ambang batas."""
        from dms_core.tracker import DrowsinessTracker
        tracker = DrowsinessTracker(drowsy_consec_frames=5)
        
        mock_features = {'face_detected': True, 'yaw': 0.0, 'pitch': 0.0}
        mock_pred = {'is_drowsy': True, 'is_yawning': False, 'status': 'MENGANTUK'}

        # Uji kedipan singkat (frame 1): status harus tetap TERJAGA dan alarm tidak aktif
        blink_status = tracker.update(mock_features, mock_pred)
        self.assertFalse(blink_status['is_alarm_active'])
        self.assertEqual(blink_status['status_label'], 'TERJAGA')

        for _ in range(3):
            status = tracker.update(mock_features, mock_pred)
            self.assertFalse(status['is_alarm_active'])

        status = tracker.update(mock_features, mock_pred)
        self.assertTrue(status['is_alarm_active'])
        self.assertEqual(status['alarm_type'], 'KANTUK')

    def test_07b_distraction_and_head_pose(self):
        """Memastikan estimasi pose kepala (solvePnP) dan deteksi distraksi bekerja presisi."""
        from dms_core.detector import FaceLandmarkDetector
        from dms_core.tracker import DrowsinessTracker
        import numpy as np

        det = FaceLandmarkDetector()
        # Mock 6 titik wajah posisi menatap lurus
        landmarks_pixel = {
            1: (320, 240),
            199: (320, 360),
            33: (240, 180),
            263: (400, 180),
            61: (260, 300),
            291: (380, 300)
        }
        pitch, yaw, roll = det.estimate_head_pose(landmarks_pixel, (480, 640))
        # Sudut menatap lurus harus mendekati 0 derajat (< 5 deg), bukan ribuan derajat
        self.assertLess(abs(pitch), 5.0)
        self.assertLess(abs(yaw), 5.0)

        # Uji tracker saat menoleh ke kanan (> 22 deg)
        tracker = DrowsinessTracker(distraction_consec_frames=2)
        distracted_features = {'face_detected': True, 'yaw': 30.0, 'pitch': -5.0}
        pred_normal = {'is_drowsy': False, 'is_yawning': False, 'status': 'TERJAGA'}
        tracker.update(distracted_features, pred_normal)
        status = tracker.update(distracted_features, pred_normal)
        self.assertTrue(status['is_alarm_active'])
        self.assertEqual(status['alarm_type'], 'TIDAK_FOKUS')
        self.assertIn('Kanan', status['alarm_message'])

        # Uji saat menoleh terlalu jauh (profil samping) sehingga face_detected = False
        tracker2 = DrowsinessTracker(distraction_consec_frames=2)
        tracker2.update({'face_detected': True, 'yaw': 35.0, 'pitch': 0.0}, pred_normal)
        status_side = tracker2.update({'face_detected': False}, pred_normal)
        self.assertTrue(status_side['is_alarm_active'])
        self.assertEqual(status_side['alarm_type'], 'TIDAK_FOKUS')
        self.assertIn('Kanan', status_side['alarm_message'])

        # Uji saat kembali fokus menatap jalan
        focus_features = {'face_detected': True, 'yaw': 2.0, 'pitch': 0.0}
        tracker.update(focus_features, pred_normal)
        status = tracker.update(focus_features, pred_normal)
        self.assertFalse(status['is_alarm_active'])

        # Uji tracker saat menunduk (melihat ponsel / bawah)
        tracker_nod = DrowsinessTracker(distraction_consec_frames=2)
        nod_features = {'face_detected': True, 'yaw': 0.0, 'pitch': 25.0}
        tracker_nod.update(nod_features, pred_normal)
        status_nod = tracker_nod.update(nod_features, pred_normal)
        self.assertTrue(status_nod['is_alarm_active'])
        self.assertEqual(status_nod['alarm_type'], 'TIDAK_FOKUS')
        self.assertIn('Menunduk', status_nod['alarm_message'])

        # Uji saat wajah benar-benar tidak terdeteksi
        tracker_noface = DrowsinessTracker(distraction_consec_frames=2)
        tracker_noface.update({'face_detected': False}, pred_normal)
        status_nf = tracker_noface.update({'face_detected': False}, pred_normal)
        self.assertTrue(status_nf['is_alarm_active'])
        self.assertEqual(status_nf['alarm_type'], 'WAJAH_HILANG')

        # Uji saat kepala miring/menggaruk kepala tetapi mata tetap fokus ke jalan
        tracker_gaze = DrowsinessTracker(distraction_consec_frames=5)
        scratch_features = {'face_detected': True, 'yaw': 26.0, 'pitch': 15.0, 'is_gaze_forward': True}
        for _ in range(5):
            status_scratch = tracker_gaze.update(scratch_features, pred_normal)
        self.assertFalse(status_scratch['is_alarm_active'])
        self.assertEqual(status_scratch['status_label'], 'TERJAGA')

    def test_08_flask_endpoints(self):
        """Memastikan route Flask (Login, Register) merespons dengan HTTP 200."""
        from app import create_app
        app = create_app()
        app.config['TESTING'] = True
        client = app.test_client()

        r_login = client.get('/auth/login')
        self.assertEqual(r_login.status_code, 200)

        r_reg = client.get('/auth/register')
        self.assertEqual(r_reg.status_code, 200)

    def test_09_process_frame_endpoint(self):
        """Memastikan endpoint /driver/process_frame menerima base64 frame dari browser dan merespons JSON."""
        from app import create_app
        from app.models import User
        app = create_app()
        app.config['TESTING'] = True
        client = app.test_client()

        # Login sebagai driver
        client.post('/auth/login', data={'username': 'driver', 'password': 'driver123'})

        # Siapkan gambar uji frame
        test_img = np.zeros((480, 640, 3), dtype=np.uint8)
        _, buf = cv2.imencode('.jpg', test_img)
        b64_data = 'data:image/jpeg;base64,' + base64.b64encode(buf).decode('utf-8')

        res = client.post('/driver/process_frame', json={'image': b64_data})
        self.assertEqual(res.status_code, 200)
        json_data = res.get_json()
        self.assertTrue(json_data['success'])
        self.assertIn('face_detected', json_data)
        self.assertIn('ear_avg', json_data)
        self.assertIn('status_label', json_data)

    def test_10_session_security_and_auth(self):
        """Memastikan fitur keamanan sesi: timeout inaktivitas, logout, dan validasi password."""
        import time
        from app import create_app
        app = create_app()
        app.config['TESTING'] = True
        app.config['SESSION_INACTIVITY_TIMEOUT'] = 2  # 2 detik untuk pengujian timeout cepat
        client = app.test_client()

        # 1. Login
        r_login = client.post('/auth/login', data={'username': 'admin', 'password': 'admin123'})
        self.assertEqual(r_login.status_code, 302)

        # 2. Akses halaman admin saat sesi aktif
        r_dash = client.get('/admin/dashboard')
        self.assertEqual(r_dash.status_code, 200)

        # 3. Akses grafik confusion matrix lewat endpoint aman
        r_cm = client.get('/admin/models/confusion_matrix')
        self.assertEqual(r_cm.status_code, 200)

        # 4. Uji Session Inactivity Timeout (tunggu > 2 detik)
        time.sleep(2.5)
        r_timeout = client.get('/admin/dashboard', follow_redirects=False)
        self.assertEqual(r_timeout.status_code, 302)
        self.assertIn('/auth/login', r_timeout.headers.get('Location', ''))

        # 5. Uji validasi panjang password pada registrasi (< 6 karakter ditolak)
        r_short_pass = client.post('/auth/register', data={
            'username': 'shortuser',
            'full_name': 'Short Password User',
            'nim_nik': '12345',
            'password': '123',
            'confirm_password': '123'
        }, follow_redirects=True)
        self.assertIn(b'minimal harus terdiri dari 6 karakter', r_short_pass.data)

if __name__ == '__main__':
    unittest.main()

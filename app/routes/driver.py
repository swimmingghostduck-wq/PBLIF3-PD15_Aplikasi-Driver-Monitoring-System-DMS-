import os
import cv2
import time
import json
import base64
import numpy as np
from flask import Blueprint, render_template, Response, jsonify, request, current_app
from flask_login import login_required, current_user
from app.models import db, AlertLog, MonitoringSession
from dms_core.detector import FaceLandmarkDetector
from dms_core.classifier import DrowsinessClassifier
from dms_core.tracker import DrowsinessTracker
from dms_core.alert_manager import AlertManager

driver_bp = Blueprint('driver', __name__)

detector = None
classifier = None
tracker = None
alert_manager = None
latest_status = {
    'face_detected': False,
    'status_label': 'STANDBY',
    'ear_avg': 0.0,
    'mar': 0.0,
    'yaw': 0.0,
    'pitch': 0.0,
    'is_alarm_active': False,
    'alarm_type': None,
    'alarm_message': 'Menghubungkan ke kamera...',
    'drowsy_score': 0.0,
    'method': 'Baseline Geometris'
}

def init_dms_engine(app):
    global detector, classifier, tracker, alert_manager
    if detector is None:
        detector = FaceLandmarkDetector()
        
        model_path = os.path.join(app.config['MODELS_DIR'], 'trained_model.joblib')
        if not os.path.exists(model_path):
            model_path = os.path.join(app.config['MODELS_DIR'], 'trained_model.pt')
        if not os.path.exists(model_path):
            model_path = None

        classifier = DrowsinessClassifier(
            model_path=model_path,
            ear_thresh=app.config['EAR_THRESHOLD'],
            mar_thresh=app.config['MAR_THRESHOLD']
        )
        tracker = DrowsinessTracker(
            drowsy_consec_frames=app.config.get('DROWSY_CONSEC_FRAMES', 12),
            distraction_consec_frames=app.config.get('DISTRACTION_CONSEC_FRAMES', 8),
            yaw_threshold=app.config.get('YAW_THRESHOLD', 12.0),
            pitch_threshold=app.config.get('PITCH_THRESHOLD', 12.0)
        )
        alert_manager = AlertManager(snapshots_dir=app.config['SNAPSHOTS_DIR'])

@driver_bp.route('/monitor')
@login_required
def monitor():
    """Halaman Dashboard Utama Monitoring Pengemudi."""
    return render_template('driver_monitor.html', user=current_user)

@driver_bp.route('/process_frame', methods=['POST'])
@login_required
def process_frame():
    """
    Menerima frame video langsung dari webcam browser (HTML5 getUserMedia),
    memproses deteksi landmark wajah, inferensi AI, dan status kantuk.
    """
    app = current_app._get_current_object()
    init_dms_engine(app)

    data = request.get_json(silent=True) or {}
    image_data = data.get('image', '')
    if not image_data:
        return jsonify({'error': 'Tidak ada data gambar'}), 400

    if ',' in image_data:
        image_data = image_data.split(',', 1)[1]

    try:
        image_bytes = base64.b64decode(image_data)
        nparr = np.frombuffer(image_bytes, np.uint8)
        frame = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
        if frame is None:
            return jsonify({'error': 'Gagal decode gambar'}), 400

        # Deteksi landmark wajah & mata
        detection_res = detector.process_frame(frame)

        # Inferensi model
        pred_res = classifier.predict(detection_res)

        # Update tracker durasi
        track_status = tracker.update(detection_res, pred_res)

        # Simpan log jika ada event bahaya
        if track_status['should_record_event']:
            snapshot_file = alert_manager.save_snapshot(
                frame, current_user.id, track_status['new_event_type']
            )
            with app.app_context():
                new_log = AlertLog(
                    driver_id=current_user.id,
                    alert_type=track_status['new_event_type'],
                    severity='BAHAYA' if track_status['new_event_type'] == 'kantuk' else 'PERINGATAN',
                    duration_seconds=2.0,
                    snapshot_path=snapshot_file,
                    notes=f"Metode: {classifier.model_name} | EAR: {detection_res.get('ear_avg', 0.0):.3f}"
                )
                db.session.add(new_log)
                db.session.commit()

        # Format respon ke client
        return jsonify({
            'success': True,
            'face_detected': detection_res.get('face_detected', False),
            'status_message': detection_res.get('status_message', ''),
            'bbox': detection_res.get('bbox', None),
            'left_eye_pts': detection_res.get('left_eye_pts', []),
            'right_eye_pts': detection_res.get('right_eye_pts', []),
            'ear_avg': detection_res.get('ear_avg', 0.0),
            'ear_left': detection_res.get('ear_left', 0.0),
            'ear_right': detection_res.get('ear_right', 0.0),
            'mar': detection_res.get('mar', 0.0),
            'yaw': detection_res.get('yaw', 0.0),
            'pitch': detection_res.get('pitch', 0.0),
            'roll': detection_res.get('roll', 0.0),
            'is_alarm_active': track_status['is_alarm_active'],
            'alarm_type': track_status['alarm_type'],
            'alarm_message': track_status['alarm_message'],
            'drowsy_score': track_status['drowsy_score'],
            'drowsy_progress': track_status['drowsy_progress'],
            'distraction_progress': track_status['distraction_progress'],
            'status_label': track_status['status_label'],
            'method': classifier.model_name
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

def generate_frames(driver_id, app):
    """Fallback generator video stream real-time (MJPEG) backend."""
    global latest_status
    with app.app_context():
        init_dms_engine(app)

    camera = cv2.VideoCapture(0)
    if not camera.isOpened():
        camera = cv2.VideoCapture(0, cv2.CAP_DSHOW)

    if not camera.isOpened():
        latest_status['alarm_message'] = 'Kamera backend tidak terdeteksi. Gunakan Kamera Browser (WebRTC).'
        black_frame = np.zeros((480, 640, 3), dtype=np.uint8)
        cv2.putText(black_frame, "KAMERA SERVER TIDAK TERSEDIA", (80, 220),
                    cv2.FONT_HERSHEY_SIMPLEX, 0.7, (0, 0, 255), 2)
        cv2.putText(black_frame, "Gunakan Mode Kamera Browser di bawah", (100, 260),
                    cv2.FONT_HERSHEY_SIMPLEX, 0.6, (200, 200, 200), 1)
        _, buffer = cv2.imencode('.jpg', black_frame)
        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + buffer.tobytes() + b'\r\n')
        return

    try:
        while True:
            success, frame = camera.read()
            if not success:
                break

            frame = cv2.flip(frame, 1)
            h, w = frame.shape[:2]

            detection_res = detector.process_frame(frame)
            pred_res = classifier.predict(detection_res)
            track_status = tracker.update(detection_res, pred_res)

            latest_status.update({
                'face_detected': detection_res.get('face_detected', False),
                'status_label': track_status['status_label'],
                'ear_avg': detection_res.get('ear_avg', 0.0),
                'mar': detection_res.get('mar', 0.0),
                'yaw': detection_res.get('yaw', 0.0),
                'pitch': detection_res.get('pitch', 0.0),
                'is_alarm_active': track_status['is_alarm_active'],
                'alarm_type': track_status['alarm_type'],
                'alarm_message': track_status['alarm_message'],
                'drowsy_score': track_status['drowsy_score'],
                'drowsy_progress': track_status['drowsy_progress'],
                'distraction_progress': track_status['distraction_progress'],
                'method': classifier.model_name
            })

            if track_status['should_record_event']:
                snapshot_file = alert_manager.save_snapshot(
                    frame, driver_id, track_status['new_event_type']
                )
                with app.app_context():
                    new_log = AlertLog(
                        driver_id=driver_id,
                        alert_type=track_status['new_event_type'],
                        severity='BAHAYA' if track_status['new_event_type'] == 'kantuk' else 'PERINGATAN',
                        duration_seconds=2.0,
                        snapshot_path=snapshot_file,
                        notes=f"Metode: {classifier.model_name} | EAR: {detection_res.get('ear_avg', 0.0):.3f}"
                    )
                    db.session.add(new_log)
                    db.session.commit()

            annotated_frame = frame.copy()
            if detection_res.get('face_detected', False):
                for pt in detection_res['left_eye_pts']:
                    cv2.circle(annotated_frame, pt, 2, (0, 255, 0), -1)
                for pt in detection_res['right_eye_pts']:
                    cv2.circle(annotated_frame, pt, 2, (0, 255, 0), -1)

                x1, y1, x2, y2 = detection_res['bbox']
                box_color = (0, 0, 255) if track_status['is_alarm_active'] else (0, 255, 0)
                cv2.rectangle(annotated_frame, (x1, y1), (x2, y2), box_color, 2)

            if track_status['is_alarm_active']:
                overlay_banner = annotated_frame.copy()
                cv2.rectangle(overlay_banner, (0, 0), (w, 55), (0, 0, 220), -1)
                cv2.addWeighted(overlay_banner, 0.85, annotated_frame, 0.15, 0, annotated_frame)
                cv2.putText(annotated_frame, f"! {track_status['alarm_message']}", (15, 38),
                            cv2.FONT_HERSHEY_SIMPLEX, 0.65, (255, 255, 255), 2, cv2.LINE_AA)

            ret, buffer = cv2.imencode('.jpg', annotated_frame, [int(cv2.IMWRITE_JPEG_QUALITY), 80])
            if not ret:
                continue

            yield (b'--frame\r\n'
                   b'Content-Type: image/jpeg\r\n\r\n' + buffer.tobytes() + b'\r\n')
    except (GeneratorExit, Exception):
        pass
    finally:
        if camera is not None and camera.isOpened():
            camera.release()

@driver_bp.route('/video_feed')
@login_required
def video_feed():
    """Endpoint Video Streaming MJPEG Backend."""
    app = current_app._get_current_object()
    return Response(
        generate_frames(current_user.id, app),
        mimetype='multipart/x-mixed-replace; boundary=frame'
    )

@driver_bp.route('/status_feed')
@login_required
def status_feed():
    """Endpoint API Real-time untuk polling status alarm backend."""
    return jsonify(latest_status)

@driver_bp.route('/history')
@login_required
def history():
    """FR-14: Driver dapat melihat riwayat peringatan dirinya sendiri."""
    page = request.args.get('page', 1, type=int)
    alerts = AlertLog.query.filter_by(driver_id=current_user.id)\
                           .order_by(AlertLog.timestamp.desc())\
                           .paginate(page=page, per_page=15, error_out=False)
    
    total_kantuk = AlertLog.query.filter_by(driver_id=current_user.id, alert_type='kantuk').count()
    total_distraksi = AlertLog.query.filter_by(driver_id=current_user.id, alert_type='tidak_fokus').count()
    total_menguap = AlertLog.query.filter_by(driver_id=current_user.id, alert_type='menguap').count()

    return render_template(
        'driver_history.html',
        alerts=alerts,
        total_kantuk=total_kantuk,
        total_distraksi=total_distraksi,
        total_menguap=total_menguap,
        user=current_user
    )

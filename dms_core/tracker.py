import time

class DrowsinessTracker:
    """
    Pelacak status kewaspadaan pengemudi dan manajemen durasi kondisi berbahaya.
    Memenuhi kebutuhan:
    - FR-05: Deteksi kondisi pengemudi mengantuk
    - FR-06: Deteksi pengemudi tidak fokus / menoleh ke samping / menunduk terlalu lama
    - NFR-03: Memberikan peringatan dalam waktu maksimal 3 detik
    """

    def __init__(self, 
                 drowsy_consec_frames=12, 
                 distraction_consec_frames=28,
                 yaw_threshold=25.0,
                 pitch_threshold=22.0):
        self.drowsy_consec_frames = drowsy_consec_frames
        self.distraction_consec_frames = distraction_consec_frames
        self.yaw_threshold = yaw_threshold
        self.pitch_threshold = pitch_threshold

        # Frame counters
        self.closed_eyes_frames = 0
        self.yawn_frames = 0
        self.distraction_frames = 0
        self.no_face_frames = 0

        # Memory orientasi & smoothing pose (EMA filter agar gerakan menggaruk kepala tidak membuat spike)
        self.smooth_yaw = 0.0
        self.smooth_pitch = 0.0
        self.last_yaw = 0.0
        self.last_pitch = 0.0
        self.last_direction = 'Tengah'
        self.is_gaze_forward = True

        # State flags
        self.is_alarm_active = False
        self.alarm_type = None           # 'KANTUK', 'TIDAK_FOKUS', 'MENGUAP', 'WAJAH_HILANG'
        self.alarm_message = ''
        self.last_alert_time = 0.0
        self.alert_cooldown = 2.0        # Jeda minimal antar event pencatatan log (detik)

        # Statistik sesi pengemudi
        self.total_drowsy_events = 0
        self.total_distraction_events = 0
        self.total_yawn_events = 0

    def update(self, features_dict, prediction_dict):
        """
        Memperbarui status tracker pada setiap frame video.
        Mengembalikan dictionary berisi status alarm dan metrik real-time.
        """
        current_time = time.time()
        should_record_event = False
        new_event_type = None

        # 1. Periksa apakah wajah terdeteksi
        if not features_dict.get('face_detected', False):
            self.no_face_frames += 1

            # Logika Cerdas: Hanya anggap sebagai menoleh/menunduk jika sebelum wajah hilang,
            # pengemudi memang terbukti sedang menoleh/menunduk dengan durasi akumulasi nyata atau sudut >= threshold
            was_really_turning = (self.distraction_frames >= 1) or (abs(self.last_yaw) >= self.yaw_threshold)
            was_really_nodding = (self.distraction_frames >= 1) or (self.last_pitch >= self.pitch_threshold)

            if was_really_turning or was_really_nodding:
                self.distraction_frames += 1
                if self.distraction_frames >= self.distraction_consec_frames:
                    self.is_alarm_active = True
                    self.alarm_type = 'TIDAK_FOKUS'
                    if was_really_nodding and not was_really_turning:
                        self.alarm_message = 'PERINGATAN: Terlalu Lama Menunduk! Tetap Fokus Menatap Jalan!'
                    else:
                        direction = self.last_direction if self.last_direction in ['Kanan', 'Kiri'] else 'Samping'
                        self.alarm_message = f'PERINGATAN: Terlalu Lama Menoleh ke {direction}! Fokus Menatap Jalan!'

                    if (current_time - self.last_alert_time) > self.alert_cooldown:
                        self.total_distraction_events += 1
                        self.last_alert_time = current_time
                        should_record_event = True
                        new_event_type = 'tidak_fokus'
                return self._build_status(features_dict, prediction_dict, should_record_event, new_event_type)

            # Jika bukan karena menoleh nyata (misal tangan menggaruk kepala / melintas sebentar),
            # jangan menaikkan distraction_frames! Berikan toleransi hingga distraction_consec_frames (~3 detik)
            if self.no_face_frames >= self.distraction_consec_frames:
                self.is_alarm_active = True
                self.alarm_type = 'WAJAH_HILANG'
                self.alarm_message = 'PERINGATAN: Wajah Pengemudi Tidak Terdeteksi! Menghadap ke Jalan!'
                if (current_time - self.last_alert_time) > self.alert_cooldown:
                    self.total_distraction_events += 1
                    self.last_alert_time = current_time
                    should_record_event = True
                    new_event_type = 'tidak_fokus'
            else:
                self.is_alarm_active = False
                self.alarm_type = None
                self.alarm_message = 'Mencari posisi wajah pengemudi...'

            return self._build_status(features_dict, prediction_dict, should_record_event, new_event_type)

        # Wajah terdeteksi normal
        self.no_face_frames = 0
        yaw = features_dict.get('yaw', 0.0)
        pitch = features_dict.get('pitch', 0.0)
        is_drowsy_pred = prediction_dict.get('is_drowsy', False)
        is_yawning_pred = prediction_dict.get('is_yawning', False)

        # Cek arah pandangan mata (Iris Gaze Priority)
        # Jika mata fokus menatap lurus ke jalan, gerakan kepala kecil / menggaruk kepala tidak dianggap distraksi
        is_gaze_forward = features_dict.get('is_gaze_forward', None)
        if is_gaze_forward is None:
            is_gaze_forward = (abs(yaw) < 20.0 and abs(pitch) < 18.0)
        self.is_gaze_forward = is_gaze_forward

        # EMA filter pada pose kepala untuk meredam glitch saat tangan mendekati wajah/kepala
        if self.smooth_yaw == 0.0 and self.smooth_pitch == 0.0:
            self.smooth_yaw = yaw
            self.smooth_pitch = pitch
        else:
            alpha = 0.45
            self.smooth_yaw = alpha * yaw + (1 - alpha) * self.smooth_yaw
            self.smooth_pitch = alpha * pitch + (1 - alpha) * self.smooth_pitch

        self.last_yaw = self.smooth_yaw
        self.last_pitch = self.smooth_pitch

        # 4. Logika Distraksi (Prioritas Pandangan Mata + Pose Kepala)
        # Jika mata tetap fokus ke depan dan kepala tidak menoleh ekstrem (> 35 deg), pengemudi tetap aman (TERJAGA)
        if is_gaze_forward and abs(self.smooth_yaw) < 35.0 and self.smooth_pitch < 25.0:
            self.last_direction = 'Tengah'
            is_distracted = False
        else:
            if self.smooth_yaw >= self.yaw_threshold:
                self.last_direction = 'Kanan'
                is_distracted = True
            elif self.smooth_yaw <= -self.yaw_threshold:
                self.last_direction = 'Kiri'
                is_distracted = True
            elif self.smooth_pitch >= self.pitch_threshold:
                self.last_direction = 'Bawah (Menunduk)'
                is_distracted = True
            elif self.smooth_pitch < -20.0:
                self.last_direction = 'Atas'
                is_distracted = True
            else:
                self.last_direction = 'Tengah'
                is_distracted = False

        if is_distracted:
            self.distraction_frames += 1
        else:
            # Pengemudi kembali fokus menatap jalan -> kurangi counter secara bertahap
            self.distraction_frames = max(0, self.distraction_frames - 2)

        # 2. Logika Kantuk / Mata Tertutup
        if is_drowsy_pred:
            self.closed_eyes_frames += 1
        else:
            self.closed_eyes_frames = max(0, self.closed_eyes_frames - 3)

        # 3. Logika Menguap
        if is_yawning_pred:
            self.yawn_frames += 1
        else:
            self.yawn_frames = max(0, self.yawn_frames - 1)

        # 5. Evaluasi Ambang Batas Peringatan (Alarm Triggers)
        if self.closed_eyes_frames >= self.drowsy_consec_frames:
            self.is_alarm_active = True
            self.alarm_type = 'KANTUK'
            self.alarm_message = 'BAHAYA: Anda Terdeteksi Mengantuk! Segera Buka Mata!'
            
            if (current_time - self.last_alert_time) > self.alert_cooldown:
                self.total_drowsy_events += 1
                self.last_alert_time = current_time
                should_record_event = True
                new_event_type = 'kantuk'

        elif self.distraction_frames >= self.distraction_consec_frames:
            self.is_alarm_active = True
            self.alarm_type = 'TIDAK_FOKUS'
            if 'Bawah' in self.last_direction:
                self.alarm_message = 'PERINGATAN: Terlalu Lama Menunduk! Tetap Fokus Menatap Jalan!'
            elif self.last_direction in ['Kanan', 'Kiri']:
                self.alarm_message = f'PERINGATAN: Terlalu Lama Menoleh ke {self.last_direction}! Fokus Menatap Jalan!'
            else:
                self.alarm_message = f'PERINGATAN: Tetap Fokus Menatap Jalan! ({self.last_direction})'
            
            if (current_time - self.last_alert_time) > self.alert_cooldown:
                self.total_distraction_events += 1
                self.last_alert_time = current_time
                should_record_event = True
                new_event_type = 'tidak_fokus'

        elif self.yawn_frames >= 30: # Sering menguap
            self.is_alarm_active = True
            self.alarm_type = 'MENGUAP'
            self.alarm_message = 'Peringatan: Terdeteksi Menguap Berulang Kali, Pertimbangkan Istirahat.'
            
            if (current_time - self.last_alert_time) > (self.alert_cooldown * 2):
                self.total_yawn_events += 1
                self.last_alert_time = current_time
                should_record_event = True
                new_event_type = 'menguap'

        else:
            self.is_alarm_active = False
            self.alarm_type = None
            self.alarm_message = 'Kondisi Pengemudi: Waspada & Fokus'

        return self._build_status(features_dict, prediction_dict, should_record_event, new_event_type)

    def _build_status(self, features_dict, prediction_dict, should_record_event, new_event_type):
        """Menyusun struktur data status komprehensif untuk dirender di UI dan dicatat di database."""
        # Ambang batas transisi status visual:
        # Melirik wajar / menggaruk kepala (< 60% durasi bahaya, misal < 16 frame / ~1.8 detik) TETAP berstatus TERJAGA.
        warning_frame_thresh = max(2, int(self.distraction_consec_frames * 0.6))

        if self.is_alarm_active:
            status_label = self.alarm_type
        elif self.closed_eyes_frames >= 5:
            status_label = 'MENGANTUK'
        elif self.distraction_frames >= warning_frame_thresh:
            status_label = 'TIDAK_FOKUS'
        elif self.no_face_frames >= warning_frame_thresh:
            status_label = 'TIDAK_FOKUS'
        elif prediction_dict.get('is_yawning', False) or self.yawn_frames >= 12:
            status_label = 'MENGUAP'
        else:
            status_label = 'TERJAGA'

        # Skor kantuk dihitung secara proporsional terhadap durasi akumulasi mata terpejam
        drowsy_progress = min(100, int((self.closed_eyes_frames / max(1, self.drowsy_consec_frames)) * 100))
        drowsy_score = round(drowsy_progress / 100.0, 2)
        distraction_progress = min(100, int((self.distraction_frames / max(1, self.distraction_consec_frames)) * 100))

        return {
            'is_alarm_active': self.is_alarm_active,
            'alarm_type': self.alarm_type,
            'alarm_message': self.alarm_message,
            'closed_eyes_frames': self.closed_eyes_frames,
            'distraction_frames': self.distraction_frames,
            'yawn_frames': self.yawn_frames,
            'drowsy_progress': drowsy_progress,
            'distraction_progress': distraction_progress,
            'total_drowsy_events': self.total_drowsy_events,
            'total_distraction_events': self.total_distraction_events,
            'total_yawn_events': self.total_yawn_events,
            'should_record_event': should_record_event,
            'new_event_type': new_event_type,
            'status_label': status_label,
            'drowsy_score': drowsy_score,
            'method': prediction_dict.get('method', 'Baseline')
        }

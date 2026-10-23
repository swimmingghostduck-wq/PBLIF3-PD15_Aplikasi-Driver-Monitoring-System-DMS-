import time

class DrowsinessTracker:
    """
    Pelacak status kewaspadaan pengemudi dan manajemen durasi kondisi berbahaya.
    Memenuhi kebutuhan:
    - FR-05: Deteksi kondisi pengemudi mengantuk
    - FR-06: Deteksi pengemudi tidak fokus / menoleh ke samping
    - NFR-03: Memberikan peringatan dalam waktu maksimal 3 detik
    """

    def __init__(self, 
                 drowsy_consec_frames=12, 
                 distraction_consec_frames=15,
                 yaw_threshold=22.0,
                 pitch_threshold=25.0):
        self.drowsy_consec_frames = drowsy_consec_frames
        self.distraction_consec_frames = distraction_consec_frames
        self.yaw_threshold = yaw_threshold
        self.pitch_threshold = pitch_threshold

        # Frame counters
        self.closed_eyes_frames = 0
        self.yawn_frames = 0
        self.distraction_frames = 0
        self.no_face_frames = 0

        # Memory orientasi terakhir (untuk menangani saat menoleh jauh / profil samping)
        self.last_yaw = 0.0
        self.last_pitch = 0.0
        self.last_direction = 'Kanan'

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

            # Logika Cerdas: Jika pengemudi sebelumnya sedang menoleh atau yaw terakhir tinggi,
            # kehilangan wajah disebabkan pengemudi menoleh terlalu jauh (profil samping).
            # Tetap akumulasikan frame distraksi hingga ~30 frame (~2.5 detik) tanpa mereset.
            was_distracted = (self.distraction_frames > 0) or (abs(self.last_yaw) > self.yaw_threshold)
            if was_distracted and self.no_face_frames <= 30:
                self.distraction_frames += 1
                if self.distraction_frames >= self.distraction_consec_frames:
                    self.is_alarm_active = True
                    self.alarm_type = 'TIDAK_FOKUS'
                    self.alarm_message = f'PERINGATAN: Tetap Fokus Menatap Jalan! (Menoleh ke {self.last_direction})'
                    if (current_time - self.last_alert_time) > self.alert_cooldown:
                        self.total_distraction_events += 1
                        self.last_alert_time = current_time
                        should_record_event = True
                        new_event_type = 'tidak_fokus'
                return self._build_status(features_dict, prediction_dict, should_record_event, new_event_type)

            # Jika bukan karena menoleh, berikan grace period 3 frame sebelum mereset closed_eyes_frames
            if self.no_face_frames > 3:
                self.closed_eyes_frames = 0
                self.distraction_frames = 0
                self.yawn_frames = 0
            
            if self.no_face_frames > 35: # ~2.5-3 detik benar-benar tidak ada orang di depan kamera
                self.is_alarm_active = True
                self.alarm_type = 'WAJAH_HILANG'
                self.alarm_message = 'Peringatan: Wajah Pengemudi Tidak Terdeteksi!'
            else:
                self.is_alarm_active = False
                self.alarm_type = None
                self.alarm_message = 'Mencari wajah pengemudi...'

            return self._build_status(features_dict, prediction_dict, should_record_event, new_event_type)

        self.no_face_frames = 0
        yaw = features_dict.get('yaw', 0.0)
        pitch = features_dict.get('pitch', 0.0)
        is_drowsy_pred = prediction_dict.get('is_drowsy', False)
        is_yawning_pred = prediction_dict.get('is_yawning', False)

        # Simpan orientasi terakhir untuk kontinuitas pose
        self.last_yaw = yaw
        self.last_pitch = pitch
        if yaw > self.yaw_threshold:
            self.last_direction = 'Kanan'
        elif yaw < -self.yaw_threshold:
            self.last_direction = 'Kiri'
        elif pitch < -self.pitch_threshold:
            self.last_direction = 'Bawah'

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

        # 4. Logika Distraksi (Menoleh ke Kanan/Kiri atau Menunduk terlalu lama)
        is_distracted = (abs(yaw) > self.yaw_threshold) or (pitch < -self.pitch_threshold)
        if is_distracted:
            self.distraction_frames += 1
        else:
            # Pengemudi kembali fokus menatap jalan -> kurangi counter dengan cepat
            self.distraction_frames = max(0, self.distraction_frames - 3)

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
            direction = self.last_direction
            self.alarm_message = f'PERINGATAN: Tetap Fokus Menatap Jalan! (Menoleh ke {direction})'
            
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
        # Penentuan label status yang stabil:
        # Kedipan mata normal (1-4 frame, ~100-300 ms) TETAP berstatus TERJAGA.
        # Hanya jika mata terpejam >= 5 frame (~400 ms ke atas), status bertransisi ke MENGANTUK.
        if self.is_alarm_active:
            status_label = self.alarm_type
        elif self.closed_eyes_frames >= 5:
            status_label = 'MENGANTUK'
        elif self.distraction_frames >= self.distraction_consec_frames:
            status_label = 'TIDAK_FOKUS'
        elif prediction_dict.get('is_yawning', False) or self.yawn_frames >= 12:
            status_label = 'MENGUAP'
        else:
            status_label = 'TERJAGA'

        # Skor kantuk dihitung secara proporsional terhadap durasi akumulasi mata terpejam
        # Kedipan normal (1-2 frame) hanya menghasilkan skor 8-15% (hijau aman), tidak loncat ke 100%
        drowsy_progress = min(100, int((self.closed_eyes_frames / max(1, self.drowsy_consec_frames)) * 100))
        drowsy_score = round(drowsy_progress / 100.0, 2)

        return {
            'is_alarm_active': self.is_alarm_active,
            'alarm_type': self.alarm_type,
            'alarm_message': self.alarm_message,
            'closed_eyes_frames': self.closed_eyes_frames,
            'distraction_frames': self.distraction_frames,
            'yawn_frames': self.yawn_frames,
            'drowsy_progress': drowsy_progress,
            'distraction_progress': min(100, int((self.distraction_frames / max(1, self.distraction_consec_frames)) * 100)),
            'total_drowsy_events': self.total_drowsy_events,
            'total_distraction_events': self.total_distraction_events,
            'total_yawn_events': self.total_yawn_events,
            'should_record_event': should_record_event,
            'new_event_type': new_event_type,
            'status_label': status_label,
            'drowsy_score': drowsy_score,
            'method': prediction_dict.get('method', 'Baseline')
        }

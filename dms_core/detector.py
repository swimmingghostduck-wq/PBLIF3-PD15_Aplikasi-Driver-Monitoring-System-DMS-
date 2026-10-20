import os
import math
import urllib.request
import cv2
import numpy as np

class FaceLandmarkDetector:
    """
    Pendeteksi Wajah dan Ekstraksi Landmark menggunakan MediaPipe.
    Mendukung MediaPipe 1.x (mp.tasks.vision.FaceLandmarker) dan versi legacy.
    Menghitung metrik penting:
    - EAR (Eye Aspect Ratio): Keterbukaan mata kiri dan kanan
    - MAR (Mouth Aspect Ratio): Bukaan mulut untuk deteksi menguap
    - Head Pose (Pitch, Yaw, Roll): Estimasi arah pandangan/kepala pengemudi via cv2.solvePnP
    """
    
    # Indeks landmark MediaPipe Face Mesh (468/478 titik)
    LEFT_EYE_IDX = [33, 160, 158, 133, 153, 144]
    RIGHT_EYE_IDX = [362, 385, 387, 263, 373, 380]
    
    MOUTH_TOP_BOTTOM = (13, 14)
    MOUTH_LEFT_RIGHT = (78, 308)
    MOUTH_VERTICAL_2 = (82, 312)
    MOUTH_VERTICAL_3 = (87, 317)
    
    MODEL_POINTS_3D = np.array([
        (0.0, 0.0, 0.0),          # Hidung (Nose tip) - 1
        (0.0, 330.0, -65.0),      # Dagu (Chin) - 199
        (-225.0, -170.0, -135.0), # Sudut mata kiri - 33
        (225.0, -170.0, -135.0),  # Sudut mata kanan - 263
        (-150.0, 150.0, -125.0),  # Sudut mulut kiri - 61
        (150.0, 150.0, -125.0)    # Sudut mulut kanan - 291
    ], dtype=np.float64)

    POSE_LANDMARK_IDX = [1, 199, 33, 263, 61, 291]

    def __init__(self, model_asset_path=None):
        import mediapipe as mp
        self.mp = mp
        self.use_tasks_api = False
        self.landmarker = None
        self.legacy_mesh = None

        # Tentukan path model task
        if model_asset_path is None:
            base_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
            model_asset_path = os.path.join(base_dir, 'models', 'face_landmarker.task')
            if not os.path.exists(model_asset_path):
                alt_path = os.path.join(base_dir, 'face_landmarker.task')
                if os.path.exists(alt_path):
                    model_asset_path = alt_path

        # 1. Coba inisialisasi menggunakan MediaPipe 1.x Tasks API (Modern)
        if hasattr(mp, 'tasks') and hasattr(mp.tasks, 'vision'):
            try:
                # Pastikan file model task tersedia di disk
                if not os.path.exists(model_asset_path):
                    print(f"[*] Mengunduh model face_landmarker.task ke: {model_asset_path}")
                    os.makedirs(os.path.dirname(model_asset_path), exist_ok=True)
                    url = 'https://storage.googleapis.com/mediapipe-models/face_landmarker/face_landmarker/float16/1/face_landmarker.task'
                    urllib.request.urlretrieve(url, model_asset_path)

                BaseOptions = mp.tasks.BaseOptions
                FaceLandmarker = mp.tasks.vision.FaceLandmarker
                FaceLandmarkerOptions = mp.tasks.vision.FaceLandmarkerOptions
                VisionRunningMode = mp.tasks.vision.RunningMode

                options = FaceLandmarkerOptions(
                    base_options=BaseOptions(model_asset_path=model_asset_path),
                    running_mode=VisionRunningMode.IMAGE,
                    num_faces=1,
                    min_face_detection_confidence=0.5,
                    min_face_presence_confidence=0.5,
                    min_tracking_confidence=0.5
                )
                self.landmarker = FaceLandmarker.create_from_options(options)
                self.use_tasks_api = True
            except Exception as e:
                print(f"[WARN] Inisialisasi MediaPipe Tasks gagal: {e}. Mencoba fallback...")

        # 2. Coba fallback ke MediaPipe legacy solutions (jika tersedia)
        if not self.use_tasks_api and hasattr(mp, 'solutions') and hasattr(mp.solutions, 'face_mesh'):
            try:
                self.legacy_mesh = mp.solutions.face_mesh.FaceMesh(
                    max_num_faces=1,
                    refine_landmarks=True,
                    min_detection_confidence=0.5,
                    min_tracking_confidence=0.5
                )
            except Exception as e:
                print(f"[WARN] Inisialisasi legacy face_mesh gagal: {e}")

    @staticmethod
    def _euclidean_dist(p1, p2):
        """Menghitung jarak Euclidean antara dua titik (x, y)."""
        return math.hypot(p1[0] - p2[0], p1[1] - p2[1])

    def calculate_ear(self, eye_points):
        """
        Menghitung Eye Aspect Ratio (EAR) sesuai formula Soukupova & Cech:
        EAR = (|p2 - p6| + |p3 - p5|) / (2 * |p1 - p4|)
        """
        vertical_1 = self._euclidean_dist(eye_points[1], eye_points[5])
        vertical_2 = self._euclidean_dist(eye_points[2], eye_points[4])
        horizontal = self._euclidean_dist(eye_points[0], eye_points[3])
        
        if horizontal == 0:
            return 0.0
        return (vertical_1 + vertical_2) / (2.0 * horizontal)

    def calculate_mar(self, landmarks_pixel):
        """
        Menghitung Mouth Aspect Ratio (MAR):
        Rasio bukaan vertikal mulut terhadap lebar horizontal bibir.
        """
        top_bottom = self._euclidean_dist(
            landmarks_pixel[self.MOUTH_TOP_BOTTOM[0]],
            landmarks_pixel[self.MOUTH_TOP_BOTTOM[1]]
        )
        v2 = self._euclidean_dist(
            landmarks_pixel[self.MOUTH_VERTICAL_2[0]],
            landmarks_pixel[self.MOUTH_VERTICAL_2[1]]
        )
        v3 = self._euclidean_dist(
            landmarks_pixel[self.MOUTH_VERTICAL_3[0]],
            landmarks_pixel[self.MOUTH_VERTICAL_3[1]]
        )
        horizontal = self._euclidean_dist(
            landmarks_pixel[self.MOUTH_LEFT_RIGHT[0]],
            landmarks_pixel[self.MOUTH_LEFT_RIGHT[1]]
        )
        
        if horizontal == 0:
            return 0.0
        return (top_bottom + v2 + v3) / (2.0 * horizontal)

    def estimate_head_pose(self, landmarks_pixel, image_shape):
        """
        Estimasi orientasi kepala pengemudi (Pitch, Yaw, Roll) dalam derajat
        menggunakan Perspective-n-Point (cv2.solvePnP).
        """
        h, w = image_shape[:2]
        
        image_points = np.array([
            landmarks_pixel[idx] for idx in self.POSE_LANDMARK_IDX
        ], dtype=np.float64)

        focal_length = w
        center = (w / 2, h / 2)
        camera_matrix = np.array([
            [focal_length, 0, center[0]],
            [0, focal_length, center[1]],
            [0, 0, 1]
        ], dtype=np.float64)

        dist_coeffs = np.zeros((4, 1), dtype=np.float64)

        success, rvec, tvec = cv2.solvePnP(
            self.MODEL_POINTS_3D,
            image_points,
            camera_matrix,
            dist_coeffs,
            flags=cv2.SOLVEPNP_ITERATIVE
        )

        if not success:
            return 0.0, 0.0, 0.0

        rmat, _ = cv2.Rodrigues(rvec)
        angles, _, _, _, _, _ = cv2.RQDecomp3x3(rmat)
        # RQDecomp3x3 sudah mengembalikan sudut dalam derajat.
        # Yaw diinversi agar perspektif pengemudi intuitif:
        # Menoleh Kanan -> yaw positif (+), Menoleh Kiri -> yaw negatif (-)
        pitch = float(angles[0])
        yaw = float(-angles[1])
        roll = float(angles[2])

        return pitch, yaw, roll

    def process_frame(self, frame):
        """
        Memproses 1 frame video kamera secara komprehensif:
        Output berupa dictionary hasil deteksi lengkap.
        """
        if frame is None:
            return {
                'face_detected': False,
                'status_message': 'Frame kosong'
            }

        h, w = frame.shape[:2]
        raw_landmarks = None

        # 1. Deteksi via MediaPipe Tasks API
        if self.use_tasks_api and self.landmarker is not None:
            try:
                rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
                mp_image = self.mp.Image(
                    image_format=self.mp.ImageFormat.SRGB,
                    data=rgb_frame
                )
                detection_result = self.landmarker.detect(mp_image)
                if detection_result.face_landmarks and len(detection_result.face_landmarks) > 0:
                    raw_landmarks = detection_result.face_landmarks[0]
            except Exception as e:
                print(f"[FaceLandmarkDetector] Error Tasks detect: {e}")

        # 2. Deteksi via MediaPipe Legacy Solutions (jika ada)
        elif self.legacy_mesh is not None:
            try:
                rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
                rgb_frame.flags.writeable = False
                res = self.legacy_mesh.process(rgb_frame)
                if res.multi_face_landmarks:
                    raw_landmarks = res.multi_face_landmarks[0].landmark
            except Exception as e:
                print(f"[FaceLandmarkDetector] Error Legacy detect: {e}")

        if raw_landmarks is None:
            return {
                'face_detected': False,
                'status_message': 'Wajah tidak terdeteksi',
                'ear_left': 0.0,
                'ear_right': 0.0,
                'ear_avg': 0.0,
                'mar': 0.0,
                'pitch': 0.0,
                'yaw': 0.0,
                'roll': 0.0,
                'features_vector': []
            }

        # Konversi koordinat normalisasi ke pixel (x, y)
        landmarks_pixel = [
            (int(pt.x * w), int(pt.y * h)) for pt in raw_landmarks
        ]

        # Ekstraksi titik mata kiri dan kanan
        left_eye_pts = [landmarks_pixel[i] for i in self.LEFT_EYE_IDX]
        right_eye_pts = [landmarks_pixel[i] for i in self.RIGHT_EYE_IDX]

        ear_left = self.calculate_ear(left_eye_pts)
        ear_right = self.calculate_ear(right_eye_pts)
        ear_avg = (ear_left + ear_right) / 2.0

        # Ekstraksi bukaan mulut (MAR)
        mar = self.calculate_mar(landmarks_pixel)

        # Ekstraksi pose kepala (Pitch, Yaw, Roll)
        pitch, yaw, roll = self.estimate_head_pose(landmarks_pixel, (h, w))

        eye_diff = abs(ear_left - ear_right)
        ear_mar_ratio = ear_avg / (mar + 1e-6)
        yaw_abs = abs(yaw)

        features_vector = [
            ear_left, ear_right, ear_avg, mar,
            pitch, yaw, roll, eye_diff, ear_mar_ratio, yaw_abs
        ]

        xs = [pt[0] for pt in landmarks_pixel]
        ys = [pt[1] for pt in landmarks_pixel]
        bbox = (max(0, min(xs)), max(0, min(ys)), min(w, max(xs)), min(h, max(ys)))

        return {
            'face_detected': True,
            'status_message': 'Wajah terdeteksi normal',
            'landmarks_pixel': landmarks_pixel,
            'left_eye_pts': left_eye_pts,
            'right_eye_pts': right_eye_pts,
            'ear_left': round(ear_left, 4),
            'ear_right': round(ear_right, 4),
            'ear_avg': round(ear_avg, 4),
            'mar': round(mar, 4),
            'pitch': round(pitch, 2),
            'yaw': round(yaw, 2),
            'roll': round(roll, 2),
            'features_vector': features_vector,
            'bbox': bbox
        }

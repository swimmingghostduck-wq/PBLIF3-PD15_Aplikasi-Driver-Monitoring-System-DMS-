import os
import joblib
import numpy as np

class DrowsinessClassifier:
    """
    Mesin Inferensi Deteksi Kantuk.
    Mendukung:
    1. Model Scikit-Learn (.joblib) kustom yang dilatih oleh pengguna (Random Forest, SVM, dll.)
    2. Model PyTorch (.pt) kustom
    3. Heuristik Baseline Geometris otomatis jika pengguna belum melatih model.
    """
    
    LABEL_TERJAGA = 'TERJAGA'
    LABEL_MENGANTUK = 'MENGANTUK'
    LABEL_MATA_TERTUTUP = 'MATA_TERTUTUP'
    LABEL_MENGUAP = 'MENGUAP'

    def __init__(self, model_path=None, ear_thresh=0.22, mar_thresh=0.65):
        self.ear_thresh = ear_thresh
        self.mar_thresh = mar_thresh
        self.model_path = model_path
        self.model = None
        self.scaler = None
        self.model_type = 'geometric_baseline' # 'sklearn', 'pytorch', atau 'geometric_baseline'
        self.model_name = 'Aturan Geometris (Baseline)'
        
        if model_path and os.path.exists(model_path):
            self.load_model(model_path)

    def load_model(self, model_path):
        """Memuat model kustom dari disk (.joblib atau .pt)."""
        if not os.path.exists(model_path):
            print(f"[DMS Classifier] File model {model_path} tidak ditemukan. Menggunakan baseline.")
            return False

        try:
            if model_path.endswith('.joblib') or model_path.endswith('.pkl'):
                bundle = joblib.load(model_path)
                if isinstance(bundle, dict):
                    self.model = bundle.get('model')
                    self.scaler = bundle.get('scaler')
                    self.model_name = bundle.get('name', 'Scikit-Learn Classifier')
                else:
                    self.model = bundle
                    self.scaler = None
                    self.model_name = 'Scikit-Learn Classifier'
                
                self.model_type = 'sklearn'
                self.model_path = model_path
                print(f"[DMS Classifier] Model Scikit-Learn berhasil dimuat: {self.model_name}")
                return True

            elif model_path.endswith('.pt') or model_path.endswith('.pth'):
                import torch
                checkpoint = torch.load(model_path, map_location='cpu')
                self.model = checkpoint.get('model_state_dict', checkpoint)
                self.scaler = checkpoint.get('scaler', None)
                self.model_name = checkpoint.get('name', 'PyTorch Neural Network')
                self.model_type = 'pytorch'
                self.model_path = model_path
                print(f"[DMS Classifier] Model PyTorch berhasil dimuat: {self.model_name}")
                return True
        except Exception as e:
            print(f"[DMS Classifier] Gagal memuat model {model_path}: {e}. Kembali ke baseline.")
            self.model = None
            self.model_type = 'geometric_baseline'
            return False

    def predict(self, features_dict):
        """
        Melakukan prediksi kondisi driver berdasarkan hasil ekstraksi fitur.
        Input: dict hasil dari FaceLandmarkDetector.process_frame()
        Output: dict berisi label prediksi, probabilitas/skor kantuk, dan nama metode.
        """
        if not features_dict.get('face_detected', False):
            return {
                'status': 'WAJAH_TIDAK_TERDETEKSI',
                'drowsy_score': 0.0,
                'is_drowsy': False,
                'is_yawning': False,
                'method': self.model_name
            }

        ear_avg = features_dict.get('ear_avg', 0.0)
        mar = features_dict.get('mar', 0.0)
        features_vector = features_dict.get('features_vector', [])

        # 1. Jika model Machine Learning (Scikit-Learn) tersedia
        if self.model_type == 'sklearn' and self.model is not None and len(features_vector) > 0:
            try:
                X = np.array(features_vector, dtype=np.float32).reshape(1, -1)
                if self.scaler:
                    X = self.scaler.transform(X)
                
                pred = self.model.predict(X)[0]
                proba = 0.5
                if hasattr(self.model, 'predict_proba'):
                    probas = self.model.predict_proba(X)[0]
                    proba = float(np.max(probas))

                # Normalisasi string kelas target
                pred_label = str(pred).upper()
                is_drowsy = ('MENGANTUK' in pred_label or 'DROWSY' in pred_label or 'CLOSED' in pred_label or pred_label == '1')
                is_yawning = (mar > self.mar_thresh or 'YAWN' in pred_label or 'MENGUAP' in pred_label)

                status = self.LABEL_MENGANTUK if is_drowsy else (self.LABEL_MENGUAP if is_yawning else self.LABEL_TERJAGA)
                
                return {
                    'status': status,
                    'drowsy_score': round(float(proba) if is_drowsy else (1.0 - float(proba)), 3),
                    'is_drowsy': is_drowsy,
                    'is_yawning': is_yawning,
                    'method': self.model_name
                }
            except Exception as e:
                print(f"[DMS Classifier] Error inferensi model: {e}. Menggunakan fallback.")

        # 2. Heuristik Baseline Geometris (Rule-Based Cerdas)
        is_eyes_closed = ear_avg < self.ear_thresh
        is_yawning = mar > self.mar_thresh

        # Perhitungan skor kantuk continuous (0.0 sampai 1.0)
        if ear_avg <= 0.10:
            drowsy_score = 1.0
        elif ear_avg >= 0.35:
            drowsy_score = 0.0
        else:
            drowsy_score = max(0.0, min(1.0, (0.35 - ear_avg) / (0.35 - 0.10)))

        if is_eyes_closed and is_yawning:
            status = self.LABEL_MENGANTUK
        elif is_eyes_closed:
            status = self.LABEL_MATA_TERTUTUP
        elif is_yawning:
            status = self.LABEL_MENGUAP
        else:
            status = self.LABEL_TERJAGA

        return {
            'status': status,
            'drowsy_score': round(float(drowsy_score), 3),
            'is_drowsy': is_eyes_closed,
            'is_yawning': is_yawning,
            'method': self.model_name
        }

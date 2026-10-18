import os
import sys

# Auto-switch ke virtual environment jika dijalankan langsung dengan Python global
ROOT_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
_VENV_PY = os.path.join(ROOT_DIR, 'venv', 'Scripts', 'python.exe')
if not os.path.exists(_VENV_PY):
    _VENV_PY = os.path.join(ROOT_DIR, 'venv', 'bin', 'python')
if os.path.exists(_VENV_PY) and os.path.abspath(sys.executable).lower() != os.path.abspath(_VENV_PY).lower():
    import subprocess
    sys.exit(subprocess.run([_VENV_PY] + sys.argv).returncode)

if ROOT_DIR not in sys.path:
    sys.path.insert(0, ROOT_DIR)

import argparse
import joblib
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier
from sklearn.svm import SVC
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix

from training_suite.dataset_loader import DatasetLoader

def train_model(model_type='rf', csv_path=None):
    """
    Melatih model Machine Learning (Scikit-Learn) untuk klasifikasi kantuk.
    Pilihan model:
    - 'rf': Random Forest Classifier (Direkomendasikan)
    - 'svm': Support Vector Classifier (RBF Kernel)
    - 'gb': Gradient Boosting Classifier
    """
    models_dir = os.path.join(ROOT_DIR, 'models')
    os.makedirs(models_dir, exist_ok=True)
    
    if csv_path is None:
        csv_path = os.path.join(ROOT_DIR, 'dataset', 'dataset_features.csv')

    print("=" * 60)
    print("      DMS MODEL TRAINER - SCIKIT-LEARN PIPELINE")
    print("=" * 60)
    print(f"Dataset path: {csv_path}")
    print(f"Pilihan Model: {model_type.upper()}")

    # 1. Muat Dataset
    loader = DatasetLoader(csv_path)
    X_train, X_test, y_train, y_test, scaler = loader.load_data()
    print(f"[+] Data Latih: {X_train.shape[0]} baris | Data Uji: {X_test.shape[0]} baris")

    # 2. Inisialisasi Model
    if model_type == 'rf':
        clf = RandomForestClassifier(
            n_estimators=100,
            max_depth=10,
            random_state=42,
            n_jobs=-1
        )
        model_name = "Random Forest Classifier"
    elif model_type == 'svm':
        clf = SVC(
            kernel='rbf',
            C=1.0,
            probability=True,
            random_state=42
        )
        model_name = "Support Vector Machine (SVM)"
    elif model_type == 'gb':
        clf = GradientBoostingClassifier(
            n_estimators=100,
            learning_rate=0.1,
            max_depth=5,
            random_state=42
        )
        model_name = "Gradient Boosting Classifier"
    else:
        raise ValueError(f"Model type '{model_type}' tidak dikenali. Pilih 'rf', 'svm', atau 'gb'.")

    # 3. Proses Training
    print(f"\n[*] Sedang melatih {model_name}...")
    clf.fit(X_train, y_train)
    print("[+] Pelatihan selesai!")

    # 4. Evaluasi Model pada Data Uji
    y_pred = clf.predict(X_test)
    accuracy = accuracy_score(y_test, y_pred)
    acc_percent = accuracy * 100

    print("\n" + "=" * 60)
    print(f"            HASIL EVALUASI MODEL ({model_name})")
    print("=" * 60)
    print(f"Akurasi Pengujian: {acc_percent:.2f}%")
    
    # Verifikasi terhadap NFR-05 (minimal 80%)
    if acc_percent >= 80.0:
        print(f"[SUCCESS] Memenuhi standar NFR-05 PBL: Akurasi >= 80% ({acc_percent:.2f}%)")
    else:
        print(f"[WARNING] Akurasi di bawah 80%. Pertimbangkan menambah variasi data latih.")

    print("\nLaporan Klasifikasi (Precision, Recall, F1-Score):")
    print(classification_report(y_test, y_pred))

    print("Confusion Matrix:")
    print(confusion_matrix(y_test, y_pred))

    # 5. Simpan Model & Scaler
    save_path = os.path.join(models_dir, 'trained_model.joblib')
    bundle = {
        'model': clf,
        'scaler': scaler,
        'model_type': model_type,
        'name': model_name,
        'accuracy': acc_percent,
        'features': DatasetLoader.FEATURE_COLUMNS
    }
    joblib.dump(bundle, save_path)
    print("\n" + "=" * 60)
    print(f"[TERSIMPAN] Model berhasil diekspor ke:")
    print(f"  -> {save_path}")
    print("Model ini akan langsung aktif otomatis pada sistem web DMS!")
    print("=" * 60)

    return save_path, acc_percent

if __name__ == '__main__':
    parser = argparse.ArgumentParser(description="Latih model deteksi kantuk dengan Scikit-Learn")
    parser.add_argument('--model', type=str, default='rf', choices=['rf', 'svm', 'gb'],
                        help="Tipe algoritma: rf (Random Forest), svm (Support Vector Machine), gb (Gradient Boosting)")
    parser.add_argument('--dataset', type=str, default=None,
                        help="Path ke file CSV dataset (opsional)")
    args = parser.parse_args()

    train_model(model_type=args.model, csv_path=args.dataset)


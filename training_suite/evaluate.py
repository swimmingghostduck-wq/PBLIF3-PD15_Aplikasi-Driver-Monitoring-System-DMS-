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

import joblib
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.metrics import confusion_matrix, classification_report, accuracy_score

from training_suite.dataset_loader import DatasetLoader

def evaluate_model(model_path=None, csv_path=None):
    """
    Evaluasi performa model dan pembuatan grafik Confusion Matrix untuk laporan PBL.
    """
    if model_path is None:
        model_path = os.path.join(ROOT_DIR, 'models', 'trained_model.joblib')
    if csv_path is None:
        csv_path = os.path.join(ROOT_DIR, 'dataset', 'dataset_features.csv')

    if not os.path.exists(model_path):
        print(f"[ERROR] Model tidak ditemukan di {model_path}. Silakan jalankan train_sklearn.py terlebih dahulu.")
        return

    print("=" * 60)
    print("      DMS MODEL EVALUATOR & REPORT GENERATOR")
    print("=" * 60)
    print(f"Memuat model: {model_path}")

    bundle = joblib.load(model_path)
    model = bundle['model']
    scaler = bundle['scaler']
    model_name = bundle.get('name', 'Model')

    loader = DatasetLoader(csv_path)
    _, X_test_scaled, _, y_test, _ = loader.load_data()

    y_pred = model.predict(X_test_scaled)
    acc = accuracy_score(y_test, y_pred) * 100

    print(f"\nModel: {model_name}")
    print(f"Akurasi: {acc:.2f}%")
    print("\nLaporan Detail:")
    print(classification_report(y_test, y_pred))

    # Plot Confusion Matrix
    cm = confusion_matrix(y_test, y_pred)
    classes = sorted(list(set(y_test)))

    plt.figure(figsize=(8, 6))
    sns.heatmap(cm, annot=True, fmt='d', cmap='Blues',
                xticklabels=classes, yticklabels=classes)
    plt.title(f'Confusion Matrix - {model_name}\nAkurasi: {acc:.2f}% (Standar PBL IF3-PD15 >= 80%)', fontsize=12)
    plt.ylabel('Kondisi Aktual (Ground Truth)', fontsize=10)
    plt.xlabel('Prediksi Model AI', fontsize=10)
    plt.tight_layout()

    out_plot = os.path.join(ROOT_DIR, 'models', 'confusion_matrix.png')
    plt.savefig(out_plot, dpi=300)
    plt.close()

    print(f"[SUKSES] Grafik Confusion Matrix berhasil disimpan ke:")
    print(f"  -> {out_plot}")
    print("Grafik ini siap dilampirkan pada Laporan, Poster, dan Presentasi PBL Anda!")

if __name__ == '__main__':
    evaluate_model()


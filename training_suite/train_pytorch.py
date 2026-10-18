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
import numpy as np
import torch
import torch.nn as nn
import torch.optim as optim
from torch.utils.data import TensorDataset, DataLoader
from sklearn.preprocessing import LabelEncoder
from sklearn.metrics import accuracy_score, classification_report

from training_suite.dataset_loader import DatasetLoader

class DrowsinessMLP(nn.Module):
    """Jaringan Saraf Tiruan Multilayer Perceptron (MLP) untuk Klasifikasi Kantuk."""
    def __init__(self, input_dim, num_classes):
        super(DrowsinessMLP, self).__init__()
        self.net = nn.Sequential(
            nn.Linear(input_dim, 64),
            nn.BatchNorm1d(64),
            nn.ReLU(),
            nn.Dropout(0.2),
            nn.Linear(64, 32),
            nn.BatchNorm1d(32),
            nn.ReLU(),
            nn.Dropout(0.2),
            nn.Linear(32, num_classes)
        )

    def forward(self, x):
        return self.net(x)

def train_pytorch(epochs=30, batch_size=32, lr=0.001, csv_path=None):
    """
    Melatih model Deep Learning PyTorch untuk klasifikasi kantuk driver.
    """
    models_dir = os.path.join(ROOT_DIR, 'models')
    os.makedirs(models_dir, exist_ok=True)
    
    if csv_path is None:
        csv_path = os.path.join(ROOT_DIR, 'dataset', 'dataset_features.csv')

    print("=" * 60)
    print("      DMS MODEL TRAINER - PYTORCH DEEP LEARNING")
    print("=" * 60)

    # 1. Muat Dataset
    loader = DatasetLoader(csv_path)
    X_train, X_test, y_train_str, y_test_str, scaler = loader.load_data()

    # Encode label string ke integer (0, 1, 2)
    le = LabelEncoder()
    y_train = le.fit_transform(y_train_str)
    y_test = le.transform(y_test_str)
    num_classes = len(le.classes_)

    print(f"[+] Kelas: {list(le.classes_)}")
    print(f"[+] Data Latih: {X_train.shape[0]} | Data Uji: {X_test.shape[0]}")

    # Konversi ke PyTorch Tensors
    train_dataset = TensorDataset(
        torch.tensor(X_train, dtype=torch.float32),
        torch.tensor(y_train, dtype=torch.long)
    )
    test_dataset = TensorDataset(
        torch.tensor(X_test, dtype=torch.float32),
        torch.tensor(y_test, dtype=torch.long)
    )

    train_loader = DataLoader(train_dataset, batch_size=batch_size, shuffle=True)
    test_loader = DataLoader(test_dataset, batch_size=batch_size, shuffle=False)

    # Inisialisasi Model, Loss, Optimizer
    input_dim = X_train.shape[1]
    model = DrowsinessMLP(input_dim, num_classes)
    criterion = nn.CrossEntropyLoss()
    optimizer = optim.Adam(model.parameters(), lr=lr)

    # Loop Pelatihan
    print(f"\n[*] Memulai pelatihan {epochs} epoch...")
    model.train()
    for epoch in range(1, epochs + 1):
        total_loss = 0.0
        for batch_x, batch_y in train_loader:
            optimizer.zero_grad()
            outputs = model(batch_x)
            loss = criterion(outputs, batch_y)
            loss.backward()
            optimizer.step()
            total_loss += loss.item()

        if epoch % 5 == 0 or epoch == epochs:
            avg_loss = total_loss / len(train_loader)
            print(f"  Epoch [{epoch:02d}/{epochs:02d}] - Loss: {avg_loss:.4f}")

    # Evaluasi pada Data Uji
    model.eval()
    all_preds = []
    all_targets = []
    with torch.no_grad():
        for batch_x, batch_y in test_loader:
            outputs = model(batch_x)
            _, preds = torch.max(outputs, 1)
            all_preds.extend(preds.cpu().numpy())
            all_targets.extend(batch_y.cpu().numpy())

    acc = accuracy_score(all_targets, all_preds) * 100
    print("\n" + "=" * 60)
    print(f"            HASIL EVALUASI PYTORCH MLP")
    print("=" * 60)
    print(f"Akurasi Pengujian: {acc:.2f}%")
    if acc >= 80.0:
        print(f"[SUCCESS] Memenuhi standar NFR-05 PBL: Akurasi >= 80% ({acc:.2f}%)")
    else:
        print(f"[WARNING] Akurasi di bawah 80%.")

    print("\nLaporan Klasifikasi:")
    print(classification_report(all_targets, all_preds, target_names=le.classes_))

    # Simpan Model PyTorch
    save_path = os.path.join(models_dir, 'trained_model.pt')
    checkpoint = {
        'model_state_dict': model.state_dict(),
        'scaler': scaler,
        'label_encoder_classes': list(le.classes_),
        'name': 'PyTorch Drowsiness MLP',
        'accuracy': acc,
        'input_dim': input_dim,
        'num_classes': num_classes
    }
    torch.save(checkpoint, save_path)
    print(f"\n[TERSIMPAN] Model PyTorch berhasil disimpan ke: {save_path}")
    return save_path, acc

if __name__ == '__main__':
    parser = argparse.ArgumentParser(description="Latih model Deep Learning PyTorch")
    parser.add_argument('--epochs', type=int, default=25, help="Jumlah epoch")
    parser.add_argument('--batch-size', type=int, default=32, help="Ukuran batch")
    parser.add_argument('--lr', type=float, default=0.001, help="Learning rate")
    args = parser.parse_args()

    train_pytorch(epochs=args.epochs, batch_size=args.batch_size, lr=args.lr)


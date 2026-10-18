import os
import numpy as np
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler

class DatasetLoader:
    """
    Modul pemuat, pembersih, dan pemroses dataset untuk pelatihan model AI kantuk.
    Mendukung dataset CSV hasil data_collector.py, dataset eksternal,
    serta generator dataset fisiologis realistis untuk uji coba awal.
    """

    FEATURE_COLUMNS = [
        'ear_left', 'ear_right', 'ear_avg', 'mar',
        'pitch', 'yaw', 'roll', 'eye_diff',
        'ear_mar_ratio', 'yaw_abs'
    ]

    LABEL_COLUMN = 'label'

    def __init__(self, csv_path=None):
        self.csv_path = csv_path
        self.scaler = StandardScaler()

    def generate_synthetic_baseline_dataset(self, samples_per_class=400):
        """
        Menghasilkan dataset realistis berbasis distribusi fisiologis manusia
        (Soukupova & Cech 2016, YawDD) untuk bootstrap pelatihan awal.
        """
        np.random.seed(42)
        rows = []

        # 1. Kelas TERJAGA (Alert)
        # Karakteristik: Mata terbuka lebar (EAR 0.28 - 0.38), mulut tertutup (MAR 0.15 - 0.45)
        for _ in range(samples_per_class):
            ear_l = np.clip(np.random.normal(0.32, 0.03), 0.26, 0.42)
            ear_r = np.clip(ear_l + np.random.normal(0.0, 0.01), 0.25, 0.42)
            ear_avg = (ear_l + ear_r) / 2.0
            mar = np.clip(np.random.normal(0.30, 0.06), 0.15, 0.50)
            pitch = np.random.normal(0.0, 5.0)
            yaw = np.random.normal(0.0, 6.0)
            roll = np.random.normal(0.0, 4.0)
            eye_diff = abs(ear_l - ear_r)
            ear_mar_ratio = ear_avg / (mar + 1e-6)
            yaw_abs = abs(yaw)
            rows.append([ear_l, ear_r, ear_avg, mar, pitch, yaw, roll, eye_diff, ear_mar_ratio, yaw_abs, 'TERJAGA'])

        # 2. Kelas MENGANTUK (Drowsy / Closed Eyes)
        # Karakteristik: Mata sayu atau terpejam (EAR 0.08 - 0.20), kepala sedikit menunduk
        for _ in range(samples_per_class):
            ear_l = np.clip(np.random.normal(0.14, 0.03), 0.05, 0.21)
            ear_r = np.clip(ear_l + np.random.normal(0.0, 0.01), 0.05, 0.22)
            ear_avg = (ear_l + ear_r) / 2.0
            mar = np.clip(np.random.normal(0.32, 0.07), 0.15, 0.52)
            pitch = np.random.normal(-8.0, 6.0) # Sedikit menunduk
            yaw = np.random.normal(0.0, 7.0)
            roll = np.random.normal(0.0, 5.0)
            eye_diff = abs(ear_l - ear_r)
            ear_mar_ratio = ear_avg / (mar + 1e-6)
            yaw_abs = abs(yaw)
            rows.append([ear_l, ear_r, ear_avg, mar, pitch, yaw, roll, eye_diff, ear_mar_ratio, yaw_abs, 'MENGANTUK'])

        # 3. Kelas MENGUAP (Yawning)
        # Karakteristik: Mulut terbuka sangat lebar (MAR 0.65 - 1.10), mata menyempit
        for _ in range(samples_per_class // 2):
            ear_l = np.clip(np.random.normal(0.20, 0.04), 0.10, 0.28)
            ear_r = np.clip(ear_l + np.random.normal(0.0, 0.01), 0.10, 0.28)
            ear_avg = (ear_l + ear_r) / 2.0
            mar = np.clip(np.random.normal(0.85, 0.10), 0.65, 1.20)
            pitch = np.random.normal(4.0, 5.0)
            yaw = np.random.normal(0.0, 6.0)
            roll = np.random.normal(0.0, 4.0)
            eye_diff = abs(ear_l - ear_r)
            ear_mar_ratio = ear_avg / (mar + 1e-6)
            yaw_abs = abs(yaw)
            rows.append([ear_l, ear_r, ear_avg, mar, pitch, yaw, roll, eye_diff, ear_mar_ratio, yaw_abs, 'MENGUAP'])

        columns = self.FEATURE_COLUMNS + [self.LABEL_COLUMN]
        df = pd.DataFrame(rows, columns=columns)
        return df

    def load_data(self, test_size=0.2, random_state=42):
        """
        Memuat dataset dari CSV. Jika file belum ada atau sample < 30,
        akan membuat dataset sintetis fisiologis agar skrip training tetap berhasil berjalan.
        """
        if self.csv_path and os.path.exists(self.csv_path):
            try:
                df = pd.read_csv(self.csv_path)
                # Bersihkan missing values
                df = df.dropna()
                if len(df) >= 30 and self.LABEL_COLUMN in df.columns:
                    print(f"[DatasetLoader] Berhasil memuat {len(df)} sample dari {self.csv_path}")
                else:
                    print(f"[DatasetLoader] Sample pada CSV ({len(df)}) terlalu sedikit untuk training.")
                    print("[DatasetLoader] Menggabungkan dengan dataset fisiologis realistis...")
                    synthetic_df = self.generate_synthetic_baseline_dataset()
                    df = pd.concat([synthetic_df, df], ignore_index=True)
            except Exception as e:
                print(f"[DatasetLoader] Gagal membaca CSV: {e}. Menggunakan dataset fisiologis.")
                df = self.generate_synthetic_baseline_dataset()
        else:
            print("[DatasetLoader] File CSV belum ditemukan. Menggunakan dataset fisiologis untuk bootstrap.")
            df = self.generate_synthetic_baseline_dataset()

        # Ekstraksi matriks fitur X dan target y
        X = df[self.FEATURE_COLUMNS].values
        y = df[self.LABEL_COLUMN].values

        # Split stratified train/test
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=test_size, random_state=random_state, stratify=y
        )

        # Normalisasi fitur
        X_train_scaled = self.scaler.fit_transform(X_train)
        X_test_scaled = self.scaler.transform(X_test)

        return X_train_scaled, X_test_scaled, y_train, y_test, self.scaler


import os
import sys
import pandas as pd
import shutil

ROOT_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
if ROOT_DIR not in sys.path:
    sys.path.insert(0, ROOT_DIR)

def clean_dataset(csv_path=None, auto_fix=True):
    if csv_path is None:
        csv_path = os.path.join(ROOT_DIR, 'dataset', 'dataset_features.csv')

    if not os.path.exists(csv_path):
        print(f"[ERROR] File dataset tidak ditemukan: {csv_path}")
        return

    print("=" * 60)
    print("      DMS DATASET SANITIZER & ANOMALY CLEANER")
    print("=" * 60)
    print(f"Memeriksa: {csv_path}\n")

    df = pd.read_csv(csv_path)
    total_awal = len(df)
    print(f"Total baris data saat ini: {total_awal}")
    print("Distribusi kelas awal:")
    for lbl, count in df['label'].value_counts().items():
        print(f"  - {lbl:12s}: {count} sampel")

    # 1. Deteksi Anomali Fisiologis Kontradiktif:
    # Anomali A: Dilabeli MENGUAP padahal mulut tertutup rapat (MAR < 0.35) dan mata terpejam (EAR < 0.17)
    mislabeled_yawn_as_drowsy = (df['label'] == 'MENGUAP') & (df['mar'] < 0.40) & (df['ear_avg'] < 0.17)
    
    # Anomali B: Dilabeli MENGANTUK padahal mata terbuka lebar (EAR > 0.25)
    mislabeled_drowsy_as_awake = (df['label'] == 'MENGANTUK') & (df['ear_avg'] > 0.25)

    # Anomali C: Nilai NaN atau duplikat ekstrem
    has_nan = df.isna().any(axis=1)

    bad_rows = mislabeled_yawn_as_drowsy | mislabeled_drowsy_as_awake | has_nan
    jumlah_anomali = bad_rows.sum()

    print(f"\n[HASIL PEMERIKSAAN]")
    print(f"  - Salah label MENGUAP (padahal mata tidur/mulut tutup): {mislabeled_yawn_as_drowsy.sum()} baris")
    print(f"  - Salah label MENGANTUK (padahal mata terbuka lebar)  : {mislabeled_drowsy_as_awake.sum()} baris")
    print(f"  - Baris korup / nilai kosong (NaN)                   : {has_nan.sum()} baris")
    print(f"  Total anomali ditemukan                             : {jumlah_anomali} baris")

    if jumlah_anomali == 0:
        print("\n[V] Dataset Anda 100% BERSIH! Tidak ada data yang kontradiktif atau salah label.")
        return

    if auto_fix:
        # Backup file lama
        bak_path = csv_path + ".bak"
        shutil.copyfile(csv_path, bak_path)
        print(f"\n[*] Cadangan dataset tersimpan di: {bak_path}")

        # Bersihkan data anomali
        df_clean = df[~bad_rows].copy()
        df_clean.to_csv(csv_path, index=False)
        print(f"[+] Berhasil membuang {jumlah_anomali} baris anomali yang salah input.")
        print(f"[+] Total data tersisa: {len(df_clean)} sampel.")
        print("\nDistribusi kelas setelah pembersihan:")
        for lbl, count in df_clean['label'].value_counts().items():
            print(f"  - {lbl:12s}: {count} sampel")
        print("\n[SELESAI] Dataset telah bersih. Anda siap melatih model kembali!")

if __name__ == '__main__':
    clean_dataset()


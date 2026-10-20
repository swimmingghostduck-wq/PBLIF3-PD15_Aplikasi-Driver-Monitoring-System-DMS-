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

import csv
import glob
import argparse
import cv2
from dms_core.detector import FaceLandmarkDetector

VALID_EXTENSIONS = ('.jpg', '.jpeg', '.png', '.bmp', '.webp')

LABEL_MAPPING = {
    # Kelas TERJAGA
    'terjaga': 'TERJAGA',
    'alert': 'TERJAGA',
    'open': 'TERJAGA',
    'open_eyes': 'TERJAGA',
    'normal': 'TERJAGA',
    'focused': 'TERJAGA',
    '0': 'TERJAGA',

    # Kelas MENGANTUK
    'mengantuk': 'MENGANTUK',
    'drowsy': 'MENGANTUK',
    'closed': 'MENGANTUK',
    'closed_eyes': 'MENGANTUK',
    'sleep': 'MENGANTUK',
    'sleepy': 'MENGANTUK',
    '1': 'MENGANTUK',

    # Kelas MENGUAP
    'menguap': 'MENGUAP',
    'yawn': 'MENGUAP',
    'yawning': 'MENGUAP',
    'mouth_open': 'MENGUAP',
    '2': 'MENGUAP'
}

def extract_features_from_images(source_dir=None, output_csv=None):
    """
    Mengekstrak 10 vektor fitur geometris MediaPipe dari kumpulan foto wajah
    dan menyimpannya ke dataset/dataset_features.csv untuk melatih model AI.
    """
    if source_dir is None:
        source_dir = os.path.join(ROOT_DIR, 'dataset', 'raw_images')
    if output_csv is None:
        output_csv = os.path.join(ROOT_DIR, 'dataset', 'dataset_features.csv')

    print("=" * 65)
    print("      DMS DATASET EXTRACTOR - BATCH FOTO WAJAH KE FITUR AI")
    print("=" * 65)
    print(f"Direktori Sumber : {source_dir}")
    print(f"File Output CSV  : {output_csv}")
    print("=" * 65)

    if not os.path.exists(source_dir):
        os.makedirs(source_dir, exist_ok=True)
        # Buat folder contoh kelas untuk memudahkan pengguna
        for cls_name in ['TERJAGA', 'MENGANTUK', 'MENGUAP']:
            os.makedirs(os.path.join(source_dir, cls_name), exist_ok=True)
        
        print(f"\n[!] Direktori {source_dir} baru saja dibuat.")
        print("Silakan masukkan foto-foto wajah ke dalam subfolder yang sesuai:")
        print(f"  1. {os.path.join(source_dir, 'TERJAGA')}   -> Foto mata terbuka normal")
        print(f"  2. {os.path.join(source_dir, 'MENGANTUK')} -> Foto mata terpejam / sayu")
        print(f"  3. {os.path.join(source_dir, 'MENGUAP')}   -> Foto mulut terbuka menguap")
        print("\nSetelah foto dimasukkan, jalankan kembali perintah ini.")
        return

    # Inisialisasi detector
    detector = FaceLandmarkDetector()

    # Siapkan file CSV
    os.makedirs(os.path.dirname(output_csv), exist_ok=True)
    file_exists = os.path.exists(output_csv)
    csv_file = open(output_csv, mode='a', newline='', encoding='utf-8')
    writer = csv.writer(csv_file)

    if not file_exists or os.path.getsize(output_csv) == 0:
        header = [
            'ear_left', 'ear_right', 'ear_avg', 'mar',
            'pitch', 'yaw', 'roll', 'eye_diff',
            'ear_mar_ratio', 'yaw_abs', 'label'
        ]
        writer.writerow(header)
        csv_file.flush()

    total_scanned = 0
    total_success = 0
    total_skipped = 0
    stats_per_class = {'TERJAGA': 0, 'MENGANTUK': 0, 'MENGUAP': 0}

    # Pindai subfolder
    subdirs = [d for d in os.listdir(source_dir) if os.path.isdir(os.path.join(source_dir, d))]
    
    if not subdirs:
        print(f"\n[WARN] Tidak ada subfolder di dalam {source_dir}.")
        print("Buat subfolder 'TERJAGA', 'MENGANTUK', dan 'MENGUAP' lalu isi foto.")
        csv_file.close()
        return

    for folder_name in subdirs:
        norm_key = folder_name.strip().lower()
        target_label = LABEL_MAPPING.get(norm_key, None)
        
        if not target_label:
            print(f"[?] Melewati folder '{folder_name}' (nama kelas tidak dikenali: gunakan TERJAGA/MENGANTUK/MENGUAP)")
            continue

        folder_path = os.path.join(source_dir, folder_name)
        image_files = []
        for ext in VALID_EXTENSIONS:
            image_files.extend(glob.glob(os.path.join(folder_path, f'*{ext}')))
            image_files.extend(glob.glob(os.path.join(folder_path, f'*{ext.upper()}')))

        print(f"\n[*] Memproses folder '{folder_name}' -> Label: {target_label} ({len(image_files)} foto ditemukan)")

        for img_path in image_files:
            total_scanned += 1
            img = cv2.imread(img_path)
            if img is None:
                total_skipped += 1
                continue

            res = detector.process_frame(img)
            if res.get('face_detected', False) and len(res.get('features_vector', [])) == 10:
                row = res['features_vector'] + [target_label]
                writer.writerow(row)
                total_success += 1
                stats_per_class[target_label] += 1
            else:
                total_skipped += 1

            if total_scanned % 50 == 0:
                print(f"    -> Telah memproses {total_scanned} foto... ({total_success} berhasil diekstrak)")

    csv_file.flush()
    csv_file.close()

    print("\n" + "=" * 65)
    print("            RINGKASAN EKSTRAKSI DATASET SELESAI")
    print("=" * 65)
    print(f"Total Foto Dipindai     : {total_scanned}")
    print(f"Berhasil Diekstrak (Ada Wajah) : {total_success}")
    print(f"Dilewati (Wajah Tidak Jelas)  : {total_skipped}")
    print("Detail Kelas:")
    for cls, count in stats_per_class.items():
        print(f"  - {cls:<12}: {count} sampel")
    print(f"\nData berhasil disimpan ke: {output_csv}")
    print("Selanjutnya, latih model AI dengan:")
    print("  python training_suite/train_sklearn.py --model rf")
    print("=" * 65)

if __name__ == '__main__':
    parser = argparse.ArgumentParser(description="Ekstrak fitur wajah dari kumpulan foto ke CSV training DMS")
    parser.add_argument('--dir', type=str, default=None, help="Path ke folder foto (default: dataset/raw_images)")
    parser.add_argument('--csv', type=str, default=None, help="Path ke file output CSV")
    args = parser.parse_args()

    extract_features_from_images(args.dir, args.csv)

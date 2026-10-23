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

import time
import csv
import cv2

from dms_core.detector import FaceLandmarkDetector

def run_collector():
    """
    Aplikasi GUI Interaktif Perekam Dataset Wajah dan Landmark Pengemudi via Webcam.
    Menyimpan fitur ke dataset/dataset_features.csv untuk melatih model custom.
    """
    dataset_dir = os.path.join(ROOT_DIR, 'dataset')
    os.makedirs(dataset_dir, exist_ok=True)
    csv_path = os.path.join(dataset_dir, 'dataset_features.csv')

    # Buat header CSV jika file belum ada
    file_exists = os.path.exists(csv_path)
    csv_file = open(csv_path, mode='a', newline='', encoding='utf-8')
    csv_writer = csv.writer(csv_file)
    
    if not file_exists:
        header = [
            'ear_left', 'ear_right', 'ear_avg', 'mar',
            'pitch', 'yaw', 'roll', 'eye_diff',
            'ear_mar_ratio', 'yaw_abs', 'label'
        ]
        csv_writer.writerow(header)
        csv_file.flush()

    detector = FaceLandmarkDetector()
    
    # Mencoba membuka webcam (DirectShow untuk Windows yang lebih stabil, dan coba index 0 lalu 1)
    cap = cv2.VideoCapture(0, cv2.CAP_DSHOW)
    if not cap.isOpened():
        cap = cv2.VideoCapture(0)
    if not cap.isOpened():
        cap = cv2.VideoCapture(1, cv2.CAP_DSHOW)
    if not cap.isOpened():
        cap = cv2.VideoCapture(1)

    if not cap.isOpened():
        print("[ERROR] Tidak dapat membuka webcam. Pastikan kamera terpasang dan tidak dipakai aplikasi lain (seperti Zoom/Meet/Browser).")
        csv_file.close()
        return

    # Status rekaman
    is_recording = False
    current_label = 'TERJAGA'  # 'TERJAGA', 'MENGANTUK', 'MENGUAP'
    recorded_samples = 0
    auto_record_end_time = 0.0
    session_batches = []
    current_batch_count = 0
    undo_notice = ""
    undo_notice_time = 0.0

    print("=" * 60)
    print("      DMS DATA COLLECTOR - TOOL PENGUMPUL DATASET")
    print("=" * 60)
    print("Instruksi Tombol:")
    print("  [1] Set Kelas: TERJAGA (Mata Terbuka Normal)")
    print("  [2] Set Kelas: MENGANTUK (Mata Terpejam/Sayu)")
    print("  [3] Set Kelas: MENGUAP (Mulut Terbuka Lebar)")
    print("  [SPASI] Mulai/Stop Rekam Kontinu (Auto 10 Detik)")
    print("  [R] Rekam 1 Sample Saat Ini (Manual Click)")
    print("  [U] UNDO / Batalkan Rekaman Batch Terakhir (Jika Salah Input)")
    print("  [Q] atau [ESC] Keluar & Selesai")
    print("=" * 60)

    win_name = "DMS Data Collector - Tekan [Q] untuk Keluar"
    cv2.namedWindow(win_name, cv2.WINDOW_AUTOSIZE)
    try:
        cv2.setWindowProperty(win_name, cv2.WND_PROP_TOPMOST, 1)
    except Exception:
        pass

    print("\n" + "=" * 60)
    print("      DMS DATA COLLECTOR AKTIF!")
    print("=" * 60)
    print("[+] Jendela kamera pop-up telah dibuka di layar Anda.")
    print("[+] Silakan fokuskan jendela kamera tersebut.")
    print("Instruksi Tombol:")
    print("  [1] Set Kelas: TERJAGA (Mata Terbuka Normal)")
    print("  [2] Set Kelas: MENGANTUK (Mata Terpejam/Sayu)")
    print("  [3] Set Kelas: MENGUAP (Mulut Terbuka Lebar)")
    print("  [SPASI] Mulai/Stop Rekam Kontinu (Auto 10 Detik)")
    print("  [R] Rekam 1 Sample Saat Ini (Manual Click)")
    print("  [Q] atau [ESC] Keluar & Selesai")
    print("=" * 60 + "\n")

    try:
        while True:
            ret, frame = cap.read()
            if not ret:
                print("[WARN] Gagal membaca frame dari webcam.")
                break

            # Flip horizontal agar seperti cermin
            frame = cv2.flip(frame, 1)
            h, w = frame.shape[:2]

            results = detector.process_frame(frame)
            hud_frame = frame.copy()

            # Visualisasi Landmark Mata & Wajah
            if results.get('face_detected', False):
                # Gambar titik landmark mata
                for pt in results['left_eye_pts']:
                    cv2.circle(hud_frame, pt, 2, (0, 255, 0), -1)
                for pt in results['right_eye_pts']:
                    cv2.circle(hud_frame, pt, 2, (0, 255, 0), -1)

                # Kotak Wajah
                x1, y1, x2, y2 = results['bbox']
                cv2.rectangle(hud_frame, (x1, y1), (x2, y2), (255, 200, 0), 1)

                # Jika sedang merekam, simpan sample
                if is_recording:
                    row = results['features_vector'] + [current_label]
                    csv_writer.writerow(row)
                    recorded_samples += 1
                    current_batch_count += 1

                    # Cek batas waktu auto-record
                    if auto_record_end_time > 0 and time.time() >= auto_record_end_time:
                        is_recording = False
                        auto_record_end_time = 0.0
                        if current_batch_count > 0:
                            session_batches.append(current_batch_count)
                            current_batch_count = 0
                        print(f"[*] Sesi perekaman selesai. Total sample tersimpan: {recorded_samples}")

            # Tampilkan Panel Informasi (HUD)
            overlay = hud_frame.copy()
            cv2.rectangle(overlay, (0, 0), (w, 140), (20, 20, 20), -1)
            cv2.addWeighted(overlay, 0.8, hud_frame, 0.2, 0, hud_frame)

            # Header Info
            status_color = (0, 0, 255) if is_recording else (0, 255, 0)
            rec_status = f"● RECORDING: {current_label}" if is_recording else f"○ STANDBY - Siap: {current_label}"
            cv2.putText(hud_frame, rec_status, (15, 30), cv2.FONT_HERSHEY_SIMPLEX, 0.75, status_color, 2)

            # Metrik Deteksi (Tampilkan EAR Kiri dan Kanan untuk deteksi rambut/poni)
            ear_l = results.get('ear_left', 0.0)
            ear_r = results.get('ear_right', 0.0)
            ear_avg = results.get('ear_avg', 0.0)
            mar = results.get('mar', 0.0)
            
            ear_text = f"EAR L: {ear_l:.3f} | R: {ear_r:.3f} | Avg: {ear_avg:.3f} | MAR: {mar:.3f}"
            pose_text = f"Head Pose: Yaw {results.get('yaw', 0.0):.1f}° | Pitch {results.get('pitch', 0.0):.1f}°"
            sample_text = f"Dataset: {recorded_samples} sampel sesi ini"
            guide_text = "Tombol: [1]=TERJAGA | [2]=MENGANTUK | [3]=MENGUAP | [SPASI]=REKAM | [U]=UNDO | [Q]=KELUAR"

            cv2.putText(hud_frame, ear_text, (15, 60), cv2.FONT_HERSHEY_SIMPLEX, 0.55, (0, 255, 255), 1)
            cv2.putText(hud_frame, pose_text, (15, 85), cv2.FONT_HERSHEY_SIMPLEX, 0.55, (200, 200, 200), 1)

            if time.time() < undo_notice_time:
                cv2.rectangle(hud_frame, (10, 95), (w - 10, 135), (0, 100, 220), -1)
                cv2.putText(hud_frame, undo_notice, (18, 122), cv2.FONT_HERSHEY_SIMPLEX, 0.52, (255, 255, 255), 2)
            else:
                cv2.putText(hud_frame, sample_text, (15, 110), cv2.FONT_HERSHEY_SIMPLEX, 0.55, (100, 255, 100), 1)
                cv2.putText(hud_frame, guide_text, (15, 132), cv2.FONT_HERSHEY_SIMPLEX, 0.42, (180, 180, 180), 1)

            cv2.imshow(win_name, hud_frame)

            # Event Handler Tombol Keyboard
            key = cv2.waitKey(1) & 0xFF
            if key in [ord('q'), ord('Q'), 27]: # ESC / Q
                break
            elif key == ord('1'):
                current_label = 'TERJAGA'
                print("[+] Target Label: TERJAGA (Alert)")
            elif key == ord('2'):
                current_label = 'MENGANTUK'
                print("[+] Target Label: MENGANTUK (Drowsy / Closed Eyes)")
            elif key == ord('3'):
                current_label = 'MENGUAP'
                print("[+] Target Label: MENGUAP (Yawn)")
            elif key == 32: # SPASI: Rekam 10 detik
                is_recording = not is_recording
                if is_recording:
                    current_batch_count = 0
                    auto_record_end_time = time.time() + 10.0
                    print(f"[!] Memulai perekaman otomatis 10 detik untuk label: {current_label}...")
                else:
                    auto_record_end_time = 0.0
                    if current_batch_count > 0:
                        session_batches.append(current_batch_count)
                        current_batch_count = 0
                    print("[!] Perekaman dihentikan.")
            elif key in [ord('r'), ord('R')]: # Manual 1 frame
                if results.get('face_detected', False):
                    row = results['features_vector'] + [current_label]
                    csv_writer.writerow(row)
                    recorded_samples += 1
                    session_batches.append(1)
                    csv_file.flush()
                    print(f"[+] 1 sample manual tersimpan ({current_label}). Total: {recorded_samples}")
            elif key in [ord('u'), ord('U')]: # UNDO / BATAL
                if is_recording:
                    is_recording = False
                    auto_record_end_time = 0.0
                    if current_batch_count > 0:
                        session_batches.append(current_batch_count)
                        current_batch_count = 0

                if session_batches:
                    count_to_remove = session_batches.pop()
                    csv_file.flush()
                    csv_file.close()

                    with open(csv_path, 'r', encoding='utf-8') as f:
                        all_rows = list(csv.reader(f))

                    if len(all_rows) > count_to_remove:
                        all_rows = all_rows[:-count_to_remove]

                    with open(csv_path, 'w', newline='', encoding='utf-8') as f:
                        writer = csv.writer(f)
                        writer.writerows(all_rows)

                    csv_file = open(csv_path, mode='a', newline='', encoding='utf-8')
                    csv_writer = csv.writer(csv_file)
                    recorded_samples = max(0, recorded_samples - count_to_remove)
                    undo_notice = f"BATAL! {count_to_remove} sampel terakhir dihapus."
                    undo_notice_time = time.time() + 3.0
                    print(f"\n[UNDO] {undo_notice}")
                else:
                    undo_notice = "Tidak ada rekaman sesi ini yang bisa dibatalkan."
                    undo_notice_time = time.time() + 2.5
                    print(f"\n[UNDO] {undo_notice}")
    finally:
        csv_file.close()
        if cap is not None and cap.isOpened():
            cap.release()
        cv2.destroyAllWindows()
    print(f"\n[SELESAI] Data berhasil disimpan ke: {csv_path}")

if __name__ == '__main__':
    run_collector()


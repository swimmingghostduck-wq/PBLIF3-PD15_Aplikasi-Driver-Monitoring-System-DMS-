import os
import sys
import time
import csv
import cv2

# Tambahkan direktori root agar dapat mengimpor dms_core
ROOT_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
if ROOT_DIR not in sys.path:
    sys.path.insert(0, ROOT_DIR)

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
    cap = cv2.VideoCapture(0)

    if not cap.isOpened():
        print("[ERROR] Tidak dapat membuka webcam. Pastikan kamera terpasang dan tidak dipakai aplikasi lain.")
        return

    # Status rekaman
    is_recording = False
    current_label = 'TERJAGA'  # 'TERJAGA', 'MENGANTUK', 'MENGUAP'
    recorded_samples = 0
    auto_record_end_time = 0.0

    print("=" * 60)
    print("      DMS DATA COLLECTOR - TOOL PENGUMPUL DATASET")
    print("=" * 60)
    print("Instruksi Tombol:")
    print("  [1] Set Kelas: TERJAGA (Mata Terbuka Normal)")
    print("  [2] Set Kelas: MENGANTUK (Mata Terpejam/Sayu)")
    print("  [3] Set Kelas: MENGUAP (Mulut Terbuka Lebar)")
    print("  [SPASI] Mulai/Stop Rekam Kontinu (Auto 10 Detik)")
    print("  [R] Rekam 1 Sample Saat Ini (Manual Click)")
    print("  [Q] atau [ESC] Keluar & Selesai")
    print("=" * 60)

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

                # Cek batas waktu auto-record
                if auto_record_end_time > 0 and time.time() >= auto_record_end_time:
                    is_recording = False
                    auto_record_end_time = 0.0
                    print(f"[*] Sesi perekaman selesai. Total sample tersimpan: {recorded_samples}")

        # Tampilkan Panel Informasi (HUD)
        overlay = hud_frame.copy()
        cv2.rectangle(overlay, (0, 0), (w, 120), (20, 20, 20), -1)
        cv2.addWeighted(overlay, 0.75, hud_frame, 0.25, 0, hud_frame)

        # Header Info
        status_color = (0, 0, 255) if is_recording else (180, 180, 180)
        rec_status = f"STATUS: RECORDING (Kelas: {current_label})" if is_recording else f"STATUS: STANDBY (Siap: {current_label})"
        cv2.putText(hud_frame, rec_status, (15, 30), cv2.FONT_HERSHEY_SIMPLEX, 0.7, status_color, 2)

        # Metrik Deteksi
        ear_text = f"EAR: {results.get('ear_avg', 0.0):.3f} | MAR: {results.get('mar', 0.0):.3f}"
        pose_text = f"Yaw: {results.get('yaw', 0.0):.1f}° | Pitch: {results.get('pitch', 0.0):.1f}°"
        sample_text = f"Total Sample: {recorded_samples} tersimpan"
        cv2.putText(hud_frame, ear_text, (15, 60), cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 255), 1)
        cv2.putText(hud_frame, pose_text, (15, 85), cv2.FONT_HERSHEY_SIMPLEX, 0.6, (200, 200, 200), 1)
        cv2.putText(hud_frame, sample_text, (15, 110), cv2.FONT_HERSHEY_SIMPLEX, 0.55, (0, 255, 0), 1)

        cv2.imshow("DMS Data Collector - Tekan [Q] untuk Keluar", hud_frame)

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
                auto_record_end_time = time.time() + 10.0
                print(f"[!] Memulai perekaman otomatis 10 detik untuk label: {current_label}...")
            else:
                auto_record_end_time = 0.0
                print("[!] Perekaman dihentikan.")
        elif key in [ord('r'), ord('R')]: # Manual 1 frame
            if results.get('face_detected', False):
                row = results['features_vector'] + [current_label]
                csv_writer.writerow(row)
                recorded_samples += 1
                csv_file.flush()
                print(f"[+] 1 sample manual tersimpan ({current_label}). Total: {recorded_samples}")

    csv_file.close()
    cap.release()
    cv2.destroyAllWindows()
    print(f"\n[SELESAI] Data berhasil disimpan ke: {csv_path}")

if __name__ == '__main__':
    run_collector()


import os
import time
import cv2

class AlertManager:
    """
    Manajer Notifikasi dan Peringatan Driver Monitoring System.
    Menghandle:
    - Penyimpanan tangkapan layar (snapshot) saat insiden terjadi secara efisien
    - Optimasi kompresi JPEG 640p Q75 untuk mencegah pembengkakan memori disk
    - Pembersihan otomatis (Auto-Pruning / FIFO) file lama
    """

    def __init__(self, snapshots_dir, max_storage_mb=100, max_retention_days=14):
        self.snapshots_dir = snapshots_dir
        self.max_storage_mb = max_storage_mb
        self.max_retention_days = max_retention_days
        self._save_counter = 0
        os.makedirs(self.snapshots_dir, exist_ok=True)

    def save_snapshot(self, frame, driver_id, alert_type):
        """
        Menyimpan tangkapan layar saat kondisi bahaya terdeteksi
        dengan resolusi efisien 640p dan kompresi JPEG Q75.
        Ukuran berkas turun dari ~250 KB menjadi hanya ~20-25 KB.
        """
        if frame is None:
            return None

        # Periodically jalankan rotasi pembersihan disk (setiap 25 snapshot)
        self._save_counter += 1
        if self._save_counter % 25 == 0:
            self.cleanup_old_snapshots()

        timestamp_str = time.strftime("%Y%m%d_%H%M%S")
        filename = f"alert_drv{driver_id}_{alert_type}_{timestamp_str}_{int(time.time()*1000)%1000}.jpg"
        filepath = os.path.join(self.snapshots_dir, filename)

        try:
            h, w = frame.shape[:2]
            # Downscale frame ke lebar standar 640p jika kamera beresolusi tinggi (720p/1080p)
            if w > 640:
                target_w = 640
                target_h = int(h * (640 / w))
                snapshot = cv2.resize(frame, (target_w, target_h), interpolation=cv2.INTER_AREA)
            else:
                snapshot = frame.copy()

            sh, sw = snapshot.shape[:2]

            # Watermark bukti forensik pada bagian bawah frame
            cv2.rectangle(snapshot, (0, sh - 30), (sw, sh), (0, 0, 0), -1)
            watermark_text = f"DMS BUKTI: {alert_type.upper()} | Driver #{driver_id} | {time.strftime('%Y-%m-%d %H:%M:%S')}"
            cv2.putText(snapshot, watermark_text, (10, sh - 10),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.45, (0, 255, 255), 1, cv2.LINE_AA)

            # Simpan dengan kompresi teroptimasi JPEG Q75
            cv2.imwrite(filepath, snapshot, [cv2.IMWRITE_JPEG_QUALITY, 75])

            # Sinkronisasi ke folder public Laravel jika tersedia
            laravel_public_snapshots = os.path.abspath(os.path.join(
                os.path.dirname(__file__), '..', 'laravel', 'public', 'snapshots'
            ))
            if os.path.exists(laravel_public_snapshots):
                laravel_filepath = os.path.join(laravel_public_snapshots, filename)
                cv2.imwrite(laravel_filepath, snapshot, [cv2.IMWRITE_JPEG_QUALITY, 75])

            return filename
        except Exception as e:
            print(f"[AlertManager] Gagal menyimpan snapshot: {e}")
            return None

    def cleanup_old_snapshots(self):
        """
        Pembersihan otomatis latar belakang (Auto-Pruning):
        1. Menghapus file snapshot yang lebih tua dari max_retention_days (default 14 hari).
        2. Jika total direktori melebihi max_storage_mb (default 100 MB), hapus file tertua secara FIFO.
        """
        try:
            if not os.path.exists(self.snapshots_dir):
                return

            now = time.time()
            retention_cutoff = now - (self.max_retention_days * 86400)
            files = []

            for fname in os.listdir(self.snapshots_dir):
                if fname.lower().endswith(('.jpg', '.jpeg', '.png')):
                    fpath = os.path.join(self.snapshots_dir, fname)
                    try:
                        stat = os.stat(fpath)
                        # Hapus jika melebihi batas hari retensi
                        if stat.st_mtime < retention_cutoff:
                            os.remove(fpath)
                            continue
                        files.append((fpath, stat.st_size, stat.st_mtime))
                    except OSError:
                        continue

            # Hitung total ukuran
            total_size_mb = sum(f[1] for f in files) / (1024 * 1024)

            # Jika masih melebihi batas kuota MB, urutkan dari terlama dan hapus (FIFO)
            if total_size_mb > self.max_storage_mb:
                files.sort(key=lambda x: x[2]) # Urutkan berdasarkan waktu modifikasi tertua
                target_size = self.max_storage_mb * 0.8 * 1024 * 1024 # Target turun ke 80% kuota
                current_bytes = sum(f[1] for f in files)

                for fpath, fsize, _ in files:
                    if current_bytes <= target_size:
                        break
                    try:
                        os.remove(fpath)
                        current_bytes -= fsize
                    except OSError:
                        pass
        except Exception as e:
            print(f"[AlertManager] Error saat rotasi cleanup snapshot: {e}")



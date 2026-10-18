import os
import time
import cv2

class AlertManager:
    """
    Manajer Notifikasi dan Peringatan Driver Monitoring System.
    Menghandle:
    - Penyimpanan tangkapan layar (snapshot) saat insiden terjadi
    - Format data event peringatan untuk database dan audio alert
    """

    def __init__(self, snapshots_dir):
        self.snapshots_dir = snapshots_dir
        os.makedirs(self.snapshots_dir, exist_ok=True)

    def save_snapshot(self, frame, driver_id, alert_type):
        """
        Menyimpan tangkapan layar saat kondisi bahaya terdeteksi
        untuk bukti evaluasi log admin dan riwayat driver.
        """
        if frame is None:
            return None

        timestamp_str = time.strftime("%Y%m%d_%H%M%S")
        filename = f"alert_drv{driver_id}_{alert_type}_{timestamp_str}_{int(time.time()*1000)%1000}.jpg"
        filepath = os.path.join(self.snapshots_dir, filename)

        try:
            # Beri watermark teks pada snapshot untuk keperluan audit
            snapshot = frame.copy()
            h, w = snapshot.shape[:2]
            cv2.rectangle(snapshot, (0, h - 35), (w, h), (0, 0, 0), -1)
            watermark_text = f"DMS ALERT: {alert_type.upper()} | Driver ID: {driver_id} | {time.strftime('%Y-%m-%d %H:%M:%S')}"
            cv2.putText(snapshot, watermark_text, (10, h - 12),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 0, 255), 1, cv2.LINE_AA)

            cv2.imwrite(filepath, snapshot)
            return filename
        except Exception as e:
            print(f"[AlertManager] Gagal menyimpan snapshot: {e}")
            return None


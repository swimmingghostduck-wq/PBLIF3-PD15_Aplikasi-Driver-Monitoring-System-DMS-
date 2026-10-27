<?php

namespace App\Http\Controllers;

use App\Models\AlertLog;
use App\Models\MonitoringSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DriverController extends Controller
{
    /**
     * Dashboard Riwayat Peringatan Pengemudi.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $alerts = AlertLog::where('driver_id', $user->id)
            ->orderBy('timestamp', 'desc')
            ->paginate(15);

        $total_kantuk = AlertLog::where('driver_id', $user->id)->where('alert_type', 'kantuk')->count();
        $total_distraksi = AlertLog::where('driver_id', $user->id)->where('alert_type', 'tidak_fokus')->count();
        $total_menguap = AlertLog::where('driver_id', $user->id)->where('alert_type', 'menguap')->count();

        return view('driver.dashboard', compact(
            'user',
            'alerts',
            'total_kantuk',
            'total_distraksi',
            'total_menguap'
        ));
    }

    /**
     * Antarmuka Kabin Real-Time Camera Monitoring.
     */
    public function monitor()
    {
        $user = Auth::user();
        return view('driver.monitor', compact('user'));
    }

    /**
     * Halaman Profil Pengemudi.
     */
    public function profile()
    {
        $user = Auth::user();
        $total_sesi = MonitoringSession::where('driver_id', $user->id)->count();
        $total_kantuk = AlertLog::where('driver_id', $user->id)->where('alert_type', 'kantuk')->count();
        $total_distraksi = AlertLog::where('driver_id', $user->id)->where('alert_type', 'tidak_fokus')->count();
        $total_menguap = AlertLog::where('driver_id', $user->id)->where('alert_type', 'menguap')->count();

        return view('driver.profile', compact(
            'user',
            'total_sesi',
            'total_kantuk',
            'total_distraksi',
            'total_menguap'
        ));
    }

    /**
     * Proxy pengiriman frame webcam ke service AI Python.
     */
    public function processFrame(Request $request)
    {
        $imageData = $request->input('image');
        $driverId = Auth::id() ?? 1;

        if (!$imageData) {
            return response()->json(['error' => 'Tidak ada data gambar'], 400);
        }

        try {
            $response = Http::timeout(6.0)->post('http://127.0.0.1:5000/driver/process_frame', [
                'image' => $imageData,
                'driver_id' => $driverId
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }
        } catch (\Exception $e) {
            // Service Python offline / timeout
        }

        return response()->json([
            'success' => false,
            'face_detected' => false,
            'status_label' => 'STANDBY (Python AI Offline)',
            'alarm_message' => 'Jalankan python run.py untuk inferensi AI',
            'is_alarm_active' => false,
            'ear_avg' => 0.0,
            'mar' => 0.0,
            'yaw' => 0.0,
            'pitch' => 0.0,
            'method' => 'Python Service Offline (Jalankan: python run.py)'
        ]);
    }

    /**
     * Pencatatan log insiden dari sesi pemantauan kamera.
     */
    public function logAlert(Request $request)
    {
        $validated = $request->validate([
            'alert_type' => 'required|string',
            'severity' => 'nullable|string',
            'duration_seconds' => 'nullable|numeric',
            'snapshot_path' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $alert = AlertLog::create([
            'driver_id' => Auth::id(),
            'alert_type' => $validated['alert_type'],
            'severity' => $validated['severity'] ?? ($validated['alert_type'] === 'kantuk' ? 'BAHAYA' : 'PERINGATAN'),
            'duration_seconds' => $validated['duration_seconds'] ?? 2.0,
            'snapshot_path' => $validated['snapshot_path'] ?? null,
            'notes' => $validated['notes'] ?? 'Insiden terdeteksi oleh sistem monitoring real-time',
            'timestamp' => now()
        ]);

        return response()->json(['success' => true, 'id' => $alert->id]);
    }
}

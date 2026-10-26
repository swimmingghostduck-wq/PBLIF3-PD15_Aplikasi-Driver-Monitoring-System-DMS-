<?php

namespace App\Http\Controllers;

use App\Models\AlertLog;
use App\Models\MonitoringSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    /**
     * Dashboard Personal Pengemudi.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $myAlerts = AlertLog::where('driver_id', $user->id)
            ->orderBy('timestamp', 'desc')
            ->limit(10)
            ->get();

        $totalMyAlerts = AlertLog::where('driver_id', $user->id)->count();
        $totalSessions = MonitoringSession::where('driver_id', $user->id)->count();

        // Safety score individual
        $safetyScore = $totalMyAlerts === 0 ? 100.0 : max(50.0, 100.0 - ($totalMyAlerts * 5.0));

        return view('driver.dashboard', compact(
            'user',
            'myAlerts',
            'totalMyAlerts',
            'totalSessions',
            'safetyScore'
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


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
}


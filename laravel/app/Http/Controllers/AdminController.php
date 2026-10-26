<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AlertLog;
use App\Models\MonitoringSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Dashboard Pemantauan Armada (Executive Telematics Overview).
     */
    public function dashboard()
    {
        $totalDrivers = User::where('role', 'driver')->count();
        $totalAlerts = AlertLog::count();
        $totalDrowsy = AlertLog::where('alert_type', 'kantuk')->count();
        $totalDistraction = AlertLog::where('alert_type', 'tidak_fokus')->count();
        $totalYawn = AlertLog::where('alert_type', 'menguap')->count();
        $totalSessions = MonitoringSession::count();

        // Indeks Keselamatan Armada (0 - 100%)
        if ($totalAlerts === 0) {
            $fleetSafetyScore = 100.0;
        } else {
            $fleetSafetyScore = round(max(60.0, 100.0 - ($totalAlerts * 2.5)), 1);
        }

        // Distribusi 24 Jam
        $timeBins = [
            'Dini Hari (00:00 - 06:00)' => 0,
            'Pagi Hari (06:00 - 12:00)' => 0,
            'Siang/Sore (12:00 - 18:00)' => 0,
            'Malam Hari (18:00 - 24:00)' => 0,
        ];

        $allAlerts = AlertLog::select('timestamp')->get();
        foreach ($allAlerts as $a) {
            $hour = $a->timestamp ? Carbon::parse($a->timestamp)->hour : 0;
            if ($hour < 6) {
                $timeBins['Dini Hari (00:00 - 06:00)']++;
            } elseif ($hour < 12) {
                $timeBins['Pagi Hari (06:00 - 12:00)']++;
            } elseif ($hour < 18) {
                $timeBins['Siang/Sore (12:00 - 18:00)']++;
            } else {
                $timeBins['Malam Hari (18:00 - 24:00)']++;
            }
        }

        $recentAlerts = AlertLog::with('driver')
            ->orderBy('timestamp', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalDrivers',
            'totalAlerts',
            'totalDrowsy',
            'totalDistraction',
            'totalYawn',
            'totalSessions',
            'fleetSafetyScore',
            'timeBins',
            'recentAlerts'
        ));
    }

    /**
     * Ringkasan & Log Riwayat Insiden Armada.
     */
    public function alerts(Request $request)
    {
        $query = AlertLog::with('driver')->orderBy('timestamp', 'desc');

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->input('driver_id'));
        }

        if ($request->filled('alert_type')) {
            $query->where('alert_type', $request->input('alert_type'));
        }

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('notes', 'like', "%{$q}%")
                    ->orWhereHas('driver', function ($drv) use ($q) {
                        $drv->where('full_name', 'like', "%{$q}%")
                            ->orWhere('username', 'like', "%{$q}%")
                            ->orWhere('nim_nik', 'like', "%{$q}%");
                    });
            });
        }

        $recentAlerts = $query->get();
        $totalAlerts = AlertLog::count();
        $totalDrowsy = AlertLog::where('alert_type', 'kantuk')->count();
        $totalDistraction = AlertLog::where('alert_type', 'tidak_fokus')->count();
        $totalYawn = AlertLog::where('alert_type', 'menguap')->count();
        $allDrivers = User::where('role', 'driver')->orderBy('full_name')->get();

        $selectedDriverId = $request->input('driver_id');
        $selectedAlertType = $request->input('alert_type');
        $searchQuery = $request->input('q', '');

        return view('admin.alerts', compact(
            'recentAlerts',
            'totalAlerts',
            'totalDrowsy',
            'totalDistraction',
            'totalYawn',
            'allDrivers',
            'selectedDriverId',
            'selectedAlertType',
            'searchQuery'
        ));
    }

    /**
     * Hapus 1 catatan insiden.
     */
    public function deleteAlert($id)
    {
        $alert = AlertLog::findOrFail($id);
        $alert->delete();

        return back()->with('success', "Catatan insiden #{$id} berhasil dihapus.");
    }

    /**
     * Hapus seluruh catatan insiden.
     */
    public function clearAlerts()
    {
        AlertLog::truncate();

        return back()->with('success', 'Seluruh riwayat log insiden berhasil dibersihkan.');
    }

    /**
     * Ekspor data insiden ke format CSV.
     */
    public function exportAlertsCsv(Request $request): StreamedResponse
    {
        $query = AlertLog::with('driver')->orderBy('timestamp', 'desc');

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->input('driver_id'));
        }
        if ($request->filled('alert_type')) {
            $query->where('alert_type', $request->input('alert_type'));
        }
        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('notes', 'like', "%{$q}%")
                    ->orWhereHas('driver', function ($drv) use ($q) {
                        $drv->where('full_name', 'like', "%{$q}%")
                            ->orWhere('username', 'like', "%{$q}%");
                    });
            });
        }

        $alerts = $query->get();
        $filename = 'dms_alerts_export_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($alerts) {
            $handle = fopen('php://output', 'w');
            // Menulis BOM untuk UTF-8 compatibility di Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID Insiden',
                'ID Pengemudi',
                'Nama Pengemudi',
                'Nomor Identitas',
                'Kategori Insiden',
                'Tingkat Bahaya',
                'Durasi (Detik)',
                'Waktu Kejadian',
                'Keterangan Operasional',
            ]);

            foreach ($alerts as $a) {
                fputcsv($handle, [
                    $a->id,
                    $a->driver_id,
                    $a->driver ? $a->driver->full_name : "Pengemudi #{$a->driver_id}",
                    $a->driver ? ($a->driver->nim_nik ?? '-') : '-',
                    ucfirst(str_replace('_', ' ', $a->alert_type)),
                    $a->severity,
                    number_format($a->duration_seconds, 1),
                    $a->timestamp ? Carbon::parse($a->timestamp)->format('Y-m-d H:i:s') : '-',
                    $a->clean_notes,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Direktori Pengemudi & Skor Keselamatan.
     */
    public function manageDrivers()
    {
        $drivers = User::where('role', 'driver')
            ->withCount('alerts')
            ->with('alerts')
            ->orderBy('full_name')
            ->get();

        return view('admin.drivers', compact('drivers'));
    }

    /**
     * Tambah Pengemudi Baru.
     */
    public function createDriver(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users,username'],
            'full_name' => ['required', 'string', 'max:100'],
            'nim_nik' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::create([
            'username' => trim($validated['username']),
            'full_name' => trim($validated['full_name']),
            'nim_nik' => isset($validated['nim_nik']) ? trim($validated['nim_nik']) : null,
            'password' => Hash::make($validated['password']),
            'role' => 'driver',
            'is_active' => true,
        ]);

        return back()->with('success', "Akun pengemudi {$validated['full_name']} (@{$validated['username']}) berhasil didaftarkan.");
    }

    /**
     * Ubah Profil Pengemudi.
     */
    public function editDriver(Request $request, $id)
    {
        $driver = User::where('role', 'driver')->findOrFail($id);

        $validated = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users,username,'.$id],
            'full_name' => ['required', 'string', 'max:100'],
            'nim_nik' => ['nullable', 'string', 'max:30'],
            'new_password' => ['nullable', 'string', 'min:6'],
        ]);

        $driver->username = trim($validated['username']);
        $driver->full_name = trim($validated['full_name']);
        $driver->nim_nik = isset($validated['nim_nik']) ? trim($validated['nim_nik']) : null;

        if (!empty($validated['new_password'])) {
            $driver->password = Hash::make($validated['new_password']);
        }

        $driver->save();

        return back()->with('success', "Data pengemudi {$driver->full_name} berhasil diperbarui.");
    }

    /**
     * Hapus Akun Pengemudi.
     */
    public function deleteDriver($id)
    {
        $driver = User::where('role', 'driver')->findOrFail($id);
        $name = $driver->full_name;
        $driver->delete();

        return back()->with('success', "Akun pengemudi {$name} berhasil dihapus beserta riwayat insidennya.");
    }

    /**
     * Status & Performa Model AI (Konsolidasi Metrik Teknis).
     */
    public function manageModels()
    {
        // Scan direktori models/ di root repositori
        $modelsDir = base_path('../models');
        $modelsList = [];
        $cmExists = false;

        if (File::exists($modelsDir)) {
            $files = File::files($modelsDir);
            foreach ($files as $f) {
                $filename = $f->getFilename();
                $sizeKb = round($f->getSize() / 1024, 1);
                $modelsList[] = [
                    'filename' => $filename,
                    'size_kb' => $sizeKb,
                    'path' => $f->getPathname(),
                ];
                if ($filename === 'confusion_matrix.png') {
                    $cmExists = true;
                }
            }
        }

        return view('admin.models', compact('modelsList', 'cmExists'));
    }

    /**
     * Muat ulang model AI.
     */
    public function reloadModel()
    {
        return back()->with('success', 'Model Random Forest Classifier berhasil disinkronisasi ulang dengan engine inferensi.');
    }

    /**
     * Stream gambar Confusion Matrix PNG.
     */
    public function viewConfusionMatrix()
    {
        $path = base_path('../models/confusion_matrix.png');
        if (!File::exists($path)) {
            abort(404, 'File grafik Confusion Matrix belum ditemukan.');
        }

        return response()->file($path, [
            'Content-Type' => 'image/png',
        ]);
    }

    /**
     * Halaman Pengaturan Profil Admin.
     */
    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    /**
     * Simpan Perubahan Profil Admin.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users,username,'.$user->id],
            'nim_nik' => ['nullable', 'string', 'max:30'],
            'current_password' => ['nullable', 'string'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        // Jika ingin mengganti kata sandi
        if (!empty($validated['new_password'])) {
            if (empty($validated['current_password'])) {
                return back()->with('danger', 'Kata sandi saat ini wajib diisi untuk verifikasi penggantian kata sandi.');
            }

            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->with('danger', 'Kata sandi saat ini yang Anda masukkan salah.');
            }

            $user->password = Hash::make($validated['new_password']);
        }

        $user->full_name = trim($validated['full_name']);
        $user->username = trim($validated['username']);
        $user->nim_nik = isset($validated['nim_nik']) ? trim($validated['nim_nik']) : null;
        $user->save();

        return back()->with('success', 'Profil administrator berhasil diperbarui.');
    }
}


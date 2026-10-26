<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MonitoringSession;
use App\Models\AlertLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'full_name' => 'Administrator DMS',
                'email' => 'admin@drivesafe.local',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'nim_nik' => 'ADM-001',
                'is_active' => true,
            ]
        );

        // 2. Akun Driver Utama
        $driverApri = User::firstOrCreate(
            ['username' => 'apri'],
            [
                'full_name' => 'Apri Catur Pramudiansyah',
                'email' => 'apri@drivesafe.local',
                'password' => Hash::make('driver123'),
                'role' => 'driver',
                'nim_nik' => '3312501067',
                'is_active' => true,
            ]
        );

        // 3. Akun Driver Rekan
        $driverNayla = User::firstOrCreate(
            ['username' => 'nayla'],
            [
                'full_name' => 'Nayla Azkyah Azra',
                'email' => 'nayla@drivesafe.local',
                'password' => Hash::make('driver123'),
                'role' => 'driver',
                'nim_nik' => '3312501061',
                'is_active' => true,
            ]
        );

        // 4. Sample Sessions
        $session = MonitoringSession::create([
            'driver_id' => $driverApri->id,
            'start_time' => Carbon::now()->subHours(2),
            'end_time' => Carbon::now()->subMinutes(10),
            'total_drowsy' => 1,
            'total_distraction' => 2,
            'total_yawn' => 1,
            'is_active' => false,
        ]);

        // 5. Sample Alert Logs
        AlertLog::create([
            'session_id' => $session->id,
            'driver_id' => $driverApri->id,
            'alert_type' => 'kantuk',
            'severity' => 'BAHAYA',
            'duration_seconds' => 1.8,
            'snapshot_path' => null,
            'timestamp' => Carbon::now()->subMinutes(45),
            'notes' => 'Mata terpejam rapat (Nilai EAR: 0.18)',
        ]);

        AlertLog::create([
            'session_id' => $session->id,
            'driver_id' => $driverApri->id,
            'alert_type' => 'tidak_fokus',
            'severity' => 'PERINGATAN',
            'duration_seconds' => 1.4,
            'snapshot_path' => null,
            'timestamp' => Carbon::now()->subMinutes(30),
            'notes' => 'Menoleh ke arah kiri (Sudut Yaw: -35.2°)',
        ]);

        AlertLog::create([
            'session_id' => $session->id,
            'driver_id' => $driverNayla->id,
            'alert_type' => 'menguap',
            'severity' => 'PERINGATAN',
            'duration_seconds' => 2.1,
            'snapshot_path' => null,
            'timestamp' => Carbon::now()->subMinutes(15),
            'notes' => 'Bukaan mulut lebar / menguap (Nilai MAR: 0.72)',
        ]);
    }
}

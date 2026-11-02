<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DmsWebTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test halaman login dapat diakses.
     */
    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke DriveSafe');
    }

    /**
     * Test admin dapat login dan mengakses dashboard.
     */
    public function test_admin_can_login_and_access_dashboard(): void
    {
        $admin = User::where('username', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Pemantauan');
        $response->assertSee('Skor Keselamatan');
    }

    /**
     * Test admin dapat mengakses halaman ringkasan insiden.
     */
    public function test_admin_can_access_alerts_page(): void
    {
        $admin = User::where('username', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/alerts');
        $response->assertStatus(200);
        $response->assertSee('Ringkasan Insiden Pengemudi');
    }

    /**
     * Test admin dapat mengakses direktori kelola pengemudi.
     */
    public function test_admin_can_access_manage_drivers_page(): void
    {
        $admin = User::where('username', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/drivers');
        $response->assertStatus(200);
        $response->assertSee('Direktori Pengemudi');
    }

    /**
     * Test admin dapat mengakses halaman model AI.
     */
    public function test_admin_can_access_models_page(): void
    {
        $admin = User::where('username', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/models');
        $response->assertStatus(200);
        $response->assertSee('Status & Performa Model AI');
    }

    /**
     * Test admin dapat mengakses pengaturan profil.
     */
    public function test_admin_can_access_profile_page(): void
    {
        $admin = User::where('username', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/profile');
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Profil');
    }

    /**
     * Test driver dapat mengakses dashboard driver.
     */
    public function test_driver_can_access_driver_dashboard(): void
    {
        $driver = User::where('role', 'driver')->first();

        $response = $this->actingAs($driver)->get('/driver/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Riwayat Peringatan');
    }

    /**
     * Test driver tidak dapat mengakses admin dashboard (middleware protection).
     */
    public function test_driver_cannot_access_admin_dashboard(): void
    {
        $driver = User::where('role', 'driver')->first();

        $response = $this->actingAs($driver)->get('/admin/dashboard');
        $response->assertRedirect('/driver/monitor');
    }

    /**
     * Test autentikasi login pengemudi langsung mengarahkan ke halaman monitoring kamera.
     */
    public function test_driver_login_redirects_to_monitor_page(): void
    {
        $response = $this->post('/login', [
            'username' => 'apri',
            'password' => 'driver123',
            'role' => 'driver',
        ]);

        $response->assertRedirect('/driver/monitor');
        $this->assertAuthenticated();
    }

    /**
     * Test login gagal dengan password salah mengembalikan flash error dan input lama.
     */
    public function test_login_fails_with_invalid_credentials_and_shows_alert(): void
    {
        $response = $this->post('/login', [
            'username' => 'apri',
            'password' => 'passwordsalah',
            'role' => 'driver',
        ]);

        $response->assertSessionHas('danger');
        $response->assertSessionHas('error');
        $response->assertSessionHasInput('username', 'apri');
        $this->assertGuest();
    }

    /**
     * Test alert notifikasi gagal login benar-benar ter-render pada HTML halaman login.
     */
    public function test_login_alert_renders_in_html(): void
    {
        $response = $this->withSession([
            'danger' => 'Username atau kata sandi yang Anda masukkan salah. Silakan periksa kembali.'
        ])->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Autentikasi Gagal');
        $response->assertSee('Username atau kata sandi yang Anda masukkan salah.');
    }

    /**
     * Test driver dapat mengakses halaman monitoring kabin.
     */
    public function test_driver_can_access_monitor_page(): void
    {
        $driver = User::where('role', 'driver')->first();

        $response = $this->actingAs($driver)->get('/driver/monitor');
        $response->assertStatus(200);
        $response->assertSee('Pemantauan Kamera Real-Time');
    }

    /**
     * Test driver dapat mencatat log insiden peringatan.
     */
    public function test_driver_can_log_alert_incident(): void
    {
        $driver = User::where('role', 'driver')->first();

        $response = $this->actingAs($driver)->postJson('/driver/alerts/log', [
            'alert_type' => 'kantuk',
            'severity' => 'BAHAYA',
            'duration_seconds' => 2.5,
            'notes' => 'Mata terpejam rapat (Nilai EAR: 0.18)'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('alert_logs', [
            'driver_id' => $driver->id,
            'alert_type' => 'kantuk',
            'severity' => 'BAHAYA'
        ]);
    }

    /**
     * Test layout admin merender modal konfirmasi ("alert dahulu") tanpa simulator widget.
     */
    public function test_admin_layout_renders_confirmation_modal(): void
    {
        $admin = User::where('username', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('adminConfirmModal');
        $response->assertSee('Konfirmasi Perubahan');
        $response->assertDontSee('adminPrototypeSimulator');
    }

    /**
     * Test layout admin merender alert pesan berhasil dan pesan gagal dengan benar.
     */
    public function test_admin_layout_renders_success_and_error_flash_alerts(): void
    {
        $admin = User::where('username', 'admin')->first();

        // 1. Uji pesan sukses
        $successResponse = $this->actingAs($admin)
            ->withSession(['success' => 'Data pengemudi berhasil diperbarui.'])
            ->get('/admin/drivers');

        $successResponse->assertStatus(200);
        $successResponse->assertSee('Perubahan Berhasil');
        $successResponse->assertSee('Data pengemudi berhasil diperbarui.');

        // 2. Uji pesan gagal
        $dangerResponse = $this->actingAs($admin)
            ->withSession(['danger' => 'Gagal memperbarui profil administrator.'])
            ->get('/admin/profile');

        $dangerResponse->assertStatus(200);
        $dangerResponse->assertSee('Perubahan Gagal');
        $dangerResponse->assertSee('Gagal memperbarui profil administrator.');
    }
}


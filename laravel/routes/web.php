<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DriverController;

/*
|--------------------------------------------------------------------------
| Web Routes - DriveSafe DMS
|--------------------------------------------------------------------------
|
| Routing halaman web, autentikasi, dashboard operasional armada,
| dan antarmuka pemantauan pengemudi.
|
*/

// Root redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// 1. AUTENTIKASI (GUEST)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ==========================================
// 2. MODUL ADMINISTRATOR (PROTECTED: AUTH + ADMIN)
// ==========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Executive Telematics Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Ringkasan & Log Riwayat Insiden
    Route::get('/alerts', [AdminController::class, 'alerts'])->name('alerts');
    Route::post('/alerts/delete/{id}', [AdminController::class, 'deleteAlert'])->name('delete_alert');
    Route::post('/alerts/clear', [AdminController::class, 'clearAlerts'])->name('clear_alerts');
    Route::get('/alerts/export/csv', [AdminController::class, 'exportAlertsCsv'])->name('export_alerts_csv');

    // Direktori & Manajemen Pengemudi
    Route::get('/drivers', [AdminController::class, 'manageDrivers'])->name('manage_drivers');
    Route::post('/drivers/create', [AdminController::class, 'createDriver'])->name('create_driver');
    Route::post('/drivers/edit/{id}', [AdminController::class, 'editDriver'])->name('edit_driver');
    Route::post('/drivers/delete/{id}', [AdminController::class, 'deleteDriver'])->name('delete_driver');

    // Status Model AI & Evaluasi Confusion Matrix
    Route::get('/models', [AdminController::class, 'manageModels'])->name('manage_models');
    Route::post('/models/reload', [AdminController::class, 'reloadModel'])->name('reload_model');
    Route::get('/models/confusion-matrix-file', [AdminController::class, 'viewConfusionMatrix'])->name('view_confusion_matrix');

    // Pengaturan Profil Admin
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('update_profile');
});

// ==========================================
// 3. MODUL PENGEMUDI (PROTECTED: AUTH)
// ==========================================
Route::middleware('auth')->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [DriverController::class, 'dashboard'])->name('dashboard');
    Route::get('/monitor', [DriverController::class, 'monitor'])->name('monitor');
});

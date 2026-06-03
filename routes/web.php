<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Peserta\DashboardController as PesertaDashboard;
use App\Http\Controllers\Peserta\AbsensiController;
use App\Http\Controllers\Peserta\IzinController as PesertaIzinController;
use App\Http\Controllers\HR\DashboardController as HRDashboard;
use App\Http\Controllers\HR\RegistrasiController;
use App\Http\Controllers\HR\ApprovalIzinController;
use App\Http\Controllers\HR\LaporanController;
use Illuminate\Support\Facades\Route;

// Public Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Debug: show centralized time (no auth)
Route::get('/_time-debug', function() {
    return \App\Services\TimeService::now()->format('Y-m-d H:i');
});

// Debug: comprehensive testing mode verification
Route::get('/_testing-verify', function() {
    $timeService = \App\Services\TimeService::now();
    $today = \App\Services\TimeService::today();
    
    return response()->json([
        'testing_mode' => env('TESTING_MODE'),
        'testing_date_config' => env('TESTING_DATE'),
        'timeservice_now' => $timeService->format('Y-m-d H:i:s'),
        'timeservice_today' => $today->format('Y-m-d'),
        'carbon_now' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
        'php_date' => date('Y-m-d H:i:s'),
        'is_testing_active' => $timeService->format('Y-m-d') !== date('Y-m-d'),
    ]);
});

// Debug: waktu server dan session id
Route::get('/__debug-waktu', function(\Illuminate\Http\Request $request) {
    return response()->json([
        'server_time' => now()->toDateTimeString(),
        'php_time' => date('Y-m-d H:i:s'),
        'session_id' => session()->getId(),
        'session_cookie' => $request->cookie(config('session.cookie')),
        'xsrf_cookie' => $request->cookie('XSRF-TOKEN'),
        'csrf_token' => csrf_token(),
    ]);
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware(['auth', 'user.active'])->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// ========================================================================================
// PESERTA MAGANG ROUTES
// ========================================================================================
Route::middleware(['auth', 'user.active', 'periode.magang', 'role:peserta_magang'])
    ->prefix('peserta')
    ->name('peserta.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [PesertaDashboard::class, 'index'])->name('dashboard');
        
        // Absensi
        Route::prefix('absensi')->name('absensi.')->group(function () {
            Route::get('/', [AbsensiController::class, 'index'])->name('index');
            Route::get('/masuk', [AbsensiController::class, 'showMasuk'])->name('masuk');
            Route::post('/masuk', [AbsensiController::class, 'masuk']);
            Route::get('/pulang', [AbsensiController::class, 'showPulang'])->name('pulang');
            Route::get('/time', [AbsensiController::class, 'time'])->name('time');
            Route::post('/pulang', [AbsensiController::class, 'pulang']);
            Route::get('/riwayat', [AbsensiController::class, 'riwayat'])->name('riwayat');
        });
        
        // Izin
        Route::resource('izin', PesertaIzinController::class)->except(['edit', 'update']);
    });

// ========================================================================================
// Semua route ketua bidang dihapus

// ========================================================================================
// HR / SUPER ADMIN ROUTES
// ========================================================================================
Route::middleware(['auth', 'user.active', 'role:hr'])
    ->prefix('hr')
    ->name('hr.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [HRDashboard::class, 'index'])->name('dashboard');

        // Route manajemen ketua bidang dihapus

        // Tambah Akun Peserta
        Route::get('/peserta/create', [RegistrasiController::class, 'create'])->name('peserta.create');
        Route::post('/peserta', [RegistrasiController::class, 'store'])->name('peserta.store');

        // Approval Izin (Layer 2)
        Route::prefix('izin')->name('izin.')->group(function () {
            Route::get('/', [ApprovalIzinController::class, 'index'])->name('index');
            Route::get('/export-csv', [ApprovalIzinController::class, 'exportCsv'])->name('export-csv');
            Route::get('/export-pdf', [ApprovalIzinController::class, 'exportPdf'])->name('export-pdf');
            Route::get('/{izin}', [ApprovalIzinController::class, 'show'])->name('show');
            Route::post('/{izin}/approve', [ApprovalIzinController::class, 'approve'])->name('approve');
            Route::post('/{izin}/reject', [ApprovalIzinController::class, 'reject'])->name('reject');
        });

        // Laporan
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/absensi', [LaporanController::class, 'absensi'])->name('absensi');
            Route::post('/absensi/{absensi}/update-status', [LaporanController::class, 'updateAbsensiStatus'])->name('absensi.update-status');
            Route::get('/absensi/export', [LaporanController::class, 'exportAbsensi'])->name('export-absensi');
            Route::get('/absensi/export-csv', [LaporanController::class, 'exportAbsensiCSV'])->name('export-absensi-csv');
            Route::get('/peserta', [LaporanController::class, 'peserta'])->name('peserta');
            Route::get('/peserta/export', [LaporanController::class, 'exportPeserta'])->name('export.peserta');
            Route::get('/peserta/export-csv', [LaporanController::class, 'exportPesertaCSV'])->name('export.peserta-csv');
            Route::post('/peserta/{id}/update-status', [LaporanController::class, 'updateStatusPeserta'])->name('peserta.update-status');
            Route::post('/peserta/{id}/reset-password', [LaporanController::class, 'resetPassword'])->name('peserta.reset-password');
            Route::get('/peserta/{id}', [LaporanController::class, 'detailPeserta'])->name('detail-peserta');
            Route::get('/peserta/{id}/export', [LaporanController::class, 'exportDetailPeserta'])->name('export.detail-peserta');
            Route::get('/peserta/{id}/export-csv', [LaporanController::class, 'exportDetailPesertaCSV'])->name('export.detail-peserta-csv');
            Route::post('/export-absensi', [LaporanController::class, 'exportAbsensi'])->name('export.absensi');
        });
    });

// Debug: session and CSRF info (DEV ONLY)
if (env('APP_ENV') === 'local' && env('APP_DEBUG') === true) {
    Route::get('/__debug-session', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'session_id' => session()->getId(),
            'session_cookie_name' => config('session.cookie'),
            'session_cookie_value' => $request->cookie(config('session.cookie')),
            'xsrf_cookie_value' => $request->cookie('XSRF-TOKEN'),
            'csrf_token' => csrf_token(),
            'app_url' => env('APP_URL'),
            'session_config' => [
                'domain' => config('session.domain'),
                'lifetime' => config('session.lifetime'),
                'same_site' => config('session.same_site'),
                'secure' => config('session.secure'),
            ],
        ]);
    });
}

<?php

// Script untuk mengupdate format catatan_sistem izin lama ke format baru
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Absensi;
use App\Models\Izin;

echo "=======================================================\n";
echo "UPDATE CATATAN SISTEM IZIN KE FORMAT BARU\n";
echo "=======================================================\n\n";

// Ambil semua absensi dengan status IZIN
$absensiList = Absensi::where(function($query) {
        $query->where('status_harian', 'IZIN')
              ->orWhere('status', 'IZIN');
    })
    ->get();

echo "Ditemukan " . $absensiList->count() . " record absensi izin\n\n";

$updated = 0;

foreach ($absensiList as $absensi) {
    $needUpdate = false;
    $newCatatan = '';
    $oldCatatan = $absensi->catatan_sistem;
    
    // Deteksi format lama atau yang perlu diupdate - ubah ke format simple
    if (str_contains($oldCatatan, 'Izin: Tidak masuk') || 
        str_contains($oldCatatan, 'Izin: tidak masuk') ||
        str_contains($oldCatatan, 'Tidak masuk') ||
        str_contains($oldCatatan, 'tidak masuk') ||
        (str_contains($oldCatatan, 'disetujui') && !str_contains($oldCatatan, 'pulang'))) {
        $needUpdate = true;
        $newCatatan = 'Izin tidak masuk';
    }
    elseif (str_contains($oldCatatan, 'Izin: Pulang cepat') || 
            str_contains($oldCatatan, 'Izin: pulang cepat') ||
            str_contains($oldCatatan, 'Pulang cepat') ||
            str_contains($oldCatatan, 'pulang cepat')) {
        $needUpdate = true;
        $newCatatan = 'Izin pulang cepat';
    }
    // Format yang catatan_sistem masih kosong atau "-" tapi status IZIN
    elseif ((!$oldCatatan || $oldCatatan == '-') && $absensi->status_harian == 'IZIN') {
        $needUpdate = true;
        $newCatatan = 'Izin tidak masuk';
    }

    if ($needUpdate && $newCatatan) {
        $absensi->update([
            'catatan_sistem' => $newCatatan
        ]);
        $updated++;
        echo "✓ Updated ID {$absensi->id} ({$absensi->tanggal->format('Y-m-d')})\n";
        echo "  Dari: {$oldCatatan}\n";
        echo "  Jadi: {$newCatatan}\n\n";
    }
}

echo "=======================================================\n";
echo "PROSES SELESAI!\n";
echo "Total diupdate: {$updated} records\n";
echo "=======================================================\n";

<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Migrasi Status IZIN Lama ke Status Baru ===\n\n";

// Cari semua record dengan status IZIN
$records = DB::table('absensi')
    ->where(function($query) {
        $query->where('status', 'IZIN')
              ->orWhere('status_harian', 'IZIN');
    })
    ->get();

echo "Total record ditemukan: " . $records->count() . "\n\n";

$updated_tidak_masuk = 0;
$updated_pulang_cepat = 0;
$skipped = 0;

foreach ($records as $record) {
    $catatan = strtolower($record->catatan_sistem ?? '');
    
    if (str_contains($catatan, 'tidak masuk')) {
        // Update ke IZIN_TIDAK_MASUK
        DB::table('absensi')
            ->where('id', $record->id)
            ->update([
                'status' => 'IZIN_TIDAK_MASUK',
                'status_harian' => 'IZIN_TIDAK_MASUK',
                'updated_at' => now()
            ]);
        
        $updated_tidak_masuk++;
        echo "✓ ID {$record->id} - Updated to IZIN_TIDAK_MASUK\n";
        
    } elseif (str_contains($catatan, 'pulang cepat')) {
        // Update ke IZIN_PULANG_CEPAT
        DB::table('absensi')
            ->where('id', $record->id)
            ->update([
                'status' => 'IZIN_PULANG_CEPAT',
                'status_harian' => 'IZIN_PULANG_CEPAT',
                'updated_at' => now()
            ]);
        
        $updated_pulang_cepat++;
        echo "✓ ID {$record->id} - Updated to IZIN_PULANG_CEPAT\n";
        
    } else {
        // Tidak bisa ditentukan, skip
        $skipped++;
        echo "⊗ ID {$record->id} - Skipped (tidak ada catatan jelas) - Catatan: {$record->catatan_sistem}\n";
    }
}

echo "\n=== Ringkasan ===\n";
echo "Total record: " . $records->count() . "\n";
echo "Updated ke IZIN_TIDAK_MASUK: {$updated_tidak_masuk}\n";
echo "Updated ke IZIN_PULANG_CEPAT: {$updated_pulang_cepat}\n";
echo "Skipped: {$skipped}\n";
echo "\n✅ Migrasi selesai!\n";

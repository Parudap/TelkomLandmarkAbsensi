<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Update ENUM Status Absensi ===\n\n";

try {
    // Step 1: Alter ENUM columns FIRST to include new values (keep IZIN temporarily)
    echo "Step 1: Modifying ENUM columns to include new values...\n";
    
    DB::statement("
        ALTER TABLE absensi 
        MODIFY COLUMN status ENUM('HADIR_TEPAT_WAKTU','HADIR_TELAT','IZIN','IZIN_TIDAK_MASUK','IZIN_PULANG_CEPAT','ALPHA','LIBUR') 
        NOT NULL DEFAULT 'ALPHA'
    ");
    echo "✓ Modified 'status' column\n";
    
    DB::statement("
        ALTER TABLE absensi 
        MODIFY COLUMN status_harian ENUM('BELUM_FINAL','HADIR_TEPAT_WAKTU','HADIR_TELAT','IZIN','IZIN_TIDAK_MASUK','IZIN_PULANG_CEPAT','ALPHA','LIBUR') 
        NOT NULL DEFAULT 'BELUM_FINAL'
    ");
    echo "✓ Modified 'status_harian' column\n\n";
    
    // Step 2: Update existing records
    echo "Step 2: Updating existing IZIN records...\n";
    
    $count1 = DB::table('absensi')
        ->where(function($query) {
            $query->where('status', 'IZIN')
                  ->orWhere('status_harian', 'IZIN');
        })
        ->where('catatan_sistem', 'like', '%tidak masuk%')
        ->update([
            'status_harian' => 'IZIN_TIDAK_MASUK',
            'status' => 'IZIN_TIDAK_MASUK'
        ]);
    echo "✓ Updated {$count1} records to IZIN_TIDAK_MASUK\n";
    
    $count2 = DB::table('absensi')
        ->where(function($query) {
            $query->where('status', 'IZIN')
                  ->orWhere('status_harian', 'IZIN');
        })
        ->where('catatan_sistem', 'like', '%pulang cepat%')
        ->update([
            'status_harian' => 'IZIN_PULANG_CEPAT',
            'status' => 'IZIN_PULANG_CEPAT'
        ]);
    echo "✓ Updated {$count2} records to IZIN_PULANG_CEPAT\n\n";
    
    echo "=== Migrasi Berhasil! ===\n";
    
    // Show summary
    $summary = DB::table('absensi')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();
    
    echo "\nRingkasan Status:\n";
    foreach ($summary as $item) {
        echo "  {$item->status}: {$item->count}\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}

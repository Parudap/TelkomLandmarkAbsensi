<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Absensi;

echo "Data Absensi dengan status IZIN:\n\n";
echo str_repeat("=", 120) . "\n";

$records = Absensi::where(function($query) {
        $query->where('status_harian', 'IZIN')
              ->orWhere('status', 'IZIN');
    })
    ->with(['user', 'izin'])
    ->orderBy('tanggal', 'desc')
    ->get();

if ($records->count() > 0) {
    foreach($records as $r) {
        echo sprintf(
            "%-12s | %-20s | %-15s | izin_id: %-5s | %s\n",
            $r->tanggal->format('Y-m-d'),
            $r->user->name ?? 'Unknown',
            $r->status_harian,
            $r->izin_id ?? 'NULL',
            $r->catatan_sistem ?? '-'
        );
    }
} else {
    echo "Tidak ada data IZIN\n";
}

echo str_repeat("=", 120) . "\n";
echo "Total: " . $records->count() . " records\n";

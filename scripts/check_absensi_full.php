<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Absensi;

echo "Data Absensi 24-27 Februari 2026 (FULL):\n\n";
echo str_repeat("=", 100) . "\n";

$records = Absensi::with('user')
    ->whereDate('tanggal', '>=', '2026-02-24')
    ->whereDate('tanggal', '<=', '2026-02-27')
    ->orderBy('tanggal')
    ->get();

foreach($records as $r) {
    echo sprintf(
        "%-12s | %-20s | status: %-10s | status_harian: %-10s | %s\n",
        $r->tanggal->format('Y-m-d'),
        $r->user->name ?? 'Unknown',
        $r->status ?? 'NULL',
        $r->status_harian ?? 'NULL',
        $r->catatan_sistem ?? '-'
    );
}

echo str_repeat("=", 100) . "\n";
echo "Total: " . $records->count() . " records\n";

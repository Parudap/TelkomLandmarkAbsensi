<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking absensi records with 'Izin tidak masuk' in catatan_sistem:\n\n";

$records = DB::table('absensi')
    ->where('catatan_sistem', 'like', '%zin%masuk%')
    ->orWhere('status_harian', 'like', '%IZIN%')
    ->orWhere('status', 'like', '%IZIN%')
    ->select('id', 'user_id', 'tanggal', 'status', 'status_harian', 'catatan_sistem')
    ->orderBy('tanggal', 'desc')
    ->limit(10)
    ->get();

foreach ($records as $record) {
    echo "ID: {$record->id}\n";
    echo "User ID: {$record->user_id}\n";
    echo "Tanggal: {$record->tanggal}\n";
    echo "Status: " . ($record->status ?? 'NULL') . "\n";
    echo "Status Harian: " . ($record->status_harian ?? 'NULL') . "\n";
    echo "Catatan Sistem: " . ($record->catatan_sistem ?? 'NULL') . "\n";
    echo str_repeat('-', 50) . "\n";
}

echo "\nTotal records: " . $records->count() . "\n";

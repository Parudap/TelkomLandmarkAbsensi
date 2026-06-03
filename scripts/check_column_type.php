<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$columns = DB::select("SHOW COLUMNS FROM absensi WHERE Field IN ('status', 'status_harian')");

foreach ($columns as $column) {
    echo "Column: {$column->Field}\n";
    echo "Type: {$column->Type}\n";
    echo "Null: {$column->Null}\n";
    echo "Default: {$column->Default}\n";
    echo str_repeat('-', 50) . "\n";
}

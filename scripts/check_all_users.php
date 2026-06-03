<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CEK SEMUA USER DI DATABASE ===\n\n";

$users = DB::table('users')->select('id', 'name', 'email', 'role')->get();

echo "Total user: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Nama: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
    echo str_repeat('-', 50) . "\n";
}

// Count by role
$byRole = DB::table('users')->select('role', DB::raw('COUNT(*) as count'))
    ->groupBy('role')
    ->get();

echo "\nJumlah per role:\n";
foreach ($byRole as $role) {
    echo "  {$role->role}: {$role->count}\n";
}

<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "========================================\n";
echo "HAPUS HISTORY ABSENSI - nanang@gmail.com\n";
echo "========================================\n\n";

$email = 'nanang@gmail.com';

try {
    DB::beginTransaction();
    
    // Find user
    $user = DB::table('users')
        ->where('email', $email)
        ->first();
    
    if (!$user) {
        echo "❌ User dengan email {$email} tidak ditemukan.\n";
        DB::rollBack();
        exit;
    }
    
    echo "👤 User ditemukan:\n";
    echo "   - ID: {$user->id}\n";
    echo "   - Nama: {$user->name}\n";
    echo "   - Email: {$user->email}\n";
    echo "   - Role: {$user->role}\n\n";
    
    // Count existing records
    $countAbsensi = DB::table('absensi')->where('user_id', $user->id)->count();
    $countIzin = DB::table('izin')->where('user_id', $user->id)->count();
    
    echo "📊 Data yang akan dihapus:\n";
    echo "   - Absensi: {$countAbsensi} records\n";
    echo "   - Izin: {$countIzin} records\n\n";
    
    if ($countAbsensi === 0 && $countIzin === 0) {
        echo "✓ Tidak ada history absensi yang perlu dihapus.\n";
        DB::rollBack();
        exit;
    }
    
    // Delete attendance history
    echo "🗑️  Menghapus history...\n";
    
    // 1. Delete absensi
    if ($countAbsensi > 0) {
        $deletedAbsensi = DB::table('absensi')
            ->where('user_id', $user->id)
            ->delete();
        echo "   ✓ Absensi dihapus: {$deletedAbsensi} records\n";
    }
    
    // 2. Delete izin
    if ($countIzin > 0) {
        $deletedIzin = DB::table('izin')
            ->where('user_id', $user->id)
            ->delete();
        echo "   ✓ Izin dihapus: {$deletedIzin} records\n";
    }
    
    DB::commit();
    
    echo "\n✅ Selesai! History absensi untuk {$user->name} ({$email}) berhasil dihapus.\n";
    echo "ℹ️  Akun user tetap ada dan tidak dihapus.\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

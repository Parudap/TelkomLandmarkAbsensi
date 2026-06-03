<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== HAPUS SELURUH AKUN PESERTA DAN DATANYA ===\n\n";
echo "PERINGATAN: Ini akan menghapus SEMUA peserta dan data terkait!\n";
echo "Tekan ENTER untuk melanjutkan atau CTRL+C untuk membatalkan...\n";
fgets(STDIN);

try {
    DB::beginTransaction();
    
    // Get all peserta users (role: peserta_magang)
    $pesertas = DB::table('users')->where('role', 'peserta_magang')->get();
    $totalPeserta = $pesertas->count();
    
    echo "\nTotal peserta yang akan dihapus: {$totalPeserta}\n\n";
    
    if ($totalPeserta == 0) {
        echo "Tidak ada peserta untuk dihapus.\n";
        DB::rollBack();
        exit;
    }
    
    $pesertaIds = $pesertas->pluck('id')->toArray();
    
    // 1. Hapus data absensi
    echo "1. Menghapus data absensi...\n";
    $deletedAbsensi = DB::table('absensi')->whereIn('user_id', $pesertaIds)->delete();
    echo "   ✓ {$deletedAbsensi} record absensi dihapus\n\n";
    
    // 2. Hapus data izin (simpan ID sebelum dihapus untuk approval logs)
    echo "2. Menghapus data izin...\n";
    $izinIds = DB::table('izin')->whereIn('user_id', $pesertaIds)->pluck('id')->toArray();
    $deletedIzin = DB::table('izin')->whereIn('user_id', $pesertaIds)->delete();
    echo "   ✓ {$deletedIzin} record izin dihapus\n\n";
    
    // 3. Hapus approval logs terkait izin yang dihapus
    echo "3. Menghapus approval logs...\n";
    $deletedLogs = 0;
    if (!empty($izinIds)) {
        $deletedLogs = DB::table('approval_logs')
            ->whereIn('approvable_id', $izinIds)
            ->where('approvable_type', 'App\\Models\\Izin')
            ->delete();
    }
    echo "   ✓ {$deletedLogs} record approval logs dihapus\n\n";
    
    // 4. Hapus akun peserta
    echo "4. Menghapus akun peserta...\n";
    $deletedUsers = DB::table('users')->where('role', 'peserta_magang')->delete();
    echo "   ✓ {$deletedUsers} akun peserta dihapus\n\n";
    
    DB::commit();
    
    echo "=== BERHASIL! ===\n";
    echo "Ringkasan penghapusan:\n";
    echo "- Akun Peserta: {$deletedUsers}\n";
    echo "- Absensi: {$deletedAbsensi}\n";
    echo "- Izin: {$deletedIzin}\n";
    echo "- Approval Logs: {$deletedLogs}\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Transaksi dibatalkan, tidak ada data yang dihapus.\n";
}

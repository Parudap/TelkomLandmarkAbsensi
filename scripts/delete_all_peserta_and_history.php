<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "========================================\n";
echo "HAPUS SEMUA PESERTA DAN HISTORYNYA\n";
echo "========================================\n\n";

try {
    DB::beginTransaction();
    
    // Get all peserta IDs
    $pesertaIds = DB::table('users')
        ->where('role', 'peserta_magang')
        ->pluck('id')
        ->toArray();
    
    if (empty($pesertaIds)) {
        echo "❌ Tidak ada peserta yang ditemukan.\n";
        DB::rollBack();
        exit;
    }
    
    $totalPeserta = count($pesertaIds);
    echo "🔍 Ditemukan {$totalPeserta} peserta magang.\n\n";
    
    echo "🗑️  Menghapus data terkait peserta...\n";
    
    // 1. Hapus absensi
    $deletedAbsensi = DB::table('absensis')
        ->whereIn('user_id', $pesertaIds)
        ->delete();
    echo "   ✓ Absensi dihapus: {$deletedAbsensi} records\n";
    
    // 2. Hapus izin
    $deletedIzin = DB::table('izins')
        ->whereIn('user_id', $pesertaIds)
        ->delete();
    echo "   ✓ Izin dihapus: {$deletedIzin} records\n";
    
    // 3. Hapus approval logs terkait peserta
    $deletedApprovalLogs = DB::table('approval_logs')
        ->whereIn('user_id', $pesertaIds)
        ->delete();
    echo "   ✓ Approval logs dihapus: {$deletedApprovalLogs} records\n";
    
    // 4. Hapus approval logs dimana peserta sebagai approver (jika ada)
    $deletedApproverLogs = DB::table('approval_logs')
        ->whereIn('approver_id', $pesertaIds)
        ->delete();
    echo "   ✓ Approval logs (as approver) dihapus: {$deletedApproverLogs} records\n";
    
    // 5. Hapus notifications terkait peserta
    $deletedNotifications = DB::table('notifications')
        ->where(function($query) use ($pesertaIds) {
            $query->whereIn('notifiable_id', $pesertaIds)
                  ->where('notifiable_type', 'App\\Models\\User');
        })
        ->delete();
    echo "   ✓ Notifications dihapus: {$deletedNotifications} records\n";
    
    // 6. Hapus file surat magang (if exists)
    $users = DB::table('users')
        ->where('role', 'peserta_magang')
        ->whereNotNull('surat_magang')
        ->get();
    
    $deletedFiles = 0;
    foreach ($users as $user) {
        if ($user->surat_magang) {
            $filePath = storage_path('app/public/' . $user->surat_magang);
            if (file_exists($filePath)) {
                unlink($filePath);
                $deletedFiles++;
            }
        }
    }
    echo "   ✓ File surat magang dihapus: {$deletedFiles} files\n";
    
    // 7. Hapus foto absensi dari storage
    // Folder: storage/app/public/absensi/
    $absensiPath = storage_path('app/public/absensi');
    if (is_dir($absensiPath)) {
        $files = glob($absensiPath . '/*');
        $deletedAbsensiFiles = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $deletedAbsensiFiles++;
            }
        }
        echo "   ✓ Foto absensi dihapus: {$deletedAbsensiFiles} files\n";
    }
    
    // 8. Terakhir, hapus user peserta
    $deletedUsers = DB::table('users')
        ->where('role', 'peserta_magang')
        ->delete();
    echo "   ✓ User peserta dihapus: {$deletedUsers} users\n";
    
    DB::commit();
    
    echo "\n========================================\n";
    echo "✅ SEMUA PESERTA DAN HISTORYNYA BERHASIL DIHAPUS!\n";
    echo "========================================\n";
    echo "\nRingkasan:\n";
    echo "- Peserta dihapus: {$deletedUsers}\n";
    echo "- Absensi dihapus: {$deletedAbsensi}\n";
    echo "- Izin dihapus: {$deletedIzin}\n";
    echo "- Approval logs dihapus: " . ($deletedApprovalLogs + $deletedApproverLogs) . "\n";
    echo "- Notifications dihapus: {$deletedNotifications}\n";
    echo "- File dihapus: " . ($deletedFiles + ($deletedAbsensiFiles ?? 0)) . "\n";
    echo "\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Izin;
use Illuminate\Support\Facades\DB;

class DeleteAllPesertaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🗑️  Menghapus semua data peserta magang...\n\n";

        // Get all peserta magang IDs
        $pesertaIds = User::where('role', 'peserta_magang')->pluck('id');
        
        if ($pesertaIds->isEmpty()) {
            echo "ℹ️  Tidak ada peserta magang yang perlu dihapus.\n";
            return;
        }

        $totalPeserta = $pesertaIds->count();

        DB::beginTransaction();
        try {
            // 1. Hapus data absensi
            $deletedAbsensi = Absensi::whereIn('user_id', $pesertaIds)->delete();
            echo "✓ Berhasil menghapus {$deletedAbsensi} record absensi\n";

            // 2. Hapus data izin
            $deletedIzin = Izin::whereIn('user_id', $pesertaIds)->delete();
            echo "✓ Berhasil menghapus {$deletedIzin} record izin\n";

            // 3. Hapus peserta magang
            $deletedPeserta = User::where('role', 'peserta_magang')->delete();
            echo "✓ Berhasil menghapus {$deletedPeserta} peserta magang\n";

            DB::commit();
            
            echo "\n✅ Selesai! Semua data peserta magang telah dihapus.\n";
            echo "📊 Summary:\n";
            echo "   - Peserta dihapus: {$deletedPeserta}\n";
            echo "   - Absensi dihapus: {$deletedAbsensi}\n";
            echo "   - Izin dihapus: {$deletedIzin}\n";
            
        } catch (\Exception $e) {
            DB::rollBack();
            echo "\n❌ Error: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}

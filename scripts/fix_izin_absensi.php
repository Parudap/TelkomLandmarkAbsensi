<?php

// Script untuk memperbaiki data absensi yang masih ALPHA padahal izin sudah di-approve
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Izin;
use App\Models\Absensi;
use Carbon\Carbon;

echo "Memulai proses perbaikan data absensi...\n\n";

// Ambil semua izin yang sudah di-approve
$approvedIzin = Izin::whereIn('status_approval', ['approved_hr', 'auto_approved'])->get();

echo "Ditemukan " . $approvedIzin->count() . " izin yang sudah di-approve\n\n";

$updated = 0;
$created = 0;

foreach ($approvedIzin as $izin) {
    $dates = [];
    
    // Kumpulkan tanggal yang perlu di-update
    if ($izin->tanggal) {
        $dates[] = $izin->tanggal;
    } elseif ($izin->tanggal_mulai && $izin->tanggal_selesai) {
        $start = $izin->tanggal_mulai->copy();
        $end = $izin->tanggal_selesai->copy();
        while ($start->lte($end)) {
            $dates[] = $start->copy();
            $start->addDay();
        }
    }
    
    foreach ($dates as $date) {
        // Skip weekend dan pulang_cepat
        if ($date->isWeekend() || $izin->jenis_izin === 'pulang_cepat') {
            continue;
        }
        
        $absensi = Absensi::where('user_id', $izin->user_id)
            ->whereDate('tanggal', $date)
            ->first();
        
        if ($absensi) {
            // Update jika status bukan HADIR dan belum IZIN
            if (!in_array($absensi->status_harian, ['HADIR_TEPAT_WAKTU', 'HADIR_TELAT']) && 
                ($absensi->status !== 'IZIN' || $absensi->status_harian !== 'IZIN')) {
                $jenisIzinText = $izin->jenis_izin == 'tidak_masuk' ? 'Izin tidak masuk' : 'Izin pulang cepat';
                $oldStatus = $absensi->status . '/' . $absensi->status_harian;
                $absensi->update([
                    'status' => 'IZIN',
                    'status_harian' => 'IZIN',
                    'catatan_sistem' => $jenisIzinText
                ]);
                $updated++;
                echo "✓ Updated: " . $izin->user->name . " - " . $date->format('Y-m-d') . " (dari {$oldStatus} ke IZIN/IZIN)\n";
            }
        } else {
            // Create record baru
            $jenisIzinText = $izin->jenis_izin == 'tidak_masuk' ? 'Izin tidak masuk' : 'Izin pulang cepat';
            Absensi::create([
                'user_id' => $izin->user_id,
                'tanggal' => $date,
                'status' => 'IZIN',
                'status_harian' => 'IZIN',
                'catatan_sistem' => $jenisIzinText
            ]);
            $created++;
            echo "✓ Created: " . $izin->user->name . " - " . $date->format('Y-m-d') . " (IZIN)\n";
        }
    }
}

echo "\n================================\n";
echo "Proses selesai!\n";
echo "Updated: {$updated} records\n";
echo "Created: {$created} records\n";
echo "Total: " . ($updated + $created) . " records\n";
echo "================================\n";

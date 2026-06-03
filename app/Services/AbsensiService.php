<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\SystemSetting;
use Carbon\Carbon;
use App\Services\TimeService as TS;

class AbsensiService
{
    /**
     * Validasi lokasi GPS
     */
    public function validateLocation(float $latitude, float $longitude): bool
    {
        $officeLat = (float) SystemSetting::get('office_latitude', -6.2264854);
        $officeLong = (float) SystemSetting::get('office_longitude', 106.8201788);
        $radius = (float) SystemSetting::get('gps_radius', 50);

        $distance = $this->calculateDistance($latitude, $longitude, $officeLat, $officeLong);

        return $distance <= $radius;
    }

    /**
     * Hitung jarak antara dua koordinat (Haversine Formula)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Tentukan status kehadiran berdasarkan jam masuk
     */
    public function determineStatus(string $jamMasuk): string
    {
        $batasJamMasuk = SystemSetting::get('jam_masuk', '08:00:00');
        
        $waktuMasuk = Carbon::createFromFormat('H:i:s', $jamMasuk);
        $batasMasuk = Carbon::createFromFormat('H:i:s', $batasJamMasuk);

        return $waktuMasuk->lte($batasMasuk) ? 'HADIR_TEPAT_WAKTU' : 'HADIR_TELAT';
    }

    /**
     * Hitung durasi kerja (dalam menit)
     */
    public function calculateDuration(string $jamMasuk, string $jamPulang): int
    {
        $masuk = Carbon::createFromFormat('H:i:s', $jamMasuk);
        $pulang = Carbon::createFromFormat('H:i:s', $jamPulang);

        return $masuk->diffInMinutes($pulang);
    }

    /**
     * Cek apakah sudah absen hari ini
     */
    public function hasAttendanceToday(int $userId): bool
    {
        return Absensi::where('user_id', $userId)
            ->whereDate('tanggal', TS::today())
            ->exists();
    }

    /**
     * Get absensi hari ini
     */
    public function getTodayAttendance(int $userId): ?Absensi
    {
        return Absensi::where('user_id', $userId)
            ->whereDate('tanggal', TS::today())
            ->first();
    }
}

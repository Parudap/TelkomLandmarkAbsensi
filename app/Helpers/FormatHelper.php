<?php

namespace App\Helpers;

class FormatHelper
{
    /**
     * Format nomor telepon
     */
    public static function formatPhone(string $phone): string
    {
        // Hilangkan karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Format: 0812-3456-7890
        if (strlen($phone) >= 10) {
            return substr($phone, 0, 4) . '-' . substr($phone, 4, 4) . '-' . substr($phone, 8);
        }
        
        return $phone;
    }

    /**
     * Format status approval
     */
    public static function formatApprovalStatus(string $status): string
    {
        return match($status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'approved_hr' => 'Disetujui HR',
            'rejected_hr' => 'Ditolak HR',
            'auto_approved' => 'Disetujui Otomatis',
            default => ucfirst($status),
        };
    }

    /**
     * Format status kehadiran
     */
    public static function formatAttendanceStatus(string $status): string
    {
        return match($status) {
            'HADIR_TEPAT_WAKTU' => 'Hadir Tepat Waktu',
            'HADIR_TELAT' => 'Hadir Terlambat',
            'IZIN' => 'Izin',
            'ALPHA' => 'Tidak Hadir',
            'LIBUR' => 'Libur',
            default => $status,
        };
    }

    /**
     * Format durasi kerja
     */
    public static function formatDuration(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        return sprintf('%d jam %d menit', $hours, $mins);
    }

    /**
     * Format ukuran file
     */
    public static function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

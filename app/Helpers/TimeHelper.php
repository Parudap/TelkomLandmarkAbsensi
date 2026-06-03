<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Services\TimeService;

class TimeHelper
{
    /**
     * Cek apakah hari ini hari kerja (Senin-Jumat)
     */
    public static function isWorkingDay(?Carbon $date = null): bool
    {
        $date = $date ?? TimeService::today();
        return $date->isWeekday();
    }

    /**
     * Format waktu ke format Indonesia
     */
    public static function formatTime(string $time): string
    {
        return Carbon::createFromFormat('H:i:s', $time)->format('H:i');
    }

    /**
     * Format tanggal ke format Indonesia
     */
    public static function formatDate($date): string
    {
        return Carbon::parse($date)->locale('id')->translatedFormat('d F Y');
    }

    /**
     * Format tanggal dan waktu
     */
    public static function formatDateTime($datetime): string
    {
        return Carbon::parse($datetime)->locale('id')->translatedFormat('d F Y, H:i');
    }

    /**
     * Hitung selisih hari
     */
    public static function daysDiff($startDate, $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        return $start->diffInDays($end);
    }
}

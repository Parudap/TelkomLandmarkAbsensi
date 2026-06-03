<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class TimeService
{
    /**
     * Return true when testing mode is enabled via .env
     */
    public static function isTesting(): bool
    {
        return filter_var(env('TESTING_MODE', false), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Return the configured testing datetime string or null
     */
    public static function getTestingDate(): ?string
    {
        // runtime override via cache (key: testing_datetime) takes precedence
        if (Cache::has('testing_datetime')) {
            return Cache::get('testing_datetime');
        }

        $val = env('TESTING_DATE') ?: env('TESTING_DATETIME');
        return $val ?: null;
    }

    /**
     * Return a Carbon instance representing current time (testing or real)
     */
    public static function now(): Carbon
    {
        // If a runtime cache override is present, always prefer it
        if (Cache::has('testing_datetime')) {
            $raw = Cache::get('testing_datetime');
            $clean = self::sanitizeDateTimeString($raw);
            $dt = Carbon::parse($clean);
            return $dt->setTime($dt->hour, $dt->minute, 0, 0);
        }

        if (self::isTesting() && self::getTestingDate()) {
            $raw = self::getTestingDate();
            $clean = self::sanitizeDateTimeString($raw);
            $dt = Carbon::parse($clean);
            return $dt->setTime($dt->hour, $dt->minute, 0, 0);
        }

        $now = Carbon::now();
        return $now->setTime($now->hour, $now->minute, 0, 0);
    }

    /**
     * Return a Carbon start-of-day for the current context
     */
    public static function today(): Carbon
    {
        return self::now()->copy()->startOfDay();
    }

    /**
     * Parse a date/time string into Carbon
     */
    public static function parse(string $value): Carbon
    {


        $clean = self::sanitizeDateTimeString($value);
        $dt = Carbon::parse($clean);
        return $dt->setTime($dt->hour, $dt->minute, 0, 0);
    }

    /**
     * Sanitize a datetime string for Carbon parsing
     */
    private static function sanitizeDateTimeString($value): string
    {
        // Remove extra spaces around date/time separator
        $value = trim($value);
        $value = preg_replace('/\s+:\s*/', ' 00:00', $value); // handle '2026-02-10 :00:00' -> '2026-02-10 00:00'
        $value = preg_replace('/\s+/', ' ', $value); // collapse multiple spaces
        $value = str_replace(' :', ' ', $value); // handle '2026-02-10 :00:00' -> '2026-02-10 00:00'
        $value = str_replace(': ', ':', $value); // handle '2026-02-10: 00:00' -> '2026-02-10:00:00'
        // Remove double time specification (e.g. 2026-02-10 00:0000:00 -> 2026-02-10 00:00)
        $value = preg_replace('/(\d{2}:\d{2})(\d{2}:\d{2})/', '$1', $value);
        // If still contains more than one time, keep only the first
        if (preg_match('/^([\d-]+) (\d{2}:\d{2})(.*)$/', $value, $m)) {
            $value = $m[1] . ' ' . $m[2];
        }
        return $value;
    }
}

<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Auto-close absensi yang tidak melakukan absen pulang
        // Dijalankan setiap hari jam 00:01 (1 menit setelah ganti hari)
        $schedule->command('attendance:close-unfinished')
            ->dailyAt('00:01')
            ->timezone('Asia/Jakarta')
            ->appendOutputTo(storage_path('logs/auto-close-attendance.log'));

        // Auto-approve izin yang sudah 24 jam
        // Dijalankan setiap jam
        $schedule->command('izin:auto-approve')
            ->hourly()
            ->timezone('Asia/Jakarta')
            ->appendOutputTo(storage_path('logs/auto-approve-izin.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

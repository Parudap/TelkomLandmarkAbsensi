<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TestingTimeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage: php artisan testing:time set "2026-01-22 07:00:00"
     *        php artisan testing:time clear
     */
    protected $signature = 'testing:time {action : set|clear} {datetime?}';

    /**
     * The console command description.
     */
    protected $description = 'Set or clear runtime testing datetime (cache key testing_datetime)';

    public function handle()
    {
        $action = $this->argument('action');

        if ($action === 'set') {
            $dt = $this->argument('datetime');
            if (!$dt) {
                $this->error('Please provide a datetime string, e.g. "2026-01-22 07:00:00"');
                return 1;
            }

            try {
                $parsed = Carbon::parse($dt)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $this->error('Invalid datetime format: ' . $e->getMessage());
                return 1;
            }

            // store in cache for 7 days (adjust as needed)
            Cache::put('testing_datetime', $parsed, now()->addDays(7));
            $this->info('testing_datetime set to ' . $parsed);
            return 0;
        }

        if ($action === 'clear') {
            Cache::forget('testing_datetime');
            $this->info('testing_datetime cache key cleared');
            return 0;
        }

        $this->error('Unknown action. Use set or clear.');
        return 1;
    }
}

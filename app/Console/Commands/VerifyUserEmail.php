<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyUserEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:verify-email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually verify user email (for testing only)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User dengan email '{$email}' tidak ditemukan!");
            return 1;
        }

        if ($user->hasVerifiedEmail()) {
            $this->info("Email '{$email}' sudah diverifikasi sebelumnya.");
            return 0;
        }

        $user->markEmailAsVerified();

        $this->info("✅ Email '{$email}' berhasil diverifikasi!");
        $this->info("User sekarang bisa login (jika sudah approved oleh HR).");

        return 0;
    }
}

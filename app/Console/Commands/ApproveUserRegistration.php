<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Services\TimeService;

class ApproveUserRegistration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:approve {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually approve user registration (for testing only)';

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

        if ($user->status_approval === 'approved') {
            $this->info("User '{$email}' sudah disetujui sebelumnya.");
            return 0;
        }

        // Verify email jika belum
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            $this->info("✅ Email diverifikasi otomatis.");
        }

        // Approve user
        $user->update([
            'status_approval' => 'approved',
            'is_active' => true,
            'approved_at' => TimeService::now(),
        ]);

        $this->info("✅ User '{$email}' berhasil disetujui!");
        $this->info("User sekarang bisa login ke sistem.");
        $this->newLine();
        $this->table(
            ['Name', 'Email', 'Role', 'Status'],
            [[$user->name, $user->email, $user->role, $user->status_approval]]
        );

        return 0;
    }
}

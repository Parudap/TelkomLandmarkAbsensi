<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:delete {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete user by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ User dengan email '{$email}' tidak ditemukan!");
            return 1;
        }

        $this->newLine();
        $this->table(
            ['ID', 'Name', 'Email', 'Role', 'Status', 'Verified'],
            [[
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->status_approval,
                $user->email_verified_at ? '✅ Yes' : '❌ No'
            ]]
        );

        if (!$this->confirm("Hapus user '{$user->name}'?")) {
            $this->info('❌ Dibatalkan.');
            return 0;
        }

        // Hapus file surat magang jika ada
        if ($user->surat_magang) {
            Storage::disk('public')->delete($user->surat_magang);
            $this->info("🗑️  File surat magang dihapus.");
        }

        $user->delete();

        $this->newLine();
        $this->info("✅ User '{$email}' berhasil dihapus!");

        return 0;
    }
}

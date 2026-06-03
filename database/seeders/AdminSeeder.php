<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Cek apakah admin sudah ada
        if (!User::where('email', 'admin@telkom.com')->exists()) {
            User::create([
                'name' => 'Admin HR',
                'email' => 'admin@telkom.com',
                'password' => bcrypt('admin123'),
                'role' => 'hr',
                'status_approval' => 'approved',
                'is_active' => true,
            ]);
        }
    }
}

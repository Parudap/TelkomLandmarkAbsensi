<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Lokasi Kantor
            [
                'setting_key' => 'office_latitude',
                'setting_value' => '-6.2264854',
                'description' => 'Koordinat latitude Telkom Landmark Tower',
                'category' => 'absensi',
                'type' => 'text',
                'is_editable' => true,
            ],
            [
                'setting_key' => 'office_longitude',
                'setting_value' => '106.8201788',
                'description' => 'Koordinat longitude Telkom Landmark Tower',
                'category' => 'absensi',
                'type' => 'text',
                'is_editable' => true,
            ],
            [
                'setting_key' => 'gps_radius',
                'setting_value' => '50',
                'description' => 'Radius GPS yang diizinkan (dalam meter)',
                'category' => 'absensi',
                'type' => 'number',
                'is_editable' => true,
            ],
            
            // Jam Kerja
            [
                'setting_key' => 'jam_masuk',
                'setting_value' => '08:00:00',
                'description' => 'Jam masuk standar (toleransi 0 menit)',
                'category' => 'absensi',
                'type' => 'text',
                'is_editable' => true,
            ],
            [
                'setting_key' => 'jam_pulang',
                'setting_value' => '17:00:00',
                'description' => 'Jam pulang standar',
                'category' => 'absensi',
                'type' => 'text',
                'is_editable' => true,
            ],
            
            // Approval System
            [
                'setting_key' => 'auto_approve_hours',
                'setting_value' => '24',
                'description' => 'Jam untuk auto approve jika tidak ada respon (dalam jam)',
                'category' => 'approval',
                'type' => 'number',
                'is_editable' => true,
            ],
            
            // Informasi Aplikasi
            [
                'setting_key' => 'app_name',
                'setting_value' => 'Telkom Landmark Absensi',
                'description' => 'Nama aplikasi',
                'category' => 'general',
                'type' => 'text',
                'is_editable' => true,
            ],
            [
                'setting_key' => 'office_address',
                'setting_value' => 'Telkom Landmark Tower, Jl. Jenderal Gatot Subroto Kav. 52, Jakarta Selatan',
                'description' => 'Alamat kantor lengkap',
                'category' => 'general',
                'type' => 'text',
                'is_editable' => true,
            ],
            
            // Email Verification
            [
                'setting_key' => 'email_verification_hours',
                'setting_value' => '24',
                'description' => 'Batas waktu verifikasi email (dalam jam)',
                'category' => 'general',
                'type' => 'number',
                'is_editable' => true,
            ],
            
            // File Upload
            [
                'setting_key' => 'max_file_size',
                'setting_value' => '2048',
                'description' => 'Ukuran maksimal file upload (KB)',
                'category' => 'general',
                'type' => 'number',
                'is_editable' => true,
            ],
            [
                'setting_key' => 'allowed_file_types',
                'setting_value' => 'pdf,jpg,jpeg,png',
                'description' => 'Tipe file yang diizinkan untuk upload',
                'category' => 'general',
                'type' => 'text',
                'is_editable' => true,
            ],
            
            // Notifikasi
            [
                'setting_key' => 'enable_email_notifications',
                'setting_value' => 'true',
                'description' => 'Aktifkan notifikasi email',
                'category' => 'notification',
                'type' => 'boolean',
                'is_editable' => true,
            ],
            
            // Hari Kerja (JSON format: [1,2,3,4,5] = Senin-Jumat)
            [
                'setting_key' => 'working_days',
                'setting_value' => '[1,2,3,4,5]',
                'description' => 'Hari kerja (1=Senin, 2=Selasa, dst)',
                'category' => 'absensi',
                'type' => 'json',
                'is_editable' => true,
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Absensi
    |--------------------------------------------------------------------------
    |
    | File ini berisi konfigurasi untuk sistem absensi magang
    |
    */

    // Jam Kerja Default (akan di-override oleh system_settings)
    'jam_kerja' => [
        'masuk' => '08:00:00',
        'pulang' => '17:00:00',
    ],

    // GPS Validation
    'gps' => [
        'office_latitude' => -6.2264854,
        'office_longitude' => 106.8201788,
        'radius' => 50, // meter
    ],

    // Foto Validation
    'foto' => [
        'max_size' => 2048, // KB
        'allowed_types' => ['jpg', 'jpeg', 'png'],
        'path' => [
            'masuk' => 'foto_absensi/masuk',
            'pulang' => 'foto_absensi/pulang',
        ],
    ],

    // Surat Magang Validation
    'surat_magang' => [
        'max_size' => 2048, // KB
        'allowed_types' => ['pdf', 'jpg', 'jpeg', 'png'],
        'path' => 'surat_magang',
    ],

    // Bukti Izin Validation
    'bukti_izin' => [
        'max_size' => 2048, // KB
        'allowed_types' => ['pdf', 'jpg', 'jpeg', 'png'],
        'path' => 'bukti_izin',
    ],

    // Auto Approval (dalam jam)
    'auto_approval' => [
        'layer1' => 24, // Ketua Bidang
        'layer2' => 24, // HR
    ],

    // Email Verification (dalam jam)
    'email_verification' => [
        'expiration' => 24,
    ],

    // Working Days (1=Senin, 2=Selasa, ..., 7=Minggu)
    'working_days' => [1, 2, 3, 4, 5],

    // Status Kehadiran
    'status_kehadiran' => [
        'HADIR_TEPAT_WAKTU',
        'HADIR_TELAT',
        'IZIN',
        'ALPHA',
        'LIBUR',
    ],

    // Role Definitions
    'roles' => [
        'peserta_magang' => 'Peserta Magang',
        // 'ketua_bidang' => 'Ketua Bidang',
        'hr' => 'HR / Super Admin',
    ],
];

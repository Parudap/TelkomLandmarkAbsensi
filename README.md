# Sistem Absensi Peserta Magang - Telkom Landmark Tower

Platform untuk pencatatan kehadiran dan manajemen peserta magang/PKL dengan validasi foto selfie dan GPS.

---

## Deskripsi Sistem

Sistem berbasis web untuk mengelola absensi peserta magang di Telkom Landmark Tower dengan fitur:
- Registrasi dengan verifikasi email
- Absensi dengan foto selfie dan validasi lokasi (GPS)
- Sistem izin berlapis (2 level approval)
- Auto-approval setelah 24 jam jika tidak ada respon
- Dashboard statistik
- Export data (Excel/PDF)
- Notifikasi email

---

## Struktur Folder

```
sistem-absensi-magang/
├── app/
│   ├── Console/Commands/          # Artisan commands
│   ├── Helpers/                   # Helper functions (TimeHelper, FormatHelper)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/             # Authentication (Register, Login, Email Verification)
│   │   │   ├── Peserta/          # Peserta magang features (Dashboard, Absensi, Izin)
│   │   │   └── HR/               # HR features (Registrations, Reports)
│   │   ├── Middleware/           # Custom middleware (CheckRole, AccountApproved)
│   │   └── Requests/             # Form validation
│   │       └── Auth/             # Auth requests (RegisterRequest)
│   ├── Models/                   # Eloquent models (User, Bidang, Absensi, Izin, etc)
│   ├── Services/                 # Business logic (AbsensiService)
│   └── Traits/                   # Reusable traits
├── config/
│   └── absensi.php               # Absensi configuration
├── database/
│   ├── migrations/               # 9 tables (users, bidang, absensi, izin, etc)
│   └── seeders/                  # BidangSeeder, SystemSettingSeeder
├── resources/
│   └── views/
│       ├── auth/                 # Auth views (register, login, verification)
│       ├── peserta/              # Peserta views
│       ├── hr/                   # HR views
│       ├── components/           # Reusable components
│       ├── emails/               # Email templates
│       └── partials/             # Partial views
└── storage/
    └── app/public/
        ├── foto_absensi/         # Attendance photos (masuk/pulang)
        ├── surat_magang/         # Internship letters
        ├── bukti_izin/           # Leave proof
        └── exports/              # Export files
```

---

## Teknologi

- Framework: Laravel 12
- PHP: 8.2+
- Database: MySQL
- Frontend: Blade + Tailwind CSS
- Storage: Local (symlink `storage:link`)

---

## Instalasi

```bash
# 1. Clone repository
git clone <repository-url>
cd TelkomLandmark

# 2. Install dependencies
composer install

# 3. Copy environment file
copy .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=telkom_landmark_absensi
DB_USERNAME=root
DB_PASSWORD=

# 6. Run migrations & seeders
php artisan migrate --seed

# 7. Create storage symlink
php artisan storage:link

# 8. Start development server
php artisan serve
```

Akses aplikasi di: `http://127.0.0.1:8000` atau `http://telkomlandmark.test`

✅ **Tidak perlu setup scheduler!** Auto-approve izin berjalan otomatis saat halaman dibuka.  
📖 Informasi lebih lanjut: [`docs/SCHEDULER_SETUP.md`](docs/SCHEDULER_SETUP.md)

---

## User Roles

### Peserta Magang (`peserta_magang`)
- Absensi masuk/pulang dengan foto dan lokasi
- Ajukan izin atau sakit
- Lihat statistik dan riwayat pribadi

### HR / Admin (`hr`)
- Menyetujui/menolak registrasi peserta
- Menyetujui/menolak izin (Layer 2)
- Mengelola sistem, bidang, dan pengaturan
- Export rekap keseluruhan

---

## Database Schema

Tabel utama: `users`, `bidang`, `absensi`, `izin`, `approval_logs`, `system_settings`, serta tabel sistem seperti `sessions`, `cache`, dan `jobs`.

---

## Fitur Utama

### Registrasi & Otentikasi
- Form registrasi lengkap
- Upload surat magang (PDF, max 2MB)
- Verifikasi email
- Validasi login (email verified + approved + active)

### Absensi
- Validasi lokasi (radius 50 m)
- Foto selfie (JPG/PNG, max 2MB)
- Deteksi keterlambatan otomatis
- Rekap harian/bulanan

### Sistem Izin
- Pengajuan izin/sakit dengan bukti
- Approval oleh HR
- Auto-approve setelah 24 jam jika tidak ada respons
- Notifikasi email untuk setiap perubahan status

### Dashboard
- Statistik per role
- Grafik kehadiran dan notifikasi

---

## Keamanan

- Password hashing (bcrypt)
- CSRF protection
- Verifikasi email wajib
- Role-based access control (RBAC)
- Validasi lokasi GPS
- Validasi foto (format dan ukuran)
- Middleware untuk proteksi route

---

## Email Notifications

- Email verification setelah registrasi
- Notification untuk approval/penolakan registrasi
- Notification untuk pengajuan izin dan status approval
- Notification untuk auto-approval

---

## Auto-Closing System

Sistem otomatis menandai absensi sebagai `ALPHA` untuk tanggal kerja yang tidak memiliki record pulang.

Status harian:

- `BELUM_FINAL` — Menunggu absen pulang
- `HADIR_TEPAT_WAKTU` — Masuk tepat waktu dan sudah pulang
- `HADIR_TELAT` — Masuk terlambat dan sudah pulang
- `ALPHA` — Tidak hadir penuh atau tidak melakukan absen pulang

Perintah artisan terkait:
```
php artisan attendance:close-unfinished
php artisan attendance:close-unfinished --dry-run
php artisan attendance:close-unfinished --date=2026-01-10
```

Scheduler: jalankan `php artisan schedule:run` setiap menit via cron.

Dokumentasi: [QUICK_START_AUTO_CLOSING.md](QUICK_START_AUTO_CLOSING.md)

---

## Configuration (examples)

GPS settings (`config/absensi.php`):
```php
'gps' => [
    'lat' => -6.2293867,
    'lng' => 106.8184191,
    'radius' => 50, // meter
]
```

Working hours:
```php
'jam_kerja' => [
    'masuk' => '08:00',
    'pulang' => '17:00',
    'toleransi' => 15, // menit
]
```

Auto approval settings:
```php
'auto_approval' => [
    'enabled' => true,
    'hours' => 24,
]
```

---

## Development

Helper functions include `TimeHelper` and `FormatHelper`.

`AbsensiService` handles business logic like location validation and duration calculation.

---

## 📞 Support

Untuk bantuan teknis dan pertanyaan:
- **Email**: it-support@telkomlandmark.co.id
- **Phone**: (021) xxxx-xxxx
- **Location**: Telkom Landmark Tower, Jakarta

---

## Support

Untuk bantuan teknis dan pertanyaan:
- Email: it-support@telkomlandmark.co.id
- Phone: (021) xxxx-xxxx

---

## Credits & License

Built with Laravel Framework.

This project uses the Laravel framework (MIT license). See Laravel docs for contribution and code of conduct.
```

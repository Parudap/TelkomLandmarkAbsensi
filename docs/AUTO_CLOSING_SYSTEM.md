# 🔄 AUTO-CLOSING SYSTEM - ABSENSI YANG TIDAK ABSEN PULANG

## 📋 Konsep

Sistem ini mengimplementasikan **praktik HR terbaik** untuk menangani kasus peserta yang:
- ✅ Sudah absen masuk (tepat waktu atau terlambat)
- ❌ Tapi LUPA absen pulang

---

## 🎯 Alur Status Absensi

### **Status Field: `status_harian`**

| Status | Kapan | Keterangan |
|--------|-------|------------|
| `BELUM_FINAL` | Setelah absen masuk (hari yang sama) | Menunggu absen pulang |
| `HADIR_TEPAT_WAKTU` | Setelah absen pulang (masuk ≤ 08:00) | ✅ Kehadiran penuh |
| `HADIR_TELAT` | Setelah absen pulang (masuk > 08:00) | ⚠️ Kehadiran penuh tapi terlambat |
| `ALPHA` | H+1 auto-close (jika tidak absen pulang) | ❌ Tidak hadir penuh |
| `IZIN` | Ada izin yang disetujui HR | 📝 Izin resmi |
| `LIBUR` | Hari Sabtu/Minggu atau libur nasional | 🏖️ Hari libur |

---

## ⏱️ Timeline Status

### **Hari Yang Sama (H+0)**

```
07:55 - Absen Masuk
       ↓
       status_harian = BELUM_FINAL
       status_masuk  = TEPAT_WAKTU
       
       UI: "⏳ Menunggu Absen Pulang"
       
17:30 - Absen Pulang
       ↓
       status_harian = HADIR_TEPAT_WAKTU ✅
       
       UI: "✅ Hadir Tepat Waktu"
```

### **Ganti Hari Tapi Tidak Absen Pulang (H+1)**

```
12 Jan 00:01 - Auto-Close Job Running
              ↓
              Cari semua absensi tanggal 11 Jan
              WHERE status_harian = BELUM_FINAL
              AND jam_masuk IS NOT NULL
              AND jam_pulang IS NULL
              ↓
              UPDATE:
              status_harian = ALPHA ❌
              catatan_sistem = "Auto-closed: Tidak melakukan absen pulang"
              
              UI: "❌ Alpha (Tidak Absen Pulang)"
```

---

## 🤖 Artisan Command: `attendance:close-unfinished`

### **Manual Execution**

```bash
# Close absensi kemarin (default)
php artisan attendance:close-unfinished

# Close absensi untuk tanggal tertentu
php artisan attendance:close-unfinished --date=2026-01-10

# Dry run (preview tanpa mengubah data)
php artisan attendance:close-unfinished --dry-run

# Dry run untuk tanggal tertentu
php artisan attendance:close-unfinished --date=2026-01-10 --dry-run
```

### **Output Example**

```
🔍 Mencari absensi yang belum final untuk tanggal: 10 Januari 2026

⚠️  Ditemukan 3 absensi yang belum selesai:

+----+------------------+----------------------+------------+------------+--------------+
| ID | Nama             | Email                | Tanggal    | Jam Masuk  | Status Masuk |
+----+------------------+----------------------+------------+------------+--------------+
| 45 | Budi Santoso     | budi@example.com     | 10/01/2026 | 07:45:00   | TEPAT_WAKTU  |
| 67 | Ani Wijaya       | ani@example.com      | 10/01/2026 | 08:15:00   | TELAT        |
| 89 | Rina Kusuma      | rina@example.com     | 10/01/2026 | 07:58:00   | TEPAT_WAKTU  |
+----+------------------+----------------------+------------+------------+--------------+

Apakah Anda yakin ingin mengubah semua data di atas menjadi ALPHA? (yes/no) [no]:
> yes

🔄 Memproses...
███████████████████████████████████████████████████████████ 100%

✅ Berhasil mengubah 3 absensi menjadi ALPHA.

📝 Detail:
   - Tanggal yang diproses: 10 Januari 2026
   - Total diubah: 3 absensi
   - Status baru: ALPHA
   - Catatan: Tidak melakukan absen pulang
```

---

## 📅 Scheduled Auto-Close (Cron Job)

### **Konfigurasi di `app/Console/Kernel.php`**

```php
protected function schedule(Schedule $schedule): void
{
    // Auto-close absensi kemarin setiap hari jam 00:01
    $schedule->command('attendance:close-unfinished')
        ->dailyAt('00:01')
        ->timezone('Asia/Jakarta')
        ->appendOutputTo(storage_path('logs/auto-close-attendance.log'));
}
```

### **Aktivasi di Server**

#### **Opsi 1: Laravel Scheduler (Recommended)**

Tambahkan ke crontab server:

```bash
* * * * * cd /path/to/TelkomLandmark && php artisan schedule:run >> /dev/null 2>&1
```

#### **Opsi 2: Direct Cron**

```bash
1 0 * * * cd /path/to/TelkomLandmark && php artisan attendance:close-unfinished >> storage/logs/auto-close.log 2>&1
```

#### **Development (Windows - Laragon)**

Jalankan scheduler secara manual:

```bash
php artisan schedule:work
```

Atau tambahkan ke Windows Task Scheduler untuk run setiap hari jam 00:01.

---

## 📊 Database Migration

### **Run Migration**

```bash
php artisan migrate
```

### **Struktur Field Baru**

```sql
ALTER TABLE absensi 
ADD COLUMN status_harian ENUM(
    'BELUM_FINAL',
    'HADIR_TEPAT_WAKTU', 
    'HADIR_TELAT',
    'ALPHA',
    'IZIN',
    'LIBUR'
) DEFAULT 'BELUM_FINAL' AFTER status;

ALTER TABLE absensi
ADD COLUMN catatan_sistem TEXT NULL AFTER catatan;
```

---

## 🎨 UI/UX Display

### **Hari Yang Sama (Setelah Absen Masuk)**

```
┌─────────────────────────────────────────┐
│ ✅ Absen Masuk Berhasil!                │
├─────────────────────────────────────────┤
│ 📅 Senin, 13 Januari 2026               │
│ 🕐 Jam Masuk     : 07:55 WIB            │
│ ✅ Status Masuk  : Tepat Waktu          │
│ ⏳ Status Harian : Menunggu Absen Pulang│
├─────────────────────────────────────────┤
│ 💡 Jangan lupa absen pulang sore nanti! │
└─────────────────────────────────────────┘
```

### **Setelah Absen Pulang**

```
┌─────────────────────────────────────────┐
│ ✅ Absen Pulang Berhasil!               │
├─────────────────────────────────────────┤
│ 🕐 Jam Masuk     : 07:55 WIB            │
│ 🕐 Jam Pulang    : 17:15 WIB            │
│ ⏱️  Durasi Kerja  : 9 jam 20 menit      │
│ ✅ Status Harian : Hadir Tepat Waktu    │
└─────────────────────────────────────────┘
```

### **Keesokan Hari (Auto-Closed)**

```
┌─────────────────────────────────────────┐
│ 📅 Senin, 13 Januari 2026               │
├─────────────────────────────────────────┤
│ 🕐 Jam Masuk     : 07:55 WIB            │
│ 🕐 Jam Pulang    : -                    │
│ ❌ Status Harian : ALPHA                │
│ 📝 Catatan       : Tidak melakukan      │
│                    absen pulang         │
└─────────────────────────────────────────┘
```

---

## 🧪 Testing

### **Skenario 1: Absen Masuk Tepat Waktu Lalu Pulang**

```bash
# Set waktu testing jam 07:55
php artisan time:set "2026-01-13 07:55:00"

# Absen masuk → status_harian = BELUM_FINAL

# Set waktu testing jam 17:15
php artisan time:set "2026-01-13 17:15:00"

# Absen pulang → status_harian = HADIR_TEPAT_WAKTU ✅
```

### **Skenario 2: Absen Masuk Tapi Lupa Pulang**

```bash
# Set waktu testing jam 07:55 (hari Senin)
php artisan time:set "2026-01-13 07:55:00"

# Absen masuk → status_harian = BELUM_FINAL

# Simulasi ganti hari (Selasa jam 00:05)
php artisan time:set "2026-01-14 00:05:00"

# Run auto-close manual
php artisan attendance:close-unfinished --date=2026-01-13

# Result: status_harian = ALPHA ❌
```

### **Skenario 3: Terlambat Tapi Tetap Pulang**

```bash
# Set waktu testing jam 08:30 (terlambat)
php artisan time:set "2026-01-13 08:30:00"

# Absen masuk → status_masuk = TELAT, status_harian = BELUM_FINAL

# Set waktu pulang
php artisan time:set "2026-01-13 17:00:00"

# Absen pulang → status_harian = HADIR_TELAT ⚠️
```

---

## 📝 Catatan Penting

### **Backward Compatibility**

Field `status` tetap ada untuk backward compatibility, tapi **status utama** sekarang adalah `status_harian`.

### **Field yang Digunakan**

- ✅ **`status_harian`** - Field utama untuk tracking status akhir
- 📋 **`status_masuk`** - TEPAT_WAKTU atau TELAT
- 🗂️ **`status`** - Deprecated (masih ada untuk kompatibilitas)
- 📝 **`catatan_sistem`** - Catatan auto-close atau sistem lainnya

### **Query Recommendation**

```php
// ✅ BENAR - Gunakan status_harian
Absensi::where('status_harian', 'HADIR_TEPAT_WAKTU')->count();

// ❌ DEPRECATED - Jangan gunakan status
Absensi::where('status', 'HADIR_TEPAT_WAKTU')->count();
```

---

## 🔧 Troubleshooting

### **Command tidak ditemukan**

```bash
# Clear cache dan reload
php artisan config:clear
composer dump-autoload
```

### **Scheduler tidak jalan**

```bash
# Test manual
php artisan schedule:run

# Cek cron log
tail -f storage/logs/auto-close-attendance.log
```

### **Rollback migration**

```bash
php artisan migrate:rollback --step=1
```

---

## 📞 Support

Jika ada pertanyaan atau issue, hubungi tim IT Development.

**Last Updated:** 11 Januari 2026

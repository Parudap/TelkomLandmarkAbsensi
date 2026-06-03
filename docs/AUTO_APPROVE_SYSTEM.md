# 🤖 AUTO-APPROVE IZIN - ON-DEMAND SYSTEM

## 📋 Konsep

Sistem auto-approve izin yang **TIDAK MEMERLUKAN SCHEDULER** atau konfigurasi tambahan.

Auto-approve berjalan otomatis **on-demand** setiap kali halaman tertentu dibuka.

---

## ✨ Keunggulan Sistem Ini

✅ **Zero Configuration** - Tidak perlu setup Task Scheduler, Cron, atau terminal yang terus berjalan  
✅ **Cross-Platform** - Bekerja di Windows, Linux, macOS tanpa konfigurasi khusus  
✅ **Easy Deployment** - Copy paste project langsung jalan  
✅ **Real-time** - Auto-approve langsung tereksekusi saat halaman dibuka  
✅ **Developer-Friendly** - User lain tidak perlu setup apapun  

---

## 🎯 Cara Kerja

### Trigger Points (Kapan Auto-Approve Berjalan)

Auto-approve otomatis dijalankan saat:

1. **HR membuka Dashboard** (`/hr/dashboard`)
   - Service: `IzinAutoApproveService::processAutoApproval()`
   - Controller: `HR\DashboardController@index`

2. **HR membuka halaman Approval Izin** (`/hr/izin`)
   - Service: `IzinAutoApproveService::processAutoApproval()`
   - Controller: `HR\ApprovalIzinController@index`

3. **Peserta membuka halaman Riwayat Izin** (`/peserta/izin`)
   - Service: `IzinAutoApproveService::processAutoApproval()`
   - Controller: `Peserta\IzinController@index`

### Proses Auto-Approve

```
User membuka halaman
    ↓
Controller dipanggil
    ↓
IzinAutoApproveService::processAutoApproval()
    ↓
Query izin pending > 24 jam
    ↓
Loop setiap izin:
  - Update status → 'approved_hr'
  - Set auto_approved_at
  - Backfill absensi
    ↓
Return jumlah izin yang di-approve
```

---

## 📊 Logika Auto-Approve

### Kriteria Izin yang Di-Auto-Approve

Izin akan otomatis disetujui jika memenuhi kondisi:

| Field | Kondisi |
|-------|---------|
| `status_approval` | `= 'pending'` |
| `created_at` | `<= NOW() - 24 HOURS` |

**Contoh:**
- Izin diajukan: **21 April 2026, 18:59**
- Auto-approve mulai: **22 April 2026, 18:59** (24 jam kemudian)
- Saat user membuka halaman trigger → Langsung di-approve

### Field yang Diupdate

```php
$izin->status_approval = 'approved_hr';
$izin->auto_approved_at = Carbon::now();
$izin->approved_by_hr = null; // Sistem, bukan user
$izin->approved_at_hr = Carbon::now();
$izin->save();
```

---

## 🔄 Backfill Absensi

Setelah izin di-approve, sistem otomatis backfill absensi:

### Izin Pulang Cepat

```php
// Jika sudah absen masuk tapi belum pulang
if ($absensi->jam_masuk && !$absensi->jam_pulang) {
    $absensi->jam_pulang = $izin->jam_pulang_diajukan;
    $absensi->status_harian = 'IZIN_PULANG_CEPAT';
    $absensi->izin_id = $izin->id;
    $absensi->save();
}

// Jika status ALPHA (sudah auto-close)
elseif ($absensi->status_harian == 'ALPHA') {
    $absensi->status_harian = 'IZIN_PULANG_CEPAT';
    $absensi->izin_id = $izin->id;
    $absensi->save();
}
```

### Izin Tidak Masuk

```php
// Loop tanggal mulai sampai selesai
while ($tanggalMulai->lte($tanggalSelesai)) {
    if (!$tanggalMulai->isWeekend()) {
        // Cari atau buat absensi
        $absensi = Absensi::updateOrCreate(
            ['user_id' => $izin->user_id, 'tanggal' => $tanggalMulai],
            [
                'status_harian' => 'IZIN_TIDAK_MASUK',
                'izin_id' => $izin->id,
                'catatan_sistem' => 'Izin tidak masuk'
            ]
        );
    }
    $tanggalMulai->addDay();
}
```

---

## 🧪 Testing

### 1. Test Manual via Tinker

```bash
php artisan tinker

# Test auto-approve
\App\Services\IzinAutoApproveService::processAutoApproval();
```

### 2. Test via Command

```bash
php artisan izin:auto-approve
```

### 3. Test via Browser

1. Ajukan izin baru
2. Ubah `created_at` menjadi 25 jam yang lalu (via database)
3. Buka salah satu halaman trigger (Dashboard HR / Approval Izin)
4. Izin otomatis di-approve

---

## 📁 File-File Terkait

### Service
- `app/Services/IzinAutoApproveService.php` - Main logic

### Controllers
- `app/Http/Controllers/HR/DashboardController.php` - Trigger dari dashboard HR
- `app/Http/Controllers/HR/ApprovalIzinController.php` - Trigger dari approval page
- `app/Http/Controllers/Peserta/IzinController.php` - Trigger dari riwayat peserta

### Command (Opsional - untuk manual trigger)
- `app/Console/Commands/AutoApproveIzin.php` - Command: `php artisan izin:auto-approve`

---

## 🔍 Monitoring & Debugging

### Cek Izin Pending

```sql
SELECT id, user_id, jenis_izin, created_at, 
       TIMESTAMPDIFF(HOUR, created_at, NOW()) as hours_pending
FROM izin
WHERE status_approval = 'pending'
  AND TIMESTAMPDIFF(HOUR, created_at, NOW()) >= 24;
```

### Cek Izin yang Sudah Auto-Approved

```sql
SELECT id, user_id, jenis_izin, auto_approved_at
FROM izin
WHERE approved_by_hr IS NULL
  AND auto_approved_at IS NOT NULL;
```

---

## ❓ FAQ

### Kenapa tidak menggunakan Laravel Scheduler?

**Alasan:**
- Memerlukan setup Task Scheduler (Windows) atau Cron Job (Linux)
- Setiap developer/user yang clone project harus setup sendiri
- Tidak portable - config berbeda di setiap environment

**Solusi On-Demand:**
- Zero configuration
- Auto-run saat halaman dibuka
- Works everywhere tanpa setup

### Apakah auto-approve berjalan setiap detik?

**Tidak.** Auto-approve hanya berjalan saat:
- User membuka halaman trigger
- Overhead minimal (query cepat)
- Tidak ada background process yang terus berjalan

### Bagaimana jika tidak ada user yang buka halaman?

Izin tetap akan di-approve saat **user pertama** membuka salah satu halaman trigger.

**Contoh:**
- Izin diajukan: Senin 08:00
- Auto-approve deadline: Selasa 08:00
- User membuka dashboard: Selasa 10:00
- ✅ Izin langsung di-approve saat itu

### Apakah ada race condition?

**Tidak.** Database query memastikan:
- Hanya izin dengan status `pending` yang di-process
- Update dilakukan transactional
- Aman untuk concurrent requests

---

## 🚀 Deployment

**No special configuration needed!**

1. Clone project
2. `composer install`
3. `php artisan migrate --seed`
4. `php artisan serve`
5. ✅ Auto-approve langsung bekerja

---

## 📝 Changelog

### v2.0 - On-Demand Auto-Approve (Current)
- ✅ Tidak perlu scheduler
- ✅ Auto-run saat halaman dibuka
- ✅ Zero configuration

### v1.0 - Scheduled Auto-Approve (Legacy)
- ❌ Memerlukan Task Scheduler/Cron
- ❌ Perlu setup manual
- ❌ Tidak portable

---

## 📚 Referensi

- [Service Class: IzinAutoApproveService.php](../app/Services/IzinAutoApproveService.php)
- [Auto Closing System](AUTO_CLOSING_SYSTEM.md)
- [Scheduler Setup (Opsional)](SCHEDULER_SETUP.md)

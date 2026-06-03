# Auto-Approve Izin - Tidak Perlu Setup Scheduler!

## ✅ Fitur Auto-Approve Otomatis (On-Demand)

**GOOD NEWS:** Sistem auto-approve sekarang **TIDAK MEMERLUKAN** setup scheduler tambahan!

Auto-approve izin yang sudah lebih dari 24 jam akan otomatis berjalan setiap kali:
- ✅ HR membuka halaman **Approval Izin**
- ✅ HR membuka **Dashboard**
- ✅ Peserta membuka halaman **Riwayat Izin**

Sistem akan otomatis mengecek dan meng-approve izin yang sudah pending lebih dari 24 jam **tanpa konfigurasi apapun**.

## Cara Kerja

1. User membuka salah satu halaman yang memicu auto-approve
2. Sistem otomatis mengecek izin yang pending > 24 jam
3. Izin otomatis di-approve dan absensi di-backfill
4. Semuanya berjalan **transparan di background**

## Keuntungan Solusi Ini

✅ **Tidak perlu setup scheduler** - Langsung jalan  
✅ **Tidak perlu Task Scheduler Windows** - Zero configuration  
✅ **Tidak perlu cron job** - Works on any platform  
✅ **Auto-update saat dibuka** - Real-time processing  
✅ **Mudah deployment** - Tinggal copy paste project  

## Test Auto-Approve Manual

Jika ingin test secara manual via command line:

```bash
php artisan izin:auto-approve
```

## Command Lainnya yang Masih Perlu Scheduler

### Auto-Close Absensi (Opsional)

Command `attendance:close-unfinished` masih menggunakan scheduler untuk menutup absensi yang tidak pulang.

**Jika tidak setup scheduler:**
- Absensi yang tidak pulang akan ditutup otomatis keesokan harinya
- Atau HR bisa close manual dari dashboard

**Jika ingin setup scheduler (opsional):**
Lihat section di bawah untuk setup Task Scheduler Windows atau Cron Job Linux.

---

# Setup Laravel Scheduler (Opsional - Hanya untuk Auto-Close)

### Metode 1: Task Scheduler Windows (Recommended - Permanen)

1. **Buka Task Scheduler** Windows:
   - Tekan `Win + R`, ketik `taskschd.msc`, Enter

2. **Create Basic Task**:
   - Klik "Create Basic Task" di panel kanan
   - Name: `Laravel Scheduler - TelkomLandmark`
   - Description: `Menjalankan scheduled tasks untuk auto-approve izin dan auto-close absensi`

3. **Trigger Setup**:
   - Trigger: `Daily`
   - Start: `00:00:00` (tengah malam)
   - Recur every: `1 days`
   - ✅ Centang "Enabled"

4. **Advanced Trigger Settings**:
   - ✅ Centang "Repeat task every"
   - Pilih: `1 minute`
   - For a duration of: `1 day`

5. **Action Setup**:
   - Action: `Start a program`
   - Program/script: `C:\laragon\www\TelkomLandmark\run-scheduler.bat`
   - (Path disesuaikan dengan lokasi project Anda)

6. **Finish** dan task akan berjalan otomatis

### Metode 2: Manual Command (Temporary - Untuk Testing)

Jalankan di terminal dan biarkan tetap terbuka:
```bash
php artisan schedule:work
```

**Kelebihan**: Langsung jalan  
**Kekurangan**: Terminal harus tetap terbuka, restart hilang

## Setup untuk Production (Linux Server)

Edit crontab:
```bash
crontab -e
```

Tambahkan baris ini:
```
* * * * * cd /path/to/TelkomLandmark && php artisan schedule:run >> /dev/null 2>&1
```

## Cara Cek Scheduler Berjalan

### Test Manual:
```bash
php artisan schedule:list
```

Output yang benar:
```
- izin:auto-approve ................ Hourly
- attendance:close-unfinished ...... Daily at 00:01
```

### Test Auto-Approve:
```bash
php artisan izin:auto-approve
```

Jika ada izin yang pending lebih dari 24 jam, akan otomatis disetujui.

## Troubleshooting

### Scheduler tidak jalan?
1. **Cek apakah Task Scheduler sudah dibuat** (Windows)
2. **Cek log scheduler** di `storage/logs/auto-approve-izin.log`
3. **Test manual** dengan `php artisan izin:auto-approve`

### Izin tidak auto-approve?
1. Cek created_at izin → harus lebih dari 24 jam
2. Cek status → harus "pending"
3. Jalankan manual: `php artisan izin:auto-approve`

### Permission Error?
```bash
chmod +x run-scheduler.bat
```

## Catatan Penting

⚠️ **WAJIB setup scheduler** agar fitur auto-approve dan auto-close berjalan!

✅ Scheduler sudah dikonfigurasi di `app/Console/Kernel.php`  
✅ File batch sudah tersedia di `run-scheduler.bat`  
✅ Tinggal setup Task Scheduler Windows atau Cron Job Linux

## Referensi
- [Laravel Task Scheduling Documentation](https://laravel.com/docs/scheduling)

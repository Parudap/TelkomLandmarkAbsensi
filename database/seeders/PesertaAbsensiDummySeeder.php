<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bidang;
use App\Models\Absensi;
use App\Models\Izin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PesertaAbsensiDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data dummy yang sudah ada
        $this->command->info('Menghapus data dummy sebelumnya...');
        User::where('email', 'like', '%@example.com')->delete();
        
        $bidangList = Bidang::all();
        
        $namaPeserta = [
            'Ahmad Rizki', 'Budi Santoso', 'Citra Dewi', 'Dina Anggraini', 'Eko Prasetyo',
            'Fitri Handayani', 'Gita Permata', 'Hendra Wijaya', 'Indah Sari', 'Joko Susilo',
            'Kartika Putri', 'Lukman Hakim', 'Maya Sinta', 'Nanda Pratama', 'Olivia Natasha',
            'Putra Rahman', 'Qori Amelia', 'Reza Firmansyah', 'Siti Nurjannah', 'Taufik Hidayat',
            'Usman Malik', 'Vina Safitri', 'Wawan Setiawan', 'Xena Puspita', 'Yudi Hermawan',
            'Zahra Kamila', 'Arif Budiman', 'Bella Kusuma', 'Candra Wijaya', 'Desi Rahayu',
            'Endra Gunawan', 'Farah Zahra', 'Gilang Ramadhan', 'Hani Pramesti', 'Irfan Maulana',
            'Jasmine Azzahra', 'Kurnia Adi', 'Laila Sari', 'Mahesa Putra', 'Nina Marlina',
            'Oscar Pratama', 'Prima Dewi', 'Qonita Rahma', 'Rian Saputra', 'Salma Kirana',
            'Teguh Prasetyo', 'Ulfa Hasanah', 'Vino Aditya', 'Wulan Dari', 'Yoga Pratama',
            'Zaki Rahman', 'Ayu Lestari', 'Bagas Santoso', 'Cinta Permata', 'Danu Wicaksono'
        ];
        
        // Periode magang: 3 bulan ke depan
        $tanggalMulai = Carbon::now()->addDays(7);
        $tanggalSelesai = Carbon::now()->addMonths(3);
        
        $pesertaIds = [];
        $namaPesertaIndex = 0;
        
        // Buat 5-7 peserta per bidang
        foreach ($bidangList as $bidang) {
            $jumlahPeserta = rand(5, 7);
            
            for ($i = 0; $i < $jumlahPeserta; $i++) {
                if ($namaPesertaIndex >= count($namaPeserta)) {
                    break;
                }
                
                $nama = $namaPeserta[$namaPesertaIndex];
                $email = strtolower(str_replace(' ', '.', $nama)) . '@example.com';
                
                $user = User::create([
                    'name' => $nama,
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'role' => 'peserta_magang',
                    'bidang_id' => $bidang->id,
                    'status_approval' => 'approved',
                    'is_active' => true,
                    'periode_magang_mulai' => $tanggalMulai,
                    'periode_magang_selesai' => $tanggalSelesai,
                ]);
                
                $pesertaIds[] = $user->id;
                $namaPesertaIndex++;
            }
        }
        
        // Generate data absensi untuk 3 minggu terakhir
        $tanggalAkhir = Carbon::now()->subDay(); // Kemarin
        $tanggalMulaiAbsensi = Carbon::now()->subWeeks(3)->startOfWeek(); // 3 minggu yang lalu
        
        $statusOptions = [
            'HADIR_TEPAT_WAKTU' => 60,  // 60% kemungkinan
            'HADIR_TELAT' => 20,         // 20% kemungkinan
            'IZIN_TIDAK_MASUK' => 10,    // 10% kemungkinan
            'IZIN_PULANG_CEPAT' => 10,   // 10% kemungkinan
        ];
        
        foreach ($pesertaIds as $userId) {
            $currentDate = $tanggalMulaiAbsensi->copy();
            
            while ($currentDate->lte($tanggalAkhir)) {
                // Skip weekends (Sabtu & Minggu)
                if ($currentDate->isWeekend()) {
                    $currentDate->addDay();
                    continue;
                }
                
                // Random status berdasarkan probabilitas
                $random = rand(1, 100);
                $cumulativePercent = 0;
                $status = 'HADIR_TEPAT_WAKTU';
                
                foreach ($statusOptions as $stat => $percent) {
                    $cumulativePercent += $percent;
                    if ($random <= $cumulativePercent) {
                        $status = $stat;
                        break;
                    }
                }
                
                // Generate jam masuk dan pulang
                $jamMasuk = null;
                $jamPulang = null;
                $fotoMasuk = null;
                $fotoPulang = null;
                $latitudeMasuk = null;
                $longitudeMasuk = null;
                $latitudePulang = null;
                $longitudePulang = null;
                
                if ($status === 'HADIR_TEPAT_WAKTU') {
                    // Masuk antara 07:00 - 08:00
                    $jamMasuk = $currentDate->copy()->setTime(7, rand(0, 59));
                    // Pulang antara 16:00 - 17:00
                    $jamPulang = $currentDate->copy()->setTime(16, rand(0, 59));
                    $fotoMasuk = 'dummy/foto_masuk.jpg';
                    $fotoPulang = 'dummy/foto_pulang.jpg';
                    $latitudeMasuk = -6.2088; // Jakarta coordinates
                    $longitudeMasuk = 106.8456;
                    $latitudePulang = -6.2088;
                    $longitudePulang = 106.8456;
                } elseif ($status === 'HADIR_TELAT') {
                    // Masuk antara 08:01 - 09:30
                    $jamMasuk = $currentDate->copy()->setTime(8, rand(1, 59));
                    if (rand(0, 1)) {
                        $jamMasuk->addHour();
                        $jamMasuk->setMinute(rand(0, 30));
                    }
                    // Pulang antara 16:00 - 17:00
                    $jamPulang = $currentDate->copy()->setTime(16, rand(0, 59));
                    $fotoMasuk = 'dummy/foto_masuk.jpg';
                    $fotoPulang = 'dummy/foto_pulang.jpg';
                    $latitudeMasuk = -6.2088;
                    $longitudeMasuk = 106.8456;
                    $latitudePulang = -6.2088;
                    $longitudePulang = 106.8456;
                } elseif ($status === 'IZIN_PULANG_CEPAT') {
                    // Masuk normal
                    $jamMasuk = $currentDate->copy()->setTime(7, rand(30, 59));
                    // Pulang lebih awal (12:00 - 15:00)
                    $jamPulang = $currentDate->copy()->setTime(rand(12, 14), rand(0, 59));
                    $fotoMasuk = 'dummy/foto_masuk.jpg';
                    $fotoPulang = 'dummy/foto_pulang.jpg';
                    $latitudeMasuk = -6.2088;
                    $longitudeMasuk = 106.8456;
                    $latitudePulang = -6.2088;
                    $longitudePulang = 106.8456;
                    
                    // Buat data izin untuk pulang cepat
                    Izin::create([
                        'user_id' => $userId,
                        'jenis_izin' => 'pulang_cepat',
                        'tanggal' => $currentDate->format('Y-m-d'),
                        'jam_pulang_diajukan' => $jamPulang->format('H:i'),
                        'alasan' => 'Keperluan keluarga',
                        'status_approval' => 'approved_hr',
                        'keterangan_hr' => 'Disetujui',
                        'created_at' => $currentDate->copy()->subDays(1),
                    ]);
                }
                // IZIN_TIDAK_MASUK tidak perlu jam masuk/pulang
                else if ($status === 'IZIN_TIDAK_MASUK') {
                    // Buat data izin untuk tidak masuk
                    Izin::create([
                        'user_id' => $userId,
                        'jenis_izin' => 'tidak_masuk',
                        'tanggal_mulai' => $currentDate->format('Y-m-d'),
                        'tanggal_selesai' => $currentDate->format('Y-m-d'),
                        'alasan' => 'Sakit ' . ['flu', 'demam', 'pusing', 'batuk'][rand(0, 3)],
                        'status_approval' => 'approved_hr',
                        'keterangan_hr' => 'Disetujui, segera sembuh',
                        'created_at' => $currentDate->copy()->subDays(1),
                    ]);
                }
                
                // Buat record absensi
                Absensi::create([
                    'user_id' => $userId,
                    'tanggal' => $currentDate->format('Y-m-d'),
                    'jam_masuk' => $jamMasuk,
                    'jam_pulang' => $jamPulang,
                    'foto_masuk' => $fotoMasuk,
                    'foto_pulang' => $fotoPulang,
                    'latitude_masuk' => $latitudeMasuk,
                    'longitude_masuk' => $longitudeMasuk,
                    'latitude_pulang' => $latitudePulang,
                    'longitude_pulang' => $longitudePulang,
                    'status_harian' => $status,
                ]);
                
                $currentDate->addDay();
            }
        }
        
        $this->command->info('✓ Berhasil membuat ' . count($pesertaIds) . ' peserta magang');
        $this->command->info('✓ Berhasil generate data absensi untuk 3 minggu');
    }
}

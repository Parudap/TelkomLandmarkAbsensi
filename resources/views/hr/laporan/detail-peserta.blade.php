@extends('layouts.app')

@section('title', 'Detail Absensi - ' . $peserta->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('hr.laporan.peserta') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Kembali ke Daftar Peserta
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Detail Absensi Peserta</h1>
        </div>
    <script>
    function toggleStatusDropdown(btn) {
        // Tutup semua dropdown-status lain
        document.querySelectorAll('.dropdown-status').forEach(function(el) {
            if (!el.contains(btn)) el.classList.add('hidden');
        });
        // Toggle dropdown pada tombol yang diklik
        var dropdown = btn.parentElement.querySelector('.dropdown-status');
        if (dropdown) dropdown.classList.toggle('hidden');
    }
    // Tutup dropdown-status jika klik di luar
    document.addEventListener('click', function(e) {
        document.querySelectorAll('.dropdown-status').forEach(function(el) {
            if (!el.contains(e.target) && !el.previousElementSibling.contains(e.target)) {
                el.classList.add('hidden');
            }
        });
    });
    </script>

        <!-- Info Peserta -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Nama Peserta</label>
                    <p class="text-gray-800 font-medium text-lg">{{ $peserta->name }}</p>
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Email</label>
                    <p class="text-gray-800 font-medium">{{ $peserta->email }}</p>
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Bidang</label>
                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-semibold">
                        {{ $peserta->bidang->nama_bidang ?? '-' }}
                    </span>
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Periode Magang</label>
                    <p class="text-gray-800 font-medium">
                        @if($peserta->periode_magang_mulai && $peserta->periode_magang_selesai)
                            {{ \Carbon\Carbon::parse($peserta->periode_magang_mulai)->locale('id')->translatedFormat('d F Y') }} - 
                            {{ \Carbon\Carbon::parse($peserta->periode_magang_selesai)->locale('id')->translatedFormat('d F Y') }}
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">No. Telepon</label>
                    <p class="text-gray-800 font-medium">{{ $peserta->no_telepon ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Status</label>
                    @if($peserta->is_active)
                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm font-semibold">Aktif</span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-sm font-semibold">Tidak Aktif</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Reset Password -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Reset Password Peserta</h3>
            <form action="{{ route('hr.laporan.peserta.reset-password', $peserta->id) }}" method="POST" class="flex gap-4 items-end" onsubmit="return confirm('Yakin ingin reset password untuk {{ $peserta->name }}?');">
                @csrf
                <div class="flex-1">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Password Baru</label>
                    <div class="relative">
                        <input type="password" id="newPassword" name="new_password" placeholder="Masukkan password baru (minimal 6 karakter)" class="w-full px-4 py-2 pr-12 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required minlength="6">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path id="eyeOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path id="eyeOpenPath" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 font-semibold transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    Reset Password
                </button>
            </form>
        </div>
        
        <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('newPassword');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        }
        </script>

        <!-- Filter Tanggal & Export -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Filter</h3>

            <form id="filterForm" action="{{ route('hr.laporan.detail-peserta', $peserta->id) }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Status Absensi</label>
                        <select id="status_harian" name="status_harian" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Status</option>
                            <option value="HADIR_TEPAT_WAKTU" {{ ($statusHarian ?? '') == 'HADIR_TEPAT_WAKTU' ? 'selected' : '' }}>Tepat Waktu</option>
                            <option value="HADIR_TELAT" {{ ($statusHarian ?? '') == 'HADIR_TELAT' ? 'selected' : '' }}>Terlambat</option>
                            <option value="ALPHA" {{ ($statusHarian ?? '') == 'ALPHA' ? 'selected' : '' }}>Alpha</option>
                            <option value="IZIN_TIDAK_MASUK" {{ ($statusHarian ?? '') == 'IZIN_TIDAK_MASUK' ? 'selected' : '' }}>Izin Tidak Masuk</option>
                            <option value="IZIN_PULANG_CEPAT" {{ ($statusHarian ?? '') == 'IZIN_PULANG_CEPAT' ? 'selected' : '' }}>Izin Pulang Cepat</option>
                            <option value="BELUM_FINAL" {{ ($statusHarian ?? '') == 'BELUM_FINAL' ? 'selected' : '' }}>Belum Final</option>
                        </select>
                    </div>

                    <div class="flex-1">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div class="flex-1">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Selesai</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div class="relative inline-block text-left">
                        <button type="button" onclick="toggleDropdown()" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-semibold transition w-full justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div id="exportDropdown" class="hidden absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                            <div class="py-1">
                                <a href="{{ route('hr.laporan.export.detail-peserta', ['id' => $peserta->id, 'tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai, 'status_harian' => ($statusHarian ?? '')]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 flex items-center gap-3 transition">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <div>
                                        <div class="font-semibold">Export PDF</div>
                                        <div class="text-xs text-gray-500">Download sebagai PDF</div>
                                    </div>
                                </a>
                                <a href="{{ route('hr.laporan.export.detail-peserta-csv', ['id' => $peserta->id, 'tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai, 'status_harian' => ($statusHarian ?? '')]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 flex items-center gap-3 transition">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <div>
                                        <div class="font-semibold">Download CSV</div>
                                        <div class="text-xs text-gray-500">Format Excel</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
            <script>
            function toggleDropdown() {
                const dropdown = document.getElementById('exportDropdown');
                dropdown.classList.toggle('hidden');
            }
            
            // Close dropdown when clicking outside
            window.addEventListener('click', function(e) {
                const dropdown = document.getElementById('exportDropdown');
                const button = e.target.closest('button[onclick*="toggleDropdown"]');
                if (!button && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
            
            // Auto-copy tanggal_mulai ke tanggal_selesai untuk memudahkan filter 1 hari
            document.getElementById('tanggal_mulai').addEventListener('change', function() {
                document.getElementById('tanggal_selesai').value = this.value;
                document.getElementById('filterForm').submit();
            });
            
            // Auto-submit saat tanggal_selesai berubah
            document.getElementById('tanggal_selesai').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            // Auto-submit saat status absensi berubah
            document.getElementById('status_harian').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
            </script>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gray-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Total Record</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_hari'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Tepat Waktu</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['hadir_tepat'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Terlambat</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['hadir_telat'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Alpha</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['alpha'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18l12-12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Izin Tidak Masuk</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['izin_tidak_masuk'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Izin Pulang Cepat</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['izin_pulang_cepat'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Absensi List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($absensiList as $absensi)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->translatedFormat('l') }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->translatedFormat('d F Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($absensi->jam_masuk)
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-500">WIB</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($absensi->jam_pulang)
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-500">WIB</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $hasPersistedAbsensi = $absensi->exists && !empty($absensi->id);
                                        $canEditStatus = $absensi->status_harian !== 'LIBUR';
                                        $statusOptions = [
                                            'HADIR_TEPAT_WAKTU' => 'Tepat Waktu',
                                            'HADIR_TELAT' => 'Terlambat',
                                            'ALPHA' => 'Alpha',
                                            'IZIN_TIDAK_MASUK' => 'Izin Tidak Masuk',
                                            'IZIN_PULANG_CEPAT' => 'Izin Pulang Cepat',
                                        ];

                                        $isFutureDate = \Carbon\Carbon::parse($absensi->tanggal)->gt(\App\Services\TimeService::today());
                                        $hasDefinedStatus = !in_array($absensi->status_harian, ['-', 'BELUM_FINAL', null], true);
                                        if ($isFutureDate && $hasDefinedStatus) {
                                            $statusOptions = ['STATUS_AWAL' => 'Status Awal'] + $statusOptions;
                                        }
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        @if($absensi->status_harian == 'HADIR_TEPAT_WAKTU')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Tepat Waktu</span>
                                        @elseif($absensi->status_harian == 'HADIR_TELAT')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Terlambat</span>
                                        @elseif($absensi->status_harian == 'ALPHA')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Alpha</span>
                                        @elseif($absensi->status_harian == 'IZIN_TIDAK_MASUK')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Izin Tidak Masuk</span>
                                        @elseif($absensi->status_harian == 'IZIN_PULANG_CEPAT')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Izin Pulang Cepat</span>
                                        @elseif($absensi->status_harian == 'BELUM_FINAL')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">-</span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $absensi->status_harian }}</span>
                                        @endif
                                        @if($canEditStatus)
                                            <div class="relative inline-block text-left">
                                                <button type="button" onclick="toggleStatusDropdown(this)" class="inline-flex items-center px-2 py-1 border border-gray-300 rounded bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                                <div class="dropdown-status hidden absolute z-10 mt-1 w-52 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                                    <form action="{{ route('hr.laporan.absensi.update-status', ['absensi' => $hasPersistedAbsensi ? $absensi->id : 0]) }}" method="POST">
                                                        @csrf
                                                        @if(!$hasPersistedAbsensi)
                                                            <input type="hidden" name="user_id" value="{{ $peserta->id }}">
                                                            <input type="hidden" name="tanggal" value="{{ \Carbon\Carbon::parse($absensi->tanggal)->format('Y-m-d') }}">
                                                        @endif
                                                        @foreach($statusOptions as $key => $label)
                                                            @if($key !== 'LIBUR' && $key !== $absensi->status_harian)
                                                                <button type="submit" name="status_harian" value="{{ $key }}" class="block w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-blue-100">{{ $label }}</button>
                                                            @endif
                                                        @endforeach
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        @if($absensi->foto_masuk)
                                            <a href="{{ Storage::url($absensi->foto_masuk) }}" target="_blank" class="inline-flex items-center gap-1 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                Masuk
                                            </a>
                                        @endif
                                        @if($absensi->foto_pulang)
                                            <a href="{{ Storage::url($absensi->foto_pulang) }}" target="_blank" class="inline-flex items-center gap-1 text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                Pulang
                                            </a>
                                        @endif
                                        @if(!$absensi->foto_masuk && !$absensi->foto_pulang)
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($absensi->latitude_masuk && $absensi->longitude_masuk)
                                        <a href="https://www.google.com/maps?q={{ $absensi->latitude_masuk }},{{ $absensi->longitude_masuk }}" target="_blank" class="inline-flex items-center gap-1 text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Lihat Peta
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <p class="text-gray-600 text-lg">Tidak ada data absensi</p>
                                        <p class="text-gray-400 text-sm mt-1">Belum ada absensi untuk periode yang dipilih</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

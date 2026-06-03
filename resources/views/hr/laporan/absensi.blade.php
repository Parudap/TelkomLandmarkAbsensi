@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">            <a href="{{ route('hr.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Dashboard
            </a>            <h1 class="text-3xl font-bold text-gray-800">Absensi</h1>
            <p class="text-gray-600 mt-2">Laporan kehadiran peserta magang per periode</p>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Filter</h3>
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleDropdown()" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-semibold transition">
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
                            <a href="{{ route('hr.laporan.export-absensi', ['bidang_id' => $bidangId, 'tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai, 'status_harian' => request('status_harian')]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 flex items-center gap-3 transition">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold">Export PDF</div>
                                    <div class="text-xs text-gray-500">Download sebagai PDF</div>
                                </div>
                            </a>
                            <a href="{{ route('hr.laporan.export-absensi-csv', ['bidang_id' => $bidangId, 'tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai, 'status_harian' => request('status_harian')]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 flex items-center gap-3 transition">
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
            </script>
            <form id="filterForm" action="{{ route('hr.laporan.absensi') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="flex-1">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Bidang</label>
                        <select name="bidang_id" onchange="document.getElementById('filterForm').submit()" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Bidang</option>
                            @foreach($bidangList as $bidang)
                                <option value="{{ $bidang->id }}" {{ $bidangId == $bidang->id ? 'selected' : '' }}>
                                    {{ $bidang->nama_bidang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-1">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Status Absensi</label>
                        <select name="status_harian" onchange="document.getElementById('filterForm').submit()" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Status</option>
                            <option value="HADIR_TEPAT_WAKTU" {{ request('status_harian') == 'HADIR_TEPAT_WAKTU' ? 'selected' : '' }}>Tepat Waktu</option>
                            <option value="HADIR_TELAT" {{ request('status_harian') == 'HADIR_TELAT' ? 'selected' : '' }}>Terlambat</option>
                            <option value="ALPHA" {{ request('status_harian') == 'ALPHA' ? 'selected' : '' }}>Alpha</option>
                            <option value="IZIN_TIDAK_MASUK" {{ request('status_harian') == 'IZIN_TIDAK_MASUK' ? 'selected' : '' }}>Izin Tidak Masuk</option>
                            <option value="IZIN_PULANG_CEPAT" {{ request('status_harian') == 'IZIN_PULANG_CEPAT' ? 'selected' : '' }}>Izin Pulang Cepat</option>
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
                </div>
            </form>
            
            <script>
            // Auto-copy tanggal_mulai ke tanggal_selesai untuk memudahkan filter 1 hari
            document.getElementById('tanggal_mulai').addEventListener('change', function() {
                document.getElementById('tanggal_selesai').value = this.value;
                document.getElementById('filterForm').submit();
            });
            
            // Auto-submit saat tanggal_selesai berubah
            document.getElementById('tanggal_selesai').addEventListener('change', function() {
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Total Record</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_absensi'] }}</p>
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
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['tepat_waktu'] }}</p>
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
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['terlambat'] }}</p>
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

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Peserta</th>
                            <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bidang</th>
                            <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                            <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                            <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($absensiData as $absensi)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        @php
                                            $hariInggris = \Carbon\Carbon::parse($absensi->tanggal)->format('l');
                                            $hariIndonesia = match($hariInggris) {
                                                'Monday' => 'Senin',
                                                'Tuesday' => 'Selasa',
                                                'Wednesday' => 'Rabu',
                                                'Thursday' => 'Kamis',
                                                'Friday' => 'Jumat',
                                                'Saturday' => 'Sabtu',
                                                'Sunday' => 'Minggu',
                                                default => $hariInggris
                                            };
                                        @endphp
                                        {{ $hariIndonesia }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->translatedFormat('d F Y') }}
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $absensi->user->name }}</div>
                                    <div class="text-xs text-gray-600">{{ $absensi->user->email }}</div>
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $absensi->user->bidang->nama_bidang ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap">
                                    @if($absensi->jam_masuk)
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-500">WIB</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap">
                                    @if($absensi->jam_pulang)
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-500">WIB</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap">
                                    @php
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
                                    <div class="flex items-center gap-1">
                                        @if($absensi->status_harian == 'HADIR_TEPAT_WAKTU')
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 whitespace-nowrap">Tepat Waktu</span>
                                        @elseif($absensi->status_harian == 'HADIR_TELAT')
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 whitespace-nowrap">Terlambat</span>
                                        @elseif($absensi->status_harian == 'ALPHA')
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 whitespace-nowrap">Alpha</span>
                                        @elseif($absensi->status_harian == 'IZIN_TIDAK_MASUK')
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 whitespace-nowrap">Izin Tidak Masuk</span>
                                        @elseif($absensi->status_harian == 'IZIN_PULANG_CEPAT')
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 whitespace-nowrap">Izin Pulang Cepat</span>
                                        @elseif($absensi->status_harian == 'BELUM_FINAL')
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600 whitespace-nowrap">-</span>
                                        @else
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 whitespace-nowrap">{{ $absensi->status_harian }}</span>
                                        @endif
                                        @if($canEditStatus)
                                            <form action="{{ route('hr.laporan.absensi.update-status', $absensi->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <select name="status_harian" class="text-xs border border-gray-300 rounded px-1 py-1 w-8 focus:ring-2 focus:ring-blue-200 focus:border-blue-400" onchange="this.form.submit()">
                                                    <option value=""></option>
                                                    @foreach($statusOptions as $key => $label)
                                                        @if($key !== 'LIBUR' && $key !== $absensi->status_harian)
                                                            <option value="{{ $key }}">{{ $label }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2 py-4">
                                    <div class="flex gap-1 flex-wrap">
                                        @if($absensi->foto_masuk)
                                            <a href="{{ Storage::url($absensi->foto_masuk) }}" target="_blank" class="inline-flex items-center justify-center text-xs bg-blue-100 text-blue-700 p-2 rounded hover:bg-blue-200 transition" title="Foto Masuk">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        @if($absensi->foto_pulang)
                                            <a href="{{ Storage::url($absensi->foto_pulang) }}" target="_blank" class="inline-flex items-center justify-center text-xs bg-green-100 text-green-700 p-2 rounded hover:bg-green-200 transition" title="Foto Pulang">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        @if(!$absensi->foto_masuk && !$absensi->foto_pulang)
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2 py-4">
                                    @if($absensi->latitude_masuk && $absensi->longitude_masuk)
                                        <a href="https://www.google.com/maps?q={{ $absensi->latitude_masuk }},{{ $absensi->longitude_masuk }}" target="_blank" class="inline-flex items-center justify-center text-xs bg-red-100 text-red-700 p-2 rounded hover:bg-red-200 transition" title="Lihat Peta">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
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

@extends('layouts.app')

@section('title', 'Dashboard Peserta')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome Section -->

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2">
            Halo, {{ $user->name }}! <span class="text-2xl">👋</span>
        </h1>
        <p class="text-gray-600 mb-6">Selamat datang kembali di Dashboard Absensi Magang.</p>
        @if(session('error'))
        <div class="mb-8">
            <div class="flex items-center bg-red-100/90 rounded-2xl shadow-lg border-l-8 border-red-500 p-5">
                <div class="flex items-center justify-center w-12 h-12 bg-red-200 rounded-full mr-5 shadow-sm">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-red-700 text-lg mb-1 tracking-wide">Tidak Dapat Melakukan Absensi</div>
                    <div class="text-base text-red-700">{{ session('error') }}</div>
                </div>
            </div>
        </div>
        @endif

        <div class="w-full bg-gradient-to-r from-blue-50 via-green-50 to-red-50 rounded-2xl shadow flex flex-col md:flex-row items-stretch md:items-center gap-0 md:gap-6 border border-gray-100 overflow-hidden mb-2">
            <div class="flex items-center gap-3 flex-1 p-5">
                <div class="bg-blue-100 rounded-xl p-3 flex items-center justify-center">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7a2 2 0 012-2h2a2 2 0 012 2v10" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17h10" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Bidang Magang</div>
                    <div class="font-bold text-base text-blue-700">{{ $user->bidang ? $user->bidang->nama_bidang : '-' }}</div>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-1 p-5 border-t md:border-t-0 md:border-l border-gray-200">
                <div class="bg-green-100 rounded-xl p-3 flex items-center justify-center">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Tanggal Mulai Magang</div>
                    <div class="font-bold text-base text-green-700">
                        {{ isset($periodeMulai) ? \Carbon\Carbon::parse($periodeMulai)->translatedFormat('d F Y') : '-' }}
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-1 p-5 border-t md:border-t-0 md:border-l border-gray-200">
                <div class="bg-red-100 rounded-xl p-3 flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2h-6a2 2 0 00-2 2v10a2 2 0 002 2h6a2 2 0 002-2v-2" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 14l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Tanggal Akhir Magang</div>
                    <div class="font-bold text-base text-red-700">
                        {{ isset($periodeSelesai) ? \Carbon\Carbon::parse($periodeSelesai)->translatedFormat('d F Y') : '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Date & Time Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500 flex flex-col" style="min-height: 240px;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Waktu Sekarang</h3>
                <span class="p-2 bg-red-50 rounded-lg text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
            </div>
            <div class="flex-1 flex flex-col justify-center">
                <div class="text-3xl font-bold text-gray-900 mb-1" id="clock">{{ $currentTime }}</div>
                <p class="text-sm text-gray-500">{{ $currentDate }}</p>
            </div>
        </div>

        <!-- Attendance Status Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 flex flex-col" style="min-height: 240px;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Status Hari Ini</h3>
                <span class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </span>
            </div>
            <div class="flex-1 flex flex-col justify-between">
                @if($absensiToday)
                    <div>
                        <div class="mb-3">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($absensiToday->status_harian == 'HADIR_TEPAT_WAKTU') bg-green-100 text-green-800
                                @elseif($absensiToday->status_harian == 'HADIR_TELAT') bg-yellow-100 text-yellow-800
                                @elseif($absensiToday->status_harian == 'IZIN_TIDAK_MASUK') bg-blue-100 text-blue-800
                                @elseif($absensiToday->status_harian == 'IZIN_PULANG_CEPAT') bg-purple-100 text-purple-800
                                @elseif($absensiToday->status_harian == 'ALPHA') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ str_replace('_', ' ', $absensiToday->status_harian) }}
                            </span>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 mb-2">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Masuk</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $absensiToday->jam_masuk ? \Carbon\Carbon::parse($absensiToday->jam_masuk)->format('H:i') : '-' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500 mb-1">Pulang</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $absensiToday->jam_pulang ? \Carbon\Carbon::parse($absensiToday->jam_pulang)->format('H:i') : '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(!$absensiToday->jam_pulang)
                        @if($izinPulangCepatToday)
                        <div class="mb-2 bg-orange-50 border border-orange-200 rounded-lg p-2.5">
                            <p class="text-xs text-orange-800">
                                <span class="font-semibold">Izin Pulang Cepat:</span> Dapat absen pulang mulai jam <span class="font-semibold">{{ \Carbon\Carbon::parse($izinPulangCepatToday->jam_pulang_diajukan)->format('H:i') }}</span>
                            </p>
                        </div>
                        @endif
                        @if(!$hasIzinToday && $absensiToday->status_harian !== 'IZIN_TIDAK_MASUK')
                        <a href="{{ route('peserta.absensi.pulang') }}" class="w-full px-4 py-2.5 bg-green-500 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-600 transition text-center">Absen Pulang</a>
                        @endif
                    @endif
                @elseif(!$hasIzinToday)
                    <div class="flex-1 flex flex-col justify-center">
                        <p class="text-gray-500 text-center mb-4">BELUM FINAL</p>
                    </div>
                    <a href="{{ route('peserta.absensi.masuk') }}" class="w-full px-4 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-red-700 transition text-center">
                        Absen Masuk
                    </a>
                @else
                    <div class="flex-1 flex flex-col justify-center">
                        <div class="text-center">
                            <div class="mb-3">
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    IZIN TIDAK MASUK
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">{{ ucwords(str_replace('_', ' ', $hasIzinToday->jenis_izin)) }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Progress Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 flex flex-col" style="min-height: 240px;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Progress Magang</h3>
                <span class="p-2 bg-green-50 rounded-lg text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </span>
            </div>
            <div class="flex-1 flex flex-col justify-center">
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold py-1 px-2.5 uppercase rounded-full text-green-600 bg-green-200">
                            {{ $progressPersen }}%
                        </span>
                        <span class="text-xs font-semibold text-green-600">
                            {{ $hariKerjaBerlalu }}/{{ $totalHariKerja }} Hari
                        </span>
                    </div>
                    <div class="overflow-hidden h-3 text-xs flex rounded-lg bg-green-200">
                        <div style="width:{{ $progressPersen }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500 transition-all duration-500"></div>
                    </div>
                </div>
                <p class="text-sm text-gray-600 text-center bg-gray-50 rounded-lg py-2">
                    Sisa {{ $sisaHariKerja }} hari kerja lagi
                </p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Menu Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @if(!$hasIzinToday)
                @if(!$absensiToday)
                <a href="{{ route('peserta.absensi.masuk') }}" class="flex flex-col items-center justify-center p-4 bg-red-50 rounded-xl hover:bg-red-100 transition duration-300 border border-red-100 group">
                    <div class="p-3 bg-red-100 rounded-full mb-3 group-hover:bg-red-200 transition">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <span class="font-medium text-gray-900">Absen Masuk</span>
                </a>
                @elseif(!$absensiToday->jam_pulang && $absensiToday->status_harian !== 'IZIN_TIDAK_MASUK')
                <a href="{{ route('peserta.absensi.pulang') }}" class="flex flex-col items-center justify-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition duration-300 border border-green-100 group">
                    <div class="p-3 bg-green-100 rounded-full mb-3 group-hover:bg-green-200 transition">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <span class="font-medium text-gray-900">Absen Pulang</span>
                </a>
                @else
                <div class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-xl border border-gray-100 opacity-75 cursor-not-allowed">
                    <div class="p-3 bg-gray-100 rounded-full mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="font-medium text-gray-500">Sudah Absen</span>
                </div>
                @endif
            @endif


            <a href="{{ route('peserta.izin.create') }}" class="flex flex-col items-center justify-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition duration-300 border border-blue-100 group">
                <div class="p-3 bg-blue-100 rounded-full mb-3 group-hover:bg-blue-200 transition">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900">Ajukan Izin</span>
            </a>

            <a href="{{ route('peserta.izin.index') }}" class="flex flex-col items-center justify-center p-4 bg-cyan-50 rounded-xl hover:bg-cyan-100 transition duration-300 border border-cyan-100 group">
                <div class="p-3 bg-cyan-100 rounded-full mb-3 group-hover:bg-cyan-200 transition">
                    <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="font-medium text-gray-900">Lihat Izin</span>
            </a>

            <a href="{{ route('peserta.absensi.riwayat') }}" class="flex flex-col items-center justify-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition duration-300 border border-purple-100 group">
                <div class="p-3 bg-purple-100 rounded-full mb-3 group-hover:bg-purple-200 transition">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900">Riwayat Absensi</span>
            </a>
        </div>
    </div>

    <!-- Recent History -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Riwayat Absensi Terakhir</h3>
            <a href="{{ route('peserta.absensi.riwayat') }}" class="text-sm text-red-600 hover:text-red-700 font-medium">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($riwayatAbsensi as $log)
                    @if($log->status_harian !== '-')
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('d F Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $log->jam_masuk ? \Carbon\Carbon::parse($log->jam_masuk)->format('H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $log->jam_pulang ? \Carbon\Carbon::parse($log->jam_pulang)->format('H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($log->status_harian == 'HADIR_TEPAT_WAKTU') bg-green-100 text-green-800
                                @elseif($log->status_harian == 'HADIR_TELAT') bg-yellow-100 text-yellow-800
                                @elseif($log->status_harian == 'IZIN_TIDAK_MASUK') bg-blue-100 text-blue-800
                                @elseif($log->status_harian == 'IZIN_PULANG_CEPAT') bg-purple-100 text-purple-800
                                @elseif($log->status_harian == 'ALPHA') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ str_replace('_', ' ', $log->status_harian) }}
                            </span>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            Belum ada riwayat absensi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

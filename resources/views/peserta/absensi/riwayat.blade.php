@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('peserta.dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Riwayat Absensi</h1>
                <p class="text-gray-600 mt-2">Lihat rekap kehadiran Anda selama periode magang</p>
            </div>
        </div>
    </div>

    <!-- Statistik Cards dengan ikon -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <!-- Total Hadir -->
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Hadir</div>
                <div class="p-2 bg-green-50 rounded-lg">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['total_hadir'] }}</div>
            <div class="text-xs text-gray-500 mt-1">hari</div>
        </div>

        <!-- Tepat Waktu -->
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tepat Waktu</div>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-blue-600">{{ $stats['tepat_waktu'] }}</div>
            <div class="text-xs text-gray-500 mt-1">hari</div>
        </div>

        <!-- Terlambat -->
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Terlambat</div>
                <div class="p-2 bg-yellow-50 rounded-lg">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-yellow-600">{{ $stats['terlambat'] }}</div>
            <div class="text-xs text-gray-500 mt-1">hari</div>
        </div>

        <!-- Alpha -->
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Alpha</div>
                <div class="p-2 bg-red-50 rounded-lg">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-red-600">{{ $stats['alpha'] }}</div>
            <div class="text-xs text-gray-500 mt-1">hari</div>
        </div>

        <!-- Izin -->
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Izin</div>
                <div class="p-2 bg-purple-50 rounded-lg">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-purple-600">{{ ($stats['izin_tidak_masuk'] ?? 0) + ($stats['izin_pulang_cepat'] ?? 0) }}</div>
            <div class="text-xs text-gray-500 mt-1">hari</div>
        </div>
    </div>

    <!-- Filter Bulan -->
    @if(isset($monthsList) && count($monthsList) > 0)
    <div class="mb-6 bg-white rounded-xl shadow-sm p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-2 sm:space-y-0">
            <label for="month-filter" class="text-sm font-medium text-gray-700 whitespace-nowrap flex items-center">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filter Bulan:
            </label>
            <select id="month-filter" onchange="window.location.href=this.value" class="flex-1 sm:flex-none sm:w-64 pl-3 pr-10 py-2.5 text-sm border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-lg border bg-white shadow-sm">
                <option value="{{ route('peserta.absensi.riwayat') }}">Semua Bulan</option>
                @foreach($monthsList as $m)
                    <option value="{{ route('peserta.absensi.riwayat', ['bulan' => $m['month'], 'tahun' => $m['year']]) }}"
                        {{ (isset($filterBulan) && $filterBulan == $m['month'] && isset($filterTahun) && $filterTahun == $m['year']) ? 'selected' : '' }}>
                        {{ $m['label'] }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    @endif

    <!-- Tabel Riwayat -->
    <!-- Tabel Riwayat -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Detail Kehadiran</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam Pulang</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @php $lastMonth = null; @endphp
                    @forelse($absensiList as $absensi)
                    @php
                        $currentMonth = \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('F Y');
                    @endphp

                    @if($lastMonth !== $currentMonth)
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <td colspan="6" class="px-6 py-3 text-sm font-bold text-gray-800 uppercase tracking-wide">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $currentMonth }}
                            </div>
                        </td>
                    </tr>
                    @php $lastMonth = $currentMonth; @endphp
                    @endif

                    <tr class="hover:bg-blue-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('d F Y') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    {{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('l') }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($absensi->jam_masuk)
                                <div class="inline-flex items-center px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-sm font-medium">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') }}
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($absensi->jam_pulang)
                                <div class="inline-flex items-center px-2.5 py-1 rounded-md bg-green-50 text-green-700 text-sm font-medium">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') }}
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($absensi->durasi_kerja)
                                @php
                                    $hours = floor($absensi->durasi_kerja / 60);
                                    $minutes = $absensi->durasi_kerja % 60;
                                @endphp
                                <div class="inline-flex flex-col items-center px-3 py-1 rounded-md bg-gray-50 text-gray-700">
                                    <span class="text-sm font-bold">{{ $hours }}j {{ $minutes }}m</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($absensi->status_harian === 'HADIR_TEPAT_WAKTU')
                                <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-lg bg-green-100 text-green-800">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    HADIR
                                </span>
                            @elseif($absensi->status_harian === 'HADIR_TELAT')
                                <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-lg bg-yellow-100 text-yellow-800">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    TELAT
                                </span>
                            @elseif($absensi->status_harian == 'BELUM_FINAL')
                                @if(
                                    \Carbon\Carbon::parse($absensi->tanggal)->isSameDay(
                                        \App\Services\TimeService::today()
                                    )
                                )
                                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-lg bg-orange-100 text-orange-800">
                                        <svg class="w-3.5 h-3.5 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        MENUNGGU
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-100 text-gray-600">
                                        -
                                    </span>
                                @endif
                            @elseif($absensi->status_harian == 'ALPHA')
                                <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-lg bg-red-100 text-red-800">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    ALPHA
                                </span>
                            @elseif(in_array($absensi->status_harian, ['IZIN_TIDAK_MASUK','IZIN_PULANG_CEPAT','IZIN']))
                                <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-lg bg-purple-100 text-purple-800">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                    </svg>
                                    IZIN
                                </span>
                            @elseif($absensi->status_harian == 'LIBUR')
                                <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-100 text-gray-800">
                                    LIBUR
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($absensi->status_harian == 'BELUM_FINAL')
                                @if(
                                    \Carbon\Carbon::parse($absensi->tanggal)->isSameDay(
                                        \App\Services\TimeService::today()
                                    )
                                )
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 mr-1.5 mt-0.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span>
                                            @if($absensi->status_masuk == 'TEPAT_WAKTU')
                                                Masuk tepat waktu, belum absen pulang
                                            @else
                                                Masuk terlambat, belum absen pulang
                                            @endif
                                        </span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            @elseif($absensi->status_harian == 'HADIR_TEPAT_WAKTU')
                                <span class="text-green-700">Hadir tepat waktu</span>
                            @elseif($absensi->status_harian == 'HADIR_TELAT')
                                <span class="text-yellow-700">Hadir terlambat</span>
                            @elseif($absensi->status_harian == 'ALPHA')
                                <span class="text-red-700">Tidak hadir</span>
                            @elseif($absensi->status_harian == 'IZIN_PULANG_CEPAT' || ($absensi->catatan_sistem && str_contains(strtolower($absensi->catatan_sistem), 'izin pulang cepat')))
                                <span class="text-purple-700">Izin pulang cepat 
                                    @if($absensi->jam_pulang)
                                        ({{ \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') }})
                                    @endif
                                </span>
                            @elseif($absensi->status_harian == 'IZIN_TIDAK_MASUK' || ($absensi->catatan_sistem && str_contains(strtolower($absensi->catatan_sistem), 'izin tidak masuk')))
                                <span class="text-purple-700">Izin tidak masuk</span>
                            @elseif($absensi->catatan_sistem && !in_array($absensi->status_harian, ['ALPHA']))
                                <span class="text-gray-600">{{ $absensi->catatan_sistem }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 text-lg font-medium mb-1">Belum Ada Riwayat Absensi</p>
                                <p class="text-gray-400 text-sm">Data absensi Anda akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($absensiList->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $absensiList->links() }}
        </div>
        @endif
    </div>

    <!-- Total Records -->
    <div class="mt-8 text-right">
        <div class="text-sm text-gray-500">
            Total: <span class="font-semibold text-gray-900">{{ $absensiList->total() }}</span> record
        </div>
    </div>
</div>
@endsection

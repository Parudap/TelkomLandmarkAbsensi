@extends('layouts.app')

@section('title', 'Dashboard Admin/HR')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard HR/Admin</h1>
        <p class="text-gray-600 mt-2">Sistem Manajemen Absensi Peserta Magang</p>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Peserta -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 relative">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">Total Peserta</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_peserta'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3 relative">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Peserta Aktif -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500 relative">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">Peserta Aktif</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['peserta_aktif'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3 relative">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Hadir Hari Ini -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500 relative">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">Hadir Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['hadir_today'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3 relative">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Izin Pending -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500 relative">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">Menunggu Persetujuan Izin</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['izin_pending'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3 relative">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Per Bidang -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Statistik Per Bidang</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bidang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Peserta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta Aktif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hadir Hari Ini</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menunggu Persetujuan Izin</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($statsBidang as $bidang)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $bidang->nama_bidang }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $bidang->total_peserta }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $bidang->peserta_aktif }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $bidang->hadir_today }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $bidang->izin_pending }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mt-8 mb-8">
        <!-- Tambah Akun Peserta -->
        <a href="{{ route('hr.peserta.create') }}" class="group relative h-full min-h-[120px] overflow-hidden bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105">
            <div class="relative z-10 flex h-full flex-col pr-16">
                <h3 class="text-xl font-bold text-white mb-1">Tambah Akun Peserta</h3>
                <p class="text-blue-100 text-sm leading-snug">Buat akun baru untuk peserta magang</p>
            </div>
            <div class="absolute top-4 right-4 opacity-20 group-hover:opacity-30 transition-opacity">
                <!-- User Check Icon -->
                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25v-1.5A4.75 4.75 0 019.25 14h1.5a4.75 4.75 0 014.75 4.75v1.5" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l2 2 4-4" />
                </svg>
            </div>
        </a>

        <!-- Absensi -->
        <a href="{{ route('hr.laporan.absensi') }}" class="group relative h-full min-h-[120px] overflow-hidden bg-gradient-to-br from-green-500 to-green-700 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105">
            <div class="relative z-10 flex h-full flex-col pr-16">
                <h3 class="text-xl font-bold text-white mb-1">Absensi</h3>
                <p class="text-green-100 text-sm leading-snug">Lihat dan kelola data kehadiran peserta</p>
            </div>
            <div class="absolute top-4 right-4 opacity-20 group-hover:opacity-30 transition-opacity">
                <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </a>

        <!-- Data Peserta -->
        <a href="{{ route('hr.laporan.peserta') }}" class="group relative h-full min-h-[120px] overflow-hidden bg-gradient-to-br from-cyan-500 to-blue-500 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105">
            <div class="relative z-10 flex h-full flex-col pr-16">
                <h3 class="text-xl font-bold text-white mb-1">Data Peserta</h3>
                <p class="text-blue-100 text-sm leading-snug">Lihat dan kelola data peserta magang</p>
            </div>
            <div class="absolute top-4 right-4 opacity-20 group-hover:opacity-30 transition-opacity">
                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25v-1.5A4.75 4.75 0 019.25 14h1.5a4.75 4.75 0 014.75 4.75v1.5" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l2 2 4-4" />
                </svg>
            </div>
        </a>
        <!-- Izin -->
        <a href="{{ route('hr.izin.index') }}" class="group relative h-full min-h-[120px] overflow-hidden bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105">
            <div class="relative z-10 flex h-full flex-col pr-16">
                <h3 class="text-xl font-bold text-white mb-1">Izin</h3>
                <p class="text-purple-100 text-sm leading-snug">Approve dan review izin peserta magang</p>
            </div>
            <div class="absolute top-4 right-4 opacity-20 group-hover:opacity-30 transition-opacity">
                <!-- Calendar Check Icon -->
                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <rect x="3" y="5" width="18" height="16" rx="2" stroke-linecap="round" stroke-linejoin="round" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 3v4M8 3v4M3 9h18" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 16l2 2 4-4" />
                </svg>
            </div>
        </a>
    </div>

</div>
@endsection

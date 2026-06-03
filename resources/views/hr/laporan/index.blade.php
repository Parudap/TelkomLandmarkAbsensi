@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('hr.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Laporan</h1>
            <p class="text-gray-600 mt-2">Pilih jenis laporan yang ingin dilihat</p>
        </div>

        <!-- Report Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Absensi -->
            <a href="{{ route('hr.laporan.absensi') }}" class="block group">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-8 hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                    <div class="flex flex-col items-center text-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-4 mb-4 group-hover:bg-opacity-30 transition">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">Absensi</h3>
                        <p class="text-blue-100">Lihat data kehadiran peserta magang</p>
                    </div>
                </div>
            </a>

        </div>

        <!-- Info Box -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-blue-800 font-semibold mb-2">Informasi Laporan</h4>
                    <ul class="text-blue-700 text-sm space-y-1">
                        <li>• <strong>Absensi:</strong> Menampilkan data kehadiran peserta dengan filter bidang, bulan, dan tahun</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

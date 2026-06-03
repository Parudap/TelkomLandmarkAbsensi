@extends('layouts.app')

@section('title', 'Detail Izin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('hr.izin.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke List
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Detail Izin</h1>
            <p class="text-gray-600 mt-2">Review detail izin dan ambil keputusan approval</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $izin->user->name }}</h2>
                        <p class="text-blue-100 mt-1">{{ $izin->user->email }}</p>
                        <div class="mt-3">
                            <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm font-semibold">
                                {{ $izin->user->bidang->nama_bidang ?? 'Tidak Ada Bidang' }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($izin->status_approval == 'pending')
                            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold inline-block">
                                Menunggu Persetujuan
                            </span>
                        @elseif($izin->status_approval == 'approved_hr')
                            <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold inline-block">
                                Disetujui
                            </span>
                        @elseif($izin->status_approval == 'rejected_hr')
                            <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-semibold inline-block">
                                Ditolak
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detail Section -->
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Izin</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-600 text-sm font-semibold mb-1">Jenis Izin</label>
                        <p class="text-gray-900">
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $izin->jenis_izin == 'tidak_masuk' ? 'bg-purple-100 text-purple-800' : 'bg-cyan-100 text-cyan-800' }}">
                                {{ $izin->jenis_izin == 'tidak_masuk' ? 'Tidak Masuk' : 'Pulang Cepat' }}
                            </span>
                        </p>
                    </div>
                    @if($izin->jenis_izin == 'tidak_masuk')
                        <div>
                            <label class="block text-gray-600 text-sm font-semibold mb-1">Periode Izin</label>
                            <p class="text-gray-900 font-semibold">
                                {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->isoFormat('D MMMM Y') }}
                                s/d
                                {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->isoFormat('D MMMM Y') }}
                            </p>
                        </div>
                    @else
                        <div>
                            <label class="block text-gray-600 text-sm font-semibold mb-1">Tanggal</label>
                            <p class="text-gray-900 font-semibold">{{ \Carbon\Carbon::parse($izin->tanggal)->isoFormat('dddd, D MMMM Y') }}</p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-gray-600 text-sm font-semibold mb-1">Periode Magang</label>
                        <p class="text-gray-900 font-semibold">
                            {{ $izin->user->periode_magang_mulai ? \Carbon\Carbon::parse($izin->user->periode_magang_mulai)->isoFormat('D MMMM Y') : '-' }}
                            s/d
                            {{ $izin->user->periode_magang_selesai ? \Carbon\Carbon::parse($izin->user->periode_magang_selesai)->isoFormat('D MMMM Y') : '-' }}
                        </p>
                    </div>
                    @if($izin->jenis_izin == 'pulang_cepat' && $izin->jam_pulang_diajukan)
                        <div>
                            <label class="block text-gray-600 text-sm font-semibold mb-1">Jam Pulang Diajukan</label>
                            <p class="text-gray-900 font-semibold">{{ $izin->jam_pulang_diajukan }}</p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-gray-600 text-sm font-semibold mb-1">Tanggal Pengajuan</label>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($izin->created_at)->isoFormat('D MMMM Y, HH:mm') }} WIB</p>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-semibold mb-1">Auto Approve</label>
                        @if($izin->status_approval == 'pending')
                            @php
                                $autoApproveTime = \Carbon\Carbon::parse($izin->created_at)->addHours(24);
                            @endphp
                            <p class="text-yellow-600 font-semibold">{{ $autoApproveTime->isoFormat('D MMMM Y, HH:mm') }} WIB</p>
                        @elseif($izin->auto_approved_at)
                            <p class="text-green-600 font-semibold">{{ \Carbon\Carbon::parse($izin->auto_approved_at)->isoFormat('D MMMM Y, HH:mm') }} WIB</p>
                            <span class="text-xs text-green-600 font-semibold">✓ Otomatis</span>
                        @else
                            <p class="text-gray-500 font-semibold">Manual oleh Admin</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Alasan Section -->
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Alasan</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-800 leading-relaxed">{{ $izin->alasan }}</p>
                </div>
            </div>

            <!-- Bukti File Section -->
            @if($izin->bukti_file)
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Bukti Pendukung</h3>
                    <a href="{{ asset('storage/' . $izin->bukti_file) }}" target="_blank" class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-3 rounded-lg hover:bg-blue-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-semibold">Lihat Bukti File</span>
                    </a>
                </div>
            @endif

            <!-- Approval History Section -->
            <div class="p-6 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Approval</h3>
                <div class="space-y-4">
                    <!-- HR Approval -->
                    @if($izin->approved_at_hr)
                        <div class="flex items-start gap-4 bg-white p-4 rounded-lg border-l-4 {{ $izin->status_approval == 'approved_hr' ? 'border-green-500' : 'border-red-500' }}">
                            <div class="flex-shrink-0 {{ $izin->status_approval == 'approved_hr' ? 'bg-green-100' : 'bg-red-100' }} rounded-full p-2">
                                @if($izin->status_approval == 'approved_hr')
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">
                                    {{ $izin->status_approval == 'approved_hr' ? 'Disetujui' : 'Ditolak' }}
                                </p>
                                <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($izin->approved_at_hr)->isoFormat('D MMMM Y, HH:mm') }}</p>
                                @if($izin->status_approval == 'rejected_hr' && $izin->keterangan_hr)
                                    <div class="mt-2 bg-gray-50 p-3 rounded">
                                        <p class="text-sm text-gray-700"><span class="font-semibold">Keterangan:</span> {{ $izin->keterangan_hr }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-4 bg-white p-4 rounded-lg border-l-4 border-yellow-500">
                            <div class="flex-shrink-0 bg-yellow-100 rounded-full p-2">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">Menunggu Persetujuan</p>
                                <p class="text-sm text-gray-600">Belum diproses</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons (Only show if pending HR approval) -->
        @if($izin->status_approval == 'pending')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Approve Form -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-green-700 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Setujui Izin
                    </h3>
                    <form action="{{ route('hr.izin.approve', $izin) }}" method="POST" onsubmit="return confirm('Yakin ingin menyetujui izin ini?')">
                        @csrf
                        <input type="hidden" name="keterangan" value="Disetujui">
                        <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-semibold transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Setujui
                        </button>
                    </form>
                </div>

                <!-- Reject Form -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-red-700 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tolak Izin
                    </h3>
                    <form action="{{ route('hr.izin.reject', $izin) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak izin ini?')">
                        @csrf
                        <input type="hidden" name="keterangan" value="Ditolak">
                        <button type="submit" class="w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-semibold transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Tolak
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

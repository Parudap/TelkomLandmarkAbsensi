@extends('layouts.app')

@section('title', 'Detail Izin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('peserta.izin.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Kembali ke Daftar Izin
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Detail Pengajuan Izin</h1>
        </div>

        <!-- Alert -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Card Informasi Izin -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <span class="px-4 py-2 rounded-full text-sm font-semibold
                            {{ $izin->jenis_izin == 'tidak_masuk' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $izin->jenis_izin == 'tidak_masuk' ? 'Izin Tidak Masuk' : 'Izin Pulang Cepat' }}
                        </span>
                        <span class="px-4 py-2 rounded-full text-sm font-semibold
                            @php
                                $status = $izin->status_approval;
                                if ($status == 'pending') {
                                    echo 'bg-yellow-100 text-yellow-800';
                                } elseif ($status == 'approved_hr') {
                                    echo 'bg-green-100 text-green-800';
                                } elseif ($status == 'rejected_hr') {
                                    echo 'bg-red-100 text-red-800';
                                } else {
                                    echo 'bg-gray-100 text-gray-800';
                                }
                            @endphp">
                            @php
                                if ($status == 'pending') {
                                    echo 'Menunggu Persetujuan';
                                } elseif ($status == 'approved_hr') {
                                    echo 'Disetujui';
                                } elseif ($status == 'rejected_hr') {
                                    echo 'Ditolak';
                                } else {
                                    echo ucfirst($status);
                                }
                            @endphp
                        </span>
                    </div>
                </div>
                
                @if($izin->status_approval == 'pending')
                    <form action="{{ route('peserta.izin.destroy', $izin) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan izin ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 text-sm font-semibold transition">
                            Batalkan Izin
                        </button>
                    </form>
                @endif
            </div>

            <!-- Detail Informasi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Nama Pemohon</label>
                    <p class="text-gray-800 font-medium">{{ $izin->user->name }}</p>
                </div>
                
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Bidang</label>
                    <p class="text-gray-800 font-medium">{{ $izin->user->bidang->nama_bidang ?? '-' }}</p>
                </div>
                
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Jenis Izin</label>
                    <p class="text-gray-800 font-medium">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $izin->jenis_izin == 'tidak_masuk' ? 'bg-purple-100 text-purple-800' : 'bg-cyan-100 text-cyan-800' }}">
                            {{ $izin->jenis_izin == 'tidak_masuk' ? 'Tidak Masuk' : 'Pulang Cepat' }}
                        </span>
                    </p>
                </div>
                
                @if($izin->jenis_izin == 'tidak_masuk')
                    <div>
                        <label class="block text-gray-600 text-sm font-semibold mb-1">Periode Izin</label>
                        <p class="text-gray-800 font-medium">
                            {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M Y') }} 
                            s/d 
                            {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M Y') }}
                        </p>
                    </div>
                @else
                    <div>
                        <label class="block text-gray-600 text-sm font-semibold mb-1">Tanggal</label>
                        <p class="text-gray-800 font-medium">{{ \Carbon\Carbon::parse($izin->tanggal ?: $izin->tanggal_mulai)->format('d F Y') }}</p>
                    </div>
                    
                    @if($izin->jam_pulang_diajukan)
                        <div>
                            <label class="block text-gray-600 text-sm font-semibold mb-1">Jam Pulang Diajukan</label>
                            <p class="text-gray-800 font-medium">{{ $izin->jam_pulang_diajukan }} WIB</p>
                        </div>
                    @endif
                @endif
                
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Periode Magang</label>
                    <p class="text-gray-800 font-medium">
                        {{ $izin->user->periode_magang_mulai ? \Carbon\Carbon::parse($izin->user->periode_magang_mulai)->format('d M Y') : '-' }}
                        s/d
                        {{ $izin->user->periode_magang_selesai ? \Carbon\Carbon::parse($izin->user->periode_magang_selesai)->format('d M Y') : '-' }}
                    </p>
                </div>
                
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Tanggal Pengajuan</label>
                    <p class="text-gray-800 font-medium">{{ \Carbon\Carbon::parse($izin->created_at)->format('d F Y, H:i') }} WIB</p>
                </div>
            </div>

            <!-- Alasan -->
            <div class="mb-6">
                <label class="block text-gray-600 text-sm font-semibold mb-2">Alasan</label>
                <div class="bg-gray-50 rounded-lg p-4 border">
                    <p class="text-gray-800 whitespace-pre-wrap">{{ $izin->alasan }}</p>
                </div>
            </div>

            <!-- Bukti File -->
            @if($izin->bukti_file)
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-2">Bukti Pendukung</label>
                    <a href="{{ Storage::url($izin->bukti_file) }}" target="_blank" class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Lihat Bukti
                    </a>
                </div>
            @endif
        </div>

        <!-- Progress Approval -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Progress Persetujuan</h2>
            
            <div class="space-y-6">
                <!-- HR/Admin -->
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl
                            {{ $izin->status_approval == 'approved_hr' ? 'bg-green-500 text-white' : 
                               ($izin->status_approval == 'rejected_hr' ? 'bg-red-500 text-white' : 'bg-yellow-300 text-gray-700') }}">
                            {{ $izin->status_approval == 'approved_hr' ? '✓' : 
                               ($izin->status_approval == 'rejected_hr' ? '✗' : '⏳') }}
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-800">HR/Admin</h3>
                        <p class="text-sm text-gray-600">
                            Status: 
                            <span class="font-semibold
                                {{ $izin->status_approval == 'approved_hr' ? 'text-green-600' : 
                                   ($izin->status_approval == 'rejected_hr' ? 'text-red-600' : 'text-yellow-600') }}">
                                {{ $izin->status_approval == 'pending' ? 'Menunggu Persetujuan' : 
                                   ($izin->status_approval == 'approved_hr' ? 'Disetujui' : 'Ditolak') }}
                            </span>
                        </p>
                        @if($izin->approved_at_hr)
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($izin->approved_at_hr)->format('d M Y, H:i') }} WIB</p>
                        @endif
                        @if($izin->keterangan_hr)
                            <div class="mt-2 bg-gray-50 rounded p-3 text-sm text-gray-700">
                                <strong>Catatan:</strong> {{ $izin->keterangan_hr }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

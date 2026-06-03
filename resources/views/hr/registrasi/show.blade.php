@extends('layouts.app')

@section('title', 'Detail Pendaftar')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('hr.registrasi.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Kembali ke Daftar Pendaftar
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Detail Pendaftar</h1>
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

        <!-- Status Badge -->
        <div class="mb-6">
            <span class="px-4 py-2 rounded-full text-sm font-semibold
                {{ $user->status_approval == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                   ($user->status_approval == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                Status: {{ $user->status_approval == 'pending' ? 'Menunggu Persetujuan' : 
                          ($user->status_approval == 'approved' ? 'Disetujui' : 'Ditolak') }}
            </span>
        </div>

        <!-- Data Pendaftar -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Data Pribadi</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Nama Lengkap</label>
                    <p class="text-gray-800 font-medium">{{ $user->name }}</p>
                </div>
                
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Email</label>
                    <p class="text-gray-800 font-medium">{{ $user->email }}</p>
                </div>
                
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">No. Telepon</label>
                    <p class="text-gray-800 font-medium">{{ $user->no_telepon ?? '-' }}</p>
                </div>
                
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Tanggal Lahir</label>
                    <p class="text-gray-800 font-medium">
                        {{ $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('d F Y') : '-' }}
                    </p>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Alamat</label>
                    <p class="text-gray-800 font-medium">{{ $user->alamat ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Data Magang -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Data Magang</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Instansi Asal</label>
                    <p class="text-gray-800 font-medium">{{ $user->instansi_asal ?? '-' }}</p>
                </div>
                
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Bidang yang Dipilih</label>
                    <p class="text-gray-800 font-medium">
                        <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                            {{ $user->bidang->nama_bidang ?? '-' }}
                        </span>
                    </p>
                </div>
                
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Periode Magang Mulai</label>
                    <p class="text-gray-800 font-medium">
                        {{ $user->periode_magang_mulai ? \Carbon\Carbon::parse($user->periode_magang_mulai)->format('d F Y') : '-' }}
                    </p>
                </div>
                
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Periode Magang Selesai</label>
                    <p class="text-gray-800 font-medium">
                        {{ $user->periode_magang_selesai ? \Carbon\Carbon::parse($user->periode_magang_selesai)->format('d F Y') : '-' }}
                    </p>
                </div>
                
                {{-- Durasi magang dihapus sesuai permintaan --}}
                
                @if($user->surat_magang)
                    <div class="md:col-span-2">
                        <label class="block text-gray-600 text-sm font-semibold mb-2">Surat Magang</label>
                        <a href="{{ Storage::url($user->surat_magang) }}" target="_blank" class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Lihat Surat Magang
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Informasi Tambahan -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Informasi Pendaftaran</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Tanggal Pendaftaran</label>
                    <p class="text-gray-800 font-medium">{{ \Carbon\Carbon::parse($user->created_at)->format('d F Y, H:i') }} WIB</p>
                </div>
                
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Email Terverifikasi</label>
                    <p class="text-gray-800 font-medium">
                        @if($user->email_verified_at)
                            <span class="text-green-600">✓ Ya ({{ \Carbon\Carbon::parse($user->email_verified_at)->format('d M Y') }})</span>
                        @else
                            <span class="text-red-600">✗ Belum</span>
                        @endif
                    </p>
                </div>
                
                @if($user->approved_at)
                    <div>
                        <label class="block text-gray-600 text-sm font-semibold mb-1">Tanggal Diproses</label>
                        <p class="text-gray-800 font-medium">{{ \Carbon\Carbon::parse($user->approved_at)->format('d F Y, H:i') }} WIB</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Form Approval (hanya jika pending) -->
        @if($user->status_approval == 'pending')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Approve -->
                <div class="bg-green-50 rounded-lg shadow-md p-6 border border-green-200">
                    <h3 class="text-lg font-bold text-green-800 mb-4">Setujui Pendaftaran</h3>
                    <form action="{{ route('hr.registrasi.approve', $user) }}" method="POST" onsubmit="return confirm('Yakin ingin menyetujui pendaftaran ini?')">
                        @csrf
                        <input type="hidden" name="keterangan" value="Disetujui oleh HR">
                        <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-semibold transition">
                            ✓ Setujui Pendaftaran
                        </button>
                    </form>
                </div>

                <!-- Reject -->
                <div class="bg-red-50 rounded-lg shadow-md p-6 border border-red-200">
                    <h3 class="text-lg font-bold text-red-800 mb-4">Tolak Pendaftaran</h3>
                    <form action="{{ route('hr.registrasi.reject', $user) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak pendaftaran ini?')">
                        @csrf
                        <input type="hidden" name="keterangan" value="Ditolak oleh HR">
                        <button type="submit" class="w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-semibold transition">
                            ✗ Tolak Pendaftaran
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="bg-gray-50 rounded-lg shadow-md p-6 text-center">
                <p class="text-gray-600">
                    Pendaftaran ini sudah 
                    <span class="font-semibold {{ $user->status_approval == 'approved' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $user->status_approval == 'approved' ? 'DISETUJUI' : 'DITOLAK' }}
                    </span>
                </p>
                @if($user->status_approval == 'rejected')
                    <form action="{{ route('hr.registrasi.destroy', $user) }}" method="POST" class="mt-4" onsubmit="return confirm('Yakin ingin menghapus data pendaftar ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 font-semibold transition">
                            Hapus Data Pendaftar
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

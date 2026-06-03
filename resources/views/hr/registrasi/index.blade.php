@extends('layouts.app')

@section('title', 'Approval Registrasi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('hr.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Approval Registrasi Peserta Magang</h1>
            <p class="text-gray-600 mt-2">Kelola persetujuan pendaftaran peserta magang baru</p>
        </div>

        <!-- Alerts are shown globally in the layout to avoid duplicates -->

        <!-- Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-700 text-sm font-semibold">Menunggu Approval</p>
                        <p class="text-3xl font-bold text-yellow-800">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="text-yellow-500 text-4xl">⏳</div>
                </div>
            </div>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-700 text-sm font-semibold">Disetujui</p>
                        <p class="text-3xl font-bold text-green-800">{{ $stats['approved'] }}</p>
                    </div>
                    <div class="text-green-500 text-4xl">✓</div>
                </div>
            </div>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-700 text-sm font-semibold">Ditolak</p>
                        <p class="text-3xl font-bold text-red-800">{{ $stats['rejected'] }}</p>
                    </div>
                    <div class="text-red-500 text-4xl">✗</div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <form method="GET" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-gray-700 font-semibold mb-2">Filter Status Approval</label>
                    <select name="status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-gray-700 font-semibold mb-2">Filter Status Aktif</label>
                    <select name="is_active" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold transition">
                    Filter
                </button>
                <a href="{{ route('hr.registrasi.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 font-semibold transition">
                    Reset
                </a>
            </form>
        </div>

        <!-- Daftar Pendaftar -->
        @if($pendaftarList->count() > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bidang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode Magang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktif</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendaftarList as $pendaftar)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $pendaftar->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $pendaftar->instansi_asal }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $pendaftar->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $pendaftar->no_telepon }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $pendaftar->bidang->nama_bidang ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($pendaftar->periode_magang_mulai && $pendaftar->periode_magang_selesai)
                                        {{ \Carbon\Carbon::parse($pendaftar->periode_magang_mulai)->format('d M Y') }}
                                        <br>s/d<br>
                                        {{ \Carbon\Carbon::parse($pendaftar->periode_magang_selesai)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $pendaftar->status_approval == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($pendaftar->status_approval == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $pendaftar->status_approval == 'pending' ? 'Menunggu' : 
                                           ($pendaftar->status_approval == 'approved' ? 'Disetujui' : 'Ditolak') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $pendaftar->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600' }}">
                                        {{ $pendaftar->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($pendaftar->created_at)->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('hr.registrasi.show', $pendaftar) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        Detail
                                    </a>
                                    @if($pendaftar->status_approval == 'rejected')
                                        <form action="{{ route('hr.registrasi.destroy', $pendaftar) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $pendaftarList->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <div class="text-gray-400 text-6xl mb-4">📋</div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Data</h3>
                <p class="text-gray-600">
                    @if(request('status'))
                        Tidak ada pendaftar dengan status "{{ request('status') }}"
                    @else
                        Belum ada pendaftar peserta magang
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Izin Saya')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('peserta.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Kembali ke Dashboard
            </a>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Izin Saya</h1>
                    <p class="text-gray-600 mt-2">Daftar pengajuan izin yang telah Anda buat</p>
                </div>
                <a href="{{ route('peserta.izin.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold transition">
                    + Ajukan Izin Baru
                </a>
            </div>
        </div>

        <!-- Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-700 text-sm font-semibold">Menunggu</p>
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
                    <label class="block text-gray-700 font-semibold mb-2">Filter Status</label>
                    <select name="status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold transition">
                    Filter
                </button>
                <a href="{{ route('peserta.izin.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 font-semibold transition">
                    Reset
                </a>
            </form>
        </div>

        <!-- Daftar Izin -->
        @if($izinList->count() > 0)
            <div class="space-y-4">
                @foreach($izinList as $izin)
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                                        {{ $izin->jenis_izin == 'tidak_masuk' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $izin->jenis_izin == 'tidak_masuk' ? 'Tidak Masuk' : 'Pulang Cepat' }}
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold
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
                                                echo 'Menunggu';
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
                                
                                <div class="text-gray-700 mb-2">
                                    <strong>Periode:</strong> 
                                    @if($izin->jenis_izin == 'pulang_cepat')
                                        {{ \Carbon\Carbon::parse($izin->tanggal ?: $izin->tanggal_mulai)->format('d M Y') }}
                                        @if($izin->jam_pulang_diajukan)
                                            - Pulang jam {{ $izin->jam_pulang_diajukan }}
                                        @endif
                                    @else
                                        {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M Y') }} 
                                        s/d 
                                        {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M Y') }}
                                    @endif
                                </div>
                                
                                <div class="text-gray-600">
                                    <strong>Alasan:</strong> {{ Str::limit($izin->alasan, 100) }}
                                </div>
                                
                                <div class="text-sm text-gray-500 mt-2">
                                    Diajukan: {{ \Carbon\Carbon::parse($izin->created_at)->format('d M Y H:i') }}
                                </div>
                            </div>
                            
                            <div class="flex gap-2 ml-4">
                                <a href="{{ route('peserta.izin.show', $izin) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-sm font-semibold transition">
                                    Detail
                                </a>
                                
                                @if($izin->status_approval == 'pending')
                                    <form action="{{ route('peserta.izin.destroy', $izin) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan izin ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 text-sm font-semibold transition">
                                            Batalkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-4 pt-4 border-t">
                            <div class="flex items-center justify-center">
                                <div class="text-center">
                                    <div class="w-10 h-10 rounded-full mx-auto mb-2 flex items-center justify-center text-lg
                                        {{ $izin->status_approval == 'approved_hr' ? 'bg-green-500 text-white' : 
                                           ($izin->status_approval == 'rejected_hr' ? 'bg-red-500 text-white' : 'bg-yellow-300 text-gray-700') }}">
                                        @if($izin->status_approval == 'approved_hr')
                                            ✓
                                        @elseif($izin->status_approval == 'rejected_hr')
                                            ✗
                                        @else
                                            ⏳
                                        @endif
                                    </div>
                                    <div class="text-gray-700 font-semibold text-sm">HR/Admin</div>
                                    <div class="text-gray-500 text-xs">
                                        @if($izin->status_approval == 'pending')
                                            Menunggu Persetujuan
                                        @elseif($izin->status_approval == 'approved_hr')
                                            Disetujui
                                        @elseif($izin->status_approval == 'rejected_hr')
                                            Ditolak
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $izinList->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <div class="text-gray-400 text-6xl mb-4">📋</div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Izin</h3>
                <p class="text-gray-600 mb-6">Anda belum pernah mengajukan izin.</p>
                <a href="{{ route('peserta.izin.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold transition">
                    Ajukan Izin Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

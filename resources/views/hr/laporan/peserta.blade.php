@extends('layouts.app')

@section('title', 'Data Peserta Magang')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('hr.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Data Peserta Magang</h1>
            <p class="text-gray-600 mt-2">Monitor seluruh peserta magang dan absensi mereka</p>
            
        <!-- Selalu tampilkan filter bidang dan summary cards, tidak perlu blok if -->
        </div>
        <!-- Filter -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-end justify-between gap-4">
                <form id="filterForm" action="{{ route('hr.laporan.peserta') }}" method="GET" class="flex gap-4 items-end flex-wrap flex-1">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Cari Nama/Email</label>
                        <input id="searchNameInput" type="text" name="search_name" placeholder="Cari nama atau email..." value="{{ $searchName ?? '' }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Filter Bidang</label>
                        <select id="bidangFilter" name="bidang_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Bidang</option>
                            @foreach($bidangList as $bidang)
                                <option value="{{ $bidang->id }}" {{ $bidangId == $bidang->id ? 'selected' : '' }}>
                                    {{ $bidang->nama_bidang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Status Peserta</label>
                        <select id="statusPesertaFilter" name="is_active" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Status</option>
                            <option value="1" {{ (isset($isActive) && $isActive === "1") ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ (isset($isActive) && $isActive === "0") ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </form>

                <!-- Export Button aligned to the right (match Absensi style) -->
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
                            <a href="{{ route('hr.laporan.export.peserta', ['bidang_id' => $bidangId, 'is_active' => $isActive, 'search_name' => $searchName]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 flex items-center gap-3 transition">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold">Export PDF</div>
                                    <div class="text-xs text-gray-500">Download sebagai PDF</div>
                                </div>
                            </a>
                            <a href="{{ route('hr.laporan.export.peserta-csv', ['bidang_id' => $bidangId, 'is_active' => $isActive, 'search_name' => $searchName]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 flex items-center gap-3 transition">
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

            const filterForm = document.getElementById('filterForm');
            const searchNameInput = document.getElementById('searchNameInput');
            const bidangFilter = document.getElementById('bidangFilter');
            const statusPesertaFilter = document.getElementById('statusPesertaFilter');
            let filterTimeout;

            if (searchNameInput) {
                searchNameInput.addEventListener('input', function() {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(function() {
                        filterForm.submit();
                    }, 400);
                });
            }

            if (bidangFilter) {
                bidangFilter.addEventListener('change', function() {
                    filterForm.submit();
                });
            }

            if (statusPesertaFilter) {
                statusPesertaFilter.addEventListener('change', function() {
                    filterForm.submit();
                });
            }
            </script>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Total Peserta</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $pesertaList->count() }}</p>
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
                        <p class="text-sm font-semibold text-gray-600">Peserta Aktif</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $pesertaList->where('is_active', true)->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Peserta Tidak Aktif</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $pesertaList->where('is_active', false)->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Total Bidang</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $bidangList->count() }}</p>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Telepon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bidang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode Magang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pesertaList as $index => $peserta)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $peserta->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">{{ $peserta->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">{{ $peserta->no_telepon ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $peserta->bidang->nama_bidang ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    @if($peserta->periode_magang_mulai && $peserta->periode_magang_selesai)
                                        {{ \Carbon\Carbon::parse($peserta->periode_magang_mulai)->format('d/m/Y') }} - 
                                        {{ \Carbon\Carbon::parse($peserta->periode_magang_selesai)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @if($peserta->is_active)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                        <form action="{{ route('hr.laporan.peserta.update-status', $peserta->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <select name="is_active" class="text-xs border border-gray-300 rounded px-1 py-1 w-8 focus:ring-2 focus:ring-blue-200 focus:border-blue-400" onchange="this.form.submit()">
                                                <option value=""></option>
                                                <option value="1" {{ $peserta->is_active ? '' : '' }}>Aktif</option>
                                                <option value="0" {{ !$peserta->is_active ? '' : '' }}>Tidak Aktif</option>
                                            </select>
                                        </form>
                                    </div>
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('hr.laporan.detail-peserta', $peserta->id) }}" class="inline-flex items-center justify-center bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <p class="text-gray-600 text-lg">Tidak ada data peserta</p>
                                        <p class="text-gray-400 text-sm mt-1">Belum ada peserta magang yang terdaftar</p>
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

@push('scripts')
<script>
    // Toggle export menu
    document.getElementById('exportBtn').addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('exportMenu').classList.toggle('hidden');
    });

    // Close menu when clicking outside
    document.addEventListener('click', function() {
        document.getElementById('exportMenu').classList.add('hidden');
    });

    // Prevent menu from closing when clicking inside
    document.getElementById('exportMenu').addEventListener('click', function(e) {
        e.stopPropagation();
    });
</script>
@endpush
@endsection

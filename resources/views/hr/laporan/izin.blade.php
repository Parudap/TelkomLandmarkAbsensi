@extends('layouts.app')

@section('title', 'Laporan Izin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('hr.laporan.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Laporan
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Laporan Izin</h1>
            <p class="text-gray-600 mt-2">Laporan data izin peserta magang</p>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <form action="{{ route('hr.laporan.izin') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end flex-1">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Bidang</label>
                    <select name="bidang_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Bidang</option>
                        @foreach($bidangList as $bidang)
                            <option value="{{ $bidang->id }}" {{ $bidangId == $bidang->id ? 'selected' : '' }}>
                                {{ $bidang->nama_bidang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Bulan</label>
                    <select name="bulan" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Tahun</label>
                    <select name="tahun" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold transition">
                    Filter
                </button>
                </form>
                <!-- Export Dropdown -->
                <div class="relative inline-block text-left mt-4 md:mt-0">
                    <button type="button" onclick="toggleExportDropdown()" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-semibold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="exportDropdownIzin" class="hidden absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                        <div class="py-1">
                            <button onclick="window.print(); toggleExportDropdown();" class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 flex items-center gap-3 transition">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold">Cetak</div>
                                    <div class="text-xs text-gray-500">Print halaman ini</div>
                                </div>
                            </button>
                            <a href="{{ route('hr.laporan.export.izin', ['bidang_id' => $bidangId, 'bulan' => $bulan, 'tahun' => $tahun, 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 flex items-center gap-3 transition">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold">Export PDF</div>
                                    <div class="text-xs text-gray-500">Buka view PDF</div>
                                </div>
                            </a>
                            <a href="{{ route('hr.laporan.export.izin', ['bidang_id' => $bidangId, 'bulan' => $bulan, 'tahun' => $tahun, 'format' => 'csv']) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 flex items-center gap-3 transition">
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
            function toggleExportDropdown() {
                const dropdown = document.getElementById('exportDropdownIzin');
                dropdown.classList.toggle('hidden');
            }
            window.addEventListener('click', function(e) {
                const dropdown = document.getElementById('exportDropdownIzin');
                const button = e.target.closest('button[onclick*="toggleExportDropdown"]');
                if (!button && dropdown && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
            </script>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600">Total Izin</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_izin'] }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600">Disetujui</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['approved'] }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600">Ditolak</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['rejected'] }}</p>
                    </div>
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600">Pending</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bidang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Izin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($izinData as $izin)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $izin->user->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $izin->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $izin->user->bidang->nama_bidang ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $izin->jenis_izin == 'tidak_masuk' ? 'bg-purple-100 text-purple-800' : 'bg-cyan-100 text-cyan-800' }}">
                                        {{ $izin->jenis_izin == 'tidak_masuk' ? 'Tidak Masuk' : 'Pulang Cepat' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($izin->tanggal)->isoFormat('dddd, D MMMM Y') }}
                                    </div>
                                    @if($izin->jenis_izin == 'pulang_cepat' && $izin->jam)
                                        <div class="text-xs text-gray-600">Jam: {{ $izin->jam }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $izin->alasan }}">
                                        {{ $izin->alasan }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($izin->status_approval == 'pending')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Menunggu Persetujuan
                                        </span>
                                    @elseif($izin->status_approval == 'approved_hr')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Disetujui
                                        </span>
                                    @elseif($izin->status_approval == 'rejected_hr')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-gray-600 text-lg">Tidak ada data izin</p>
                                        <p class="text-gray-400 text-sm mt-1">Belum ada data izin untuk periode yang dipilih</p>
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
@endsection

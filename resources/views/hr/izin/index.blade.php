@extends('layouts.app')

@section('title', 'Izin')

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
            <h1 class="text-3xl font-bold text-gray-800">Izin</h1>
            <p class="text-gray-600 mt-2">Review dan approve izin peserta magang</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Menunggu Approval</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Disetujui</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['approved'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Ditolak</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['rejected'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Filter</h3>

            <div class="flex flex-col lg:flex-row gap-4 lg:items-end">
                <form id="filterForm" action="{{ route('hr.izin.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 flex-1">
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Cari Nama/Email</label>
                        <input type="text" name="search_nama" placeholder="Nama atau email peserta..." value="{{ request('search_nama') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Status</label>
                        <select id="statusFilter" name="status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Approval</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Bidang</label>
                        <select id="bidangFilter" name="bidang_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Bidang</option>
                            @foreach($bidangList as $bidang)
                                <option value="{{ $bidang->id }}" {{ (string)request('bidang_id') === (string)$bidang->id ? 'selected' : '' }}>{{ $bidang->nama_bidang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Mulai</label>
                        <input type="date" id="tanggalMulaiFilter" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Selesai</label>
                        <input type="date" id="tanggalSelesaiFilter" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="md:col-span-2 lg:col-span-4 flex gap-2">
                        <a href="{{ route('hr.izin.index') }}" class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition font-semibold">
                            Reset
                        </a>
                    </div>
                </form>

                <!-- Export Dropdown -->
                <div class="relative lg:ml-auto">
                    <button type="button" onclick="toggleExportDropdown(event)" class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div id="exportIzinDropdown" class="hidden absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                        <div class="py-1">
                            <a href="{{ route('hr.izin.export-pdf', ['status' => request('status'), 'bidang_id' => request('bidang_id'), 'tanggal_mulai' => request('tanggal_mulai'), 'tanggal_selesai' => request('tanggal_selesai'), 'search_nama' => request('search_nama')]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 flex items-center gap-3 transition">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold">Export PDF</div>
                                    <div class="text-xs text-gray-500">Download sebagai PDF</div>
                                </div>
                            </a>
                            <a href="{{ route('hr.izin.export-csv', ['status' => request('status'), 'bidang_id' => request('bidang_id'), 'tanggal_mulai' => request('tanggal_mulai'), 'tanggal_selesai' => request('tanggal_selesai'), 'search_nama' => request('search_nama')]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 flex items-center gap-3 transition">
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
            function toggleExportDropdown(event) {
                event.preventDefault();
                event.stopPropagation();
                const dropdown = document.getElementById('exportIzinDropdown');
                dropdown.classList.toggle('hidden');
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('exportIzinDropdown');
                const button = event.target.closest('button[onclick*="toggleExportDropdown"]');
                
                if (!button && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            const filterForm = document.getElementById('filterForm');
            const statusFilter = document.getElementById('statusFilter');
            const bidangFilter = document.getElementById('bidangFilter');
            const tanggalMulaiFilter = document.getElementById('tanggalMulaiFilter');
            const tanggalSelesaiFilter = document.getElementById('tanggalSelesaiFilter');

            statusFilter.addEventListener('change', function() {
                filterForm.submit();
            });

            bidangFilter.addEventListener('change', function() {
                filterForm.submit();
            });

            tanggalMulaiFilter.addEventListener('change', function() {
                tanggalSelesaiFilter.value = this.value;
                filterForm.submit();
            });

            tanggalSelesaiFilter.addEventListener('change', function() {
                filterForm.submit();
            });
            </script>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode Izin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auto Approve</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($izinList as $izin)
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
                                        @if($izin->jenis_izin == 'tidak_masuk')
                                            {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d/m/Y') }}
                                            s/d
                                            {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d/m/Y') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($izin->tanggal)->format('d/m/Y') }}
                                        @endif
                                    </div>
                                    @if($izin->jenis_izin == 'pulang_cepat' && $izin->jam_pulang_diajukan)
                                        <div class="text-xs text-gray-600">Jam: {{ $izin->jam_pulang_diajukan }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($izin->created_at)->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($izin->created_at)->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($izin->status_approval == 'pending')
                                        @php
                                            $autoApproveTime = \Carbon\Carbon::parse($izin->created_at)->addHours(24);
                                        @endphp
                                        <div class="text-sm text-yellow-600 font-medium">{{ $autoApproveTime->format('d/m/Y') }}</div>
                                        <div class="text-xs text-yellow-600">{{ $autoApproveTime->format('H:i') }} WIB</div>
                                    @elseif($izin->auto_approved_at)
                                        <div class="text-sm text-green-600 font-medium">{{ \Carbon\Carbon::parse($izin->auto_approved_at)->format('d/m/Y') }}</div>
                                        <div class="text-xs text-green-500">{{ \Carbon\Carbon::parse($izin->auto_approved_at)->format('H:i') }} WIB</div>
                                        <span class="text-xs text-green-600 font-semibold">✓ Auto</span>
                                    @else
                                        <span class="text-xs text-gray-500 font-medium">Manual</span>
                                    @endif
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
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $izin->status_approval }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('hr.izin.show', $izin) }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-gray-600 text-lg">Tidak ada izin</p>
                                        <p class="text-gray-400 text-sm mt-1">Belum ada izin yang perlu di-review</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($izinList->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $izinList->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

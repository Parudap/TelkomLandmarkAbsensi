<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peserta Magang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: white;
            color: black;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid black;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header h2 {
            font-size: 12px;
            font-weight: normal;
        }
        
        .info-section {
            margin-bottom: 15px;
            font-size: 10px;
            border: 1px solid black;
            padding: 8px;
            background: #f9f9f9;
        }
        
        .info-item {
            margin-bottom: 3px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 110px;
        }
        
        .info-value {
            display: inline-block;
        }
        
        .stats-section {
            margin-bottom: 15px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .stat-card {
            display: table-cell;
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            width: 25%;
        }
        
        .stat-label {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        
        .stat-value {
            font-size: 16px;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid black;
        }
        
        thead {
            background: black;
            color: white;
        }
        
        th {
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid black;
            text-transform: uppercase;
        }
        
        td {
            padding: 6px 5px;
            font-size: 9px;
            border: 1px solid #666;
            vertical-align: top;
        }
        
        tbody tr:nth-child(odd) {
            background: white;
        }
        
        tbody tr:nth-child(even) {
            background: #f0f0f0;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 2px solid black;
            text-align: center;
            font-size: 8px;
        }
        
        .status-badge {
            padding: 2px 6px;
            border: 1px solid black;
            font-weight: bold;
            display: inline-block;
            font-size: 8px;
            background: white;
        }
        
        @media print {
            body {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Data Peserta Magang</h1>
        <h2>Telkom Landmark Tower</h2>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-item">
            <span class="info-label">Tanggal Cetak</span>
            <span class="info-value">: {{ date('d/m/Y H:i') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Filter Bidang</span>
            <span class="info-value">: {{ $filterBidang }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Filter Status</span>
            <span class="info-value">: {{ $filterStatus }}</span>
        </div>
    </div>

    <!-- Statistics -->
    <div class="stats-section">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Peserta</div>
                <div class="stat-value">{{ $pesertaList->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Peserta Aktif</div>
                <div class="stat-value">{{ $pesertaList->where('is_active', true)->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Tidak Aktif</div>
                <div class="stat-value">{{ $pesertaList->where('is_active', false)->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Bidang</div>
                <div class="stat-value">{{ $pesertaList->pluck('bidang_id')->unique()->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 22%;">Nama</th>
                <th style="width: 25%;">Email</th>
                <th style="width: 12%;">No. Telepon</th>
                <th style="width: 13%;">Bidang</th>
                <th style="width: 14%;">Periode Magang</th>
                <th style="width: 10%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesertaList as $index => $peserta)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $peserta->name }}</td>
                    <td>{{ $peserta->email }}</td>
                    <td>{{ $peserta->no_telepon ?? '-' }}</td>
                    <td>{{ $peserta->bidang->nama_bidang ?? '-' }}</td>
                    <td style="text-align: center;">
                        @if($peserta->periode_magang_mulai && $peserta->periode_magang_selesai)
                            {{ \Carbon\Carbon::parse($peserta->periode_magang_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($peserta->periode_magang_selesai)->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="status-badge">
                            {{ $peserta->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px;">Tidak ada data peserta</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis pada {{ date('d/m/Y H:i') }}</p>
        <p>&copy; {{ date('Y') }} Telkom Landmark Tower. All rights reserved.</p>
    </div>
</body>
</html>

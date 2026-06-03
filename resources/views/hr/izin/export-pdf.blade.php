<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Izin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: white;
            color: black;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid black;
        }
        
        .header h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            margin-bottom: 5px;
        }
        
        .info-section {
            margin-bottom: 15px;
            font-size: 11px;
            line-height: 1.6;
        }
        
        .info-item {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        
        .stats-table {
            width: 100%;
            margin-bottom: 15px;
            border: 1px solid black;
            border-collapse: collapse;
        }
        
        .stats-table th,
        .stats-table td {
            padding: 8px;
            border: 1px solid black;
            text-align: center;
            font-size: 11px;
        }
        
        .stats-table th {
            background: black;
            color: white;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border: 1px solid black;
        }
        
        thead {
            background: black;
            color: white;
        }
        
        th {
            padding: 8px 6px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid black;
        }
        
        td {
            padding: 6px 5px;
            font-size: 8px;
            border: 1px solid black;
            word-wrap: break-word;
            vertical-align: top;
        }
        
        .text-center {
            text-align: center;
        }
        
        tbody tr:nth-child(even) {
            background: #f5f5f5;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid black;
            text-align: center;
            font-size: 10px;
        }
        
        @media print {
            body {
                padding: 20px;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Izin</h1>
        <h2>Telkom Landmark Tower</h2>
    </div>
    
    <div class="info-section">
        <div class="info-item">
            <span class="info-label">Status Filter:</span>
            <span>{{ $statusLabel }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Bidang Filter:</span>
            <span>{{ $bidangLabel ?? '-' }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Periode Filter:</span>
            <span>{{ $tanggalMulai ?? '-' }} s/d {{ $tanggalSelesai ?? '-' }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Tanggal Export:</span>
            <span>{{ \App\Services\TimeService::now()->locale('id')->translatedFormat('d F Y - H:i') }} WIB</span>
        </div>
        <div class="info-item">
            <span class="info-label">Total Data:</span>
            <span>{{ $izinList->count() }} izin</span>
        </div>
    </div>
    
    <!-- Statistics -->
    <table class="stats-table">
        <thead>
            <tr>
                <th>Menunggu Approval</th>
                <th>Disetujui</th>
                <th>Ditolak</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $stats['pending'] }}</td>
                <td>{{ $stats['approved'] }}</td>
                <td>{{ $stats['rejected'] }}</td>
                <td>{{ $stats['pending'] + $stats['approved'] + $stats['rejected'] }}</td>
            </tr>
        </tbody>
    </table>
    
    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 3%;">No</th>
                <th style="width: 13%;">Nama</th>
                <th style="width: 11%;">Bidang</th>
                <th style="width: 9%;">Jenis</th>
                <th class="text-center" style="width: 8%;">Tgl Mulai</th>
                <th class="text-center" style="width: 8%;">Tgl Selesai</th>
                <th style="width: 16%;">Alasan</th>
                <th class="text-center" style="width: 9%;">Pengajuan</th>
                <th class="text-center" style="width: 7%;">Status</th>
                <th class="text-center" style="width: 9%;">Disetujui</th>
                <th class="text-center" style="width: 7%;">Auto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($izinList as $index => $izin)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $izin->user->name ?? '-' }}</td>
                    <td>{{ $izin->user->bidang->nama_bidang ?? '-' }}</td>
                    <td>{{ $izin->jenis_izin == 'tidak_masuk' ? 'Tidak Masuk' : 'Pulang Cepat' }}</td>
                    <td class="text-center">{{ $izin->tanggal_mulai ? $izin->tanggal_mulai->format('d/m/Y') : ($izin->tanggal ? $izin->tanggal->format('d/m/Y') : '-') }}</td>
                    <td class="text-center">{{ $izin->tanggal_selesai ? $izin->tanggal_selesai->format('d/m/Y') : ($izin->tanggal ? $izin->tanggal->format('d/m/Y') : '-') }}</td>
                    <td>{{ Str::limit($izin->alasan, 60) ?? '-' }}</td>
                    <td class="text-center">{{ $izin->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-center">
                        @if($izin->status_approval == 'pending')
                            Pending
                        @elseif($izin->status_approval == 'approved_hr')
                            Disetujui
                        @elseif($izin->status_approval == 'rejected_hr')
                            Ditolak
                        @else
                            {{ $izin->status_approval }}
                        @endif
                    </td>
                    <td class="text-center">{{ $izin->approved_at_hr ? $izin->approved_at_hr->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center">{{ $izin->auto_approved_hr_at ? 'Ya' : 'Tidak' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px;">Tidak ada data izin</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh Sistem Absensi Telkom Landmark Tower</p>
        <p>Tanggal Cetak: {{ \App\Services\TimeService::now()->locale('id')->translatedFormat('d F Y - H:i:s') }} WIB</p>
    </div>
</body>
</html>

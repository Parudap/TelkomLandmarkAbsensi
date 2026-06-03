<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi - {{ $peserta->name }}</title>
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
            font-size: 16px;
            font-weight: normal;
            margin-bottom: 10px;
        }
        
        .info-section {
            margin-bottom: 20px;
            font-size: 12px;
        }
        
        .info-grid {
            display: block;
        }
        
        .info-item {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 140px;
        }
        
        .info-value {
            display: inline-block;
        }
        
        .stats-section {
            margin-bottom: 20px;
        }
        
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid black;
        }
        
        .stats-table th {
            background: black;
            color: white;
            padding: 8px;
            font-size: 10px;
            text-align: center;
            border: 1px solid black;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .stats-table td {
            padding: 8px;
            text-align: center;
            border: 1px solid black;
            font-size: 14px;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid black;
        }
        
        thead {
            background: black;
            color: white;
        }
        
        th {
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid black;
        }
        
        td {
            padding: 8px;
            font-size: 10px;
            border: 1px solid black;
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
            }
            
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
            
            thead {
                display: table-header-group;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PT TELKOM LANDMARK TOWER</h1>
        <h2>Laporan Absensi Peserta Magang</h2>
    </div>
    
    <div class="info-section">
        <div class="info-item">
            <span class="info-label">Nama Peserta:</span>
            <span class="info-value">{{ $peserta->name }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $peserta->email }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Bidang:</span>
            <span class="info-value">{{ $peserta->bidang->nama_bidang ?? '-' }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Periode Magang:</span>
            <span class="info-value">
                {{ $peserta->periode_magang_mulai ? \Carbon\Carbon::parse($peserta->periode_magang_mulai)->format('d/m/Y') : '-' }}
                s/d
                {{ $peserta->periode_magang_selesai ? \Carbon\Carbon::parse($peserta->periode_magang_selesai)->format('d/m/Y') : '-' }}
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Periode Laporan:</span>
            <span class="info-value">
                {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} - 
                {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Tanggal Cetak:</span>
            <span class="info-value">{{ \App\Services\TimeService::now()->format('d/m/Y H:i') }} WIB</span>
        </div>
    </div>
    
    <div class="stats-section">
        <table class="stats-table">
            <thead>
                <tr>
                    <th>Total Record</th>
                    <th>Tepat Waktu</th>
                    <th>Terlambat</th>
                    <th>Alpha</th>
                    <th>Izin T.Masuk</th>
                    <th>Izin P.Cepat</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $stats['total_hari'] }}</td>
                    <td>{{ $stats['hadir_tepat'] }}</td>
                    <td>{{ $stats['hadir_telat'] }}</td>
                    <td>{{ $stats['alpha'] }}</td>
                    <td>{{ $stats['izin_tidak_masuk'] }}</td>
                    <td>{{ $stats['izin_pulang_cepat'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 12%">Hari</th>
                <th style="width: 12%">Tanggal</th>
                <th style="width: 12%">Jam Masuk</th>
                <th style="width: 12%">Jam Pulang</th>
                <th style="width: 12%">Durasi</th>
                <th style="width: 15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensiList as $index => $absensi)
                <tr>
                    <td style="text-align: center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('l') }}</td>
                    <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d/m/Y') }}</td>
                    <td>
                        @if($absensi->jam_masuk)
                            {{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($absensi->jam_pulang)
                            {{ \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($absensi->jam_masuk && $absensi->jam_pulang)
                            @php
                                $masuk = \Carbon\Carbon::parse($absensi->jam_masuk);
                                $pulang = \Carbon\Carbon::parse($absensi->jam_pulang);
                                $durasi = $masuk->diff($pulang);
                            @endphp
                            {{ $durasi->h }}j {{ $durasi->i }}m
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($absensi->status_harian == 'HADIR_TEPAT_WAKTU')
                            Tepat Waktu
                        @elseif($absensi->status_harian == 'HADIR_TELAT')
                            Terlambat
                        @elseif($absensi->status_harian == 'ALPHA')
                            Alpha
                        @elseif($absensi->status_harian == 'IZIN_TIDAK_MASUK')
                            Izin Tidak Masuk
                        @elseif($absensi->status_harian == 'IZIN_PULANG_CEPAT')
                            Izin Pulang Cepat
                        @else
                            {{ $absensi->status_harian }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px;">
                        Tidak ada data absensi untuk periode yang dipilih
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        PT Telkom Landmark Tower - Sistem Absensi Peserta Magang
    </div>
    
    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>

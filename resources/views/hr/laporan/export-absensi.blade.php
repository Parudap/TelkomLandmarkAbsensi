<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi</title>
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
        
        .info-item {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
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
            <span class="info-label">Bidang:</span>
            {{ $bidang ? $bidang->nama_bidang : 'Semua Bidang' }}
        </div>
        <div class="info-item">
            <span class="info-label">Periode:</span>
            {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}
        </div>
        <div class="info-item">
            <span class="info-label">Total Record:</span>
            {{ $stats['total_absensi'] }} data
        </div>
        <div class="info-item">
            <span class="info-label">Tanggal Cetak:</span>
            {{ \App\Services\TimeService::now()->format('d/m/Y H:i') }} WIB
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Hari</th>
                <th>Tanggal</th>
                <th>Nama Peserta</th>
                <th>Bidang</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Durasi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensiData as $index => $absensi)
                <tr>
                    <td style="text-align: center">{{ $index + 1 }}</td>
                    <td>
                        @php
                            $hariInggris = \Carbon\Carbon::parse($absensi->tanggal)->format('l');
                            $hariIndonesia = match($hariInggris) {
                                'Monday' => 'Senin',
                                'Tuesday' => 'Selasa',
                                'Wednesday' => 'Rabu',
                                'Thursday' => 'Kamis',
                                'Friday' => 'Jumat',
                                'Saturday' => 'Sabtu',
                                'Sunday' => 'Minggu',
                                default => $hariInggris
                            };
                        @endphp
                        {{ $hariIndonesia }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $absensi->user->name }}</td>
                    <td>{{ $absensi->user->bidang->nama_bidang ?? '-' }}</td>
                    <td>{{ $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') : '-' }}</td>
                    <td>{{ $absensi->jam_pulang ? \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') : '-' }}</td>
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
                    <td colspan="9" style="text-align: center; padding: 20px;">
                        Tidak ada data absensi
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p>PT Telkom Landmark Tower - Sistem Absensi Peserta Magang</p>
    </div>
</body>
</html>

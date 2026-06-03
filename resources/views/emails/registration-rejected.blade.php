<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Ditolak</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #dc2626;
        }
        .header h1 {
            color: #dc2626;
            margin: 0;
            font-size: 24px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 10px;
        }
        .error-icon {
            font-size: 60px;
            color: #ef4444;
            margin: 20px 0;
        }
        .content {
            margin: 30px 0;
        }
        .info-box {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box h3 {
            margin-top: 0;
            color: #991b1b;
        }
        .reason-box {
            background-color: #fff7ed;
            border: 1px solid #fed7aa;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .reason-box h4 {
            margin-top: 0;
            color: #9a3412;
        }
        .user-info {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .user-info p {
            margin: 8px 0;
        }
        .user-info strong {
            color: #374151;
        }
        .cta-button {
            display: inline-block;
            background-color: #dc2626;
            color: white;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
        }
        .cta-button:hover {
            background-color: #b91c1c;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
        }
        .note {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 12px;
            margin: 15px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Telkom Landmark</div>
            <h1>Pemberitahuan Registrasi</h1>
        </div>

        <div class="content">
            <div style="text-align: center;">
                <div class="error-icon">❌</div>
            </div>

            <p>Halo <strong>{{ $user->name }}</strong>,</p>

            <div class="info-box">
                <h3>📢 Informasi Registrasi</h3>
                <p>Terima kasih telah mendaftar sebagai Peserta Magang di <strong>Telkom Landmark Tower</strong>.</p>
                <p>Setelah meninjau pendaftaran Anda, dengan berat hati kami informasikan bahwa pendaftaran Anda <strong>belum dapat disetujui</strong> pada saat ini.</p>
            </div>

            <div class="user-info">
                <h3 style="margin-top: 0; color: #374151;">📋 Data Pendaftaran Anda</h3>
                <p><strong>Nama:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Bidang yang Dipilih:</strong> {{ $user->bidang->nama_bidang ?? '-' }}</p>
                <p><strong>Tanggal Pendaftaran:</strong> {{ $user->created_at->format('d M Y H:i') }}</p>
            </div>

            @if($keterangan)
            <div class="reason-box">
                <h4>📝 Alasan Penolakan:</h4>
                <p>{{ $keterangan }}</p>
            </div>
            @endif

            <div class="note">
                <strong>ℹ️ Catatan Penting:</strong><br>
                • Penolakan ini tidak menutup kemungkinan untuk mendaftar kembali di masa mendatang<br>
                • Anda dapat memperbaiki dokumen atau persyaratan yang belum memenuhi kriteria<br>
                • Jika ada pertanyaan, silakan hubungi tim HR kami untuk informasi lebih lanjut
            </div>

            <p>Kami menghargai minat Anda untuk bergabung dengan Telkom Landmark Tower. Jangan berkecil hati, terus tingkatkan kemampuan Anda dan jangan ragu untuk mencoba lagi di kesempatan berikutnya.</p>

            <div style="text-align: center;">
                <a href="{{ route('register') }}" class="cta-button">🔄 Daftar Ulang (Opsional)</a>
            </div>

            <p style="margin-top: 30px;">
                Semoga sukses untuk langkah Anda selanjutnya!<br>
                <strong>Tim HR Telkom Landmark Tower</strong>
            </p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh Sistem Absensi Telkom Landmark Tower.</p>
            <p>Untuk pertanyaan lebih lanjut, silakan hubungi Tim HR kami.</p>
            <p style="margin-top: 10px; color: #9ca3af; font-size: 12px;">
                © {{ date('Y') }} Telkom Landmark Tower. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>

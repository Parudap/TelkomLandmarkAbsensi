<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Disetujui</title>
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
        .success-icon {
            font-size: 60px;
            color: #22c55e;
            margin: 20px 0;
        }
        .content {
            margin: 30px 0;
        }
        .info-box {
            background-color: #f0fdf4;
            border-left: 4px solid #22c55e;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box h3 {
            margin-top: 0;
            color: #166534;
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
            color: #ffffff !important;
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
        .steps {
            margin: 20px 0;
        }
        .steps ol {
            padding-left: 20px;
        }
        .steps li {
            margin: 10px 0;
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
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
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
            <h1>Registrasi Anda Disetujui</h1>
        </div>

        <div class="content">
            <p>Halo <strong>{{ $user->name }}</strong>,</p>

            <p style="margin: 20px 0; line-height: 1.8;">
                Selamat! Pendaftaran Anda sebagai Peserta Magang di <strong>Telkom Landmark Tower</strong> telah disetujui oleh Tim HR kami.
            </p>

            <div class="user-info">
                <h3 style="margin-top: 0; color: #374151; font-size: 16px;">Informasi Akun Anda</h3>
                <p><strong>Nama:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Bidang:</strong> {{ $user->bidang->nama_bidang ?? 'Belum ditentukan' }}</p>
                @if($user->periode_magang_mulai && $user->periode_magang_selesai)
                <p><strong>Periode Magang:</strong> {{ \Carbon\Carbon::parse($user->periode_magang_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($user->periode_magang_selesai)->format('d M Y') }}</p>
                @endif
            </div>

            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="cta-button">Login Sekarang</a>
            </div>

            <p style="margin-top: 30px; color: #6b7280; font-size: 14px;">
                Jika Anda mengalami kesulitan dalam mengakses sistem, silakan hubungi tim HR kami.
            </p>

            <p style="margin-top: 30px;">
                Selamat bergabung dan semoga sukses dalam program magang Anda!<br>
                <strong>Tim HR Telkom Landmark Tower</strong>
            </p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh Sistem Absensi Telkom Landmark Tower.</p>
            <p>Jangan balas email ini. Untuk pertanyaan, hubungi HR kami.</p>
            <p style="margin-top: 10px; color: #9ca3af; font-size: 12px;">
                © {{ date('Y') }} Telkom Landmark Tower. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>

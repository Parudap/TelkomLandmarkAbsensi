@extends('layouts.app')

@section('title', 'Registrasi Peserta Magang - Telkom Landmark')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Registrasi Peserta Magang</h1>
            <p class="text-gray-600 mt-2">Telkom Landmark Tower</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Data Pribadi -->
            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Data Pribadi</h2>
                
                <!-- Nama Lengkap -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Masukkan nama lengkap">
                </div>

                <!-- Tanggal Lahir -->
                <div class="mb-4">
                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Lahir <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>

                <!-- Alamat -->
                <div class="mb-4">
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat Tempat Tinggal <span class="text-red-500">*</span>
                    </label>
                    <textarea id="alamat" name="alamat" rows="3" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                </div>

                <!-- Nomor Telepon -->
                <div class="mb-4">
                    <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Telepon <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Contoh: 081234567890">
                    <p class="text-xs text-gray-500 mt-1">Format: 10-15 digit angka</p>
                </div>
            </div>

            <!-- Data Akun -->
            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Data Akun</h2>
                
                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="contoh@email.com">
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Minimal 8 karakter">
                    <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter, kombinasi huruf dan angka</p>
                </div>

                <!-- Konfirmasi Password -->
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Ulangi password">
                </div>
            </div>

            <!-- Data Magang -->
            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Data Magang</h2>
                
                <!-- Instansi Asal -->
                <div class="mb-4">
                    <label for="instansi_asal" class="block text-sm font-medium text-gray-700 mb-2">
                        Instansi Asal (Kampus/Sekolah) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="instansi_asal" name="instansi_asal" value="{{ old('instansi_asal') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Contoh: Universitas Indonesia">
                </div>

                <!-- Bidang -->
                <div class="mb-4">
                    <label for="bidang_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Bidang <span class="text-red-500">*</span>
                    </label>
                    <select id="bidang_id" name="bidang_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">-- Pilih Bidang --</option>
                        @foreach($bidangList as $bidang)
                            <option value="{{ $bidang->id }}" {{ old('bidang_id') == $bidang->id ? 'selected' : '' }}>
                                {{ $bidang->nama_bidang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Periode Magang -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="periode_magang_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="periode_magang_mulai" name="periode_magang_mulai" 
                            value="{{ old('periode_magang_mulai') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="periode_magang_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="periode_magang_selesai" name="periode_magang_selesai" 
                            value="{{ old('periode_magang_selesai') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Upload Surat Magang -->
                <div class="mb-4">
                    <label for="surat_magang" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Surat Konfirmasi Magang <span class="text-red-500">*</span>
                    </label>
                    <input type="file" id="surat_magang" name="surat_magang" required accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Format: PDF, JPG, JPEG, PNG. Maksimal 2MB</p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('login') }}" class="text-red-600 hover:text-red-700">
                    Sudah punya akun? Login
                </a>
                <button type="submit" 
                    class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Daftar Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

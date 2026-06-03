@extends('layouts.app')

@section('title', 'Tambah Akun Peserta')

@section('content')
<div class="max-w-lg mx-auto mt-10">
    <a href="{{ route('hr.dashboard') }}" class="inline-block mb-4 text-red-600 hover:underline font-semibold">&larr; Kembali ke Dashboard</a>
    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Tambah Akun Peserta Magang</h1>
        <form method="POST" action="{{ route('hr.peserta.store') }}" class="space-y-6">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500" placeholder="Nama Peserta">
            @error('name')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500" placeholder="email@domain.com">
            @error('email')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
            <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500" placeholder="08xxxxxxxxxx">
            @error('no_telepon')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="bidang_id" class="block text-sm font-medium text-gray-700 mb-1">Bidang</label>
            <select id="bidang_id" name="bidang_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
                <option value="">-- Pilih Bidang --</option>
                @foreach($bidangList as $bidang)
                    <option value="{{ $bidang->id }}" @if(old('bidang_id') == $bidang->id) selected @endif>{{ $bidang->nama_bidang }}</option>
                @endforeach
            </select>
            @error('bidang_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" id="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500" placeholder="Minimal 6 karakter">
            @error('password')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="periode_magang_mulai" class="block text-sm font-medium text-gray-700 mb-1">Periode Magang Mulai</label>
                <input type="date" id="periode_magang_mulai" name="periode_magang_mulai" value="{{ old('periode_magang_mulai') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
                @error('periode_magang_mulai')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="periode_magang_selesai" class="block text-sm font-medium text-gray-700 mb-1">Periode Magang Selesai</label>
                <input type="date" id="periode_magang_selesai" name="periode_magang_selesai" value="{{ old('periode_magang_selesai') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
                @error('periode_magang_selesai')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
        <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-lg font-bold hover:bg-red-700 transition">Simpan Akun Peserta</button>
    </form>
    </div>
</div>
@endsection

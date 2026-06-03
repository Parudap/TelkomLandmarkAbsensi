@extends('layouts.app')

@section('title', 'Registrasi Berhasil')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <div class="mb-6">
            <svg class="w-20 h-20 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">Registrasi Berhasil!</h1>
        
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 text-left">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Langkah 1: Verifikasi Email</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p class="font-semibold">Belum Aktif - Menunggu Verifikasi Email</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-left space-y-4 mb-6">
            <p class="text-gray-700">
                <strong>Langkah selanjutnya:</strong>
            </p>
            <ol class="list-decimal list-inside space-y-3 text-gray-600">
                <li class="font-semibold text-blue-600">
                    <span class="font-normal text-gray-600">Cek email Anda di <strong class="text-gray-900">{{ session('user_email', 'inbox Anda') }}</strong> (termasuk folder spam/junk)</span>
                </li>
                <li class="font-semibold text-blue-600">
                    <span class="font-normal text-gray-600">Klik link verifikasi yang dikirimkan</span>
                </li>
                <li>
                    <span class="text-gray-500">Setelah email terverifikasi, tunggu persetujuan dari Admin/HR</span>
                </li>
                <li>
                    <span class="text-gray-500">Setelah disetujui, Anda dapat login dan mulai absensi</span>
                </li>
            </ol>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800">
                <strong>Catatan:</strong> Link verifikasi berlaku selama 60 menit. Jika tidak menerima email dalam 5 menit, klik tombol "Kirim Ulang Link Verifikasi" di halaman login.
            </p>
        </div>

        <a href="{{ route('login') }}" 
            class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700">
            Kembali ke Halaman Login
        </a>
    </div>
</div>
@endsection

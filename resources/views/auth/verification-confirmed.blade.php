@extends('layouts.app')

@section('title', 'Verifikasi Email Berhasil')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <div class="mb-6">
            <svg class="w-20 h-20 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">Email Berhasil Diverifikasi!</h1>
        
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 text-left">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Status Akun</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p class="font-semibold">Email Terverifikasi - Menunggu Persetujuan Admin</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-left space-y-4 mb-6">
            <p class="text-gray-700">
                Email Anda telah berhasil diverifikasi. Data pendaftaran Anda sudah masuk ke dalam sistem.
            </p>
            
            <p class="text-gray-700">
                <strong>Langkah selanjutnya:</strong>
            </p>
            <ul class="list-disc list-inside space-y-2 text-gray-600">
                <li>Tim HR/Admin kami akan meninjau pendaftaran Anda</li>
                <li>Anda akan menerima email pemberitahuan hasil verifikasi</li>
                <li>Proses verifikasi biasanya memakan waktu 1-2 hari kerja</li>
                <li>Setelah disetujui, Anda dapat login dan mulai menggunakan sistem</li>
            </ul>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800">
                <strong>Catatan:</strong> Anda akan menerima notifikasi email ketika akun Anda disetujui atau jika ada informasi tambahan yang diperlukan.
            </p>
        </div>

        @auth
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-yellow-800">
                <strong>Info:</strong> Anda sedang login dengan akun yang masih menunggu persetujuan. Sistem tidak dapat digunakan sampai akun disetujui admin.
            </p>
        </div>
        
        <div class="text-center">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                    class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700">
                    Logout
                </button>
            </form>
            <p class="text-sm text-gray-500 mt-4">Silakan logout dan tunggu email konfirmasi persetujuan dari admin.</p>
        </div>
        @else
        <a href="{{ route('login') }}" 
            class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700">
            Login
        </a>
        @endauth
    </div>
</div>
@endsection

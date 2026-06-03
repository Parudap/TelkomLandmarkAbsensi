@extends('layouts.guest')

@section('title', 'Login - Telkom Landmark Absensi')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden flex transform transition-all hover:scale-[1.01] duration-300">
        
        <!-- Left Side - Image & Branding -->
        <div class="hidden md:block md:w-1/2 relative bg-gray-900">
            <div class="absolute inset-0 bg-cover bg-center opacity-60" 
                 style="background-image: url('{{ asset('images/telkomlandmarkbg.jpg') }}');">
            </div>
            <div class="absolute inset-0 bg-gradient-to-br from-red-600/90 to-blue-900/90 mix-blend-multiply"></div>
            
            <div class="relative z-10 flex flex-col justify-between h-full p-12 text-white">
                <div>
                    <h2 class="text-4xl font-extrabold mb-2 tracking-wider leading-tight">
                        TELKOM LANDMARK<br>TOWER
                    </h2>
                </div>
                
                <div class="space-y-6">
                    <h3 class="text-2xl font-semibold leading-tight">
                        Sistem Absensi <br>
                        Digital & Terintegrasi
                    </h3>
                    <p class="text-gray-200 text-sm leading-relaxed">
                        Kelola kehadiran peserta magang dengan validasi biometrik dan lokasi yang akurat untuk produktivitas maksimal.
                    </p>
                </div>
                
                <div class="flex items-center space-x-2 text-xs text-gray-400">
                    <span>&copy; {{ date('Y') }} Telkom Landmark Tower</span>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center relative">
            <!-- Back Button -->
            <a href="{{ url('/') }}" class="absolute top-6 right-6 text-gray-400 hover:text-red-600 transition-colors duration-200 group flex items-center gap-2 text-sm font-medium z-20">
                <span class="group-hover:-translate-x-1 transition-transform">←</span> Kembali
            </a>

            <!-- Decorative circle -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-40 h-40 rounded-full bg-red-50 blur-3xl opacity-50 pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
                    <p class="text-gray-500">Silakan masuk untuk mengakses akun Anda</p>
                </div>

                @if (session('status'))
                    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded mb-6 text-sm" role="alert">
                        <p class="font-bold">Info</p>
                        <p>{{ session('status') }}</p>
                    </div>
                @endif
                
                @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 text-sm" role="alert">
                        <p class="font-bold">Berhasil</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 text-sm" role="alert">
                        <p class="font-bold">Gagal</p>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 animate-pulse text-sm" role="alert">
                        <div class="flex">
                            <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                            <div>
                                <p class="font-bold">Login Gagal</p>
                                <ul class="list-disc list-inside mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6" id="loginForm">
                    @csrf

                    <!-- Email Input -->
                    <div class="group">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1 transition-colors group-focus-within:text-red-600">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all shadow-sm placeholder-gray-400 hover:border-gray-400"
                                placeholder="nama@email.com">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="group">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1 transition-colors group-focus-within:text-red-600">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" required
                                class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all shadow-sm placeholder-gray-400 hover:border-gray-400"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer z-10">
                                <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eyeOffIcon" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-1.563 3.029m-5.858-.908l-3.59-3.59m-3.59-3.59L3 3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" 
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded cursor-pointer transition-colors">
                            <label for="remember" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none hover:text-gray-900">
                                Ingat saya
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submitBtn"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transform transition-all hover:-translate-y-0.5 hover:shadow-lg active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="btnText">Masuk Sekarang</span>
                        <svg id="btnSpinner" class="animate-spin ml-2 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <p class="text-xs text-gray-400">
                        Masalah saat login? Hubungi Administrator HR.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Entrance Animation */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .max-w-4xl {
        animation: slideInUp 0.6s ease-out forwards;
    }
</style>

@push('scripts')
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
        }
    }

    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        
        // Prevent double submission handled by browser, but visual feedback is key
        // We do not preventDefault to allow form submission
        
        // Slight delay to ensure form submits before disabling (though usually fine)
        // Better: just change visuals, disable pointer events class
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        btnText.textContent = 'Memproses...';
        btnSpinner.classList.remove('hidden');
    });
</script>
@endpush
@endsection

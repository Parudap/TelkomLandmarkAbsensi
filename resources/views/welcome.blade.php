
@extends('layouts.guest')

@section('title', 'Beranda - Telkom Landmark Absensi')

@section('content')
<!-- Hero Section -->
<div class="relative h-screen w-full overflow-hidden flex items-center justify-center">
    <!-- Background Parallax Image -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat transform scale-105 transition-transform duration-[20s] hover:scale-110" 
         style="background-image: url('{{ asset('images/telkomlandmarkbg.jpg') }}'); animation: slowZoom 20s infinite alternate;">
    </div>
    
    <!-- Gradient Overlay with Glass Effect -->
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900/70 via-gray-900/50 to-gray-900/80 backdrop-blur-[2px]"></div>

    <!-- Navigation Overlay -->
    <nav class="absolute top-0 w-full z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0">
                    <span class="font-bold text-xl tracking-wider">
                        <span class="text-red-600">TELKOM</span> <span class="text-white">LANDMARK TOWER</span>
                    </span>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="#" class="text-white hover:text-red-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">HOME</a>
                        <a href="#about" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">ABOUT</a>
                        <a href="#features" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">FEATURES</a>
                        <a href="{{ route('login') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-full text-sm font-medium transition-all transform hover:scale-105 shadow-lg hover:shadow-red-500/30">LOGIN</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Content -->
    <div class="relative z-10 text-center px-4 max-w-5xl mx-auto reveal">
        <!-- Removed Smart Attendance System text -->
        
        <h1 class="text-5xl md:text-7xl lg:text-8xl font-extrabold text-white mb-8 leading-tight tracking-tight">
            <span class="block bg-clip-text text-transparent bg-gradient-to-r from-white via-gray-200 to-gray-400 animate-gradient-x">
                Absensi Digital
            </span>
            Peserta Magang
        </h1>
        
        <p class="text-lg md:text-2xl text-gray-300 mb-12 max-w-3xl mx-auto leading-relaxed font-light">
            Platform absensi online dengan <span class="text-white font-medium">validasi foto</span> 
            dan <span class="text-white font-medium">lokasi</span> untuk pencatatan kehadiran.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="{{ route('login') }}" 
               class="group relative px-8 py-4 bg-red-600 text-white rounded-full font-bold text-lg overflow-hidden transition-all hover:scale-105 hover:shadow-[0_0_40px_-10px_rgba(220,38,38,0.5)]">
                <span class="relative z-10 flex items-center gap-2">
                    Mulai Sekarang
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </span>
            </a>
            <a href="#about" 
               class="px-8 py-4 bg-white/10 backdrop-blur-md border border-white/20 text-white rounded-full font-semibold text-lg hover:bg-white/20 transition-all">
                Selengkapnya
            </a>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 z-10 animate-bounce">
        <a href="#about" class="text-white/50 hover:text-white transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </a>
    </div>
</div>

<!-- About Section -->
<div id="about" class="bg-white py-24 relative overflow-hidden">
    <!-- Decorative blobs -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-red-50 rounded-full blur-3xl opacity-50"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 bg-blue-50 rounded-full blur-3xl opacity-50"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-5xl reveal-left">
                <h2 class="text-red-600 font-bold tracking-widest uppercase mb-2">Tentang Sistem</h2>
                <h3 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                    Sistem Absensi <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-red-800">Peserta Magang</span>
                </h3>
                <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                    Platform absensi digital untuk peserta magang di Telkom Landmark Tower yang dirancang untuk mendukung proses kehadiran harian secara lebih tertib, cepat, dan transparan.
                </p>
                <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                    Melalui satu alur yang sederhana, peserta dapat melakukan absen masuk dan pulang, mengajukan izin, serta melihat riwayat kehadiran. Di sisi admin, data dapat dipantau dan diverifikasi dengan lebih rapi sehingga proses evaluasi kehadiran menjadi lebih mudah.
                </p>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-center gap-3 text-gray-700">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center">✓</span>
                        Pencatatan kehadiran berbasis lokasi
                    </li>
                    <li class="flex items-center gap-3 text-gray-700">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center">✓</span>
                        Pengajuan izin secara online
                    </li>
                    <li class="flex items-center gap-3 text-gray-700">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center">✓</span>
                        Laporan kehadiran otomatis
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div id="features" class="bg-gray-50 py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-20 reveal-up">
            <h2 class="text-red-600 font-bold tracking-widest uppercase mb-2">Fitur Sistem</h2>
            <h3 class="text-4xl font-bold text-gray-900 mb-6">Fitur Utama</h3>
            <p class="text-xl text-gray-600">
                Berbagai fitur untuk memudahkan proses absensi dan administrasi kehadiran
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group reveal-up" style="transition-delay: 100ms;">
                <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-red-600 transition-colors duration-300">
                    <span class="text-2xl group-hover:text-white transition-colors duration-300">📸</span>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Foto Selfie</h3>
                <p class="text-gray-600 leading-relaxed">
                    Upload foto saat absen masuk dan pulang.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group reveal-up" style="transition-delay: 200ms;">
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-blue-600 transition-colors duration-300">
                    <span class="text-2xl group-hover:text-white transition-colors duration-300">📍</span>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Validasi Lokasi</h3>
                <p class="text-gray-600 leading-relaxed">
                    Absen hanya bisa di area Telkom Landmark Tower (radius 50m).
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group reveal-up" style="transition-delay: 300ms;">
                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-green-600 transition-colors duration-300">
                    <span class="text-2xl group-hover:text-white transition-colors duration-300">✅</span>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Persetujuan Izin</h3>
                <p class="text-gray-600 leading-relaxed">
                    Pengajuan izin disetujui oleh HR.
                </p>
            </div>
            
            <!-- Feature 4 -->
            <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group reveal-up" style="transition-delay: 400ms;">
                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-purple-600 transition-colors duration-300">
                    <span class="text-2xl group-hover:text-white transition-colors duration-300">⏰</span>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Monitoring Kehadiran</h3>
                <p class="text-gray-600 leading-relaxed">
                    Lihat riwayat kehadiran dan status izin di dashboard.
                </p>
            </div>

            <!-- Feature 5 -->
            <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group reveal-up" style="transition-delay: 500ms;">
                <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-yellow-600 transition-colors duration-300">
                    <span class="text-2xl group-hover:text-white transition-colors duration-300">📊</span>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Laporan Absensi</h3>
                <p class="text-gray-600 leading-relaxed">
                    Export laporan kehadiran ke format Excel.
                </p>
            </div>

            <!-- Feature 6 -->
            <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group reveal-up" style="transition-delay: 600ms;">
                <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-indigo-600 transition-colors duration-300">
                    <span class="text-2xl group-hover:text-white transition-colors duration-300">🔒</span>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Keamanan Data</h3>
                <p class="text-gray-600 leading-relaxed">
                    Data tersimpan aman dengan sistem autentikasi.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="relative bg-gray-900 py-24 overflow-hidden">
    <div class="absolute inset-0 bg-red-900/20"></div>
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-30"></div>
    
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10 reveal-up">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Mulai Gunakan Sistem</h2>
        <p class="text-xl text-gray-300 mb-10 max-w-2xl mx-auto">
            Login untuk melakukan absensi dan mengelola kehadiran magang Anda.
        </p>
        <div class="flex gap-6 justify-center">
            <a href="{{ route('login') }}" 
               class="bg-red-600 text-white px-10 py-4 rounded-full text-lg font-bold hover:bg-red-700 transition-all transform hover:scale-105 shadow-lg shadow-red-600/30">
                Login Sekarang
            </a>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-gray-950 text-white pt-20 pb-10 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-12 mb-16">
            <div class="col-span-2">
                <h3 class="text-2xl font-bold mb-6 tracking-wider">TELKOM LANDMARK</h3>
                <p class="text-gray-400 mb-6 max-w-sm leading-relaxed">
                    Sistem absensi digital untuk peserta magang Telkom Landmark Tower, Jakarta.
                </p>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold mb-6 text-white">Contact Us</h4>
                <ul class="space-y-4 text-gray-400">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Jl. Jend. Gatot Subroto Kav. 52, Jakarta Selatan 12710</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>info@telkomlandmark.co.id</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>(021) 2924-3924</span>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="text-lg font-semibold mb-6 text-white">Quick Links</h4>
                <ul class="space-y-3 text-gray-400">
                    <li><a href="{{ route('login') }}" class="hover:text-red-500 transition-colors">Login Portal</a></li>
                    <li><a href="#features" class="hover:text-red-500 transition-colors">Fitur Sistem</a></li>
                    <li><a href="#about" class="hover:text-red-500 transition-colors">Tentang Kami</a></li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-800 pt-8 text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} Telkom Landmark Tower. All rights reserved.</p>
        </div>
    </div>
</footer>

<style>
    /* Smooth Scroll */
    html {
        scroll-behavior: smooth;
    }

    /* Animation Classes */
    .reveal, .reveal-up, .reveal-left, .reveal-right {
        opacity: 0;
        transition: all 0.8s ease-out;
    }

    .reveal.active {
        opacity: 1;
        transform: translateY(0);
    }

    .reveal-up {
        transform: translateY(50px);
    }
    .reveal-up.active {
        opacity: 1;
        transform: translateY(0);
    }

    .reveal-left {
        transform: translateX(-50px);
    }
    .reveal-left.active {
        opacity: 1;
        transform: translateX(0);
    }

    .reveal-right {
        transform: translateX(50px);
    }
    .reveal-right.active {
        opacity: 1;
        transform: translateX(0);
    }

    /* Keyframes */
    @keyframes slowZoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }
    
    @keyframes fade-in-down {
        0% {
            opacity: 0;
            transform: translateY(-20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-down {
        animation: fade-in-down 1s ease-out;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    
                    // Trigger counter animation if element has counters
                    const counters = entry.target.querySelectorAll('.counter');
                    if (counters.length > 0) {
                        counters.forEach(counter => {
                            const target = +counter.getAttribute('data-target');
                            const duration = 2000; // 2 seconds
                            const increment = target / (duration / 16); // 60fps
                            
                            let current = 0;
                            const updateCounter = () => {
                                current += increment;
                                if (current < target) {
                                    counter.innerText = Math.ceil(current);
                                    requestAnimationFrame(updateCounter);
                                } else {
                                    counter.innerText = target + (target > 100 ? '+' : '');
                                }
                            };
                            updateCounter();
                            counter.classList.remove('counter'); // Prevent re-running
                        });
                    }
                }
            });
        }, observerOptions);

        // Observe elements
        document.querySelectorAll('.reveal, .reveal-up, .reveal-left, .reveal-right').forEach(el => {
            observer.observe(el);
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('bg-gray-900/90', 'backdrop-blur-md', 'shadow-lg');
            } else {
                navbar.classList.remove('bg-gray-900/90', 'backdrop-blur-md', 'shadow-lg');
            }
        });
    });
</script>
@endsection

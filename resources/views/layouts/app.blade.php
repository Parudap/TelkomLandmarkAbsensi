<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Telkom Landmark Absensi')</title>
    
    {{-- Tailwind CSS CDN untuk development --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Custom Tailwind Configuration --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'telkom-red': '#ED1C24',
                    }
                }
            }
        }
    </script>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="flex items-center">
                    <span class="text-xl font-bold text-red-600">Telkom</span>
                    <span class="text-xl font-bold text-gray-800 ml-1">Landmark</span>
                    <span class="text-xl font-bold text-black ml-1">Tower</span>
                </a>
                
                @auth
                <div class="flex items-center gap-4">
                    <span class="text-gray-700">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                            Logout
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error') && strpos(session('error'), 'Tidak dapat absensi') === false && strpos(session('error'), 'izin pulang cepat') === false && strpos(session('error'), 'Absen pulang hanya') === false)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if (session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                {{ session('info') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-600 text-sm">
                &copy; {{ date('Y') }} Telkom Landmark Tower. All rights reserved.
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

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
<body class="bg-gray-50 min-h-screen font-sans antialiased">
    @yield('content')
    
    @stack('scripts')
</body>
</html>

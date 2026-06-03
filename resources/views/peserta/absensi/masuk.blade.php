@extends('layouts.app')

@section('title', 'Absen Masuk')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Absen Masuk</h1>
        <p class="text-gray-600">{{ $tanggal }}</p>
        <p class="text-xl font-semibold text-red-600 mt-1">{{ $jamSekarang }} WIB</p>
        <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-3 text-left max-w-2xl mx-auto">
            <p class="text-sm text-blue-800">Silahkan ambil foto selfie dan pastikan Anda berada di area Telkom Landmark Tower</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <form id="absenForm" method="POST" action="{{ route('peserta.absensi.masuk') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="bg-white rounded-lg shadow-lg p-6 space-y-6">
            <!-- Step 1: Camera -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-900">Silahkan Ambil Foto Selfie Anda</h3>
                    <span class="text-xs bg-red-100 text-red-800 px-3 py-1 rounded-full font-medium">Wajib</span>
                </div>
                <p class="text-sm text-gray-600 mb-4">Nyalakan kamera dan ambil foto dengan jelas</p>
                
                <!-- Camera Mode -->
                <div id="cameraMode">
                    <!-- Camera Preview -->
                    <div class="relative bg-gray-900 rounded-lg overflow-hidden mb-4" style="max-width: 640px; margin: 0 auto;">
                        <video id="camera" autoplay playsinline class="w-full" style="display: block;"></video>
                        <canvas id="canvas" class="w-full" style="display: none;"></canvas>
                        <img id="preview" class="w-full" style="display: none;" />
                    </div>

                    <!-- Camera Controls -->
                    <div class="flex gap-4 justify-center mb-4">
                        <button type="button" id="startCamera" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                            Aktifkan Kamera
                        </button>
                        <button type="button" id="captureBtn" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-medium" style="display: none;">
                            Ambil Foto
                        </button>
                        <button type="button" id="retakeBtn" class="bg-yellow-600 text-white px-6 py-3 rounded-lg hover:bg-yellow-700 font-medium" style="display: none;">
                            Foto Ulang
                        </button>
                    </div>
                </div>

                <input type="hidden" name="foto" id="fotoInput">
                @error('foto')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Step 2: GPS Location -->
            <div id="gpsSection" style="display: none;">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-900">Validasi Lokasi</h3>
                    <span class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full font-medium">Otomatis</span>
                </div>
                
                <div id="gpsStatus" class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-600">Mendeteksi lokasi Anda...</p>
                </div>

                <input type="hidden" name="latitude" id="latitudeInput">
                <input type="hidden" name="longitude" id="longitudeInput">
                @error('latitude')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4">
                <a href="{{ route('peserta.dashboard') }}" class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 text-center font-medium">
                    ← Batal
                </a>
                <button type="submit" id="submitBtn" class="flex-1 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-medium">
                    Absen Masuk
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const video = document.getElementById('camera');
    const canvas = document.getElementById('canvas');
    const preview = document.getElementById('preview');
    const startCameraBtn = document.getElementById('startCamera');
    const captureBtn = document.getElementById('captureBtn');
    const retakeBtn = document.getElementById('retakeBtn');
    const fotoInput = document.getElementById('fotoInput');
    const gpsSection = document.getElementById('gpsSection');
    const gpsStatus = document.getElementById('gpsStatus');
    const latitudeInput = document.getElementById('latitudeInput');
    const longitudeInput = document.getElementById('longitudeInput');
    const submitBtn = document.getElementById('submitBtn');
    
    let stream = null;
    let photoTaken = false;
    let locationValid = false;

    // Koordinat Telkom Landmark Tower
    const tltLat = {{ $coordinates['lat'] }};
    const tltLng = {{ $coordinates['lng'] }};
    const maxRadius = {{ $coordinates['radius'] }};

    // Start Camera
    startCameraBtn.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'user' },
                audio: false 
            });
            video.srcObject = stream;
            video.style.display = 'block';
            canvas.style.display = 'none';
            preview.style.display = 'none';
            
            startCameraBtn.style.display = 'none';
            captureBtn.style.display = 'inline-block';
        } catch (error) {
            console.error('Camera error:', error);
            
            let errorMsg = '❌ Tidak dapat mengakses kamera.\n\n';
            errorMsg += '📋 Solusi:\n\n';
            errorMsg += '1️⃣ ENABLE HTTPS (Paling Mudah):\n';
            errorMsg += '   • Klik kanan icon Laragon (system tray)\n';
            errorMsg += '   • Apache → SSL → Enabled (centang)\n';
            errorMsg += '   • Apache → Reload\n';
            errorMsg += '   • Akses: https://telkomlandmark.test\n\n';
            errorMsg += '2️⃣ ATAU Chrome Flag:\n';
            errorMsg += '   • Buka: chrome://flags/#unsafely-treat-insecure-origin-as-secure\n';
            errorMsg += '   • Tambahkan: http://telkomlandmark.test\n';
            errorMsg += '   • Restart Chrome\n\n';
            errorMsg += '3️⃣ Pastikan browser sudah Allow Camera';
            
            alert(errorMsg);
        }
    });

    // Capture Photo
    captureBtn.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0);
        
        // Convert to blob
        canvas.toBlob((blob) => {
            const reader = new FileReader();
            reader.onloadend = () => {
                fotoInput.value = reader.result;
                preview.src = reader.result;
                
                // Show preview
                video.style.display = 'none';
                preview.style.display = 'block';
                
                // Stop camera
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                
                captureBtn.style.display = 'none';
                retakeBtn.style.display = 'inline-block';
                
                photoTaken = true;
                
                // Show GPS section
                gpsSection.style.display = 'block';
                getLocation();
            };
            reader.readAsDataURL(blob);
        }, 'image/jpeg', 0.8);
    });

    // Retake Photo
    retakeBtn.addEventListener('click', () => {
        preview.style.display = 'none';
        fotoInput.value = '';
        retakeBtn.style.display = 'none';
        startCameraBtn.style.display = 'inline-block';
        gpsSection.style.display = 'none';
        
        photoTaken = false;
        locationValid = false;
    });

    // Get GPS Location
    function getLocation() {
        if (!navigator.geolocation) {
            gpsStatus.innerHTML = '<p class="text-red-600">❌ Browser Anda tidak mendukung GPS.</p>';
            return;
        }

        gpsStatus.innerHTML = '<p class="text-blue-600">🔄 Mendeteksi lokasi Anda...</p>';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                
                latitudeInput.value = userLat;
                longitudeInput.value = userLng;
                
                // Calculate distance
                const distance = calculateDistance(userLat, userLng, tltLat, tltLng);
                
                if (distance <= maxRadius) {
                    gpsStatus.innerHTML = `
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-green-700 font-medium">✅ Lokasi Valid</p>
                            <p class="text-sm text-green-600 mt-1">Anda berada di Telkom Landmark Tower</p>
                            <p class="text-xs text-gray-500 mt-1">Jarak dari titik absen: ${distance.toFixed(0)} meter</p>
                        </div>
                    `;
                    locationValid = true;
                } else {
                    gpsStatus.innerHTML = `
                        <div class="bg-red-50 p-4 rounded-lg">
                            <p class="text-red-700 font-medium">❌ Lokasi Terlalu Jauh</p>
                            <p class="text-sm text-red-600 mt-1">Jarak dari kantor: ${distance.toFixed(0)} meter</p>
                            <p class="text-sm text-red-600">Maksimal: ${maxRadius} meter</p>
                            <p class="text-xs text-gray-600 mt-1">Pastikan Anda berada di Telkom Landmark Tower.</p>
                        </div>
                    `;
                    locationValid = false;
                }
            },
            (error) => {
                gpsStatus.innerHTML = '<p class="text-red-600">❌ Tidak dapat mendeteksi lokasi. Pastikan GPS aktif dan izin lokasi diberikan.</p>';
                console.error('GPS error:', error);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    // Calculate distance between two coordinates (Haversine formula)
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000; // Earth radius in meters
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // Form submit validation
    document.getElementById('absenForm').addEventListener('submit', (e) => {
        if (!photoTaken) {
            e.preventDefault();
            alert('⚠️ Silahkan ambil gambar selfie terlebih dahulu!\n\nKlik tombol "Nyalakan Kamera" untuk memulai.');
            return false;
        }
        
        if (!locationValid) {
            e.preventDefault();
            alert('❌ Lokasi Anda tidak valid. Pastikan Anda berada di Telkom Landmark Tower.');
            return false;
        }

        // Convert base64 to file
        if (fotoInput.value) {
            const dataUrl = fotoInput.value;
            const blob = dataURLtoBlob(dataUrl);
            const file = new File([blob], 'selfie.jpg', { type: 'image/jpeg' });
            
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.name = 'foto';
            fileInput.files = dataTransfer.files;
            fileInput.style.display = 'none';
            
            e.target.appendChild(fileInput);
        }
    });

    function dataURLtoBlob(dataurl) {
        const arr = dataurl.split(',');
        const mime = arr[0].match(/:(.*?);/)[1];
        const bstr = atob(arr[1]);
        let n = bstr.length;
        const u8arr = new Uint8Array(n);
        while (n--) {
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new Blob([u8arr], { type: mime });
    }
</script>
@endpush
@endsection

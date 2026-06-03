@extends('layouts.app')

@section('title', 'Ajukan Izin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('peserta.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Ajukan Izin</h1>
        </div>


        <!-- Form -->
        <form action="{{ route('peserta.izin.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-6">
            @csrf

            <!-- Jenis Izin -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Jenis Izin <span class="text-red-500">*</span></label>
                <div class="space-y-3">
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="jenis_izin" value="tidak_masuk" class="mr-3" required {{ old('jenis_izin') == 'tidak_masuk' ? 'checked' : '' }}>
                        <div>
                            <div class="font-semibold text-gray-800">Izin Tidak Masuk</div>
                            <div class="text-sm text-gray-600">Untuk izin sakit, keperluan keluarga, dll</div>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="jenis_izin" value="pulang_cepat" class="mr-3" required {{ old('jenis_izin') == 'pulang_cepat' ? 'checked' : '' }}>
                        <div>
                            <div class="font-semibold text-gray-800">Izin Pulang Cepat</div>
                            <div class="text-sm text-gray-600">Untuk pulang lebih awal hari ini (setelah absen masuk)</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Tanggal -->
            <div id="tanggal-section" class="mb-6 hidden">
                <!-- Weekend Warning - muncul saat user klik Sabtu/Minggu -->
                <div id="weekend-warning" class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded hidden">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                <strong>⚠️ Perhatian!</strong> Sabtu dan Minggu adalah hari libur. Anda tidak perlu mengajukan izin di hari libur.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label id="label-tanggal-mulai" class="block text-gray-700 font-semibold mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required value="{{ old('tanggal_mulai', $today ?? '') }}" min="{{ $today ?? '' }}">
                    </div>
                    <div id="tanggal-selesai-wrapper">
                        <label class="block text-gray-700 font-semibold mb-2">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required value="{{ old('tanggal_selesai', $today ?? '') }}" min="{{ $today ?? '' }}">
                    </div>
                </div>
                <div id="tips-izin-1hari" class="mt-3 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Tips:</strong> Untuk izin <strong>1 hari saja</strong>, isi tanggal yang <strong>sama</strong> di kedua kolom (Tanggal Mulai dan Tanggal Selesai).
                            </p>
                        </div>
                    </div>
                </div>
                <div id="tips-pulang-cepat" class="mt-3 bg-amber-50 border-l-4 border-amber-400 p-4 rounded" style="display: none;">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-700">
                                <strong>Penting:</strong> Izin pulang cepat <strong>hanya bisa digunakan untuk hari ini</strong> dan <strong>setelah Anda melakukan absen masuk</strong> di pagi hari. Untuk izin hari lain, gunakan "Izin Tidak Masuk".
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jam Pulang (untuk pulang cepat) -->
            <div id="jam-pulang-section" class="mb-6 hidden">
                <label class="block text-gray-700 font-semibold mb-2">Jam Pulang Diajukan <span class="text-red-500">*</span></label>
                <input type="time" name="jam_pulang_diajukan" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('jam_pulang_diajukan') }}">
                <p class="text-sm text-gray-600 mt-1">Jam pulang yang Anda rencanakan</p>
            </div>

            <!-- Alasan -->
            <div id="alasan-bukti-section" class="mb-6 hidden">
                <label class="block text-gray-700 font-semibold mb-2">Alasan Izin <span class="text-red-500">*</span></label>
                <textarea name="alasan" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required placeholder="Jelaskan alasan izin Anda...">{{ old('alasan') }}</textarea>
                <p class="text-sm text-gray-600 mt-1">Maksimal 500 karakter</p>
                <!-- Bukti (Opsional) -->
                <div class="mt-6">
                    <label class="block text-gray-700 font-semibold mb-2">Bukti Pendukung (Opsional)</label>
                    <input type="file" name="bukti" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-sm text-gray-600 mt-1">Format: PDF, JPG, PNG. Maksimal 2MB. Contoh: surat dokter, undangan, dll.</p>
                </div>
            </div>

            <!-- Buttons -->
            <div id="tombol-section" class="flex gap-4 hidden">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold transition">
                    Ajukan Izin
                </button>
                <a href="{{ route('peserta.dashboard') }}" class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 font-semibold text-center transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
        // Ganti label tanggal mulai jika pulang_cepat
        function updateLabelTanggalMulai() {
            const jenisIzin = document.querySelector('input[name="jenis_izin"]:checked');
            const label = document.getElementById('label-tanggal-mulai');
            if (jenisIzin && jenisIzin.value === 'pulang_cepat') {
                label.innerHTML = 'Tanggal Izin Pulang Cepat <span class="text-red-500">*</span>';
            } else {
                label.innerHTML = 'Tanggal Mulai <span class="text-red-500">*</span>';
            }
        }
        // Panggil saat radio berubah dan saat load
        document.addEventListener('DOMContentLoaded', function() {
            updateLabelTanggalMulai();
            const radioButtons = document.querySelectorAll('input[name="jenis_izin"]');
            radioButtons.forEach(radio => {
                radio.addEventListener('change', updateLabelTanggalMulai);
            });
        });
    document.addEventListener('DOMContentLoaded', function() {
        // Sembunyikan tanggal-section, alasan-bukti-section, dan tombol-section sampai radio dipilih
        const tanggalSection = document.getElementById('tanggal-section');
        const alasanBuktiSection = document.getElementById('alasan-bukti-section');
        const tombolSection = document.getElementById('tombol-section');
        const radioButtons = document.querySelectorAll('input[name="jenis_izin"]');
        // Jika sudah ada yang terpilih (misal reload karena error), tampilkan
        function updateFormSections() {
            const jenisIzin = document.querySelector('input[name="jenis_izin"]:checked');
            if (jenisIzin) {
                tanggalSection.classList.remove('hidden');
                alasanBuktiSection.classList.remove('hidden');
                tombolSection.classList.remove('hidden');
            } else {
                tanggalSection.classList.add('hidden');
                alasanBuktiSection.classList.add('hidden');
                tombolSection.classList.add('hidden');
            }
        }
        // Inisialisasi saat DOM siap
        updateFormSections();
        // Event listener untuk radio
        radioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                updateFormSections();
                updateTipsBox();
                // ...existing code...
            });
        });
    });
    // Toggle form fields berdasarkan jenis izin
    const radioButtons = document.querySelectorAll('input[name="jenis_izin"]');
    const jamPulangSection = document.getElementById('jam-pulang-section');
    const tanggalSelesaiWrapper = document.getElementById('tanggal-selesai-wrapper');
    const tipsIzin1Hari = document.getElementById('tips-izin-1hari');
    const tipsPulangCepat = document.getElementById('tips-pulang-cepat');
    const tanggalMulaiInput = document.querySelector('input[name="tanggal_mulai"]');
    const tanggalSelesaiInput = document.querySelector('input[name="tanggal_selesai"]');
    const today = '{{ $today ?? "" }}';

    function getTomorrowISO() {
        const d = new Date();
        d.setDate(d.getDate() + 1);
        return d.toISOString().slice(0,10);
    }

    function updateTipsBox() {
        const jenisIzin = document.querySelector('input[name="jenis_izin"]:checked');
        if (jenisIzin && jenisIzin.value === 'pulang_cepat') {
            tipsIzin1Hari.style.display = 'none';
            tipsPulangCepat.style.display = '';
        } else {
            tipsIzin1Hari.style.display = '';
            tipsPulangCepat.style.display = 'none';
        }
    }

    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'pulang_cepat') {
                jamPulangSection.classList.remove('hidden');
                tanggalSelesaiWrapper.classList.add('hidden');
                
                // Set tanggal ke hari ini dan disable
                tanggalMulaiInput.value = today;
                tanggalSelesaiInput.value = today;
                tanggalMulaiInput.setAttribute('readonly', 'readonly');
                tanggalMulaiInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                // ensure min allows today for pulang_cepat
                tanggalMulaiInput.min = today;
                tanggalSelesaiInput.min = today;
            } else {
                jamPulangSection.classList.add('hidden');
                tanggalSelesaiWrapper.classList.remove('hidden');
                
                // Enable kembali field tanggal
                tanggalMulaiInput.removeAttribute('readonly');
                tanggalMulaiInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                // if selecting tidak_masuk, set min to tomorrow to prevent same-day 'tidak_masuk'
                if (this.value === 'tidak_masuk') {
                    const tomorrow = getTomorrowISO();
                    tanggalMulaiInput.min = tomorrow;
                    tanggalSelesaiInput.min = tomorrow;
                    if (new Date(tanggalMulaiInput.value) < new Date(tomorrow)) {
                        tanggalMulaiInput.value = tomorrow;
                        tanggalSelesaiInput.value = tomorrow;
                    }
                } else {
                    // default min to today
                    tanggalMulaiInput.min = today;
                    tanggalSelesaiInput.min = today;
                }
            }
            updateTipsBox();
        });
    });
    // Initial state
    updateTipsBox();
    
    // Check initial state on page load
    const initialJenisIzin = document.querySelector('input[name="jenis_izin"]:checked');
    if (initialJenisIzin && initialJenisIzin.value === 'pulang_cepat') {
        tanggalMulaiInput.value = today;
        tanggalSelesaiInput.value = today;
        tanggalMulaiInput.setAttribute('readonly', 'readonly');
        tanggalMulaiInput.classList.add('bg-gray-100', 'cursor-not-allowed');
        tanggalMulaiInput.min = today;
        tanggalSelesaiInput.min = today;
    } else if (initialJenisIzin && initialJenisIzin.value === 'tidak_masuk') {
        const tomorrow = getTomorrowISO();
        tanggalMulaiInput.min = tomorrow;
        tanggalSelesaiInput.min = tomorrow;
    }
    
    // Sync tanggal_selesai dengan tanggal_mulai untuk pulang_cepat
    tanggalMulaiInput.addEventListener('change', function() {
        const jenisIzin = document.querySelector('input[name="jenis_izin"]:checked');
        if (jenisIzin && jenisIzin.value === 'pulang_cepat') {
            tanggalSelesaiInput.value = this.value;
        }
    });

    // Auto-copy tanggal_mulai ke tanggal_selesai untuk memudahkan izin 1 hari
    // (User masih bisa mengubahnya manual kalau mau izin beberapa hari)
    document.getElementById('tanggal_mulai').addEventListener('change', function() {
        const jenisIzin = document.querySelector('input[name="jenis_izin"]:checked');
        // Hanya untuk "tidak_masuk" (bukan "pulang_cepat" karena sudah ada logic di atas)
        if (!jenisIzin || jenisIzin.value === 'tidak_masuk') {
            document.getElementById('tanggal_selesai').value = this.value;
        }
        // Check weekend setelah tanggal berubah
        checkWeekendDates();
    });

    // Function to check weekend dates and show warning
    function checkWeekendDates() {
        const tanggalMulai = document.getElementById('tanggal_mulai');
        const tanggalSelesai = document.getElementById('tanggal_selesai');
        const warningDiv = document.getElementById('weekend-warning');
        
        if (!tanggalMulai || !tanggalSelesai || !warningDiv) return;
        
        const startDate = tanggalMulai.value ? new Date(tanggalMulai.value) : null;
        const endDate = tanggalSelesai.value ? new Date(tanggalSelesai.value) : null;
        
        let isWeekend = false;
        
        // Check if tanggal mulai is weekend
        if (startDate) {
            const dayStart = startDate.getDay();
            if (dayStart === 0 || dayStart === 6) { // 0 = Sunday, 6 = Saturday
                isWeekend = true;
            }
        }
        
        // Check if tanggal selesai is weekend
        if (endDate) {
            const dayEnd = endDate.getDay();
            if (dayEnd === 0 || dayEnd === 6) {
                isWeekend = true;
            }
        }
        
        // Show or hide warning
        if (isWeekend) {
            warningDiv.classList.remove('hidden');
        } else {
            warningDiv.classList.add('hidden');
        }
    }

    // Add event listener to tanggal_selesai for weekend check
    document.getElementById('tanggal_selesai').addEventListener('change', checkWeekendDates);
    
    // Validasi weekend: notifikasi real-time di atas + backend validation
    // Backend akan mencegah submit jika ada tanggal weekend
</script>
@endsection

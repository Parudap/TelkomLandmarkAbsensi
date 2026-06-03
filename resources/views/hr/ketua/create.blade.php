{{-- Halaman tambah ketua bidang dihapus --}}
                <label class="block text-gray-700 font-semibold mb-2">Nama</label>
                <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Email</label>
                <input type="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Bidang</label>
                <select name="bidang_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <option value="">Pilih Bidang</option>
                    @foreach($bidangList as $bidang)
                        <option value="{{ $bidang->id }}">{{ $bidang->nama_bidang }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold transition">Simpan</button>
                {{-- Link batal ke hr.ketua.index dihapus --}}
        </form>
    </div>
</div>
@endsection

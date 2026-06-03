<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\WelcomeEmail;
use App\Models\Bidang;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    /**
     * Tampilkan form registrasi
     */
    public function showRegistrationForm()
    {
        $bidangList = Bidang::where('is_active', true)
            ->orderBy('nama_bidang')
            ->get();
            
        return view('auth.register', compact('bidangList'));
    }

    /**
     * Handle registrasi peserta magang
     */
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        
        try {
            // Upload surat magang
            $suratPath = null;
            if ($request->hasFile('surat_magang')) {
                $file = $request->file('surat_magang');
                $filename = time() . '_' . $file->getClientOriginalName();
                $suratPath = $file->storeAs('surat_magang', $filename, 'public');
            }

            // Buat user baru
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'peserta_magang',
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'no_telepon' => $request->no_telepon,
                'instansi_asal' => $request->instansi_asal,
                'periode_magang_mulai' => $request->periode_magang_mulai,
                'periode_magang_selesai' => $request->periode_magang_selesai,
                'bidang_id' => $request->bidang_id,
                'surat_magang' => $suratPath,
                'status_approval' => 'pending',
                'is_active' => false,
                'created_at' => \App\Services\TimeService::now(),
                'updated_at' => \App\Services\TimeService::now(),
            ]);

            DB::commit();

            // Tidak perlu kirim email welcome atau verifikasi
            return redirect()->route('register.success')
                ->with('success', 'Registrasi berhasil! Silakan tunggu persetujuan admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Hapus file jika ada error
            if ($suratPath && Storage::disk('public')->exists($suratPath)) {
                Storage::disk('public')->delete($suratPath);
            }

            // Log error untuk debugging
            \Log::error('Registrasi error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Halaman sukses registrasi
     */
    public function success()
    {
        return view('auth.register-success');
    }
}

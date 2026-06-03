<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    /**
     * Tampilkan notice untuk verifikasi email
     */
    public function notice()
    {
        return view('auth.verify-email');
    }

    /**
     * Handle verifikasi email dari link (tanpa perlu login)
     */
    public function verify(Request $request, $id, $hash)
    {
        // Cari user berdasarkan ID
        $user = User::findOrFail($id);

        // Verifikasi hash
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Link verifikasi tidak valid.');
        }

        // Cek apakah sudah terverifikasi
        if ($user->hasVerifiedEmail()) {
            Auth::login($user);
            return redirect()->route('verification.confirmed')
                ->with('info', 'Email Anda sudah diverifikasi sebelumnya. Silakan tunggu persetujuan admin.');
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            
            // Kirim notifikasi ke HR bahwa ada pendaftar baru
            $this->notifyHR($user);
        }

        // Jangan auto-login, biarkan user login manual setelah approved
        return redirect()->route('verification.confirmed')
            ->with('success', 'Email berhasil diverifikasi! Silakan tunggu persetujuan dari admin.');
    }
    
    /**
     * Handle verifikasi email (untuk yang sudah login)
     */
    public function verifyAuthenticated(EmailVerificationRequest $request)
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('verification.confirmed')
                ->with('info', 'Email Anda sudah diverifikasi sebelumnya. Silakan tunggu persetujuan admin.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            
            // Kirim notifikasi ke HR bahwa ada pendaftar baru
            $this->notifyHR($user);
        }

        return redirect()->route('verification.confirmed')
            ->with('success', 'Email berhasil diverifikasi! Silakan tunggu persetujuan dari admin.');
    }

    /**
     * Halaman konfirmasi setelah email terverifikasi
     */
    public function confirmed()
    {
        return view('auth.verification-confirmed');
    }

    /**
     * Resend verification email
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('info', 'Email Anda sudah diverifikasi.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Link verifikasi baru telah dikirim ke email Anda.');
    }

    /**
     * Kirim notifikasi ke semua HR
     */
    protected function notifyHR(User $user)
    {
        $hrUsers = User::where('role', 'hr')
            ->where('is_active', true)
            ->get();

        foreach ($hrUsers as $hr) {
            // Kirim email notifikasi ke HR
            // Mail::to($hr->email)->send(new NewRegistrantNotification($user));
        }
    }
// Email verification sudah tidak digunakan
class EmailVerificationController extends Controller
{
    // Semua fungsi email verification telah dihapus
}

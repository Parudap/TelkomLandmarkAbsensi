<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Tampilkan form login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Paksa session baru sebelum validasi apapun
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->filled('remember');

        // VALIDASI 1: Cek apakah email terdaftar
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Email tidak terdaftar.',
            ]);
        }

        // VALIDASI 2: Cek password benar
        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // VALIDASI 3: Cek apakah akun aktif (langsung setelah login berhasil)
        if (!$user->is_active) {
            Auth::logout(); // Logout segera jika tidak aktif
            $request->session()->invalidate();
            throw ValidationException::withMessages([
                'email' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ]);
        }

        // ✅ Semua validasi terpenuhi - Login berhasil
        $request->session()->regenerate();

        // Redirect berdasarkan role
        return $this->redirectUser($user);
    }

    /**
     * Redirect user berdasarkan role
     */
    protected function redirectUser($user)
    {
        return match($user->role) {
            'hr' => redirect()->intended(route('hr.dashboard')),
            'peserta_magang' => redirect()->intended(route('peserta.dashboard')),
            default => redirect()->intended('/'),
        };
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda berhasil logout.');
    }
}

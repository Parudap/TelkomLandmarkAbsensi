<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Halaman home
     */
    public function index()
    {
        // Jika user sudah login
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->is_active) {
                return $this->redirectToDashboard($user);
            }
        }
        
        // Jika belum login atau tidak ada kondisi di atas, tampilkan welcome page
        return view('welcome');
    }
    
    /**
     * Redirect ke dashboard sesuai role
     */
    protected function redirectToDashboard($user)
    {
        switch ($user->role) {
            case 'peserta_magang':
                return redirect()->route('peserta.dashboard');
            case 'hr':
                return redirect()->route('hr.dashboard');
            default:
                return redirect()->route('login');
        }
    }
}

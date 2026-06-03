<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\TimeService;

class CheckPeriodeMagang
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'peserta_magang') {
            $user = Auth::user();
            
            // Use centralized TimeService (supports testing mode and cache override)
            $now = TimeService::now();

            // Cek apakah user punya periode magang
            if (!$user->periode_magang_mulai || !$user->periode_magang_selesai) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Data periode magang Anda belum lengkap. Silakan hubungi HR.');
            }

            $periodeStart = Carbon::parse($user->periode_magang_mulai)->startOfDay();
            $periodeEnd = Carbon::parse($user->periode_magang_selesai)->endOfDay();

            // Cek apakah periode magang sudah berakhir
            if ($now->gt($periodeEnd)) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Periode magang Anda telah berakhir pada ' . $periodeEnd->format('d F Y') . '. Terima kasih atas partisipasi Anda.');
            }

            // Cek apakah periode magang belum dimulai
            if ($now->lt($periodeStart)) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Periode magang Anda belum dimulai. Periode magang akan dimulai pada ' . $periodeStart->format('d F Y') . '.');
            }
        }

        return $next($request);
    }
}

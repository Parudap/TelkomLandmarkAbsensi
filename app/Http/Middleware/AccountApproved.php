<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Refresh user dari database untuk memastikan status terbaru
        $user->refresh();

        // Cek apakah akun sudah disetujui
        if ($user->status_approval !== 'approved') {
            // Logout user dan redirect ke login dengan pesan
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda masih menunggu persetujuan admin. Anda akan menerima email setelah disetujui.');
        }

        // Cek apakah akun aktif
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda tidak aktif. Silakan hubungi administrator.');
        }

        return $next($request);
    }
}

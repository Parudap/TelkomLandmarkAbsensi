<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ApprovalLog;
use App\Mail\RegistrationApprovedEmail;
use App\Mail\RegistrationRejectedEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RegistrasiController extends Controller
{
    // Form tambah akun peserta
    public function create()
    {
        $bidangList = \App\Models\Bidang::where('is_active', true)->get();
        return view('hr.registrasi.create', compact('bidangList'));
    }

    // Simpan akun peserta baru
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'no_telepon' => 'required|string|max:20',
            'bidang_id' => 'required|exists:bidang,id',
            'password' => 'required|string|min:6',
            'periode_magang_mulai' => 'required|date',
            'periode_magang_selesai' => 'required|date|after_or_equal:periode_magang_mulai',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_telepon' => $validated['no_telepon'],
            'bidang_id' => $validated['bidang_id'],
            'password' => bcrypt($validated['password']),
            'role' => 'peserta_magang',
            'is_active' => true,
            'status_approval' => 'approved',
            'periode_magang_mulai' => $validated['periode_magang_mulai'],
            'periode_magang_selesai' => $validated['periode_magang_selesai'],
            'created_at' => \App\Services\TimeService::now(),
            'updated_at' => \App\Services\TimeService::now(),
        ]);

        return redirect()->route('hr.dashboard')->with('success', 'Akun peserta berhasil dibuat!');
    }
}
{
    // Semua proses approval registrasi dinonaktifkan. Akan diganti dengan fitur tambah akun peserta oleh admin/HR.
}

<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Krs;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $uktStatus = Pembayaran::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'lunas')
            ->exists() ? 'Lunas' : 'Belum';

        $krsAktual = Krs::where('mahasiswa_id', $mahasiswa->id)->first();

        return view('mahasiswa.dashboard', [
            'title' => 'Dashboard Mahasiswa',
            'mahasiswa' => $mahasiswa,
            'uktStatus' => $uktStatus,
            'krsStatus' => $krsAktual?->status ?? 'Belum Diisi',
            'ipk' => $mahasiswa->ipk,
        ]);
    }
}

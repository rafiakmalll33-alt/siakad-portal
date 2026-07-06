<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Krs;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        $hariIni = now()->format('l');
        $hariMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];

        $jadwalHariIni = Kelas::where('dosen_id', $user->id)
            ->with('jadwals')
            ->get()
            ->filter(fn($k) => $k->jadwals->pluck('hari')->contains($hariMap[$hariIni] ?? ''));

        $totalKelas = Kelas::where('dosen_id', $user->id)->count();
        $mahasiswaBimbingan = $dosen->mahasiswaBimbingan;
        $krsMenunggu = Krs::whereIn('mahasiswa_id', $mahasiswaBimbingan->pluck('id'))->where('status', 'pending')->count();

        return view('dosen.dashboard', [
            'title' => 'Dashboard Dosen',
            'jadwalHariIni' => $jadwalHariIni,
            'totalKelas' => $totalKelas,
            'krsMenunggu' => $krsMenunggu,
            'mahasiswaBimbingan' => $mahasiswaBimbingan,
        ]);
    }
}

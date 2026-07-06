<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $nilais = Nilai::where('mahasiswa_id', $mahasiswa->id)
            ->with('kelas.mataKuliah')
            ->get();

        return view('mahasiswa.nilai.index', ['nilais' => $nilais, 'title' => 'Nilai & KHS']);
    }

    public function exportPDF()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $nilais = Nilai::where('mahasiswa_id', $mahasiswa->id)
            ->with('kelas.mataKuliah')
            ->get();

        return view('mahasiswa.nilai.khs', ['mahasiswa' => $mahasiswa, 'nilais' => $nilais]);
    }
}

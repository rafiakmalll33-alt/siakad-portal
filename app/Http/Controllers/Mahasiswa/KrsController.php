<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Krs;
use App\Models\Kelas;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KrsController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;
        $tahunAkademik = TahunAkademik::where('status', 'aktif')->first();

        if (!$tahunAkademik) {
            return redirect()->back()->with('error', 'Tahun akademik aktif tidak ditemukan');
        }

        $kelas = Kelas::where('tahun_akademik_id', $tahunAkademik->id)
            ->with('mataKuliah', 'dosen', 'jadwals')
            ->get();

        $krsExisting = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->first();

        return view('mahasiswa.krs.create', [
            'title' => 'Pengisian KRS',
            'kelas' => $kelas,
            'tahunAkademik' => $tahunAkademik,
            'krsExisting' => $krsExisting,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;
        $tahunAkademik = TahunAkademik::where('status', 'aktif')->first();

        $validated = $request->validate([
            'kelas_ids' => 'required|array|min:1',
            'kelas_ids.*' => 'required|exists:kelas,id',
        ]);

        $kelas = Kelas::whereIn('id', $validated['kelas_ids'])->get();
        $totalSks = $kelas->sum(fn($k) => $k->mataKuliah->sks);

        if ($totalSks > 24) {
            return back()->with('error', 'Total SKS tidak boleh melebihi 24');
        }

        $krs = Krs::updateOrCreate(
            ['mahasiswa_id' => $mahasiswa->id, 'tahun_akademik_id' => $tahunAkademik->id],
            ['kelas_ids' => $validated['kelas_ids'], 'total_sks' => $totalSks, 'status' => 'pending']
        );

        return redirect()->route('mahasiswa.krs.show', $krs->id)->with('success', 'KRS berhasil disimpan');
    }

    public function show(Krs $krs)
    {
        $krs->load('mahasiswa', 'tahunAkademik');
        $kelasData = Kelas::whereIn('id', $krs->kelas_ids)->with('mataKuliah', 'dosen')->get();
        return view('mahasiswa.krs.show', ['krs' => $krs, 'kelasData' => $kelasData, 'title' => 'Detail KRS']);
    }
}

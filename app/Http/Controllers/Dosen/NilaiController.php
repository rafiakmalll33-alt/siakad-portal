<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $kelas = Kelas::where('dosen_id', $user->id)->with('nilais')->get();
        return view('dosen.nilai.index', ['kelas' => $kelas, 'title' => 'Input Nilai']);
    }

    public function edit(Kelas $kelas)
    {
        $kelas->load(['nilais', 'mataKuliah']);
        $nilais = Nilai::where('kelas_id', $kelas->id)->get();
        return view('dosen.nilai.edit', ['kelas' => $kelas, 'nilais' => $nilais, 'title' => 'Edit Nilai']);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nilais' => 'required|array',
            'nilais.*.mahasiswa_id' => 'required|integer',
            'nilais.*.tugas' => 'nullable|numeric|min:0|max:100',
            'nilais.*.uts' => 'nullable|numeric|min:0|max:100',
            'nilais.*.uas' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($validated['nilais'] as $data) {
            $nilai = Nilai::firstOrCreate(['kelas_id' => $kelas->id, 'mahasiswa_id' => $data['mahasiswa_id']]);
            $nilai->tugas = $data['tugas'] ?? null;
            $nilai->uts = $data['uts'] ?? null;
            $nilai->uas = $data['uas'] ?? null;
            $nilai->hitungNilaiAkhir();
            $nilai->save();
        }
        return redirect()->route('dosen.nilai.index')->with('success', 'Nilai berhasil disimpan');
    }

    public function exportPDF(Kelas $kelas)
    {
        $kelas->load(['nilais', 'mataKuliah', 'dosen']);
        return view('dosen.nilai.pdf', ['kelas' => $kelas]);
    }
}

<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $kelas = Kelas::where('dosen_id', $user->id)->with('absensis')->get();
        return view('dosen.absensi.index', ['kelas' => $kelas, 'title' => 'Input Absensi']);
    }

    public function edit(Kelas $kelas)
    {
        $kelas->load(['absensis', 'mataKuliah']);
        $pertemuan = range(1, 16);
        return view('dosen.absensi.edit', ['kelas' => $kelas, 'pertemuan' => $pertemuan, 'title' => 'Edit Absensi']);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'absensis' => 'required|array',
            'absensis.*.mahasiswa_id' => 'required|integer',
            'absensis.*.pertemuan_ke' => 'required|integer',
            'absensis.*.status' => 'required|in:hadir,sakit,izin,alpa',
        ]);

        foreach ($validated['absensis'] as $data) {
            Absensi::updateOrCreate(
                ['kelas_id' => $kelas->id, 'mahasiswa_id' => $data['mahasiswa_id'], 'pertemuan_ke' => $data['pertemuan_ke']],
                ['status' => $data['status']]
            );
        }
        return redirect()->route('dosen.absensi.index')->with('success', 'Absensi berhasil disimpan');
    }
}

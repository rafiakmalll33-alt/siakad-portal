<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Krs;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KrsApprovalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dosen = $user->dosen;
        $mahasiswaBimbingan = $dosen->mahasiswaBimbingan->pluck('id');

        $krsMenunggu = Krs::with(['mahasiswa', 'tahunAkademik'])
            ->whereIn('mahasiswa_id', $mahasiswaBimbingan)
            ->where('status', 'pending')
            ->paginate(10);

        return view('dosen.krs.index', ['krsMenunggu' => $krsMenunggu, 'title' => 'Persetujuan KRS Mahasiswa Bimbingan']);
    }

    public function detail(Krs $krs)
    {
        $krs->load(['mahasiswa', 'tahunAkademik']);
        $kelasData = Kelas::whereIn('id', $krs->kelas_ids)->with('mataKuliah', 'dosen')->get();
        return view('dosen.krs.detail', ['krs' => $krs, 'kelasData' => $kelasData, 'title' => 'Detail KRS']);
    }

    public function approve(Request $request, Krs $krs)
    {
        $krs->update([
            'status' => 'disetujui',
            'disetujui_oleh' => Auth::id(),
            'disetujui_tanggal' => now(),
        ]);
        return redirect()->back()->with('success', 'KRS berhasil disetujui');
    }

    public function reject(Request $request, Krs $krs)
    {
        $validated = $request->validate(['catatan_revisi' => 'required|string|min:10']);
        $krs->update([
            'status' => 'ditolak',
            'catatan_revisi' => $validated['catatan_revisi'],
            'disetujui_oleh' => Auth::id(),
            'disetujui_tanggal' => now(),
        ]);
        return redirect()->back()->with('success', 'KRS ditolak. Mahasiswa dapat merevisi.');
    }
}

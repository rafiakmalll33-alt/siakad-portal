<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Krs;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KrsApprovalController extends Controller
{
    public function index()
    {
        $krsMenunggu = Krs::with(['mahasiswa', 'tahunAkademik'])
            ->where('status', 'pending')
            ->paginate(10);

        return view('admin.krs.index', ['krsMenunggu' => $krsMenunggu, 'title' => 'Persetujuan KRS']);
    }

    public function detail(Krs $krs)
    {
        $krs->load(['mahasiswa', 'tahunAkademik']);
        $kelasData = Kelas::whereIn('id', $krs->kelas_ids)->with('mataKuliah', 'dosen')->get();

        return view('admin.krs.detail', ['krs' => $krs, 'kelasData' => $kelasData, 'title' => 'Detail KRS']);
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

        return redirect()->back()->with('success', 'KRS ditolak. Mahasiswa dapat merevisi KRS-nya.');
    }
}

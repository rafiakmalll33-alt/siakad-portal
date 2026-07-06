<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;
        $pembayarans = Pembayaran::where('mahasiswa_id', $mahasiswa->id)
            ->with('ukt', 'tahunAkademik')
            ->latest()
            ->get();

        return view('mahasiswa.pembayaran.index', ['pembayarans' => $pembayarans, 'title' => 'Pembayaran UKT']);
    }

    public function create()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;
        $tahunAkademik = TahunAkademik::where('status', 'aktif')->first();
        return view('mahasiswa.pembayaran.create', ['mahasiswa' => $mahasiswa, 'tahunAkademik' => $tahunAkademik, 'title' => 'Unggah Bukti Pembayaran']);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $validated = $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'metode_pembayaran' => 'required|in:transfer,qris',
            'bukti_bayar' => 'required|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('bukti_bayar')) {
            $path = $request->file('bukti_bayar')->store('pembayaran', 'public');
        }

        Pembayaran::create([
            'mahasiswa_id' => $mahasiswa->id,
            'ukt_id' => $mahasiswa->ukt_id,
            'tahun_akademik_id' => $validated['tahun_akademik_id'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'jumlah_bayar' => $mahasiswa->ukt->nominal,
            'bukti_bayar' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('mahasiswa.pembayaran.index')->with('success', 'Bukti pembayaran berhasil diunggah');
    }
}

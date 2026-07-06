<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Mahasiswa;
use App\Models\Ukt;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayarans = Pembayaran::with(['mahasiswa', 'ukt', 'tahunAkademik'])
            ->latest()
            ->paginate(15);

        $statistik = [
            'pending' => Pembayaran::where('status', 'pending')->count(),
            'lunas' => Pembayaran::where('status', 'lunas')->count(),
            'total_pending' => Pembayaran::where('status', 'pending')->sum('jumlah_bayar'),
            'total_lunas' => Pembayaran::where('status', 'lunas')->sum('jumlah_bayar'),
        ];

        return view('admin.pembayaran.index', ['pembayarans' => $pembayarans, 'statistik' => $statistik, 'title' => 'Validasi Pembayaran']);
    }

    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['mahasiswa', 'ukt', 'tahunAkademik']);
        return view('admin.pembayaran.show', ['pembayaran' => $pembayaran, 'title' => 'Detail Pembayaran']);
    }

    public function verify(Request $request, Pembayaran $pembayaran)
    {
        $validated = $request->validate(['action' => 'required|in:approve,reject', 'catatan' => 'nullable|string']);

        if ($validated['action'] === 'approve') {
            $pembayaran->update([
                'status' => 'lunas',
                'diverifikasi_oleh' => Auth::id(),
                'diverifikasi_tanggal' => now(),
                'catatan' => $validated['catatan'],
            ]);
            return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi sebagai Lunas');
        } else {
            $pembayaran->update(['status' => 'pending', 'catatan' => $validated['catatan']]);
            return redirect()->back()->with('success', 'Pembayaran ditolak.');
        }
    }

    public function createCash()
    {
        $mahasiswas = Mahasiswa::with('user')->get();
        $ukts = Ukt::all();
        $tahunAkademik = TahunAkademik::where('status', 'aktif')->first();
        return view('admin.pembayaran.create-cash', ['mahasiswas' => $mahasiswas, 'ukts' => $ukts, 'tahunAkademik' => $tahunAkademik, 'title' => 'Input Pembayaran Cash']);
    }

    public function storeCash(Request $request)
    {
        $validated = $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'ukt_id' => 'required|exists:ukts,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'jumlah_bayar' => 'required|numeric|min:1000',
            'catatan' => 'nullable|string',
        ]);

        Pembayaran::create([
            'mahasiswa_id' => $validated['mahasiswa_id'],
            'ukt_id' => $validated['ukt_id'],
            'tahun_akademik_id' => $validated['tahun_akademik_id'],
            'metode_pembayaran' => 'cash',
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'status' => 'lunas',
            'diverifikasi_oleh' => Auth::id(),
            'diverifikasi_tanggal' => now(),
            'catatan' => $validated['catatan'],
        ]);

        return redirect()->route('admin.pembayaran.index')->with('success', 'Pembayaran cash berhasil dicatat');
    }
}

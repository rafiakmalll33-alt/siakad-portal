<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Pembayaran;
use App\Models\Krs;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMahasiswa = Mahasiswa::count();
        $totalDosen = User::whereHas('role', fn($q) => $q->where('name', 'dosen'))->count();
        
        $pendapatanUKT = Pembayaran::where('status', 'lunas')
            ->whereMonth('updated_at', now()->month)
            ->sum('jumlah_bayar');
        
        $krsMenunggu = Krs::where('status', 'pending')->count();
        
        $statusPembayaran = Pembayaran::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return view('admin.dashboard', [
            'title' => 'Dashboard Admin',
            'totalMahasiswa' => $totalMahasiswa,
            'totalDosen' => $totalDosen,
            'pendapatanUKT' => $pendapatanUKT,
            'krsMenunggu' => $krsMenunggu,
            'statusPembayaran' => $statusPembayaran,
        ]);
    }
}

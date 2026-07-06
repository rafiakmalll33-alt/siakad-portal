<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Ukt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswas = Mahasiswa::with(['user', 'ukt', 'dosenWali'])->paginate(15);
        return view('admin.mahasiswa.index', ['mahasiswas' => $mahasiswas, 'title' => 'Manajemen Mahasiswa']);
    }

    public function create()
    {
        $ukts = Ukt::all();
        $dosens = User::whereHas('role', fn($q) => $q->where('name', 'dosen'))->get();
        return view('admin.mahasiswa.create', ['ukts' => $ukts, 'dosens' => $dosens, 'title' => 'Tambah Mahasiswa']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'nim' => 'required|unique:mahasiswas',
            'ukt_id' => 'required|exists:ukts,id',
            'dosen_wali_id' => 'nullable|exists:users,id',
            'no_hp' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('password'),
            'role_id' => 3,
            'no_hp' => $validated['no_hp'] ?? null,
            'status' => 'aktif',
        ]);

        Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $validated['nim'],
            'ukt_id' => $validated['ukt_id'],
            'dosen_wali_id' => $validated['dosen_wali_id'],
            'status_kuliah' => 'aktif',
        ]);

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan');
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $ukts = Ukt::all();
        $dosens = User::whereHas('role', fn($q) => $q->where('name', 'dosen'))->get();
        return view('admin.mahasiswa.edit', ['mahasiswa' => $mahasiswa, 'ukts' => $ukts, 'dosens' => $dosens, 'title' => 'Edit Mahasiswa']);
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $mahasiswa->user_id,
            'nim' => 'required|unique:mahasiswas,nim,' . $mahasiswa->id,
            'ukt_id' => 'required|exists:ukts,id',
            'dosen_wali_id' => 'nullable|exists:users,id',
            'no_hp' => 'nullable|string',
            'status_kuliah' => 'required|in:aktif,nonaktif,cuti,lulus',
        ]);

        $mahasiswa->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
        ]);

        $mahasiswa->update([
            'nim' => $validated['nim'],
            'ukt_id' => $validated['ukt_id'],
            'dosen_wali_id' => $validated['dosen_wali_id'],
            'status_kuliah' => $validated['status_kuliah'],
        ]);

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->user->delete();
        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus');
    }
}

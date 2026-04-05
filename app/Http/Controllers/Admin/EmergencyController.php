<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmergencyNumber;

class EmergencyController extends Controller
{
    // Menampilkan daftar semua nomor darurat
    public function index()
    {
        $numbers = EmergencyNumber::latest()->get(); // ambil data terbaru
        return view('admin.emergency.index', compact('numbers'));
    }

    // Menampilkan form tambah nomor darurat
    public function create()
    {
        return view('admin.emergency.create');
    }

    // Menyimpan data nomor darurat baru
    public function store(Request $request)
    {
        // ================= VALIDASI INPUT =================
        $request->validate([
            'nama' => 'required|string|max:255',
            'keperluan' => 'required|string|max:255',
            'nomor' => 'required|string|max:30',
            'keterangan' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
        ]);

        // ================= SIMPAN DATA =================
        EmergencyNumber::create(
            $request->only(['nama', 'keperluan', 'nomor', 'keterangan', 'icon'])
        );

        // ================= REDIRECT =================
        return redirect()->route('admin.emergency.index')
            ->with('success', 'Nomor darurat berhasil ditambahkan!');
    }

    // Menampilkan form edit nomor darurat
    public function edit($id)
    {
        $number = EmergencyNumber::findOrFail($id);
        return view('admin.emergency.edit', compact('number'));
    }

    // Mengupdate data nomor darurat
    public function update(Request $request, $id)
    {
        // ================= VALIDASI INPUT =================
        $request->validate([
            'nama' => 'required|string|max:255',
            'nomor' => 'required|string|max:50',
            'icon' => 'nullable|string|max:10',
        ]);

        // ================= UPDATE DATA =================
        $number = EmergencyNumber::findOrFail($id);
        $number->update(
            $request->only(['nama', 'nomor', 'icon'])
        );

        return redirect()->route('admin.emergency.index')
            ->with('success', 'Nomor darurat berhasil diperbarui!');
    }

    // Menghapus data nomor darurat
    public function destroy($id)
    {
        EmergencyNumber::findOrFail($id)->delete();

        return redirect()->route('admin.emergency.index')
            ->with('success', 'Nomor darurat berhasil dihapus!');
    }
}
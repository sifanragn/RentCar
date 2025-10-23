<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use Illuminate\Support\Facades\Storage;

class DriverAdminController extends Controller
{
    /**
     * 📋 Tampilkan semua driver
     */
    public function index()
    {
        $drivers = Driver::orderBy('status', 'desc')->get();
        return view('admin.drivers.index', compact('drivers'));
    }

    /**
     * 🧾 Form tambah driver baru
     */
    public function create()
    {
        return view('admin.drivers.create');
    }

    /**
     * 💾 Simpan data driver baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'            => 'required|string|max:100',
            'no_hp'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:100',
            'foto'            => 'nullable|image|max:2048',
            'sim_number'      => 'nullable|string|max:50',
            'harga_per_hari'  => 'required|numeric|min:0',
            'pengalaman'      => 'nullable|string|max:100',
            'lokasi'          => 'nullable|string|max:100',
            'deskripsi'       => 'nullable|string',
            'status'          => 'nullable|in:aktif,nonaktif',
            'status_verifikasi' => 'nullable|in:menunggu,disetujui,ditolak',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('drivers', 'public');
        }

        Driver::create($data);

        return redirect()
            ->route('admin.drivers.index')
            ->with('success', '✅ Driver baru berhasil ditambahkan!');
    }

    /**
     * 🔍 Tampilkan detail driver
     */
    public function show(Driver $driver)
    {
        return view('admin.drivers.show', compact('driver'));
    }

    /**
     * ✏ Form edit driver
     */
    public function edit(Driver $driver)
    {
        return view('admin.drivers.edit', compact('driver'));
    }

    /**
     * 🔁 Update data driver
     */
    public function update(Request $request, Driver $driver)
    {
        $data = $request->validate([
            'nama'            => 'required|string|max:100',
            'no_hp'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:100',
            'foto'            => 'nullable|image|max:2048',
            'sim_number'      => 'nullable|string|max:50',
            'harga_per_hari'  => 'required|numeric|min:0',
            'pengalaman'      => 'nullable|string|max:100',
            'lokasi'          => 'nullable|string|max:100',
            'deskripsi'       => 'nullable|string',
            'status'          => 'nullable|in:aktif,nonaktif',
            'status_verifikasi' => 'nullable|in:menunggu,disetujui,ditolak',
        ]);

        // Jika upload foto baru, hapus yang lama
        if ($request->hasFile('foto')) {
            if ($driver->foto && Storage::disk('public')->exists($driver->foto)) {
                Storage::disk('public')->delete($driver->foto);
            }
            $data['foto'] = $request->file('foto')->store('drivers', 'public');
        }

        $driver->update($data);

        return redirect()
            ->route('admin.drivers.index')
            ->with('success', '✅ Data driver berhasil diperbarui!');
    }

    /**
     * 🗑 Hapus driver
     */
    public function destroy(Driver $driver)
    {
        // Hapus foto jika ada
        if ($driver->foto && Storage::disk('public')->exists($driver->foto)) {
            Storage::disk('public')->delete($driver->foto);
        }

        $driver->delete();

        return back()->with('success', '🗑 Driver berhasil dihapus!');
    }
}

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
        'foto_sim'        => 'nullable|image|max:2048',
        'foto_ktp'        => 'nullable|image|max:2048',
        'foto_kk'         => 'nullable|image|max:2048',
        'sim_number'      => 'nullable|string|max:50',
        'harga_per_hari'  => 'required|numeric|min:0',
        'pengalaman'      => 'nullable|string|max:100',
        'lokasi'          => 'nullable|string|max:100',
        'deskripsi'       => 'nullable|string',
        'status'          => 'nullable|in:aktif,nonaktif',
    ]);

    $data['status_verifikasi'] = 'disetujui'; // ✅ otomatis disetujui

    // Upload semua file
    if ($request->hasFile('foto')) {
        $data['foto'] = $request->file('foto')->store('drivers/foto', 'public');
    }
    if ($request->hasFile('foto_sim')) {
        $data['foto_sim'] = $request->file('foto_sim')->store('drivers/sim', 'public');
    }
    if ($request->hasFile('foto_ktp')) {
        $data['foto_ktp'] = $request->file('foto_ktp')->store('drivers/ktp', 'public');
    }
    if ($request->hasFile('foto_kk')) {
        $data['foto_kk'] = $request->file('foto_kk')->store('drivers/kk', 'public');
    }

    Driver::create($data);

    return redirect()->route('admin.drivers.index')
        ->with('success', '✅ Driver baru berhasil ditambahkan!');
}


    /**
     * 🔍 Detail driver
     */
    public function show(Driver $driver)
    {
        return view('admin.drivers.show', compact('driver'));
    }

    /**
     * ✏ Edit driver
     */
    public function edit(Driver $driver)
    {
        return view('admin.drivers.edit', compact('driver'));
    }

    /**
     * 🔁 Update driver
     */
    public function update(Request $request, Driver $driver)
{
    $data = $request->validate([
        'nama'            => 'required|string|max:100',
        'no_hp'           => 'nullable|string|max:20',
        'email'           => 'nullable|email|max:100',
        'foto'            => 'nullable|image|max:2048',
        'foto_sim'        => 'nullable|image|max:2048',
        'foto_ktp'        => 'nullable|image|max:2048',
        'foto_kk'         => 'nullable|image|max:2048',
        'sim_number'      => 'nullable|string|max:50',
        'harga_per_hari'  => 'required|numeric|min:0',
        'pengalaman'      => 'nullable|string|max:100',
        'lokasi'          => 'nullable|string|max:100',
        'deskripsi'       => 'nullable|string',
        'status'          => 'nullable|in:aktif,nonaktif',
    ]);

    $data['status_verifikasi'] = 'disetujui';

    // ✅ Hapus & ganti foto lama kalau diupload baru
    foreach (['foto', 'foto_sim', 'foto_ktp', 'foto_kk'] as $field) {
        if ($request->hasFile($field)) {
            if ($driver->$field && Storage::disk('public')->exists($driver->$field)) {
                Storage::disk('public')->delete($driver->$field);
            }
            $folder = match($field) {
                'foto' => 'drivers/foto',
                'foto_sim' => 'drivers/sim',
                'foto_ktp' => 'drivers/ktp',
                'foto_kk' => 'drivers/kk',
            };
            $data[$field] = $request->file($field)->store($folder, 'public');
        }
    }

    $driver->update($data);

    return redirect()->route('admin.drivers.index')
        ->with('success', '✅ Data driver berhasil diperbarui!');
}

    /**
     * 🗑 Hapus driver
     */
    public function destroy(Driver $driver)
    {
        if ($driver->foto && Storage::disk('public')->exists($driver->foto)) {
            Storage::disk('public')->delete($driver->foto);
        }
        if ($driver->foto_sim && Storage::disk('public')->exists($driver->foto_sim)) {
            Storage::disk('public')->delete($driver->foto_sim);
        }

        $driver->delete();

        return back()->with('success', '🗑 Driver berhasil dihapus!');
    }
}

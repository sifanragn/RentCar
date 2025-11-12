<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmergencyNumber;

class EmergencyController extends Controller
{
    public function index()
    {
        $numbers = EmergencyNumber::latest()->get();
        return view('admin.emergency.index', compact('numbers'));
    }

    public function create()
    {
        return view('admin.emergency.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'keperluan' => 'required|string|max:255',
        'nomor' => 'required|string|max:30',
        'keterangan' => 'nullable|string',
        'icon' => 'nullable|string|max:10',
    ]);

    EmergencyNumber::create($request->only(['nama', 'keperluan', 'nomor', 'keterangan', 'icon']));

    return redirect()->route('admin.emergency.index')
        ->with('success', 'Nomor darurat berhasil ditambahkan!');
}


    public function edit($id)
    {
        $number = EmergencyNumber::findOrFail($id);
        return view('admin.emergency.edit', compact('number'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nomor' => 'required|string|max:50',
            'icon' => 'nullable|string|max:10',
        ]);

        $number = EmergencyNumber::findOrFail($id);
        $number->update($request->only(['nama', 'nomor', 'icon']));
        return redirect()->route('admin.emergency.index')->with('success', 'Nomor darurat berhasil diperbarui!');
    }

    public function destroy($id)
    {
        EmergencyNumber::findOrFail($id)->delete();
        return redirect()->route('admin.emergency.index')->with('success', 'Nomor darurat berhasil dihapus!');
    }
}

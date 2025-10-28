<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\CarCapacity;
use App\Models\CarPhoto;   // ⬅️ Pindah ke sini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class CarAdminController extends Controller
{
    // ====================== MOBIL ============================

    public function index()
{
    $cars = Car::with(['brand', 'capacity', 'photos'])
        ->orderBy('created_at', 'desc')
        ->get();

    return view('admin.mobil.index', compact('cars'));
}


    public function create()
    {
        $brands = CarBrand::orderBy('nama_merek')->get();
        $capacities = CarCapacity::orderBy('jumlah_orang')->get();
        return view('admin.mobil.create', compact('brands', 'capacities'));
    }

  public function show($id)
{
    $car = Car::with(['brand', 'capacity'])->findOrFail($id);

    if (request()->ajax()) {
        // hanya ubah cara return → jadi `response()->view`
        // biar modal bisa baca HTML parsial dengan benar
        return response()->view('admin.mobil.partials.show', compact('car'));
    }

    // fallback tetap sama persis
    return view('admin.mobil.show', compact('car'));
}

public function store(Request $request)
{
    $request->validate([
    'brand_id' => 'required|exists:car_brands,brand_id',
    'model' => 'required|string|max:100',
    'tahun' => 'required|integer|min:1900|max:' . date('Y'),
    'warna' => 'required|string|max:50',
    'tipe_transmisi' => 'required|in:manual,otomatis',
    'capacity_id' => 'required|exists:car_capacities,capacity_id',
    'bahan_bakar' => 'required|in:bensin,diesel,hybrid',
    'harga_sewa_per_jam' => 'required|numeric|min:0',
    'lokasi' => 'required|string|max:100',
    'kilometer' => 'required|integer|min:0',
    'liter_tangki' => 'required|integer|min:1',
    'deskripsi' => 'nullable|string',
    'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    'gallery.*' => 'image|mimes:jpeg,png,jpg|max:2048',
]);


    // Simpan foto utama
    $path = $request->file('foto')->store('cars', 'public');

    // Simpan data mobil
    $car = Car::create([
        'brand_id' => $request->brand_id,
        'model' => $request->model,
        'tahun' => $request->tahun,
        'warna' => $request->warna,
        'tipe_transmisi' => $request->tipe_transmisi,
        'capacity_id' => $request->capacity_id,
        'bahan_bakar' => $request->bahan_bakar,
        'harga_sewa_per_jam' => $request->harga_sewa_per_jam,
        'status' => 'tersedia',
        'lokasi' => $request->lokasi,
        'kilometer' => $request->kilometer,
        'liter_tangki' => $request->liter_tangki,
        'deskripsi' => $request->deskripsi,
        'foto' => $path,
    ]);

    // Simpan foto tambahan (jika ada)
    if ($request->hasFile('gallery')) {
        foreach ($request->file('gallery') as $file) {
            $galleryPath = $file->store('cars/gallery', 'public');
            CarPhoto::create([
                'car_id' => $car->car_id,
                'path' => $galleryPath,
            ]);
        }
    }

    return redirect()->route('cars.index')->with('success', 'Mobil dan foto tambahan berhasil ditambahkan.');
}


    public function edit($id)
    {
        $car = Car::findOrFail($id);
        $brands = CarBrand::orderBy('nama_merek')->get();
        $capacities = CarCapacity::orderBy('jumlah_orang')->get();
        return view('admin.mobil.edit', compact('car', 'brands', 'capacities'));
    }

    public function update(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        $request->validate([
        'brand_id' => 'required|exists:car_brands,brand_id',
        'model' => 'required|string|max:100',
        'tahun' => 'required|integer|min:1900|max:' . date('Y'),
        'warna' => 'required|string|max:50',
        'tipe_transmisi' => 'required|in:manual,otomatis',
        'capacity_id' => 'required|exists:car_capacities,capacity_id',
        'bahan_bakar' => 'required|in:bensin,diesel,hybrid',
        'harga_sewa_per_jam' => 'required|numeric|min:0',
        'lokasi' => 'required|string|max:100',
        'kilometer' => 'required|integer|min:0',
        'liter_tangki' => 'required|integer|min:1',
        'deskripsi' => 'nullable|string',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'gallery.*' => 'image|mimes:jpeg,png,jpg|max:2048',
    ]);

        $path = $car->foto;
        if ($request->hasFile('foto')) {
            if ($car->foto && Storage::disk('public')->exists($car->foto)) {
                Storage::disk('public')->delete($car->foto);
            }
            $path = $request->file('foto')->store('cars', 'public');
        }

        $car->update([
            'brand_id' => $request->brand_id,
            'model' => $request->model,
            'tahun' => $request->tahun,
            'warna' => $request->warna,
            'tipe_transmisi' => $request->tipe_transmisi,
            'capacity_id' => $request->capacity_id,
            'bahan_bakar' => $request->bahan_bakar,
            'harga_sewa_per_jam' => $request->harga_sewa_per_jam,
            'status' => 'tersedia',
            'lokasi' => $request->lokasi,
            'kilometer' => $request->kilometer,
            'liter_tangki' => $request->liter_tangki,
            'deskripsi' => $request->deskripsi,
            'foto' => $path,
        ]);

        return redirect()->route('cars.index')->with('success', 'Data mobil berhasil diperbarui.');
    }

    public function destroy($id)
{
    $car = Car::with('photos')->findOrFail($id);

    // Hapus foto utama
    if ($car->foto && Storage::disk('public')->exists($car->foto)) {
        Storage::disk('public')->delete($car->foto);
    }

    // Hapus foto tambahan
    foreach ($car->photos as $photo) {
        if (Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }
        $photo->delete();
    }

    $car->delete();

    return redirect()->route('cars.index')->with('success', 'Mobil dan semua foto berhasil dihapus.');
}


    // ====================== MEREK ============================

    public function brandIndex()
    {
        $brands = CarBrand::orderBy('nama_merek')->get();
        return view('admin.merek.index', compact('brands'));
    }

   public function brandStore(Request $request)
{
    $request->validate([
        'nama_merek' => 'required'
    ]);

    $nama = strtolower(trim($request->nama_merek));

    // Cegah duplikat manual (case insensitive)
    if (CarBrand::whereRaw('LOWER(TRIM(nama_merek)) = ?', [$nama])->exists()) {
        return redirect()->back()->with('success', 'Merek sudah ada!');
    }

    $logoMap = [
        'toyota' => 'toyota.png',
        'honda' => 'honda.png',
        'daihatsu' => 'daihatsu.png',
        'suzuki' => 'suzuki.png',
        'mitsubishi' => 'mitsubishi.png',
        'nissan' => 'nissan.png',
        'hyundai' => 'hyundai.png',
        'mazda' => 'mazda.png',
        'wuling' => 'wuling.png',
        'porsche' => 'porsche.png',
    ];

    $logo = $logoMap[$nama] ?? 'default.png';

    CarBrand::create([
        'nama_merek' => ucfirst($nama),
        'logo' => $logo, // ✅ sekarang disimpan ke DB
    ]);

    return redirect()->route('cars.brands')->with('success', 'Merek berhasil ditambahkan!');
}


public function brandDestroy($id)
{
    $brand = CarBrand::findOrFail($id);

    // Optional: hapus semua mobil yang pakai merek ini
    if ($brand->cars()->count()) {
        foreach ($brand->cars as $car) {
            // hapus foto mobil juga
            if ($car->foto && Storage::disk('public')->exists($car->foto)) {
                Storage::disk('public')->delete($car->foto);
            }

            foreach ($car->photos as $photo) {
                if (Storage::disk('public')->exists($photo->path)) {
                    Storage::disk('public')->delete($photo->path);
                }
                $photo->delete();
            }

            $car->delete();
        }
    }

    $brand->delete();

    return redirect()->route('cars.brands')->with('success', 'Merek dan semua mobil terkait berhasil dihapus!');
}


    // ====================== MODEL ============================

    public function modelIndex()
    {
        $models = CarModel::with('brand')->get();
        $brands = CarBrand::all();
        return view('admin.model.index', compact('models', 'brands'));
    }

    public function modelStore(Request $request)
    {
        $request->validate([
            'brand_id' => 'required|exists:car_brands,brand_id',
            'nama_model' => 'required|string|max:100'
        ]);

        CarModel::create([
            'brand_id' => $request->brand_id,
            'nama_model' => $request->nama_model
        ]);

        return redirect()->route('cars.models')->with('success', 'Model berhasil ditambahkan!');
    }

    // ====================== API UNTUK DROPDOWN ============================

    public function getModelsByBrand($brand_id)
    {
        $models = CarModel::where('brand_id', $brand_id)
            ->select('nama_model')
            ->orderBy('nama_model')
            ->get();

        return response()->json($models);
    }
}

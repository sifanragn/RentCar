<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\CarCapacity;
use App\Models\CarPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarAdminController extends Controller
{
    // ====================== MOBIL ============================

    // Menampilkan daftar semua mobil beserta relasinya
    public function index()
    {
        $cars = Car::with(['brand', 'capacity', 'photos'])
            ->orderBy('created_at', 'desc') // urutkan dari terbaru
            ->get();

        return view('admin.mobil.index', compact('cars'));
    }

    // Menampilkan form tambah mobil
    public function create()
    {
        $brands = CarBrand::orderBy('nama_merek')->get();
        $capacities = CarCapacity::orderBy('jumlah_orang')->get();

        return view('admin.mobil.create', compact('brands', 'capacities'));
    }

    // Menampilkan detail mobil
    public function show($id)
    {
        $car = Car::with(['brand', 'capacity', 'photos'])->findOrFail($id);

        // Jika request AJAX (modal / partial view)
        if (request()->ajax()) {
            return response()->view('admin.mobil.partials.show', compact('car'));
        }

        return view('admin.mobil.show', compact('car'));
    }

    // Menyimpan data mobil baru beserta foto utama dan galeri
    public function store(Request $request)
    {
        // ================= VALIDASI INPUT =================
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

        // ================= SIMPAN FOTO UTAMA =================
        $path = $request->file('foto')->store('cars', 'public');

        // ================= SIMPAN DATA MOBIL =================
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

        // ================= SIMPAN FOTO TAMBAHAN =================
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {

                $galleryPath = $file->store('cars/gallery', 'public');

                CarPhoto::create([
                    'car_id' => $car->car_id,
                    'path' => $galleryPath,
                ]);
            }
        }

        // ================= REDIRECT =================
        return redirect()->route('admin.cars.index')
            ->with('success', 'Mobil dan foto tambahan berhasil ditambahkan.');
    }

    // Menampilkan form edit mobil
    public function edit($id)
    {
        $car = Car::findOrFail($id);
        $brands = CarBrand::orderBy('nama_merek')->get();
        $capacities = CarCapacity::orderBy('jumlah_orang')->get();

        return view('admin.mobil.edit', compact('car', 'brands', 'capacities'));
    }

    // Mengupdate data mobil beserta foto utama dan galeri
    public function update(Request $request, $id)
    {
        $car = Car::with('photos')->findOrFail($id);

        // ================= VALIDASI =================
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
            'delete_gallery' => 'array',
        ]);

        // ================= FOTO UTAMA =================
        $path = $car->foto;

        if ($request->hasFile('foto')) {

            // hapus foto lama jika ada
            if ($car->foto && Storage::disk('public')->exists($car->foto)) {
                Storage::disk('public')->delete($car->foto);
            }

            // simpan foto baru
            $path = $request->file('foto')->store('cars', 'public');
        }

        // ================= HAPUS FOTO GALERI =================
        if ($request->delete_gallery) {
            foreach ($request->delete_gallery as $photoId) {

                $photo = CarPhoto::find($photoId);

                if ($photo && Storage::disk('public')->exists($photo->path)) {
                    Storage::disk('public')->delete($photo->path);
                }

                if ($photo) {
                    $photo->delete();
                }
            }
        }

        // ================= TAMBAH FOTO BARU =================
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {

                $galleryPath = $file->store('cars/gallery', 'public');

                CarPhoto::create([
                    'car_id' => $car->car_id,
                    'path' => $galleryPath,
                ]);
            }
        }

        // ================= UPDATE DATA =================
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

        return redirect()->route('admin.cars.index')
            ->with('success', 'Data mobil berhasil diperbarui.');
    }

    // Menghapus mobil beserta semua foto
    public function destroy($id)
    {
        $car = Car::with('photos')->findOrFail($id);

        // hapus foto utama
        if ($car->foto && Storage::disk('public')->exists($car->foto)) {
            Storage::disk('public')->delete($car->foto);
        }

        // hapus foto galeri
        foreach ($car->photos as $photo) {
            if (Storage::disk('public')->exists($photo->path)) {
                Storage::disk('public')->delete($photo->path);
            }
            $photo->delete();
        }

        $car->delete();

        return redirect()->route('admin.cars.index')
            ->with('success', 'Mobil dan semua foto berhasil dihapus.');
    }

    // ====================== MEREK ============================

    // Menampilkan daftar merek mobil
    public function brandIndex()
    {
        $brands = CarBrand::orderBy('nama_merek')->get();
        return view('admin.merek.index', compact('brands'));
    }

    // Menyimpan merek mobil dan menentukan logo otomatis
    public function brandStore(Request $request)
    {
        $request->validate([
            'nama_merek' => 'required'
        ]);

        $nama = strtolower(trim($request->nama_merek));

        // cek duplikat
        if (CarBrand::whereRaw('LOWER(TRIM(nama_merek)) = ?', [$nama])->exists()) {
            return redirect()->back()->with('success', 'Merek sudah ada!');
        }

        // mapping logo otomatis
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
            'logo' => $logo,
        ]);

        return redirect()->route('admin.cars.brands')
            ->with('success', 'Merek berhasil ditambahkan!');
    }

    // Menghapus merek beserta semua mobil terkait
    public function brandDestroy($id)
    {
        $brand = CarBrand::findOrFail($id);

        if ($brand->cars()->count()) {
            foreach ($brand->cars as $car) {

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

        return redirect()->route('admin.cars.brands')
            ->with('success', 'Merek dan semua mobil terkait berhasil dihapus!');
    }

    // ====================== MODEL ============================

    // Menampilkan daftar model mobil
    public function modelIndex()
    {
        $models = CarModel::with('brand')->get();
        $brands = CarBrand::all();

        return view('admin.model.index', compact('models', 'brands'));
    }

    // Menyimpan model mobil
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

        return redirect()->route('admin.cars.models')
            ->with('success', 'Model berhasil ditambahkan!');
    }

    // ====================== API ============================

    // Mengambil model berdasarkan brand (untuk dropdown dinamis)
    public function getModelsByBrand($brand_id)
    {
        $models = CarModel::where('brand_id', $brand_id)
            ->select('nama_model')
            ->orderBy('nama_model')
            ->get();

        return response()->json($models);
    }

    // Menghapus satu foto galeri via AJAX
    public function deletePhoto(CarPhoto $photo)
    {
        if ($photo->path && Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }

        $photo->delete();

        return response()->json(['success' => true]);
    }
}
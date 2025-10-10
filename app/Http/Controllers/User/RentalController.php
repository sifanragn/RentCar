<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\Car;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RentalController extends Controller
{
    /**
     * 🧾 Tampilkan daftar penyewaan user
     */
    public function index()
    {
        $rentals = Rental::with(['car.brand'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.rentals.index', compact('rentals'));
    }

    /**
     * 🚗 Form penyewaan mobil
     */
    public function create($car_id)
    {
        $car = Car::with('brand')->findOrFail($car_id);
        return view('user.rentals.create', compact('car'));
    }

    /**
     * 💾 Simpan data penyewaan baru, tapi belum buat pembayaran
     */
    public function store(Request $request, $car_id)
    {
        $user = auth()->user();
        $car  = Car::findOrFail($car_id);

        // 🚫 Cegah mobil disewa ganda
        $existingRental = Rental::where('car_id', $car->car_id)
            ->whereIn('status_rental', ['verifikasi_diperlukan', 'menunggu_pembayaran', 'berjalan'])
            ->exists();

        if ($existingRental) {
            return response()->json([
                'success' => false,
                'message' => 'Mobil ini sedang disewa atau menunggu konfirmasi. Silakan pilih mobil lain.'
            ], 409);
        }

        // ✅ Validasi form
        $validated = $request->validate([
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'driver'          => 'required|in:ya,tidak',
            'metode_pickup'   => 'required|in:ambil_sendiri,pickup_alamat',
        ]);

        // 🔹 Hitung durasi & total biaya
        $durasi = Carbon::parse($validated['tanggal_mulai'])->diffInDays(Carbon::parse($validated['tanggal_selesai']));
        $hargaDriver = $validated['driver'] === 'ya' ? 150000 * $durasi : 0;
        $total = ($car->harga_sewa_per_hari * $durasi) + $hargaDriver;

        // 🔹 Tentukan status awal
        $statusAwal = ($user->status_verifikasi === 'disetujui')
            ? 'menunggu_pembayaran'
            : 'verifikasi_diperlukan';

        // 🔹 Simpan data rental
        $rental = Rental::create([
            'user_id'         => $user->user_id,
            'car_id'          => $car->car_id,
            'tanggal_mulai'   => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'durasi_hari'     => $durasi,
            'driver'          => $validated['driver'],
            'metode_pickup'   => $validated['metode_pickup'],
            'total_biaya'     => $total,
            'status_rental'   => $statusAwal,
        ]);

        Log::info('📦 Penyewaan dibuat', [
            'rental_id' => $rental->rental_id,
            'user'      => $user->email,
            'status'    => $statusAwal
        ]);

        // 🚫 Jika belum diverifikasi, arahkan ke profil
        if ($statusAwal === 'verifikasi_diperlukan') {
            return response()->json([
                'success' => false,
                'redirect_url' => route('user.profile'),
            ]);
        }

        // ✅ Jika sudah diverifikasi, arahkan ke detail pembayaran
        return response()->json([
            'success' => true,
            'redirect_url' => route('user.payments.detailRental', $rental->rental_id),
        ]);
    }

    /**
     * 🔍 Detail penyewaan (untuk tampilan biasa)
     */
    public function show($id)
    {
        $rental = Rental::with(['car.brand'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('user.rentals.show', compact('rental'));
    }

    /**
     * ❌ Batalkan penyewaan terbaru (belum dibayar)
     */
    public function cancelLatest()
    {
        $rental = Rental::where('user_id', auth()->id())
            ->whereIn('status_rental', ['verifikasi_diperlukan', 'menunggu_pembayaran'])
            ->latest()
            ->first();

        if ($rental) {
            $rental->update(['status_rental' => 'dibatalkan']);
        }

        return response()->json(['message' => 'Transaksi dibatalkan.']);
    }
}

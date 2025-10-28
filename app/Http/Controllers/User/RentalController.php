<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Payment;
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
    // 🚘 Ambil data mobil beserta brand-nya
    $car = \App\Models\Car::with('brand')->findOrFail($car_id);

    // 👨‍✈️ Ambil semua driver aktif & sudah diverifikasi
    $drivers = \App\Models\Driver::where('status', 'aktif')
        ->where('status_verifikasi', 'disetujui')
        ->orderBy('nama', 'asc')
        ->get(['driver_id', 'nama', 'foto', 'harga_per_jam', 'lokasi', 'pengalaman', 'deskripsi', 'status_verifikasi']);

    // 📦 Kirim data ke view
    return view('user.rentals.create', [
        'car' => $car,
        'drivers' => $drivers,
    ]);
}

    /**
     * 💾 Simpan data penyewaan baru (dengan blokir transaksi ganda)
     */
    public function store(Request $request, $car_id)
{
    $user = auth()->user();
    $car  = Car::findOrFail($car_id);

    // 🚫 Cegah transaksi ganda
    $hasPendingPayment = Payment::whereHas('rental', function ($q) use ($user) {
            $q->where('user_id', $user->user_id);
        })
        ->where('payment_type', 'main')
        ->where('status_pembayaran', 'pending')
        ->where(function ($q) {
            $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
        })
        ->exists();

    if ($hasPendingPayment) {
        return response()->json([
            'success' => false,
            'message' => '⚠️ Kamu masih memiliki pembayaran yang belum diselesaikan.',
            'redirect_url' => route('user.payments.index'),
        ], 409);
    }

    // 🚫 Cegah mobil sedang disewa
    $existingRental = Rental::where('car_id', $car->car_id)
        ->whereIn('status_rental', ['verifikasi_diperlukan', 'menunggu_pembayaran', 'berjalan'])
        ->exists();

    if ($existingRental) {
        return response()->json([
            'success' => false,
            'message' => 'Mobil ini sedang disewa atau menunggu konfirmasi. Silakan pilih mobil lain.',
        ], 409);
    }

    // ✅ Validasi input
    $validated = $request->validate([
        'tanggal_mulai'   => 'required|date|after_or_equal:today',
        'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        'driver'          => 'required|in:ya,tidak',
        'driver_id'       => 'nullable|exists:drivers,driver_id',
        'metode_pickup'   => 'required|in:ambil_sendiri,pickup_alamat',
    ]);

    // 🕒 Konversi ke WIB
    $timezone = 'Asia/Jakarta';
    $tanggalMulai   = Carbon::createFromFormat('Y-m-d\TH:i', $validated['tanggal_mulai'], $timezone)->setTimezone($timezone);
    $tanggalSelesai = Carbon::createFromFormat('Y-m-d\TH:i', $validated['tanggal_selesai'], $timezone)->setTimezone($timezone);

    // 🔹 Validasi jam operasional (08–22 WIB)
    // 🔹 Validasi jam operasional hanya untuk pickup & return
$jamMulai   = (int) $tanggalMulai->format('H');
$jamSelesai = (int) $tanggalSelesai->format('H');

// Pengambilan (mulai) harus di jam buka
if ($jamMulai < 8 || $jamMulai > 22) {
    return response()->json([
        'success' => false,
        'message' => '⚠️ Pengambilan mobil hanya bisa antara pukul 08:00 - 22:00 WIB.',
    ], 422);
}

// Pengembalian boleh lewat tengah malam, tapi kalau masih di hari yang sama → wajib <= 22:00
if ($tanggalMulai->isSameDay($tanggalSelesai) && $jamSelesai > 22) {
    return response()->json([
        'success' => false,
        'message' => '⚠️ Pengembalian di hari yang sama maksimal pukul 22:00 WIB.',
    ], 422);
}

   // 🔹 Hitung durasi dan total biaya (per jam, minimal 6 jam)
$durasiJam = $tanggalMulai->diffInHours($tanggalSelesai);
if ($durasiJam < 6) {
    $durasiJam = 6; // minimal 6 jam
}

$hargaDriver = 0;
$driver = null;

// 💰 Jika pakai driver, ambil data dari tabel driver (harga disesuaikan per jam)
if ($validated['driver'] === 'ya' && $validated['driver_id']) {
    $driver = Driver::where('status', 'aktif')
        ->where('status_verifikasi', 'disetujui')
        ->find($validated['driver_id']);

    if ($driver) {
    // 💰 Ambil harga driver per jam langsung dari database
    $hargaPerJamDriver = $driver->harga_per_jam ?? 0;
    $hargaDriver = $hargaPerJamDriver * $durasiJam;
}
}

// 💰 Harga mobil per jam (gunakan langsung kolom harga_sewa_per_jam)
$hargaPerJamMobil = $car->harga_sewa_per_jam ?? 0;
$total = ($hargaPerJamMobil * $durasiJam) + $hargaDriver;

// 🔹 Pastikan total minimal 10.000 (biar diterima Duitku)
if ($total < 10000) {
    $total = 10000;
}

    // 🔹 Status awal
    $statusAwal = ($user->status_verifikasi === 'disetujui')
        ? 'draft'
        : 'verifikasi_diperlukan';
        $expiredAt = null;
        if ($statusAwal === 'draft') {
            $expiredAt = now()->addMinutes(30);
        }

    // 🔹 Simpan rental
    $rental = Rental::create([
        'user_id'         => $user->user_id,
        'car_id'          => $car->car_id,
        'tanggal_mulai'   => $tanggalMulai,
        'tanggal_selesai' => $tanggalSelesai,
        'durasi_jam'      => $durasiJam,
        'driver'          => $validated['driver'],
        'driver_id'       => $driver?->driver_id,
        'metode_pickup'   => $validated['metode_pickup'],
        'total_biaya'     => $total,
        'status_rental'   => $statusAwal,
        'expired_at'      => $expiredAt, // 🕒 simpan waktu kedaluwarsa
    ]);

    Log::info('📦 Penyewaan dibuat', [
        'rental_id' => $rental->rental_id,
        'user'      => $user->email,
        'driver'    => $driver?->nama ?? 'tidak ada',
        'total'     => $total,
    ]);

    // 🚀 Redirect
    if ($statusAwal === 'draft') {
        return response()->json([
            'success' => true,
            'message' => 'Draft disimpan sementara, silakan lanjut ke pembayaran.',
            'redirect_url' => route('user.payments.detailRental', $rental->rental_id),
        ]);
    }

    if ($statusAwal === 'verifikasi_diperlukan') {
        return response()->json([
            'success' => false,
            'redirect_url' => route('user.verifikasi.index'),
        ]);
    }

    return response()->json([
        'success' => true,
        'redirect_url' => route('user.payments.detailRental', $rental->rental_id),
    ]);
}

    /**
     * 🔍 Detail penyewaan user
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
            Log::info('❌ Rental dibatalkan oleh user', ['rental_id' => $rental->rental_id]);
        }

        return response()->json(['message' => 'Transaksi dibatalkan.']);
    }
}

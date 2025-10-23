<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\Car;
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
        $car  = Car::with('brand')->findOrFail($car_id);
        $user = auth()->user()->refresh(); // pastikan data terbaru
        return view('user.rentals.create', compact('car', 'user'));
    }

    /**
     * 💾 Simpan data penyewaan baru (dengan blokir transaksi ganda)
     */
    public function store(Request $request, $car_id)
{
    $user = auth()->user();
    $car  = Car::findOrFail($car_id);

    // 🚫 Cek apakah masih ada payment pending
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
            'message' => '⚠️ Kamu masih memiliki pembayaran yang belum diselesaikan. 
            Selesaikan atau batalkan dulu sebelum menyewa mobil lain.',
            'redirect_url' => route('user.payments.index'),
        ], 409);
    }

    // 🚫 Cegah mobil sedang disewa user lain
    $existingRental = Rental::where('car_id', $car->car_id)
        ->whereIn('status_rental', ['verifikasi_diperlukan', 'menunggu_pembayaran', 'berjalan'])
        ->exists();

    if ($existingRental) {
        return response()->json([
            'success' => false,
            'message' => 'Mobil ini sedang disewa atau menunggu konfirmasi. Silakan pilih mobil lain.'
        ], 409);
    }

    // ✅ Validasi datetime-local (format: 2025-10-14T09:30)
    $validated = $request->validate([
        'tanggal_mulai'   => 'required|date|after_or_equal:today',
        'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        'driver'          => 'required|in:ya,tidak',
        'metode_pickup'   => 'required|in:ambil_sendiri,pickup_alamat',
    ]);

    // 🕒 Konversi waktu ke zona Indonesia
    $timezone = 'Asia/Jakarta';
    $tanggalMulai   = Carbon::createFromFormat('Y-m-d\TH:i', $validated['tanggal_mulai'], $timezone)->setTimezone($timezone);
    $tanggalSelesai = Carbon::createFromFormat('Y-m-d\TH:i', $validated['tanggal_selesai'], $timezone)->setTimezone($timezone);

    // ⏰ Validasi jam input (08:00 - 22:00 WIB)
    $jamMulai   = (int) $tanggalMulai->format('H');
    $jamSelesai = (int) $tanggalSelesai->format('H');
    if ($jamMulai < 8 || $jamMulai > 22 || $jamSelesai < 8 || $jamSelesai > 22) {
        return response()->json([
            'success' => false,
            'message' => '⚠️ Jam penyewaan hanya diperbolehkan antara 08:00 hingga 22:00 WIB.',
        ], 422);
    }

    // 🔹 Hitung durasi dan total biaya
    $durasiJam  = $tanggalMulai->diffInHours($tanggalSelesai);
    $durasiHari = ceil($durasiJam / 24);
    $hargaDriver = $validated['driver'] === 'ya' ? 150000 * $durasiHari : 0;
    $total = ($car->harga_sewa_per_hari * $durasiHari) + $hargaDriver;

    // 🔹 Status awal
    $statusAwal = ($user->status_verifikasi === 'disetujui')
        ? 'draft'
        : 'verifikasi_diperlukan';

    // 🔹 Simpan rental
    $rental = Rental::create([
        'user_id'         => $user->user_id,
        'car_id'          => $car->car_id,
        'tanggal_mulai'   => $tanggalMulai,
        'tanggal_selesai' => $tanggalSelesai,
        'durasi_hari'     => $durasiHari,
        'driver'          => $validated['driver'],
        'metode_pickup'   => $validated['metode_pickup'],
        'total_biaya'     => $total,
        'status_rental'   => $statusAwal,
    ]);

    Log::info('📦 Penyewaan dibuat', [
        'rental_id' => $rental->rental_id,
        'user'      => $user->email,
        'status'    => $statusAwal,
        'mulai'     => $tanggalMulai->format('Y-m-d H:i:s'),
        'selesai'   => $tanggalSelesai->format('Y-m-d H:i:s'),
        'zona'      => $timezone,
    ]);

    // 🧹 Jika status draft, hapus langsung dari DB (tidak muncul di riwayat)
if ($statusAwal === 'draft') {
    return response()->json([
        'success' => true,
        'message' => 'Draft disimpan sementara, silakan lanjutkan ke pembayaran.',
        'redirect_url' => route('user.payments.detailRental', $rental->rental_id),
    ]);
}

    // 🟢 Kalau perlu verifikasi manual
    if ($statusAwal === 'verifikasi_diperlukan') {
        return response()->json([
            'success' => false,
            'redirect_url' => route('user.verifikasi.index'),
        ]);
    }

    // Default (user verified)
    return response()->json([
        'success' => true,
        'redirect_url' => route('user.payments.detailRental', $rental->rental_id),
    ]);
}


    /**
     * 🔍 Detail penyewaan
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

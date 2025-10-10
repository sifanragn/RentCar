<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\Car;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RentalController extends Controller
{
    /** 
     * Tampilkan semua penyewaan milik user
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
     * Form penyewaan
     */
    public function create($car_id)
    {
        $car = Car::with('brand')->findOrFail($car_id);
        return view('user.rentals.create', compact('car'));
    }

    /**
     * Simpan data penyewaan dan arahkan langsung ke halaman pembayaran
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

        // ✅ Validasi input
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

        // 🚫 Jika user belum diverifikasi
        if ($statusAwal === 'verifikasi_diperlukan') {
            return response()->json([
                'success' => false,
                'redirect_url' => route('user.profile')
            ]);
        }

        // ✅ Buat data pembayaran otomatis
        $payment = Payment::create([
            'rental_id'         => $rental->rental_id,
            'gateway'           => 'midtrans',
            'metode'            => 'online',
            'total_bayar'       => $total,
            'status_pembayaran' => 'pending',
            'gateway_reference' => 'MID-' . rand(100000, 999999),
            'payment_token'     => 'PAY-' . strtoupper(uniqid()),
            'callback_status'   => 'waiting',
            'tanggal_bayar'     => now(),
        ]);

        // URL tujuan ke halaman pembayaran
        $redirectUrl = route('user.payments.show', $payment->payment_id);

        return response()->json([
            'success' => true,
            'redirect_url' => $redirectUrl
        ]);
    }

    /**
     * Detail penyewaan user
     */
    public function show($id)
    {
        $rental = Rental::with(['car.brand'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('user.rentals.show', compact('rental'));
    }

    /**
     * Batalkan penyewaan terbaru yang belum diproses
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

        return response()->json(['message' => 'Transaksi dibatalkan']);
    }
}

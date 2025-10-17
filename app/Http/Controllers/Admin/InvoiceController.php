<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Rental;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * 📋 Daftar semua invoice
     */
    public function index()
    {
        $invoices = Invoice::with(['rental.car.brand', 'admin'])
            ->orderByDesc('tanggal_cetak')
            ->get();

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * 🧾 Form buat invoice baru
     */
    public function create($rental_id)
    {
        $rental = Rental::with('car.brand', 'user')->findOrFail($rental_id);
        return view('admin.invoices.create', compact('rental'));
    }

    /**
     * 🔁 Buat ulang pembayaran charge (retry tagihan denda)
     */
    public function retryPayment(Request $request, $invoice_id)
    {
        $invoice = Invoice::with(['rental.user', 'rental.car'])->findOrFail($invoice_id);

        if (!$invoice->rental) {
            return back()->with('error', '❌ Rental tidak ditemukan untuk invoice ini.');
        }

        // Pastikan invoice sudah dibatalkan
        if (!in_array(strtolower($invoice->status_invoice), ['cancel', 'dibatalkan'])) {
            return back()->with('info', '⚠️ Invoice ini belum dibatalkan, tidak perlu dikirim ulang.');
        }

        // Pastikan ada denda
        if ($invoice->denda_tambahan <= 0) {
            return back()->with('error', 'Invoice ini tidak memiliki denda tambahan untuk ditagihkan ulang.');
        }

        // Cek apakah masih ada payment charge pending
        $existing = Payment::where('rental_id', $invoice->rental_id)
            ->where('payment_type', 'charge')
            ->where('status_pembayaran', 'pending')
            ->first();

        if ($existing) {
            return redirect()
                ->route('admin.invoices.show', $invoice->invoice_id)
                ->with('info', 'Masih ada tagihan charge yang menunggu. Gunakan link yang sudah ada.');
        }

        // Pilihan metode pembayaran dari admin (default qris)
        $pilihan = strtolower($request->input('payment_method', 'qris'));
        $mapMetode = [
            'qris' => 'QRIS',
            'bca'  => 'BC',
            'bri'  => 'BR',
            'bni'  => 'N2',
            'mandiri' => 'M2',
        ];
        $duitkuMethod = $mapMetode[$pilihan] ?? 'QRIS';

        // Tandai payment lama kalau masih pending → failed
        if ($invoice->rental->payment && $invoice->rental->payment->status_pembayaran === 'pending') {
            $invoice->rental->payment->update([
                'status_pembayaran' => 'failed',
                'callback_status'   => 'expired',
            ]);
        }

        // 🟢 Buat payment baru khusus charge denda
        $payment = Payment::create([
            'rental_id'         => $invoice->rental_id,
            'gateway'           => 'Duitku',
            'metode'            => $pilihan,
            'payment_type'      => 'charge',
            'total_bayar'       => $invoice->denda_tambahan,
            'status_pembayaran' => 'pending',
            'gateway_reference' => 'CHARGE-RETRY-' . strtoupper(uniqid()),
            'payment_token'     => 'WAIT-' . strtoupper(uniqid()),
            'callback_status'   => 'waiting',
            'tanggal_bayar'     => now(),
            'expired_at'        => now()->addMinutes(30),
        ]);

        // Ubah invoice jadi pending lagi
        $invoice->update(['status_invoice' => 'pending']);

        // 🚀 Request baru ke Duitku
        try {
            $this->createDuitkuPayment(
                $invoice->rental,
                $payment,
                $invoice->invoice_id,
                $duitkuMethod,
                $invoice->denda_tambahan,
                'RETRY'
            );
        } catch (\Throwable $e) {
            Log::error('❌ Gagal membuat pembayaran ulang denda: ' . $e->getMessage());
        }

        return redirect()
            ->route('admin.invoices.show', $invoice->invoice_id)
            ->with('success', '✅ Pembayaran ulang untuk denda berhasil dibuat dengan metode ' . strtoupper($pilihan) . '.');
    }

    /**
     * 💾 Simpan invoice baru + otomatis buat payment charge (asynchronous)
     */
    public function store(Request $request, $rental_id)
{
    $request->validate([
        'status_pengembalian' => 'required|in:tepat_waktu,telat,rusak',
        'denda'               => 'nullable|numeric|min:0',
        'catatan'             => 'nullable|string|max:255',
        'payment_method'      => 'nullable|string',
    ]);

    $rental = Rental::with(['car.brand', 'user'])->findOrFail($rental_id);
    $denda = (float) $request->input('denda', 0);
    $total_tagihan = (float) $rental->total_biaya;
    $total_akhir = $total_tagihan + $denda;

    $mapMetode = [
        'qris' => 'QRIS',
        'bca'  => 'BC',
        'bri'  => 'BR',
        'bni'  => 'N2',
        'mandiri' => 'M2',
    ];
    $pilihan = strtolower($request->input('payment_method', 'qris'));
    $duitkuMethod = $mapMetode[$pilihan] ?? 'QRIS';

    DB::beginTransaction();
    try {
        // 🧾 Buat invoice baru
        $invoice = Invoice::create([
            'rental_id'           => $rental->rental_id,
            'tanggal_cetak'       => now(),
            'total_tagihan'       => $total_tagihan,
            'status_pengembalian' => $request->status_pengembalian,
            'denda_tambahan'      => $denda,
            'total_akhir'         => $total_akhir,
            'status_invoice'      => 'pending',
            'admin_id'            => Auth::id(),
        ]);

        // 💰 Kalau ada denda → buat payment charge
        if ($denda > 0) {
            $payment = Payment::create([
                'rental_id'         => $rental->rental_id,
                'gateway'           => 'Duitku',
                'metode'            => $pilihan,
                'payment_type'      => 'charge',
                'total_bayar'       => $denda,
                'status_pembayaran' => 'pending',
                'gateway_reference' => 'CHARGE-' . strtoupper(uniqid()),
                'payment_token'     => 'WAIT-' . strtoupper(uniqid()),
                'callback_status'   => 'waiting',
                'tanggal_bayar'     => now(),
                'expired_at'        => now()->addMinutes(30),
            ]);

            // panggil Duitku langsung
            $this->createDuitkuPayment($rental, $payment, $invoice->invoice_id, $duitkuMethod, $denda, 'CHARGE');
        }

        // 🚗 Update status rental
        $rental->update([
            'status_rental' => $denda > 0 ? 'selesai_dengan_charge' : 'selesai',
        ]);

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('❌ Error saat buat invoice: ' . $e->getMessage());
        return back()->with('error', 'Gagal membuat invoice: ' . $e->getMessage());
    }

    try {
    $this->createDuitkuPayment($rental, $payment, $invoice->invoice_id, $duitkuMethod, $denda, 'CHARGE');
} catch (\Throwable $e) {
    Log::error('❌ Error saat kirim ke Duitku: ' . $e->getMessage());
    return back()->with('error', 'Gagal kirim ke Duitku: ' . $e->getMessage());
}


    return redirect()->route('admin.invoices.show', $invoice->invoice_id)
        ->with('success', '✅ Invoice berhasil dibuat dengan tagihan denda.');
}

    /**
     * 🔍 Detail invoice + auto refresh status pembayaran jika masih pending
     */
    public function show($invoice_id)
{
    $invoice = Invoice::with([
        'rental.car.brand',
        'rental.user',
        'admin',
        'rental.payments', // ✅ pakai plural, bukan rental.payment
    ])->findOrFail($invoice_id);

    // 🔄 Auto-refresh payment yang pending (utama & charge)
    foreach ($invoice->rental->payments as $payment) {
        if ($payment->status_pembayaran === 'pending') {
            $this->refreshPaymentStatus($payment);
        }
    }

    // 🔁 Refresh data invoice biar up to date
    $invoice->refresh();

    return view('admin.invoices.show', compact('invoice'));
}


    /**
     * 🔧 Util: Buat pembayaran di Duitku
     */
    private function createDuitkuPayment($rental, $payment, $invoiceId, $method, $amount, $prefix)
{
    $merchantCode = 'DS25394';
    $apiKey = '06a924ce717ea70f6522e5c51241ccc6';
    $merchantOrderId = 'INV' . $invoiceId . '-P' . $payment->id; // ✅ lebih aman
    $amount = (int) $amount; // ✅ wajib integer
    $signature = md5($merchantCode . $merchantOrderId . $amount . $apiKey);

    $payload = [
        "merchantCode"     => $merchantCode,
        "paymentAmount"    => $amount,
        "paymentMethod"    => $method,
        "merchantOrderId"  => $merchantOrderId,
        "productDetails"   => "Denda sewa mobil " . ($rental->car->brand->nama_merek ?? ''),
        "email"            => $rental->user->email,
        "phoneNumber"      => $rental->user->no_hp ?? '08123456789',
        "customerVaName"   => $rental->user->nama_lengkap ?? 'Penyewa',
        "callbackUrl"      => "https://amiyah-mouselike-stably.ngrok-free.dev/api/payment/callback",
        "returnUrl"        => url('/user/payments'), // ✅ FIXED
        "signature"        => $signature,
        "expiryPeriod"     => 30,
    ];

    $response = Http::post('https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry', $payload);
    $result = $response->json();

    Log::info('💬 Duitku Response (Invoice)', [
        'payload' => $payload,
        'result' => $result,
    ]);

    if (!empty($result['paymentUrl'])) {
        $payment->update([
            'gateway_reference' => $result['reference'] ?? $payment->gateway_reference,
            'payment_token'     => $result['paymentUrl'],
            'expired_at'        => now()->addMinutes(30),
        ]);
        Log::info('✅ Duitku charge berhasil dibuat.', ['url' => $result['paymentUrl']]);
    } else {
        $payment->update([
            'status_pembayaran' => 'failed',
            'callback_status'   => 'error',
        ]);
        Log::error('❌ Gagal buat pembayaran charge di Duitku.', ['response' => $result]);
    }
}

    /**
     * 🔄 Auto-refresh status pembayaran jika pending
     */
    private function refreshPaymentStatus($payment)
    {
        if (!$payment || $payment->status_pembayaran !== 'pending') {
            return;
        }

        try {
            $merchantCode = 'DS25394';
            $apiKey = '06a924ce717ea70f6522e5c51241ccc6';
            $merchantOrderId = $payment->gateway_reference;
            $signature = md5($merchantCode . $merchantOrderId . $apiKey);

            $payload = [
                'merchantCode'    => $merchantCode,
                'merchantOrderId' => $merchantOrderId,
                'signature'       => $signature,
            ];

            $response = Http::post('https://sandbox.duitku.com/webapi/api/merchant/transactionStatus', $payload);
            $result = $response->json();

            if (isset($result['resultCode'])) {
                if ($result['resultCode'] === '00') {
                    $payment->update(['status_pembayaran' => 'success', 'callback_status' => 'done']);
                } elseif (in_array($result['resultCode'], ['01', '02', '03'])) {
                    $payment->update(['status_pembayaran' => 'pending']);
                } else {
                    $payment->update(['status_pembayaran' => 'failed', 'callback_status' => 'error']);
                }
            }
        } catch (\Throwable $e) {
            Log::error('❌ Gagal refresh status payment: ' . $e->getMessage());
        }
    }

    /**
     * ❌ Batalkan invoice + sinkron rental & payment
     */
   public function cancel($id)
{
    $invoice = Invoice::with(['rental.payments'])->findOrFail($id);

    DB::beginTransaction();
    try {
        // Update status invoice
        $invoice->update(['status_invoice' => 'cancel']);

        // Update status rental (jika ada)
        if ($invoice->rental) {
            foreach ($invoice->rental->payments as $payment) {
                // Batalkan hanya payment charge yang masih pending
                if ($payment->payment_type === 'charge' && $payment->status_pembayaran === 'pending') {
                    $payment->update([
                        'status_pembayaran' => 'failed',
                        'callback_status'   => 'cancelled',
                    ]);
                }
            }

            // Jangan ubah payment main yang sudah sukses
            $invoice->rental->update(['status_rental' => 'selesai']);
        }

        DB::commit();
        return back()->with('success', '✅ Invoice dibatalkan dan pembayaran denda ikut dibatalkan.');
    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error('❌ Gagal batalkan invoice: ' . $e->getMessage());
        return back()->with('error', 'Gagal membatalkan invoice: ' . $e->getMessage());
    }
}
 public function manualUpdate(Request $request, $id)
{
    $request->validate([
        'status_pembayaran' => 'required|in:pending,success,failed',
    ]);

    $payment = Payment::findOrFail($id);
    $payment->update(['status_pembayaran' => $request->status_pembayaran]);

    return back()->with('success', 'Status pembayaran berhasil diperbarui secara manual.');
}


}

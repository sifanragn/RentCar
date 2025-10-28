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
     * 💾 Simpan invoice baru + otomatis buat payment charge
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

            $amountToCharge = $denda;
            $merchantOrderId = 'INV' . $rental->rental_id . '-' . strtoupper(uniqid());

            // 💳 Buat payment
            $payment = Payment::create([
                'no_transaksi'      => 'INV' . str_pad($rental->rental_id, 4, '0', STR_PAD_LEFT),
                'rental_id'         => $rental->rental_id,
                'gateway'           => 'Duitku',
                'metode'            => $pilihan,
                'payment_type'      => $denda > 0 ? 'charge' : 'invoice',
                'total_bayar'       => $amountToCharge,
                'status_pembayaran' => 'pending',
                'gateway_reference' => 'INV-' . strtoupper(uniqid()),
                'merchant_order_id' => $merchantOrderId,
                'payment_token'     => 'WAIT-' . strtoupper(uniqid()),
                'callback_status'   => 'waiting',
                'tanggal_bayar'     => now(),
                'expired_at'        => now()->addMinutes(30),
            ]);

            // 🚗 Update status rental → selesai (karena sudah dikembalikan)
            $rental->update(['status_rental' => 'selesai']);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            dd('❌ ERROR ASLI:', $e->getMessage(), $e->getTraceAsString());
        }

        // 🚀 Kirim ke Duitku
        try {
            $this->createDuitkuPayment($rental, $payment, $invoice->invoice_id, $duitkuMethod, $amountToCharge, 'CHARGE');

        } catch (\Throwable $e) {
            Log::error('❌ Gagal kirim ke Duitku: ' . $e->getMessage());
        }

        return redirect()->route('admin.invoices.show', $invoice->invoice_id)
            ->with('success', '✅ Invoice & pembayaran berhasil dibuat!');
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
            'rental.payments',
        ])->findOrFail($invoice_id);

        foreach ($invoice->rental->payments as $payment) {
            if ($payment->status_pembayaran === 'pending') {
                $this->refreshPaymentStatus($payment);
            }
        }

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
        $merchantOrderId = $payment->merchant_order_id ?? ('INV' . $invoiceId . '-P' . $payment->id);
        $amount = (int) $amount;
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
            "returnUrl"        => url('/user/payments'),
            "signature"        => $signature,
            "expiryPeriod"     => 30,
        ];

        $response = Http::post('https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry', $payload);
        $result = $response->json();

        if (!empty($result['paymentUrl'])) {
            $payment->update([
                'gateway_reference' => $result['reference'] ?? $payment->gateway_reference,
                'payment_token'     => $result['paymentUrl'],
                'expired_at'        => now()->addMinutes(30),
            ]);
            Log::info('✅ Duitku charge berhasil dibuat.', ['url' => $result['paymentUrl']]);
        } else {
            $payment->update([
                'gateway_reference' => $result['reference'] ?? $payment->gateway_reference,
                'payment_token'     => $result['paymentUrl'],
                'merchant_order_id' => $merchantOrderId,
                'expired_at'        => now()->addMinutes(30),
            ]);
            Log::error('❌ Gagal buat pembayaran charge di Duitku.', ['response' => $result]);
        }
    }

    /**
     * 🔄 Auto-refresh status pembayaran jika pending
     */
    private function refreshPaymentStatus($payment)
    {
        if (!$payment || $payment->status_pembayaran !== 'pending') return;

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
            $invoice->update(['status_invoice' => 'dibatalkan']);

            if ($invoice->rental) {
                $rental = $invoice->rental;

                $paymentUtama = $rental->payments()
                    ->where('payment_type', 'main')
                    ->where('status_pembayaran', 'success')
                    ->first();

                if ($paymentUtama) {
                    $rental->update(['status_rental' => 'selesai']);
                } else {
                    $rental->update(['status_rental' => 'dibatalkan']);
                }

                foreach ($rental->payments as $payment) {
                    if ($payment->status_pembayaran === 'pending') {
                        $payment->update([
                            'status_pembayaran' => 'failed',
                            'callback_status'   => 'cancelled',
                        ]);
                    }
                }
            }

            DB::commit();
            return back()->with('success', '✅ Invoice dibatalkan dan pembayaran denda ikut dibatalkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('❌ Gagal batalkan invoice: ' . $e->getMessage());
            return back()->with('error', 'Gagal membatalkan invoice: ' . $e->getMessage());
        }
    }

    /**
     * 🛠 Update manual status invoice dari admin
     */
    public function manualUpdate(Request $request, $id)
    {
        $request->validate([
            'status_invoice' => 'required|in:pending,selesai,dibatalkan',
        ]);

        $invoice = Invoice::with('rental.payments')->findOrFail($id);
        $invoice->update(['status_invoice' => $request->status_invoice]);

        if ($invoice->rental) {
            $invoice->rental->update([
                'status_rental' => match ($request->status_invoice) {
                    'selesai' => 'selesai',
                    'dibatalkan' => 'dibatalkan',
                    default => $invoice->rental->status_rental,
                },
            ]);

            foreach ($invoice->rental->payments as $p) {
                if ($request->status_invoice === 'selesai') {
                    $p->update(['status_pembayaran' => 'success', 'callback_status' => 'done']);
                } elseif ($request->status_invoice === 'dibatalkan') {
                    $p->update(['status_pembayaran' => 'failed', 'callback_status' => 'cancelled']);
                }
            }
        }

        return back()->with('success', '✅ Status invoice berhasil diperbarui dan disinkron ke pembayaran.');
    }

    /**
 * 🔁 Kirim ulang pembayaran (retry) untuk invoice yang pending
 */
public function retryPayment(Request $request, $id)
{
    $invoice = Invoice::with(['rental.car.brand', 'rental.user', 'rental.payments'])->findOrFail($id);
    $rental = $invoice->rental;

    // 💬 Validasi awal
    if (!$rental || $rental->status_rental !== 'selesai') {
        return back()->with('error', 'Penyewaan belum selesai, tidak bisa kirim ulang pembayaran.');
    }

    if (!in_array($invoice->status_invoice, ['dibatalkan', 'pending'])) {
        return back()->with('error', 'Invoice ini tidak dapat dikirim ulang karena statusnya sudah selesai.');
    }

    $method = strtolower($request->input('payment_method', 'qris'));
    $mapMetode = [
        'qris' => 'QRIS',
        'bca' => 'BC',
        'bri' => 'BR',
        'bni' => 'N2',
        'mandiri' => 'M2',
    ];
    $duitkuMethod = $mapMetode[$method] ?? 'QRIS';

    // cari payment denda terakhir (charge)
    $lastCharge = $rental->payments()
        ->where('payment_type', 'charge')
        ->latest('created_at')
        ->first();

    if ($lastCharge && $lastCharge->status_pembayaran === 'success') {
        return back()->with('info', 'Denda sudah dibayar, tidak perlu kirim ulang.');
    }

    // nominal denda
    $amount = (int) $invoice->denda_tambahan;
    if ($amount <= 0) {
        return back()->with('error', 'Invoice ini tidak memiliki denda untuk dibayarkan.');
    }

    try {
        // buat ulang payment baru hanya untuk denda
        $payment = Payment::create([
            'rental_id' => $rental->rental_id,
            'gateway' => 'Duitku',
            'metode' => $method,
            'payment_type' => 'charge',
            'total_bayar' => $amount,
            'status_pembayaran' => 'pending',
            'gateway_reference' => 'INV-CHG-' . strtoupper(uniqid()),
            'merchant_order_id' => 'CHG' . $invoice->invoice_id . '-' . strtoupper(uniqid()),
            'payment_token' => null,
            'callback_status' => 'waiting',
            'tanggal_bayar' => now(),
            'expired_at' => now()->addMinutes(30),
        ]);

        // panggil fungsi kirim duitku
        $this->createDuitkuPayment(
            $rental,
            $payment,
            $invoice->invoice_id,
            $duitkuMethod,
            $amount,
            'RETRY'
        );

        // ubah status invoice jadi pending ulang
        $invoice->update(['status_invoice' => 'pending']);

        Log::info('🔁 Retry pembayaran denda berhasil dibuat.', ['invoice_id' => $invoice->invoice_id, 'payment_id' => $payment->id]);

        return back()->with('success', '✅ Pembayaran denda berhasil dikirim ulang ke Duitku.');
    } catch (\Throwable $e) {
        Log::error('❌ Gagal membuat ulang pembayaran: ' . $e->getMessage());
        return back()->with('error', 'Gagal membuat ulang pembayaran.');
    }
}
}

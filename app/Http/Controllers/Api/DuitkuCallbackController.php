<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class DuitkuCallbackController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('✅ Callback Duitku diterima', $request->all());

        $merchantOrderId = $request->merchantOrderId ?? null;
        $resultCode = $request->resultCode ?? null;

        // 🔍 Cari payment berdasarkan merchant_order_id
        $payment = Payment::where('merchant_order_id', $merchantOrderId)->first();

        if (!$payment) {
            Log::warning("❌ Payment tidak ditemukan untuk merchantOrderId: {$merchantOrderId}");
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $isSuccess = ($resultCode == '00');

        // 💳 Update status payment
        $payment->update([
            'status_pembayaran' => $isSuccess ? 'success' : 'failed',
            'callback_status'   => 'done',
            'tanggal_bayar'     => now(),
        ]);

        // 🚗 Update status rental berdasarkan jenis pembayaran
        if ($payment->rental) {
            $rental = $payment->rental;

            // 🔹 Kalau payment utama
            if ($payment->payment_type === 'main') {
                if ($isSuccess) {
                    // setelah user bayar pertama kali, rental mulai berjalan
                    $rental->update(['status_rental' => 'berjalan']);
                } else {
                    $rental->update(['status_rental' => 'dibatalkan']);
                }
            }

            // 🔸 Kalau payment tambahan (charge/invoice)
            if ($payment->payment_type === 'charge') {
                if ($isSuccess) {
                    // ✅ invoice charge berhasil → ubah jadi selesai
                    $rental->update(['status_rental' => 'selesai']);

                    $invoice = Invoice::where('rental_id', $rental->rental_id)
                        ->latest('tanggal_cetak')
                        ->first();
                    if ($invoice) {
                        $invoice->update(['status_invoice' => 'selesai']);
                    }

                    Log::info("✅ Charge invoice sukses untuk rental #{$rental->rental_id}");
                } else {
                    // ❌ charge gagal → ubah jadi dibatalkan
                    $invoice = Invoice::where('rental_id', $rental->rental_id)
                        ->latest('tanggal_cetak')
                        ->first();
                    if ($invoice) {
                        $invoice->update(['status_invoice' => 'dibatalkan']);
                    }
                }
            }
        }

        return response()->json(['message' => 'Callback processed successfully']);
    }
}

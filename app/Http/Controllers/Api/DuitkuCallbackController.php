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
        $resultCode      = $request->resultCode ?? null;
        $reference       = $request->reference ?? null;

        // ✅ Validasi Signature (penting untuk keamanan)
        $merchantCode = $request->merchantCode ?? '';
        $amount       = preg_replace('/[^0-9]/', '', ($request->amount ?? ''));
        $apiKey       = env('DUITKU_API_KEY');

        $expectedSign = md5($merchantCode . $amount . $merchantOrderId . $apiKey);

        if (($request->signature ?? '') !== $expectedSign) {
            Log::error('❌ Signature tidak valid pada callback', [
                'expected' => $expectedSign,
                'got'      => $request->signature
            ]);

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // 🔍 Cari payment berdasarkan merchantOrderId / reference
        $payment = Payment::where('merchant_order_id', $merchantOrderId)
            ->orWhere('gateway_reference', $reference)
            ->first();

        if (!$payment) {
            Log::warning("❌ Payment tidak ditemukan untuk merchantOrderId: {$merchantOrderId}");
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $isSuccess = ($resultCode == '00');

        // 💳 Update status payment
        $payment->update([
            'status_pembayaran' => $isSuccess ? 'success' : 'failed',
            'callback_status'   => $isSuccess ? 'done' : 'error',
            'tanggal_bayar'     => now(),
        ]);

        // 🚗 Update status rental berdasarkan jenis pembayaran
        if ($payment->rental) {
            $rental = $payment->rental;

            // 🔹 Payment utama
            if ($payment->payment_type === 'main') {
                $rental->update([
                    'status_rental' => $isSuccess ? 'berjalan' : 'dibatalkan'
                ]);
            }

            // 🔸 Payment charge
            if ($payment->payment_type === 'charge') {
                $invoice = Invoice::where('rental_id', $rental->rental_id)
                                   ->latest()
                                   ->first();

                if ($invoice) {
                    $invoice->update([
                        'status_invoice' => $isSuccess ? 'selesai' : 'dibatalkan'
                    ]);
                }

                if ($isSuccess) {
                    $rental->update(['status_rental' => 'selesai']);
                }
            }
        }

        return response()->json(['message' => 'Callback processed successfully']);
    }
}

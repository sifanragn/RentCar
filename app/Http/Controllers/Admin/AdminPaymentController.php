<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class AdminPaymentController extends Controller
{
    /**
     * 🔁 Admin refresh status pembayaran dari Duitku
     */
    public function refresh($id)
    {
        $payment = Payment::findOrFail($id);

        $merchantCode = 'DS25394';
        $apiKey = '06a924ce717ea70f6522e5c51241ccc6';

        $merchantOrderId = $payment->gateway_reference;
        $signature = md5($merchantCode . $merchantOrderId . $apiKey);

        $payload = [
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'signature' => $signature,
        ];

        try {
            $response = Http::post('https://sandbox.duitku.com/webapi/api/merchant/transactionStatus', $payload);
            $result = $response->json();

            Log::info('💬 Duitku Refresh Result', $result);

            if (isset($result['resultCode'])) {
                if ($result['resultCode'] == '00') {
                    $payment->update([
                        'status_pembayaran' => 'success',
                        'callback_status' => 'refreshed',
                    ]);
                } else {
                    $payment->update([
                        'status_pembayaran' => 'failed',
                        'callback_status' => 'refreshed',
                    ]);
                }

                return back()->with('success', '✅ Status pembayaran diperbarui dari Duitku.');
            } else {
                return back()->with('error', '⚠️ Gagal mengambil status dari Duitku.');
            }

        } catch (\Throwable $e) {
            Log::error('❌ Gagal refresh payment: ' . $e->getMessage());
            return back()->with('error', '❌ Terjadi kesalahan saat menghubungi Duitku.');
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class DuitkuCallbackController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('✅ Callback Duitku diterima', $request->all());

        $merchantOrderId = $request->merchantOrderId ?? null;
        $resultCode = $request->resultCode ?? null;

        $payment = Payment::where('gateway_reference', $merchantOrderId)->first();
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $isSuccess = $resultCode == '00';

        // update payment status
        $payment->update([
            'status_pembayaran' => $isSuccess ? 'success' : 'failed',
            'callback_status'   => 'done',
        ]);

        // update rental status
        $payment->rental?->update([
            'status_rental' => $isSuccess ? 'berhasil_dibayar' : 'gagal',
        ]);

        // 🔁 sinkronkan ke invoice juga
        if ($payment->rental && $payment->rental->invoice) {
            $invoice = $payment->rental->invoice;
            $invoice->update([
                'status_invoice' => $isSuccess ? 'selesai' : 'cancel',
            ]);
        }

        return response()->json(['message' => 'Callback processed successfully']);
    }
}

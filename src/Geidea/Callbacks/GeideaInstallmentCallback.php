<?php

namespace App\Services\Payment\PaymentProviders\Geidea\Callbacks;

use App\Constants\OrderStatus;
use App\Order;
use App\Services\Payment\Logger\Models\Log;
use App\Services\Payment\Models\PaymentTransaction;
use App\Support\HandleExceptionSupport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsController;

class GeideaInstallmentCallback
{
    use AsController;

    private Request $request;

    public function handle(Request $request): JsonResponse
    {
        $this->request = $request;

        Log::create([
            'key' => 'geidea_installment_callback',
            'payload' => json_encode($request->all()),
        ]);

        $transactionUuid = $request->get('order')['merchantReferenceId'];

        $paymentTransaction = PaymentTransaction::where('uuid', $transactionUuid)
            ->firstOr(fn () => HandleExceptionSupport::badRequest('Transaction not found'));

        $paymentTransaction->update([
            'provider_response_status' => 'responded',
            'provider_response' => array_merge($paymentTransaction->provider_response, [
                'transactionId' => $request->get('order')['orderId'],
                'orderReference' => $transactionUuid,
                'errors' => []
            ]),
        ]);

        $order = Order::where('_id', $paymentTransaction->payment_transactionable_id)
            ->firstOr(fn () => HandleExceptionSupport::badRequest('Order not found'));

        $order->update([
            'status' => OrderStatus::PLACED,
            'active' => true,
            'not_paid' => false,
            'paid_at' => now(),
            'hidden_at' => null,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}

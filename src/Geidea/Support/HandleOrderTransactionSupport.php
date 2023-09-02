<?php

namespace App\Services\Payment\PaymentProviders\Geidea\Support;

use App\Constants\OrderStatus;
use App\Order;
use App\Services\Payment\PaymentProviders\Geidea\Responses\BaseGeideaResponse;
use App\Services\Payment\PaymentProviders\Geidea\Responses\ErrorResponse;

class HandleOrderTransactionSupport
{
    public static function successfulTransaction(Order $order, BaseGeideaResponse|ErrorResponse $response): void
    {
        $order->update([
            'not_paid' => false,
            'paid_at' => now(),
            'status' => OrderStatus::PLACED,
            'active' => true,
            'hidden_at' => null,
        ]);

        $order->paymentTransaction->update([
            'status' => 'success',
            'provider_response_status' => $response->getStatus(),
            'provider_response' => array_merge($order->paymentTransaction->provider_response, [
                'transactionId' => $response->getTransactionId(),
                'orderReference' => $response->getOrderReference(),
                'errors' => []
            ]),
        ]);
    }

    public static function failedTransaction(Order $order, $response): void
    {
        $order->update([
            'hidden_at' => now(),
            'status' => OrderStatus::CANCELLED,
            'active' => false,
        ]);

        $order->paymentTransaction->update([
            'status' => 'failed',
            'provider_response' => array_merge($order->paymentTransaction->provider_response, [
                'errors' => $response->response(),
            ])
        ]);
    }
}

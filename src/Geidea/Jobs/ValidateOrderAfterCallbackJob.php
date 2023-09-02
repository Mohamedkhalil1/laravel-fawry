<?php

namespace App\Services\Payment\PaymentProviders\Geidea\Jobs;

use App\Constants\OrderStatus;
use App\Order;
use App\PaymentTransaction;
use App\Services\Payment\PaymentProviders\Geidea\GeideaClient;
use App\Services\Payment\PaymentProviders\Geidea\Models\GeideaIntegrationKey;
use App\Services\Payment\PaymentProviders\Geidea\Responses\BaseGeideaResponse;
use App\Services\Payment\PaymentProviders\Geidea\Responses\ErrorResponse;
use App\Services\Payment\PaymentProviders\Geidea\Support\HandleOrderTransactionSupport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ValidateOrderAfterCallbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public function __construct(
        private readonly string $transactionId,
        private readonly string $appId,
        private readonly GeideaIntegrationKey $integrationKey,
    ) {
    }

    public function handle(): void
    {
        $transaction = PaymentTransaction::where('uuid', $this->transactionId)->first();

        $order = Order::where('_id', $transaction->payment_transactionable_id)->first();

        if ($order->isPaid()) {
            return;
        }

        $client = GeideaClient::make($this->integrationKey);

        $response = $client->fetchTransactionByReferenceKey($this->transactionId)->response();

        if ($response->hasErrors()) {
            $response = $response->getErrorResponse();
            HandleOrderTransactionSupport::failedTransaction($order, $response);
            $this->release(60);
            return;
        }

        if ($response->getStatus() === 'Success' && $response->getDetailedStatus() === 'Paid') {
            HandleOrderTransactionSupport::successfulTransaction($order, $response);
        }
    }
}

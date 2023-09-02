<?php

namespace App\Services\Payment\PaymentProviders\Geidea;

use App\Services\Payment\Models\PaymentCheckout;
use App\Services\Payment\PaymentProviders\Geidea\Jobs\ValidateOrderAfterCallbackJob;
use App\Services\Payment\PaymentProviders\Geidea\Models\GeideaIntegrationKey;
use App\Services\Payment\PaymentProviders\Geidea\RequestBuilders\CheckoutRequestBuilder;
use App\Services\Payment\PaymentProviders\Geidea\RequestBuilders\RefundRequestBuilder;
use App\Services\Payment\PaymentProviders\Geidea\Responses\BaseGeideaResponse;
use App\Services\Payment\PaymentProviders\PaymentProviderInterface;
use Exception;

class GeideaPaymentServiceProvider implements PaymentProviderInterface
{
    private GeideaClient $client;
    private $business;
    private GeideaIntegrationKey $geideaIntegrationKey;

    public function __construct()
    {
        $this->geideaIntegrationKey = GeideaIntegrationKey::where('business_id', request()->get('app')->business_id)
            ->first();

        $this->client = new GeideaClient($this->geideaIntegrationKey);
    }


    public function checkout(PaymentCheckout $paymentCheckout): void
    {
        $memberProfile = $paymentCheckout->member->currentProfile($paymentCheckout->appId);

        $requestBuilder = CheckoutRequestBuilder::make();

        $paymentCheckout->isInstallment ? $requestBuilder->buildInstallmentPayment($paymentCheckout, $memberProfile) : $requestBuilder->buildCheckoutPayment($paymentCheckout, $memberProfile);

        $response = $this->client->setRequestBuilder($requestBuilder)
            ->createCheckout()
            ->response();

        if ($response->hasErrors()) {
            report(new Exception(json_encode($response->getErrorResponse()->response())));
            return;
        }

        if ($paymentCheckout->isInstallment) {
            ValidateOrderAfterCallbackJob::dispatch($paymentCheckout->orderId, $paymentCheckout->appId, $this->geideaIntegrationKey)
                ->delay(now()->addMinutes(GeideaClient::CALLBACK_TIMEOUT));

            return;
        }

        $this->client->finishTransaction();
    }

    public function refund(array $data): void
    {
        $requestBuilder = RefundRequestBuilder::make()
            ->setOrderId($data['transaction_id'])
            ->setRefundAmount($data['amount'] / 100);

        $this->client->setRequestBuilder($requestBuilder)->refund();
    }


    public function timedout(): bool
    {
        return $this->response()->isTimeout();
    }

    public function failed(): bool
    {
        return $this->response()->hasErrors();
    }

    public function getTransactionId(): string
    {
        return $this->response()->getTransactionId();
    }

    public function getMaskedCard(): string
    {
        return $this->response()->getMaskedCard();
    }

    public function getCardToken(): string
    {
        // TODO: Implement getCardToken() method.
    }

    public function getError(): string
    {
        return $this->response()->getErrorBody();
    }

    public function setBusiness($business): void
    {
        $this->business = $business;
    }

    public function response(): BaseGeideaResponse
    {
        return $this->client->response();
    }
}

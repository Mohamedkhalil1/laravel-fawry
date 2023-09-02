<?php

namespace App\Services\Payment\PaymentProviders\Geidea;

use App\Services\Payment\PaymentProviders\Geidea\Models\GeideaIntegrationKey;
use App\Services\Payment\PaymentProviders\Geidea\RequestBuilders\BaseRequestBuilder;
use App\Services\Payment\PaymentProviders\Geidea\Responses\BaseGeideaResponse;
use App\Services\Payment\PaymentProviders\Geidea\Responses\RefundResponse;
use App\Services\Payment\PaymentProviders\Geidea\Responses\SessionResponse;
use App\Services\Payment\PaymentProviders\Geidea\Responses\TransactionResponse;
use Illuminate\Http\Client\PendingRequest;

class GeideaClient
{
    const BASE_URL = 'https://api.merchant.geidea.net';
    const CALLBACK_TIMEOUT = 10;

    public PendingRequest $client;
    private BaseRequestBuilder $requestBuilder;
    private GeideaIntegrationKey $integrationKey;
    private BaseGeideaResponse $response;

    public function __construct(GeideaIntegrationKey $integrationKey)
    {
        $this->client = new PendingRequest();
        $this->integrationKey = $integrationKey;
        $this->setHeaders();
    }

    public static function make(GeideaIntegrationKey $integrationKey): static
    {
        return new static($integrationKey);
    }

    private function setHeaders(): self
    {
        $this->client = $this->client->withHeaders([
            'Authorization' => 'Basic ' . base64_encode("{$this->integrationKey->username}:{$this->integrationKey->password}"),
            'Content-Type'  => 'application/json',
        ]);
        return $this;
    }

    public function createCheckout(): self
    {
        $response = $this->post(self::BASE_URL . '/payment-intent/api/v1/direct/session');

        $this->response = SessionResponse::make($response);

        return $this;
    }

    public function refund(): self
    {
        $response = $this->post(self::BASE_URL . '/pgw/api/v1/direct/refund');

        $this->response = RefundResponse::make($response);

        return $this;
    }

    public function finishTransaction(): self
    {
        $response = $this->post(self::BASE_URL . '/pgw/api/v1/direct/pay/token');

        $this->response = TransactionResponse::make($response);

        return $this;
    }

    public function fetchTransactionByReferenceKey(string $merchantReferenceKey): self
    {
        $response = $this->get(self::BASE_URL . '/pgw/api/v1/direct/order', [
            'MerchantReferenceId' => $merchantReferenceKey,
        ]);

        $this->response = TransactionResponse::make($response);

        return $this;
    }

    public function get(string $endpoint, array $queryParams): array
    {
        StreamLog::log('Request to Geidea', [
            'endpoint' => $endpoint,
            'request'  => $queryParams,
        ]);

        $response = $this->client->get($endpoint, $queryParams);

        return $response->json();
    }


    public function post(string $endpoint): array
    {
        StreamLog::log('Request to Geidea', [
            'endpoint' => $endpoint,
            'request'  => $this->requestBuilder->build(),
        ]);

        $response = $this->client->post($endpoint, $this->requestBuilder->build());

        StreamLog::log('Response from Geidea', [
            'endpoint' => $endpoint,
            'request'  => $response->json(),
        ]);

        return $response->json();
    }

    public function setRequestBuilder(BaseRequestBuilder $requestBuilder): GeideaClient
    {
        $this->requestBuilder = $requestBuilder;
        return $this;
    }

    public function response(): BaseGeideaResponse
    {
        return $this->response;
    }
}

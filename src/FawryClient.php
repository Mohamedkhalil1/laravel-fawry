<?php

namespace Maherelgamil\LaravelFawry;

use Illuminate\Http\Client\PendingRequest;
use Maherelgamil\LaravelFawry\RequestBuilders\BaseRequestBuilder;
use Maherelgamil\LaravelFawry\RequestBuilders\TokenRequestBuilder;
use Maherelgamil\LaravelFawry\Responses\BaseResponse;
use Maherelgamil\LaravelFawry\Responses\TokenResponse;

class FawryClient
{
    const DEVELOPMENT_URL = 'https://api.merchant.geidea.net';
    const PRODUCTION_URL = 'https://api.merchant.geidea.net';
    public PendingRequest $client;
    private BaseRequestBuilder $requestBuilder;
    private BaseResponse $response;
    private string $merchantCode;
    private string $securityKey;
    private string $baseUri;

    public function __construct()
    {
        $this->client = new PendingRequest();
        $this->merchantCode = config('fawry.merchant_code');
        $this->securityKey = config('fawry.security_key');
        $this->baseUri = config('fawry.debug') ? self::DEVELOPMENT_URL : self::PRODUCTION_URL;
        $this->setHeaders();
    }

    public static function make(): static
    {
        return new static();
    }

    public function createCardToken($cardNumber, $expiryYear, $expiryMonth, $cvv, $user): static
    {
        $this->requestBuilder = TokenRequestBuilder::make($cardNumber, $expiryYear, $expiryMonth, $cvv, $user)
            ->setSignature($this->generateSignature($user->id))
            ->setMerchantCode($this->merchantCode);

        $response = $this->post($this->baseUri . '/cards/cardToken');
        $this->response = TokenResponse::make($response);

        if ($this->response->hasErrors()) {
            ## throw exception
            return $this;
        }

        $user->update($this->response->getUserCardTokenArray());
        return $this;
    }

    public function createCheckout()
    {
//        $response = $this->post(self::BASE_URL . '/payment-intent/api/v1/direct/session');
//
//        $this->response = SessionResponse::make($response);
//
//        return $this;
    }

    public function refund()
    {
//        $response = $this->post(self::BASE_URL . '/pgw/api/v1/direct/refund');
//
//        $this->response = RefundResponse::make($response);
//
//        return $this;
    }

    public function finishTransaction()
    {
//        $response = $this->post(self::BASE_URL . '/pgw/api/v1/direct/pay/token');
//
//        $this->response = TransactionResponse::make($response);
//
//        return $this;
    }

    public function fetchTransactionByReferenceKey(string $merchantReferenceKey)
    {
//        $response = $this->get(self::BASE_URL . '/pgw/api/v1/direct/order', [
//            'MerchantReferenceId' => $merchantReferenceKey,
//        ]);
//
//        $this->response = TransactionResponse::make($response);
//
//        return $this;
    }

    public function get(string $endpoint, array $queryParams): array
    {
        $response = $this->client->get($endpoint, $queryParams);

        return $response->json();
    }


    public function post(string $endpoint): array
    {
        $response = $this->client->post($endpoint, $this->requestBuilder->build());
        return $response->json();
    }


    public function response(): BaseResponse
    {
        return $this->response;
    }

    #region private methods
    private function generateSignature($id): string
    {
        return hash('sha256', $this->merchantCode . md5($id) . $this->securityKey);
    }

    private function setHeaders(): void
    {
        $this->client = $this->client->withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }
    #endregion
}

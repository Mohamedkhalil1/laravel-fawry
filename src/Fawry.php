<?php

namespace Maherelgamil\LaravelFawry;

use Maherelgamil\LaravelFawry\Interfaces\PaymentProviderInterface;
use Maherelgamil\LaravelFawry\Responses\BaseResponse;

class Fawry implements PaymentProviderInterface
{
    private FawryClient $client;

    public function __construct()
    {
        $this->client = FawryClient::make();
    }

    public function createCardToken($cardNumber, $expiryYear, $expiryMonth, $cvv, $user): void
    {
        $this->client->createCardToken($cardNumber, $expiryYear, $expiryMonth, $cvv, $user);
    }

    public function checkout(array $data): void
    {
        $this->client->finishTransaction();
    }

    public function refund(array $data): void {}

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

    public function getError(): string
    {
        return $this->response()->getErrorBody();
    }

    public function response(): BaseResponse
    {
        return $this->client->response();
    }
}

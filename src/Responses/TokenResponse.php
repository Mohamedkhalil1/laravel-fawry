<?php

namespace Maherelgamil\LaravelFawry\Responses;

class TokenResponse extends BaseResponse
{
    protected ?string $token = null;
    private ?string $brand = null;
    private ?string $lastFourDigits = null;

    public function toResponseObject(): TokenResponse
    {
        $this
            ->setLastFourDigits($this->responseBody['card']['lastFourDigits'] ?? null)
            ->setBrand($this->responseBody['card']['brand'] ?? null)
            ->setToken($this->responseBody['token'] ?? null)
            ->setResponseMessage($this->responseBody['responseMessage'] ?? null);

        return $this;
    }

    private function setToken($value): static
    {
        $this->token = $value;
        return $this;
    }

    private function setLastFourDigits($value): static
    {
        $this->lastFourDigits = $value;
        return $this;
    }

    private function setBrand($value): static
    {
        $this->brand = $value;
        return $this;
    }

    public function getUserCardTokenArray(): array
    {
        return [
            'payment_card_last_four'   => $this->lastFourDigits,
            'payment_card_brand'       => str_replace(' ', '', $this->brand),
            'payment_card_fawry_token' => $this->token,
        ];
    }

    public function toArray(): array
    {
        return [
            'status'         => $this->statusCode,
            'message'        => $this->responseMessage,
            'token'          => $this->token,
            'brand'          => $this->brand,
            'lastFourDigits' => $this->lastFourDigits,
        ];
    }
}
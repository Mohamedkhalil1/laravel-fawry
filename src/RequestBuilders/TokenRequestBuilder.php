<?php

namespace Maherelgamil\LaravelFawry\RequestBuilders;

use Illuminate\Database\Eloquent\Model;

class TokenRequestBuilder extends BaseRequestBuilder
{
    protected string $merchantCode;
    private string $signature;

    private string $cardNumber;
    private string $expiryYear;
    private string $expiryMonth;
    private string $cvv;
    private Model $user;

    public function __construct() {}

    public function setSignature(string $signature): self
    {
        $this->signature = $signature;
        return $this;
    }

    public function setMerchantCode(string $merchantCode): self
    {
        $this->merchantCode = $merchantCode;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'signature'         => $this->signature,
            "merchantCode"      => $this->merchantCode,
            "customerProfileId" => md5($this->user->id),
            "customerMobile"    => $this->user->mobile,
            "customerEmail"     => $this->user->email,
            "cardNumber"        => $this->cardNumber,
            "expiryYear"        => $this->expiryYear,
            "cvv"               => $this->cvv,
        ];
    }

    public function setCardNumber(string $cardNumber): static
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    public function setExpiryYear(string $expiryYear): static
    {
        $this->expiryYear = $expiryYear;
        return $this;
    }

    public function setExpiryMonth(string $expiryMonth): static
    {
        $this->expiryMonth = $expiryMonth;
        return $this;
    }

    public function setCvv(string $cvv): static
    {
        $this->cvv = $cvv;
        return $this;
    }

    public function setUser(Model $user): static
    {
        $this->user = $user;
        return $this;
    }
}

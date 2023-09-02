<?php

namespace Maherelgamil\LaravelFawry\RequestBuilders;

use Illuminate\Database\Eloquent\Model;

class TokenRequestBuilder extends BaseRequestBuilder
{
    protected string $merchantCode;
    private string $signature;

    public function __construct(
        private string $cardNumber, private string $expiryYear,
        private string $expiryMonth, private string $cvv, private Model $user,
    ) {}

    public static function make(string $cardNumber, string $expiryYear, string $expiryMonth, string $cvv, Model $user): static
    {
        return new static($cardNumber, $expiryYear, $expiryMonth, $cvv, $user);
    }

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
            "expiryMonth"       => $this->expiryYear,
            "cvv"               => $this->cvv,
        ];
    }
}

<?php

namespace App\Services\Payment\PaymentProviders\Geidea\RequestBuilders;

class RefundRequestBuilder extends BaseRequestBuilder
{
    private string $orderId;
    private float $refundAmount;

    public static function make(): static
    {
        return new static();
    }

    public function setRefundAmount(?float $amount = null): self
    {
        if (!$amount) {
            return $this;
        }
        $this->refundAmount = $amount;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'orderId' => $this->orderId,
            'refundAmount' => $this->refundAmount,
        ];
    }

    public function setOrderId(string $orderId): RefundRequestBuilder
    {
        $this->orderId = $orderId;
        return $this;
    }
}

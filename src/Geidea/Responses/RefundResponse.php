<?php

namespace App\Services\Payment\PaymentProviders\Geidea\Responses;

class RefundResponse extends BaseGeideaResponse
{
    private string $orderId;

    public function toResponseObject(): RefundResponse
    {
        if ($this->hasErrors) {
            return $this;
        }

        return $this
            ->setResponseMessage($this->responseBody['responseMessage'])
            ->setDetailedResponseMessage($this->responseBody['detailedResponseMessage'])
            ->setOrderId($this->responseBody['order']['orderId'] ?? $this->responseBody['orderId']);
    }

    public function toArray(): array
    {
        return [
            'status' => $this->responseMessage,
            'message' => $this->detailedResponseMessage,
            'order_id' => $this->orderId,
        ];
    }

    public function setResponseMessage(string $responseMessage = null): self
    {
        $this->responseMessage = $responseMessage;
        return $this;
    }

    public function setDetailedResponseMessage(?string $detailedResponseMessage = null): RefundResponse
    {
        $this->detailedResponseMessage = $detailedResponseMessage;
        return $this;
    }

    public function setOrderId(string $orderId): RefundResponse
    {
        $this->orderId = $orderId;
        return $this;
    }
}

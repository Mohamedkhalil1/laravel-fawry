<?php

namespace App\Services\Payment\PaymentProviders\Geidea\Responses;

class TransactionResponse extends BaseGeideaResponse
{
    private ?string $status;
    private string $detailedStatus;
    private string $orderId;

    public function toResponseObject(): self
    {
        if (array_key_exists('orders', $this->responseBody)) {
            $this->setFirstTransaction();
        }

        if ($this->hasErrors) {
            return $this;
        }

        $this
            ->setResponseMessage($this->responseBody['responseMessage'] ?? null)
            ->setDetailedResponseMessage($this->responseBody['detailedResponseMessage'] ?? null)
            ->setOrderId($this->responseBody['order']['orderId'])
            ->setStatus($this->responseBody['order']['status'])
            ->setDetailedStatus($this->responseBody['order']['detailedStatus']);

        return $this;
    }

    public function validateErrors(): self
    {
        parent::validateErrors();

        if (!isset($this->responseBody['orders']) && !isset($this->responseBody['order'])) {
            $this->hasErrors = true;
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->responseMessage,
            'message' => $this->detailedResponseMessage,
            'order_id' => $this->orderId,
            'order_status' => $this->status,
            'detailed_status' => $this->detailedStatus,
        ];
    }

    public function setDetailedStatus(string $detailedStatus): TransactionResponse
    {
        $this->detailedStatus = $detailedStatus;
        return $this;
    }

    public function setStatus(string $status): TransactionResponse
    {
        $this->status = $status;
        return $this;
    }

    public function setOrderId(string $orderId): TransactionResponse
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getStatus(): ?string
    {
        return $this->status ?? null;
    }

    public function getDetailedStatus(): string
    {
        return $this->detailedStatus;
    }

    private function setFirstTransaction(): self
    {
        if (empty($this->responseBody['orders'][0])) {
            $this->errorResponse->setResponseMessage('No orders found');
            $this->hasErrors = true;
            return $this;
        }
        $this->responseBody = [
            'order' => $this->responseBody['orders'][0],
            'responseMessage' => $this->responseBody['responseCode'],
            'detailedResponseMessage' => $this->responseBody['detailedResponseCode'],
        ];

        return $this;
    }
}

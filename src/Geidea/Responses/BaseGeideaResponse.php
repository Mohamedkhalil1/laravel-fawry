<?php

namespace App\Services\Payment\PaymentProviders\Geidea\Responses;

use App\Services\Payment\PaymentProviders\Geidea\Traits\BaseGeideaResponseStructure;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;

abstract class BaseGeideaResponse
{
    use ConditionallyLoadsAttributes, BaseGeideaResponseStructure;

    protected bool $hasErrors = false;
    protected ErrorResponse $errorResponse;

    public function __construct(array $responseBody)
    {
        $this->responseBody = $responseBody;
        $this->errorResponse = ErrorResponse::make($this->responseBody);
    }

    public static function make(array $responseBody): static
    {
        return (new static($responseBody))
            ->validateErrors()
            ->toResponseObject();
    }

    abstract public function toResponseObject(): self;

    abstract public function toArray(): array;

    public function getTransactionId(): string
    {
        return $this->responseBody['order']['orderId'] ?? '';
    }

    public function getOrderReference(): string
    {
        return $this->responseBody['order']['merchantReferenceId'] ?? '';
    }

    public function getMaskedCard()
    {
        return $this->responseBody['session']['paymentMethod']['maskedCard'] ?? '';
    }

    public function validateErrors(): self
    {
        if (isset($this->responseBody['errors'])) {
            $this->hasErrors = true;
        }

        $this->hasErrors = $this->responseBody['responseCode'] !== '000';

        return $this;
    }

    public function getErrorResponse(): ErrorResponse
    {
        return $this->errorResponse->toResponseObject();
    }

    public function getErrorBody(): string
    {
        return json_encode($this->responseBody);
    }

    public function isTimeout(): bool
    {
        if (!isset($this->responseBody['detailedResponseCode'])) {
            return false;
        }

        return $this->responseBody['detailedResponseCode'] === '515';
    }

    public function hasErrors(): bool
    {
        return $this->hasErrors;
    }
}

<?php

namespace App\Services\Payment\PaymentProviders\Geidea\Responses;

use App\Services\Payment\PaymentProviders\Geidea\Traits\BaseGeideaResponseStructure;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Illuminate\Http\Resources\MissingValue;

class ErrorResponse
{
    use ConditionallyLoadsAttributes, BaseGeideaResponseStructure;

    public function __construct(array $responseBody)
    {
        $this->responseBody = $responseBody;
    }

    public static function make(array $responseBody): ErrorResponse
    {
        return (new static($responseBody))->toResponseObject();
    }

    public function toResponseObject(): self
    {
        return $this
            ->setResponseMessage($this->responseBody['responseMessage'] ?? null)
            ->setDetailedResponseMessage($this->responseBody['detailedResponseMessage'] ?? null)
            ->setStatusCode($this->responseBody['responseCode'])
            ->setDetailedStatusCode($this->responseBody['detailedResponseCode']);
    }

    public function toArray(): array
    {
        return [
            'status' => $this->statusCode,
            'detailed_status' => $this->detailedStatusCode,
            'message' => $this->responseMessage ?? new MissingValue(),
            'detailed_message' => $this->detailedResponseMessage ?? new MissingValue(),
        ];
    }

    public function hasCustomerNotifiableError(): bool
    {
        if (!array_key_exists('detailedResponseCode', $this->responseBody)) {
            return false;
        }
        return $this->responseBody['detailedResponseCode'] > 500 && $this->responseBody['detailedResponseCode'] <= 531;
    }
}

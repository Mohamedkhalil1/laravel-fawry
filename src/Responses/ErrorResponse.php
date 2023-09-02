<?php

namespace Maherelgamil\LaravelFawry\Responses;

use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Illuminate\Http\Resources\MissingValue;
use Maherelgamil\LaravelFawry\Traits\BaseResponseStructure;

class ErrorResponse
{
    use ConditionallyLoadsAttributes, BaseResponseStructure;

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
        ## handle error response
        return $this
            ->setResponseMessage($this->responseBody['responseMessage'] ?? null)
            ->setStatusCode($this->responseBody['responseCode'])
            ->setDetailedStatusCode($this->responseBody['detailedResponseCode']);
    }

    public function toArray(): array
    {
        return [
            'status'          => $this->statusCode,
            'detailed_status' => $this->detailedStatusCode,
            'message'         => $this->responseMessage ?? new MissingValue(),
        ];
    }
}

<?php

namespace App\Services\Payment\PaymentProviders\Geidea\Responses;

use Illuminate\Http\Resources\MissingValue;

class SessionResponse extends BaseGeideaResponse
{
    private string $sessionId;

    public function toResponseObject(): SessionResponse
    {
        return $this
            ->setResponseMessage($this->responseBody['responseMessage'])
            ->setDetailedResponseMessage($this->responseBody['detailedResponseMessage'])
            ->setSessionId($this->responseBody['session']['id'] ?? null);
    }

    public function toArray(): array
    {
        return [
            'status' => $this->responseMessage,
            'message' => $this->detailedResponseMessage,
            'session_id' => $this->sessionId ?? new MissingValue(),
        ];
    }


    public function setSessionId(string $sessionId = null): SessionResponse
    {
        if (!$sessionId) {
            return $this;
        }

        $this->sessionId = $sessionId;
        return $this;
    }

    public function getExtras(): array
    {
        return [
            'session_id' => $this->sessionId,
        ];
    }
}

<?php

namespace App\Services\Payment\PaymentProviders\Geidea\Traits;

trait BaseGeideaResponseStructure
{
    protected ?string $responseMessage;
    protected ?string $detailedResponseMessage;
    protected array $responseBody;
    protected string $statusCode;
    protected string $detailedStatusCode;

    public function response(): array
    {
        return $this->filter($this->toArray());
    }

    public function getResponseBody(): array
    {
        return $this->responseBody;
    }

    public function getResponseMessage(): string
    {
        return $this->responseMessage;
    }

    public function setResponseMessage(string $responseMessage = null): self
    {
        if (isset($this->responseMessage)) {
            return $this;
        }

        $this->responseMessage = $responseMessage;
        return $this;
    }

    public function setDetailedResponseMessage(string $detailedResponseMessage = null): self
    {
        $this->detailedResponseMessage = $detailedResponseMessage;
        return $this;
    }
    public function setStatusCode(string $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function setDetailedStatusCode(string $detailedStatusCode): self
    {
        $this->detailedStatusCode = $detailedStatusCode;

        return $this;
    }
}

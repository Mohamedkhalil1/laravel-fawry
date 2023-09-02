<?php

namespace Maherelgamil\LaravelFawry\Traits;

trait BaseResponseStructure
{
    protected ?string $responseMessage;
    protected string $statusCode;
    protected string $detailedStatusCode;

    protected array $responseBody;

    public function response(): array
    {
        return $this->filter($this->toArray());
    }

    public function getResponseBody(): array
    {
        return $this->responseBody;
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

    public function setResponseMessage(string $responseMessage = null): self
    {
        if (isset($this->responseMessage)) {
            return $this;
        }

        $this->responseMessage = $responseMessage;
        return $this;
    }
}

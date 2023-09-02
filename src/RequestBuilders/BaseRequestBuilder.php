<?php

namespace Maherelgamil\LaravelFawry\RequestBuilders;

use Illuminate\Http\Resources\ConditionallyLoadsAttributes;

abstract class BaseRequestBuilder
{
    use ConditionallyLoadsAttributes;

    public static function make(): static
    {
        return new static();
    }

    public function build(): array
    {
        return $this->filter($this->toArray());
    }

    abstract public function toArray(): array;
}

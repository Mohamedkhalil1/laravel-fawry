<?php

namespace Maherelgamil\LaravelFawry\Interfaces;

interface PaymentProviderInterface
{
    public function checkout(array $data);

    public function refund(array $data);

    public function getTransactionId(): string;

    public function timedout(): bool;

    public function failed(): bool;

    public function getError(): string;

}

<?php

namespace App\Services\Payment\PaymentProviders\Geidea\Enums;

use Illuminate\Validation\Rules\Enum;

trait BaseEnum
{
    public static function labels(): array
    {
        static $names;

        if (!$names) {
            $names = array_map(function ($s) {
                return $s->label();
            }, static::cases());
        }

        return $names;
    }

    public static function keys(): array
    {
        static $keys;

        if (!$keys) {
            $keys = array_column(static::cases(), 'value');
        }

        return $keys;
    }

    public static function valid(): Enum
    {
        return new Enum(static::class);
    }

    public static function values(): array
    {
        $array = [];
        foreach (static::cases() as $case) {
            $array[$case->value] = $case->label();
        }
        return $array;
    }

    public function resource(): array
    {
        return [
            'id'   => $this->value,
            'name' => $this->label(),
        ];
    }

    public static function exists($status): bool
    {
        return in_array($status, static::keys());
    }

    abstract public function label(): string;
}

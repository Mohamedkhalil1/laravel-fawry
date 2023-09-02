<?php

namespace App\Services\Payment\PaymentProviders\Geidea\Enums;

use function Symfony\Component\String\s;

enum GeideaPaymentOptionEnum: string
{
    use BaseEnum;

    case VISA = 'visa';
    case MASTER_CARD = 'mastercard';
    case MADA = 'mada';
    case SHAHRY = 'shahry';
    case MEEZA_DIGITAL = 'meeza_digital';
    case VALU = 'valu';


    public function label(): string
    {
        return match ($this) {
            self::VISA => 'Visa',
            self::MASTER_CARD => 'MasterCard',
            self::MADA => 'Mada',
            self::SHAHRY => 'Shahry',
            self::MEEZA_DIGITAL => 'Meeza Digital',
            self::VALU => 'Valu',
        };
    }
}

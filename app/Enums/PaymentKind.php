<?php

namespace App\Enums;

enum PaymentKind: string
{
    case Payment = 'payment';
    case Correction = 'correction';

    public function label(): string
    {
        return match ($this) {
            self::Payment => 'Pembayaran',
            self::Correction => 'Koreksi',
        };
    }
}

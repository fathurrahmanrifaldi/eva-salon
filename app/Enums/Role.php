<?php

namespace App\Enums;

enum Role: string
{
    case Owner = 'owner';
    case Kasir = 'kasir';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Owner',
            self::Kasir => 'Kasir',
        };
    }
}

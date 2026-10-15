<?php

namespace App\Enums;

enum BookingSource: string
{
    case Online = 'online';
    case WalkIn = 'walk_in';
    case PhoneHold = 'phone_hold';

    public function label(): string
    {
        return match ($this) {
            self::Online => 'Online',
            self::WalkIn => 'Walk-in',
            self::PhoneHold => 'Telepon (tahan)',
        };
    }
}

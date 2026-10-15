<?php

namespace App\Enums;

enum ExceptionType: string
{
    case SalonClosed = 'salon_closed';
    case StaffLeave = 'staff_leave';

    public function label(): string
    {
        return match ($this) {
            self::SalonClosed => 'Salon tutup',
            self::StaffLeave => 'Cuti staf',
        };
    }
}

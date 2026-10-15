<?php

namespace App\Support;

class Phone
{
    /** Menerima 08xxx, +628xxx, 628xxx. Mengembalikan +628xxx atau null jika tidak valid. */
    public static function normalize(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        $p = preg_replace('/[\s\-().]/', '', $input);

        if (str_starts_with($p, '08')) {
            $p = '+62'.substr($p, 1);
        } elseif (str_starts_with($p, '628')) {
            $p = '+'.$p;
        }

        return preg_match('/^\+628\d{7,11}$/', $p) ? $p : null;
    }

    public static function format(?string $phone): string
    {
        if (! $phone) {
            return '-';
        }

        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '628')) {
            $digits = '08'.substr($digits, 3);
        }

        if (str_starts_with($digits, '08') && strlen($digits) >= 10) {
            return substr($digits, 0, 4).'-'.substr($digits, 4, 4).'-'.substr($digits, 8);
        }

        return $phone;
    }
}

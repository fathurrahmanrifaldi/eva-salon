<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalonProfile extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['require_payment_to_complete' => 'boolean'];
    }

    /** Hanya ada satu baris profil. */
    public static function current(): self
    {
        return static::query()->first() ?? new static([
            'name' => 'Eva Salon', 'address' => '', 'wa_number' => '',
            'cancel_limit_minutes' => 30, 'hold_default_minutes' => 30, 'hold_max_minutes' => 120,
            'pending_alert_hours' => 24, 'require_payment_to_complete' => false,
        ]);
    }
}

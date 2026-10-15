<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_closed' => 'boolean'];
    }

    public const DAYS = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public function dayName(): string
    {
        return self::DAYS[$this->day_of_week];
    }
}

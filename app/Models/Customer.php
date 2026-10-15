<?php

namespace App\Models;

use App\Models\Concerns\Undeletable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use Undeletable;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['consent_at' => 'datetime', 'last_visit_at' => 'datetime'];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}

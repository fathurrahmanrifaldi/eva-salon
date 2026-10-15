<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    protected $table = 'staff';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_staff');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(StaffSchedule::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}

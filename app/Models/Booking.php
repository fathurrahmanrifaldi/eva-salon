<?php

namespace App\Models;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Models\Concerns\Undeletable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use Undeletable;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'source' => BookingSource::class,
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'blocked_until' => 'datetime',
            'hold_until' => 'datetime',
            'consent_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class)->orderBy('sequence');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BookingStatusLog::class)->orderBy('id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderBy('id');
    }

    /** Booking yang mengunci slot: pending/confirmed/completed, atau hold yang belum kedaluwarsa. */
    public function scopeBlocking(Builder $q): Builder
    {
        return $q->where(function (Builder $q) {
            $q->whereIn('status', array_map(fn ($s) => $s->value, BookingStatus::blocking()))
                ->orWhere(fn (Builder $q) => $q->where('status', 'hold')->where('hold_until', '>', now()));
        });
    }

    public function holdExpired(): bool
    {
        return $this->status === BookingStatus::Hold && $this->hold_until && $this->hold_until->lte(now());
    }

    public function paidTotal(): int
    {
        return (int) ($this->relationLoaded('payments') ? $this->payments->sum('amount') : $this->payments()->sum('amount'));
    }

    public function remaining(): int
    {
        return max(0, (int) $this->total_price - $this->paidTotal());
    }

    public function isPaid(): bool
    {
        return $this->paidTotal() >= (int) $this->total_price;
    }
}

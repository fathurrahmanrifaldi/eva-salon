<?php

namespace App\Models;

use App\Enums\PaymentKind;
use App\Enums\PaymentMethod;
use App\Models\Concerns\Undeletable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use Undeletable;

    public const UPDATED_AT = null;

    public bool $appendOnly = true;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'method' => PaymentMethod::class,
            'kind' => PaymentKind::class,
            'paid_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}

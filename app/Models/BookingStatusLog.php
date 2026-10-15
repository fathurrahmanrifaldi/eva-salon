<?php

namespace App\Models;

use App\Enums\ActorType;
use App\Enums\BookingStatus;
use App\Models\Concerns\Undeletable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingStatusLog extends Model
{
    use Undeletable;

    public const UPDATED_AT = null;

    public bool $appendOnly = true;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'from_status' => BookingStatus::class,
            'to_status' => BookingStatus::class,
            'actor_type' => ActorType::class,
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

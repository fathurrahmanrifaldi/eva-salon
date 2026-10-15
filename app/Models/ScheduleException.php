<?php

namespace App\Models;

use App\Enums\ExceptionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleException extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_on' => 'date', 'ends_on' => 'date', 'type' => ExceptionType::class];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}

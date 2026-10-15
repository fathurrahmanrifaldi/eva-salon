<?php

namespace App\Models\Concerns;

use LogicException;

/**
 * agent.md bagian 4: bookings, payments, status logs, dan customers tidak boleh dihapus.
 * Model dengan $appendOnly = true juga tidak boleh diubah.
 */
trait Undeletable
{
    protected static function bootUndeletable(): void
    {
        static::deleting(fn ($model) => throw new LogicException(class_basename($model).' tidak boleh dihapus.'));

        static::updating(function ($model) {
            if (property_exists($model, 'appendOnly') && $model->appendOnly) {
                throw new LogicException(class_basename($model).' bersifat append-only.');
            }
        });
    }
}

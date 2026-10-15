<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_available' => 'boolean', 'is_featured' => 'boolean', 'is_sensitive' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'service_staff');
    }

    /** "Rp 150.000" atau "Rp 150.000 – Rp 250.000" */
    public function priceLabel(): string
    {
        return $this->price_max && $this->price_max > $this->price
            ? rupiah($this->price).' – '.rupiah($this->price_max)
            : rupiah($this->price);
    }

    /** Isi paket: satu poin per baris di description. */
    public function lines(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $this->description))));
    }
}

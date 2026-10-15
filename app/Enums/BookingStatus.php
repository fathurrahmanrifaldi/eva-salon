<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Hold = 'hold';
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Hold => 'Ditahan',
            self::Pending => 'Menunggu konfirmasi',
            self::Confirmed => 'Terkonfirmasi',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
            self::NoShow => 'Tidak datang',
        };
    }

    /** Nama ikon di components/icon.blade.php. Warna tidak boleh jadi satu-satunya pembeda. */
    public function icon(): string
    {
        return match ($this) {
            self::Hold => 'lock',
            self::Pending => 'clock',
            self::Confirmed => 'check',
            self::Completed => 'check-double',
            self::Cancelled => 'x-mark',
            self::NoShow => 'no-show',
        };
    }

    /** Kelas CSS di resources/css/app.css (token warna status dari design.md bagian 3). */
    public function badge(): string
    {
        return 'badge '.$this->badgeClass();
    }

    public function badgeClass(): string
    {
        return 'badge-'.$this->value;
    }

    /** Status yang mengunci slot staf (hold dicek terpisah lewat hold_until). */
    public static function blocking(): array
    {
        return [self::Pending, self::Confirmed, self::Completed];
    }

    /** @return list<self> */
    public function next(): array
    {
        return match ($this) {
            self::Hold => [self::Pending, self::Cancelled],
            self::Pending => [self::Confirmed, self::Cancelled],
            self::Confirmed => [self::Completed, self::Cancelled, self::NoShow],
            default => [],
        };
    }

    public function canGoTo(self $to): bool
    {
        return in_array($to, $this->next(), true);
    }

    public function needsReason(): bool
    {
        return $this === self::Cancelled || $this === self::NoShow;
    }
}

<?php

namespace App\Support;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\SalonProfile;

/** Tautan WhatsApp wa.me dengan pesan terisi (PRD bagian 10). Nada pesan: "kamu" (agent.md bagian 12). */
class Wa
{
    public static function url(?string $phone, string $text = ''): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);

        return $digits ? 'https://wa.me/'.$digits.($text ? '?text='.rawurlencode($text) : '') : null;
    }

    public static function directLink(?string $phone): ?string
    {
        return self::url($phone);
    }

    public static function bookingLink(Booking $b): ?string
    {
        return self::toCustomer($b) ?? self::directLink($b->customer_phone);
    }

    /** Pelanggan -> salon, dari halaman konfirmasi booking online. */
    public static function toSalon(Booking $b): ?string
    {
        $profile = SalonProfile::current();

        return self::url($profile->wa_number, "Halo {$profile->name}, saya {$b->customer_name}.\n"
            ."Booking {$b->booking_code}: ".self::summary($b)."\nMohon dikonfirmasi ya. Terima kasih.");
    }

    /** Kasir -> pelanggan, isi pesan mengikuti status. Null jika tidak ada nomor atau status tidak relevan. */
    public static function toCustomer(Booking $b): ?string
    {
        $profile = SalonProfile::current();
        $name = $b->customer_name;
        $detail = self::summary($b);

        $text = match ($b->status) {
            BookingStatus::Hold, BookingStatus::Pending => "Halo kak {$name}, booking kamu di {$profile->name} sudah kami konfirmasi.\nKode: {$b->booking_code}\n{$detail}\nKami tunggu kedatangannya di {$profile->address}.",
            BookingStatus::Confirmed => "Halo kak {$name}, pengingat jadwal kamu di {$profile->name}.\nKode: {$b->booking_code}\n{$detail}\nSampai ketemu ya!",
            BookingStatus::Cancelled => "Halo kak {$name}, booking {$b->booking_code} ({$detail}) di {$profile->name} sudah dibatalkan. Kapan pun mau booking lagi, kami siap membantu.",
            default => null,
        };

        return $text ? self::url($b->customer_phone, $text) : null;
    }

    private static function summary(Booking $b): string
    {
        $services = $b->relationLoaded('items')
            ? $b->items->pluck('service_name')->join(', ')
            : $b->items()->pluck('service_name')->join(', ');

        $staffName = $b->relationLoaded('staff')
            ? ($b->staff?->name ?? 'Staf')
            : ($b->staff()->value('name') ?? 'Staf');

        return "{$services} bersama {$staffName}, ".tanggal($b->start_at).' pukul '.jam($b->start_at).' WIB';
    }
}

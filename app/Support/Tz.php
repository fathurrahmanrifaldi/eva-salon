<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Satu-satunya tempat konversi zona waktu: database UTC, tampilan Asia/Jakarta (agent.md bagian 3).
 */
class Tz
{
    public static function display(): string
    {
        return config('app.display_timezone');
    }

    /** UTC -> zona tampilan. */
    public static function local(CarbonInterface|string|null $value): ?CarbonImmutable
    {
        return $value === null ? null : CarbonImmutable::parse($value)->setTimezone(self::display());
    }

    /** Input pengguna (waktu Jakarta) -> UTC. */
    public static function parse(string $local): CarbonImmutable
    {
        return CarbonImmutable::parse($local, self::display())->utc();
    }

    /** Hari lokal (00:00 Jakarta) untuk sebuah instan atau tanggal Y-m-d. */
    public static function day(CarbonInterface|string $value): CarbonImmutable
    {
        return is_string($value)
            ? CarbonImmutable::parse($value, self::display())->startOfDay()
            : self::local($value)->startOfDay();
    }

    /** Jam lokal (H:i[:s]) pada hari lokal tertentu -> UTC. */
    public static function at(CarbonImmutable $localDay, string $time): CarbonImmutable
    {
        return $localDay->setTimeFromTimeString($time)->utc();
    }

    public static function now(): CarbonImmutable
    {
        return CarbonImmutable::now('UTC');
    }
}

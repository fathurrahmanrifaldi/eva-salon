<?php

use App\Support\Tz;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

if (! function_exists('rupiah')) {
    function rupiah(int|float|null $n): string
    {
        return 'Rp '.number_format((int) $n, 0, ',', '.');
    }
}

if (! function_exists('durasi')) {
    function durasi(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return trim(($h ? "$h jam " : '').($m ? "$m mnt" : ''));
    }
}

if (! function_exists('local')) {
    function local(CarbonInterface|string|null $value): ?CarbonImmutable
    {
        return Tz::local($value);
    }
}

if (! function_exists('tanggal')) {
    /** "Sabtu, 17 Oktober 2026" */
    function tanggal(CarbonInterface|string|null $value, string $format = 'l, j F Y'): string
    {
        return $value ? Tz::local($value)->translatedFormat($format) : '';
    }
}

if (! function_exists('jam')) {
    /** "10.00" */
    function jam(CarbonInterface|string|null $value): string
    {
        return $value ? Tz::local($value)->format('H.i') : '';
    }
}

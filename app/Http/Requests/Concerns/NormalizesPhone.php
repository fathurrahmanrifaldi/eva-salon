<?php

namespace App\Http\Requests\Concerns;

use App\Support\Phone;

trait NormalizesPhone
{
    /** Ubah 08xxx / +628xxx menjadi +628xxx sebelum validasi; nilai tak valid dibiarkan agar ditolak regex. */
    protected function normalizePhone(string $key = 'phone'): void
    {
        $raw = $this->input($key);
        if (filled($raw)) {
            $this->merge([$key => Phone::normalize($raw) ?? $raw]);
        }
    }

    protected const PHONE_RULE = 'regex:/^\+628\d{7,11}$/';

    protected const PHONE_MESSAGE = 'Nomor telepon tidak valid. Gunakan format 08xxx atau +628xxx.';
}

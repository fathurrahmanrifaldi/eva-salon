<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesPhone;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    use NormalizesPhone;

    protected function prepareForValidation(): void
    {
        $this->normalizePhone('phone');
        $this->normalizePhone('wa_number');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['nullable', self::PHONE_RULE],
            'wa_number' => ['required', self::PHONE_RULE],
            // Hanya URL embed Google Maps, dirender sebagai src iframe (bukan HTML mentah).
            'map_embed' => ['nullable', 'url', 'starts_with:https://www.google.com/maps/embed', 'max:1000'],
            'operating_note' => ['nullable', 'string', 'max:255'],
            'cancel_limit_minutes' => ['required', 'integer', 'min:0', 'max:1440'],
            'hold_default_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'hold_max_minutes' => ['required', 'integer', 'gte:hold_default_minutes', 'max:1440'],
            'pending_alert_hours' => ['required', 'integer', 'min:1', 'max:168'],
            'hours' => ['required', 'array', 'size:7'],
            'hours.*.open_time' => ['nullable', 'date_format:H:i'],
            'hours.*.close_time' => ['nullable', 'date_format:H:i', 'after:hours.*.open_time'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => self::PHONE_MESSAGE,
            'wa_number.regex' => self::PHONE_MESSAGE,
            'map_embed.starts_with' => 'Gunakan URL embed dari Google Maps (https://www.google.com/maps/embed...).',
            'hours.*.close_time.after' => 'Jam tutup harus setelah jam buka.',
        ];
    }
}

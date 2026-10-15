<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesPhone;
use Illuminate\Foundation\Http\FormRequest;

class StaffRequest extends FormRequest
{
    use NormalizesPhone;

    protected function prepareForValidation(): void
    {
        $this->normalizePhone();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'in:female,male'],
            'phone' => ['nullable', self::PHONE_RULE],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            // schedule[hari 0-6][urutan shift][start|end]
            'schedule' => ['nullable', 'array'],
            'schedule.*.*.start' => ['nullable', 'date_format:H:i', 'required_with:schedule.*.*.end'],
            'schedule.*.*.end' => ['nullable', 'date_format:H:i', 'required_with:schedule.*.*.start'],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => self::PHONE_MESSAGE, 'schedule.*.*.*.required_with' => 'Isi jam mulai dan selesai shift.'];
    }

    /** Validasi lintas kolom: jam selesai setelah jam mulai. */
    public function after(): array
    {
        return [function ($validator) {
            foreach ((array) $this->input('schedule', []) as $day => $shifts) {
                foreach ((array) $shifts as $i => $shift) {
                    if (filled($shift['start'] ?? null) && filled($shift['end'] ?? null) && $shift['end'] <= $shift['start']) {
                        $validator->errors()->add("schedule.$day.$i.end", 'Jam selesai harus setelah jam mulai.');
                    }
                }
            }
        }];
    }
}

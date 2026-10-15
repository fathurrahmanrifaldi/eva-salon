<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesPhone;
use App\Models\SalonProfile;
use App\Support\Tz;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class HoldRequest extends FormRequest
{
    use NormalizesPhone;

    protected $errorBag = 'hold';

    protected function prepareForValidation(): void
    {
        $this->normalizePhone();
        if (in_array($this->input('staff_id'), ['any', '', null], true)) {
            $this->merge(['staff_id' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            'staff_id' => ['nullable', 'integer', 'exists:staff,id'],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', self::PHONE_RULE],
            'hold_minutes' => ['required_without:hold_until_time', 'nullable', 'integer', 'min:5', 'max:'.SalonProfile::current()->hold_max_minutes],
            'hold_until_time' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_ids.required' => 'Pilih minimal satu layanan.',
            'phone.regex' => self::PHONE_MESSAGE,
            'hold_minutes.max' => 'Maksimal waktu tahan adalah :max menit.',
        ];
    }

    public function startAt(): CarbonImmutable
    {
        return Tz::parse($this->date.' '.$this->time);
    }

    public function holdUntil(): CarbonImmutable
    {
        if ($this->filled('hold_until_time')) {
            return Tz::parse(Tz::local(Tz::now())->toDateString().' '.$this->hold_until_time);
        }

        return Tz::now()->addMinutes((int) $this->hold_minutes);
    }
}

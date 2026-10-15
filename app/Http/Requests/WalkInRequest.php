<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesPhone;
use App\Support\Tz;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class WalkInRequest extends FormRequest
{
    use NormalizesPhone;

    protected $errorBag = 'walkin';

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
            'start_time' => ['required', 'date_format:H:i'],
            'name' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', self::PHONE_RULE],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return ['service_ids.required' => 'Pilih minimal satu layanan.', 'phone.regex' => self::PHONE_MESSAGE];
    }

    /** Walk-in selalu hari ini (waktu Jakarta). */
    public function startAt(): CarbonImmutable
    {
        return Tz::parse(Tz::local(Tz::now())->toDateString().' '.$this->start_time);
    }
}

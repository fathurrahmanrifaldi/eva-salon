<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesPhone;
use App\Support\Tz;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    use NormalizesPhone;

    protected function prepareForValidation(): void
    {
        $this->normalizePhone();
        if (in_array($this->input('staff_id'), ['any', '', null], true)) {
            $this->merge(['staff_id' => null]);
        }
    }

    public function rules(): array
    {
        $days = config('salon.booking_window_days');

        return [
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            'staff_id' => ['nullable', 'integer', 'exists:staff,id'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', "before_or_equal:+{$days} days"],
            'time' => ['required', 'date_format:H:i'],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', self::PHONE_RULE],
            'notes' => ['nullable', 'string', 'max:500'],
            'consent' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_ids.required' => 'Pilih minimal satu layanan.',
            'date.required' => 'Pilih tanggal.',
            'time.required' => 'Pilih jam.',
            'name.required' => 'Nama wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => self::PHONE_MESSAGE,
            'consent.accepted' => 'Anda perlu menyetujui penggunaan data untuk melanjutkan.',
        ];
    }

    public function startAt(): CarbonImmutable
    {
        return Tz::parse($this->date.' '.$this->time);
    }
}

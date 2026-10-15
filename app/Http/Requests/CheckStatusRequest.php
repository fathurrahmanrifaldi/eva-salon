<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesPhone;
use Illuminate\Foundation\Http\FormRequest;

class CheckStatusRequest extends FormRequest
{
    use NormalizesPhone;

    protected function prepareForValidation(): void
    {
        $this->normalizePhone();
        $this->merge(['code' => strtoupper(trim((string) $this->input('code')))]);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'size:8'],
            'phone' => ['required', self::PHONE_RULE],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => self::PHONE_MESSAGE, 'code.size' => 'Kode booking terdiri dari 8 karakter.'];
    }
}

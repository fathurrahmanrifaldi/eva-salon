<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
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
            'phone' => ['nullable', self::PHONE_RULE, Rule::unique('customers', 'phone')->ignore($this->route('customer'))],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => self::PHONE_MESSAGE, 'phone.unique' => 'Nomor ini sudah dipakai pelanggan lain.'];
    }
}

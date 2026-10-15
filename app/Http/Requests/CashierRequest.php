<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CashierRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('cashier'))],
        ];
    }

    public function messages(): array
    {
        return ['email.unique' => 'Email ini sudah terdaftar.'];
    }
}

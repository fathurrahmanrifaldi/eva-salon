<?php

namespace App\Http\Requests;

use App\Enums\PaymentKind;
use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:1', 'max:100000000'],
            'method' => ['required', Rule::enum(PaymentMethod::class)],
            'kind' => ['required', Rule::enum(PaymentKind::class)],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }
}

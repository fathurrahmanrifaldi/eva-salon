<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65000'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:service_categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'price_max' => ['nullable', 'integer', 'gte:price', 'max:100000000'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:600'],
            'buffer_minutes' => ['required', 'integer', 'min:0', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'staff_ids' => ['nullable', 'array'],
            'staff_ids.*' => ['integer', 'exists:staff,id'],
        ];
    }

    public function messages(): array
    {
        return ['price_max.gte' => 'Harga maksimum harus lebih besar atau sama dengan harga minimum.'];
    }
}

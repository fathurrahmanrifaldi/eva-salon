<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GalleryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'caption' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return ['image.max' => 'Ukuran foto maksimal 2 MB.', 'image.required' => 'Pilih foto yang akan diunggah.'];
    }
}

<?php

namespace App\Http\Requests;

use App\Enums\ExceptionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExceptionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(ExceptionType::class)],
            // salon_closed boleh tanpa staf (seluruh salon); staff_leave wajib punya staf.
            'staff_id' => [Rule::requiredIf($this->input('type') === ExceptionType::StaffLeave->value), 'nullable', 'exists:staff,id'],
            'starts_on' => ['required', 'date_format:Y-m-d'],
            'ends_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:starts_on'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return ['staff_id.required' => 'Pilih staf yang cuti.', 'ends_on.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.'];
    }
}

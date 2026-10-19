<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'tickets' => ['required', 'array', 'min:1'],
            'tickets.*' => ['integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'tickets.required' => 'Dữ liệu số lượng vé không hợp lệ.',
            'tickets.array' => 'Dữ liệu vé phải là một mảng.',
            'tickets.*.integer' => 'Số lượng vé phải là số nguyên.',
            'tickets.*.min' => 'Số lượng vé không được âm.',
        ];
    }
}

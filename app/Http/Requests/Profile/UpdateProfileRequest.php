<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^(0|\+84)[35789][0-9]{8}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'name.string' => 'Họ và tên không hợp lệ.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'phone.regex' => 'Số điện thoại không hợp lệ (gồm 10 số, VD: 0912345678 hoặc +84912345678).',
        ];
    }
}

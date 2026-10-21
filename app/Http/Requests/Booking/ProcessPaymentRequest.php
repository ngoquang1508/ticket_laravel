<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'in:vnpay,direct,free'],
            'customer_name'  => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'regex:/^(0|\+84)[35789][0-9]{8}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Vui lòng chọn hình thức thanh toán.',
            'payment_method.in'       => 'Phương thức thanh toán không hợp lệ.',
            'customer_name.required'  => 'Vui lòng nhập họ và tên người nhận vé.',
            'customer_name.max'       => 'Họ và tên không được vượt quá 255 ký tự.',
            'customer_email.required' => 'Vui lòng nhập địa chỉ email nhận vé.',
            'customer_email.email'    => 'Địa chỉ email không đúng định dạng.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại người nhận vé.',
            'customer_phone.regex'    => 'Số điện thoại không hợp lệ (VD: 0912345678).',
        ];
    }
}

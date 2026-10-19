<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'cart_data' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'cart_data.required' => 'Vui lòng chọn vé trước khi tiến hành thanh toán.',
            'cart_data.string' => 'Dữ liệu giỏ vé không hợp lệ.',
        ];
    }

    /**
     * Lấy mảng dữ liệu giỏ vé đã parse từ JSON
     */
    public function getCartData(): array
    {
        $raw = $this->input('cart_data');
        if (is_array($raw)) {
            return $raw;
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
}

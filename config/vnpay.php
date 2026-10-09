<?php

/**
 * Cấu hình VNPay Payment Gateway.
 * Không dùng env() trực tiếp trong controller – dùng config('vnpay.*') thay thế.
 */
return [
    // Mã terminal (TMN Code) từ VNPay
    'tmn_code'    => env('VNP_TMN_CODE', ''),

    // Hash secret từ VNPay
    'hash_secret' => env('VNP_HASH_SECRET', ''),

    // URL cổng thanh toán VNPay (sandbox hoặc production)
    'url'         => env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
];

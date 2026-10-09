<?php

namespace App\Services;

use App\Models\Order;

class VNPayService
{
    public function createPaymentUrl(Order $order): string
    {
        $vnp_Url = env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnp_Returnurl = route('orders.vnpay.return');
        $vnp_TmnCode = env('VNP_TMN_CODE'); // Mã website tại VNPAY 
        $vnp_HashSecret = env('VNP_HASH_SECRET'); // Chuỗi bí mật

        $vnp_TxnRef = $order->order_code; // Mã đơn hàng.
        $vnp_OrderInfo = 'Thanh toan don hang ' . $order->order_code;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $order->total_amount * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = ''; // Để trống thì VNPAY sẽ hiển thị danh sách ngân hàng
        $vnp_IpAddr = request()->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return $vnp_Url;
    }

    public function verifyPayment(array $vnpayData): array
    {
        $vnp_SecureHash = $vnpayData['vnp_SecureHash'] ?? '';
        $inputData = array();
        
        foreach ($vnpayData as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        if ($secureHash === $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00' || $inputData['vnp_TransactionStatus'] == '00') {
                return ['success' => true, 'order_code' => $inputData['vnp_TxnRef'], 'transaction_no' => $inputData['vnp_TransactionNo']];
            } else {
                return ['success' => false, 'message' => 'Giao dịch không thành công. Mã lỗi: ' . $inputData['vnp_ResponseCode'], 'order_code' => $inputData['vnp_TxnRef']];
            }
        } else {
            return ['success' => false, 'message' => 'Chữ ký không hợp lệ.', 'order_code' => $inputData['vnp_TxnRef'] ?? ''];
        }
    }
}

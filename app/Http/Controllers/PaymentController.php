<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Exception;

class PaymentController extends Controller
{
    public function makePayment(Request $request)
    {
        try {
            $data = $request->all();

            // Kiểm tra xem 'total' có tồn tại và hợp lệ không
            if (!isset($data['total']) || !is_numeric($data['total']) || $data['total'] <= 0) {
                return ApiResponse::error("Invalid total amount", 400);
            }

            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

            // Determine the return URL based on the environment
            $vnp_Returnurl = env('APP_ENV') === 'production'
                ? env('PRODUCTION_RETURN_URL')
                : env('FRONTEND_URL_RRETURN_AFTER_PAYMENT');

            $vnp_TmnCode = "V1O3H94J"; // Mã website tại VNPAY
            $vnp_HashSecret = "ORWNNIISEYIOYFPCVQGSPKGCIACSEPPP"; // Chuỗi bí mật

            $vnp_TxnRef = 'ECOSAVE' . rand(1, 999999) . rand(0, 99999);
            $vnp_OrderInfo = "Thanh toan hoa don";
            $vnp_OrderType = "grocerymart coffee shop";
            $vnp_Amount = $data['total'] * 100;
            $vnp_Locale = 'VN';
            $vnp_BankCode = 'NCB';
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

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

            return ApiResponse::success($vnp_Url, "Post total amount on VNpay successfully");
        } catch (Exception $e) {
            // Xử lý lỗi chung
            return ApiResponse::error("An error occurred: " . $e->getMessage(), 500);
        }
    }
}

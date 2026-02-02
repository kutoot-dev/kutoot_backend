<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsHelper
{
    public static function sendOtp($phone, $otp)
    {
        try {
            $url = "https://apibulksms.way2mint.com/pushsms";

            // Prepare full message
            $message = "Your Kutoot login OTP is: $otp This code is valid for 10 minutes. "
                     . "Use it to securely access your Kutoot account. Do not share this code with anyone. "
                     . "-Team Kutoot | Shopping is Winning";

            // Build query manually (URL encoded)
            $query = [
                'username' => config('services.sms.username'),
                'password' => config('services.sms.password'),
                'to'       => "91" . ltrim($phone, "+"),
                'from'     => config('services.sms.sender'),
                'text'     => $message,
                // data4 is optional, but provider expects it → add unique ids if needed
                'data4'    => '1701175557617315269,1702173216915572636'
            ];

            // Call API with encoded query string
            $response = Http::withOptions(['verify' => false]) // disable SSL issues if any
                ->get($url . '?' . http_build_query($query));

            $body = $response->body();

            // Normalize response
            $normalized = [
                'success' => false,
                'message' => 'Unknown error',
                'raw'     => $body
            ];

            // Way2mint usually returns 'success' in plain text or JSON
            if (stripos($body, 'success') !== false) {
                $normalized['success'] = true;
                $normalized['message'] = 'SMS sent successfully';
            }

            return $normalized;
        } catch (\Exception $e) {
            Log::error('SmsHelper sendOtp failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'SMS sending failed: ' . $e->getMessage(),
                'raw'     => $e->getMessage()
            ];
        }
    }
}

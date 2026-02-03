<?php

namespace App\Http\Controllers\API;

use App\Helpers\MailHelper;
use App\Helpers\SmsHelper;
use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

/**
 * @group Otp
 */
class OtpController extends Controller
{
    /**
     * Send OTP to phone number
     * POST /api/otp/send
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number. Please provide a 10-digit phone number.',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->phone;

        // Rate limiting: check if OTP was sent recently (within 60 seconds)
        $lastSent = Cache::get('otp_last_sent_' . $phone);
        if ($lastSent && now()->diffInSeconds($lastSent) < 60) {
            $remainingSeconds = 60 - now()->diffInSeconds($lastSent);
            return response()->json([
                'success' => false,
                'message' => "Please wait {$remainingSeconds} seconds before requesting another OTP"
            ], 429);
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Store OTP in cache for 10 minutes
        Cache::put('otp_' . $phone, $otp, now()->addMinutes(10));
        Cache::put('otp_last_sent_' . $phone, now(), now()->addMinutes(1));

        // Attempt to send SMS
        $smsSent = false;
        $smsResult = null;
        try {
            $smsResult = SmsHelper::sendOtp($phone, $otp);
            $smsSent = isset($smsResult['success']) && $smsResult['success'];
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            // Return success even if SMS fails, but log the error
            // The OTP is still valid for 10 minutes
        }

        // Log for debugging (remove in production)
        Log::info('OTP for ' . $phone . ': ' . $otp);

        $response = [
            'success' => true,
            'message' => 'OTP sent successfully'
        ];

        // Include debug OTP in non-production environments
        if (config('app.debug')) {
            $response['debug_otp'] = $otp;
            $response['sms_sent'] = $smsSent;
        }

        return response()->json($response);
    }

    /**
     * Verify OTP
     * POST /api/otp/verify
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->phone;
        $otp = $request->otp;

        $storedOtp = Cache::get('otp_' . $phone);

        if (!$storedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired. Please request a new OTP.'
            ], 400);
        }

        if ($storedOtp != $otp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP invalid or expired'
            ], 400);
        }

        // Mark as verified in cache (valid for 30 minutes)
        Cache::put('otp_verified_' . $phone, true, now()->addMinutes(30));

        // Clear the OTP
        Cache::forget('otp_' . $phone);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified'
        ]);
    }

    /**
     * Send OTP to email
     * POST /api/otp/send-email
     */
    public function sendEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email address',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;

        // Rate limiting: check if OTP was sent recently (within 60 seconds)
        $lastSent = Cache::get('otp_email_last_sent_' . $email);
        if ($lastSent && now()->diffInSeconds($lastSent) < 60) {
            $remainingSeconds = 60 - now()->diffInSeconds($lastSent);
            return response()->json([
                'success' => false,
                'message' => "Please wait {$remainingSeconds} seconds before requesting another OTP"
            ], 429);
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Store OTP in cache for 10 minutes
        Cache::put('otp_email_' . $email, $otp, now()->addMinutes(10));
        Cache::put('otp_email_last_sent_' . $email, now(), now()->addMinutes(1));

        // Send email using .env credentials (SendGrid)
        $emailSent = false;
        try {
            MailHelper::setEnvMailConfig();
            Mail::to($email)->send(new OtpMail($otp));
            $emailSent = true;
        } catch (\Exception $e) {
            Log::error('OTP email sending failed: ' . $e->getMessage());
        }

        // Log for debugging
        Log::info('OTP for email ' . $email . ': ' . $otp);

        $response = [
            'success' => true,
            'message' => 'OTP sent successfully to your email'
        ];

        // Include debug OTP in non-production environments
        if (config('app.debug')) {
            $response['debug_otp'] = $otp;
            $response['email_sent'] = $emailSent;
        }

        return response()->json($response);
    }

    /**
     * Verify email OTP
     * POST /api/otp/verify-email
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;
        $otp = $request->otp;

        $storedOtp = Cache::get('otp_email_' . $email);

        if (!$storedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired. Please request a new OTP.'
            ], 400);
        }

        if ($storedOtp != $otp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP invalid or expired'
            ], 400);
        }

        // Mark as verified in cache (valid for 30 minutes)
        Cache::put('otp_email_verified_' . $email, true, now()->addMinutes(30));

        // Clear the OTP
        Cache::forget('otp_email_' . $email);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified'
        ]);
    }
}


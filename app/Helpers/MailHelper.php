<?php

namespace App\Helpers;

use App\Models\EmailConfiguration;
use Illuminate\Support\Facades\Mail;


class MailHelper
{

    public static function setMailConfig(){

        $email_setting=EmailConfiguration::first();

        $mailConfig = [
            'transport' => 'smtp',
            'host' => $email_setting->mail_host,
            'port' => $email_setting->mail_port,
            'encryption' => $email_setting->mail_encryption,
            'username' => $email_setting->smtp_username,
            'password' =>$email_setting->smtp_password,
            'timeout' => null
        ];

        config(['mail.mailers.smtp' => $mailConfig]);
        config(['mail.from.address' => $email_setting->email]);
    }

    // public static function sendOtp($email, $otp)
    // {
    //     try {
    //         Mail::raw("Your OTP code is: {$otp}", function ($message) use ($email) {
    //             $message->to($email)
    //                     ->subject('Your OTP Code');
    //         });

    //         return [
    //             'success' => true,
    //             'raw' => 'Email sent successfully'
    //         ];
    //     } catch (\Exception $e) {
    //         return [
    //             'success' => false,
    //             'raw' => $e->getMessage()
    //         ];
    //     }
    // }

    public static function sendOtp($email, $otp)
{
    try {
        // apply dynamic mail settings first
        self::setMailConfig();

        Mail::raw("Your OTP code is: {$otp}", function ($message) use ($email) {
            $message->to($email)
                    ->subject('Your OTP Code');
        });

        return [
            'success' => true,
            'raw' => 'Email sent successfully'
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'raw' => $e->getMessage()
        ];
    }
}
}

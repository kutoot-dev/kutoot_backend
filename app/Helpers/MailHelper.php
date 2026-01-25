<?php

namespace App\Helpers;

use App\Models\EmailConfiguration;
use Illuminate\Support\Facades\Mail;


class MailHelper
{

    /**
     * Set mail config from database (EmailConfiguration model)
     * Falls back to .env config if no database record exists
     */
    public static function setMailConfig(){

        $email_setting = EmailConfiguration::first();

        // Fall back to .env config if no database record exists
        if (!$email_setting) {
            self::setEnvMailConfig();
            return;
        }

        $mailConfig = [
            'transport' => 'smtp',
            'host' => $email_setting->mail_host,
            'port' => $email_setting->mail_port,
            'encryption' => $email_setting->mail_encryption,
            'username' => $email_setting->smtp_username,
            'password' => $email_setting->smtp_password,
            'timeout' => null
        ];

        config(['mail.mailers.smtp' => $mailConfig]);
        config(['mail.from.address' => $email_setting->email]);

        // Clear the mailer instance to pick up new config
        Mail::purge('smtp');
    }

    /**
     * Set mail config from .env file (for SendGrid or other SMTP)
     * Uses MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION, MAIL_FROM_ADDRESS from .env
     */
    public static function setEnvMailConfig(){
        $mailConfig = [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.sendgrid.net'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME', 'apikey'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null
        ];

        config(['mail.mailers.smtp' => $mailConfig]);
        config(['mail.from.address' => env('MAIL_FROM_ADDRESS', 'noreply@kutoot.com')]);
        config(['mail.from.name' => env('MAIL_FROM_NAME', 'Kutoot')]);

        // Clear the mailer instance to pick up new config
        Mail::purge('smtp');
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

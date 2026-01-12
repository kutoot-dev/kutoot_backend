<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Setting;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;
    public $otp;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($otp)
    {
              $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $setting = Setting::select('logo')->first();
        $logo = $setting ? $setting->logo : null;

        return $this->subject('Your OTP Code')
                    ->view('emails.otp')
                    ->with([
                        'otp' => $this->otp,
                        'logo' => $logo,
                        'name' => null,
                        'expiry' => 10,
                        'supportEmail' => 'support@kutoot.com'
                    ]);
    }
}

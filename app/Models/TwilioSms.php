<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwilioSms extends Model
{
    use HasFactory;

    protected $table = 'twilio_sms';

    protected $fillable = [
        'account_sid',
        'auth_token',
        'twilio_phone_number',
        'enable_register_sms',
        'enable_reset_pass_sms',
        'enable_order_confirmation_sms',
    ];
}

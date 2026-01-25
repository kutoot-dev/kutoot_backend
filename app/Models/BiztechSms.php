<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiztechSms extends Model
{
    use HasFactory;

    protected $table = 'biztech_sms';

    protected $fillable = [
        'api_key',
        'client_id',
        'sender_id',
        'enable_register_sms',
        'enable_reset_pass_sms',
        'enable_order_confirmation_sms',
    ];
}

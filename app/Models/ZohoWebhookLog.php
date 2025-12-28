<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZohoWebhookLog extends Model
{
    protected $fillable = ['event','payload'];
    protected $casts = ['payload' => 'array'];
}
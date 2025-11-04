<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CouponTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'ticket_code',
        'ticket_hash',
        'user_id',
        'issued_at',
    ];

    protected $dates = ['issued_at'];

    // Relationships
    public function campaign()
    {
        return $this->belongsTo(CouponCampaign::class, 'campaign_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

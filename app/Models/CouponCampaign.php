<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CouponCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'series_prefix',
        'number_min',
        'number_max',
        'numbers_per_ticket',
        'max_combinations',
        'tickets_issued',
        'goal_target',
        'is_active',
        'draw_triggered',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'draw_triggered' => 'boolean',
    ];

    // Relationships
    public function tickets()
    {
        return $this->hasMany(CouponTicket::class, 'campaign_id');
    }
}


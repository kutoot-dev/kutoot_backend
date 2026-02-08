<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Baseplans extends Model
{
    use HasFactory;

    protected $table = 'coinbase_plans';

    protected $fillable = [
            'camp_id',
            'title',
            'description',
            'ticket_price',
            'img',
            'total_tickets',
            'coins_per_campaign',
            'coupons_per_campaign',
            'duration',
            'point1',
            'point2',
            'point3',
            'point4',
            'point5',
            'referral_form_url',
            'task_form_url',
            'status',
    ];

    public function getImgAttribute($value)
    {
        return $value ? asset($value) : null;
    }

    public function planslinked(){

        return $this->belongsTo(BaseplanCampaignLinked::class,'baseplan_id','id');

    }

public function campaigns()
{
    return $this->belongsToMany(
        CoinCampaigns::class,
        'baseplan_campaign_linked',
        'baseplan_id',
        'campaign_id'
    );
}


}


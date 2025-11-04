<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseplanCampaignLinked extends Model
{
    use HasFactory;

    protected $table = 'baseplan_campaign_linked';
    
    protected $fillable = [
            'baseplan_id',
            'campaign_id'
    ];

    public function getImgAttribute($value)
    {
        return $value ? asset($value) : null;
    }

}


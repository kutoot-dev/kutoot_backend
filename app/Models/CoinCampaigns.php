<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CoinCampaigns extends Model
{
    use HasFactory;

    protected $table = 'coin_campaigns';
    protected $casts = [
        // 'tags' => 'json',
        'highlights' => 'array',
    ];

    protected $fillable = [
        'title',
        'title1',
        'title2',
        'campaign_id',
        'description',
        'ticket_price',
        'img',
        'total_tickets',
        'sold_tickets',
        'coins_per_campaign',
        'coupons_per_campaign',
        'max_coins_per_transaction',
        'tags',
        'start_date',
        'end_date',
        'status',
        'video',
        'category',
        'promotion',
        'created_at',
        'updated_at',
        'marketing_start_percent',
        'series_prefix',
        'number_min',
        'number_max',
        'numbers_per_ticket',
        'tag1',
        'tag2',
        'image1',
        'image2',
        'short_description',
        'winner_announcement_date',
        'highlights',
        'max_length',
        'sponsored_by',
        'details_content',
    ];


    // public function scopeRunning($query)
    // {
    //     return $query->where(function ($q) {
    //         $q->whereColumn('total_tickets', '>', 'sold_tickets')
    //           ->where('start_date', '<=', now())
    //         //   ->where('status', 1)
    //           ->whereNull('end_date')
    //           ->orWhere(function ($q) {
    //               $q->whereColumn('total_tickets', '>', 'sold_tickets')
    //                 ->where('end_date', '>', now());
    //           })
    //           ->orderBy('start_date', 'desc');
    //     });
    // }

    public function marketingManifest()
    {
        $totalGoal = $this->total_tickets;
        $currentBuys = $this->sold_tickets;

        if ($totalGoal <= 0) {
            return [
                'progress' => 0,
                'message' => 'Invalid goal quantity',
                'display_percentage' => 0,
            ];
        }

        $progress = ($currentBuys / $totalGoal) * 100;
        

        if($progress < 11){
            $display_percentage=$this->marketing_start_percent;
        }else{
            $display_percentage=$progress;
        }


        if ($display_percentage < 20) {

            return [
                'progress' => $progress,
                'message' => 'Be the first to grab this exclusive offer!',
                'display_percentage' => $display_percentage*2.1, // Slight fake boost for early stage
            ];

        }elseif ($display_percentage > 40) {

            return [
                'progress' => $progress,
                'message' => 'Be the first to grab this exclusive offer!',
                'display_percentage' => $display_percentage, // Slight fake boost for early stage
            ];
        }elseif ($progress < 11) {

            return [
                'progress' => $progress,
                'message' => 'Be the first to grab this exclusive offer!',
                'display_percentage' => $progress*2.7, // Slight fake boost for early stage
            ];
        }elseif ($progress < 17) {
            return [
                'progress' => $progress,
                'message' => 'Be the first to grab this exclusive offer!',
                'display_percentage' => $progress*2.8, // Slight fake boost for early stage
            ];
        }elseif ($progress < 25) {
            return [
                'progress' => $progress,
                'message' => 'Be the first to grab this exclusive offer!',
                'display_percentage' => $progress*1.6, // Slight fake boost for early stage
            ];
        } elseif ($progress >= 25 && $progress < 60) {
            return [
                'progress' => $progress,
                'message' => 'Selling fast! Don\'t miss out!',
                'display_percentage' => round($progress*1.3),
            ];
        }elseif ($progress >= 60 && $progress < 80) {
            return [
                'progress' => $progress,
                'message' => 'Selling fast! Don\'t miss out!',
                'display_percentage' => round($progress),
            ];
        }  elseif ($progress >= 80 && $progress < 90) {
            return [
                'progress' => $progress,
                'message' => 'Almost gone! Limited quantity left!',
                'display_percentage' => round($progress),
            ];
        } elseif ($progress >= 90 && $progress <= 100) {
            return [
                'progress' => $progress,
                'message' => 'Hurry up! Closing soon!',
                'display_percentage' => round($progress),
            ];
        } else {
            return [
                'progress' => 100,
                'message' => 'Campaign completed!',
                'display_percentage' => 100,
            ];
        }
    }


    public function scopeRunning($query)
{
    return $query->where(function ($q) {
        $q->whereColumn('total_tickets', '>', 'sold_tickets')
          ->where('start_date', '<=', now())
          ->whereNull('end_date');
    })
    ->orWhere(function ($q) {
        $q->whereColumn('total_tickets', '>', 'sold_tickets')
          ->where('start_date', '<=', now())
          ->where('end_date', '>', now());
    })
    ->orderBy('start_date', 'desc');
}


    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now())
          ->orderBy('start_date', 'desc');
        //   ->where('status', 1);
    }



    public function scopeCompleted($query)
    {
        return $query->where(function ($q) {
            $q->whereColumn('total_tickets', '=', 'sold_tickets')
            //    ->where('status', 1)
              ->orWhere(function ($q) {
                  $q->whereNotNull('end_date')
                    ->where('end_date', '<=', now());
              })
              ->orderBy('start_date', 'desc');
        });
    }

    public function getImgAttribute($value)
    {
        return $value ? asset($value) : null;
    }

public function baseplans()
{
    return $this->belongsToMany(
        Baseplans::class,
        'baseplan_campaign_linked',
        'campaign_id',
        'baseplan_id'
    )->where('coinbase_plans.status', 1);
}




    
}

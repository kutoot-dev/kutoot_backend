<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerWithdraw extends Model
{
    use HasFactory;

    public function seller(){
        return $this->belongsTo(Vendor::class,'seller_id');
    }
}

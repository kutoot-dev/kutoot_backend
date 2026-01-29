<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaystackAndMollie extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function currency(){
        return $this->belongsTo(\Nnjeim\World\Models\Currency::class, 'currency_id');
    }
}

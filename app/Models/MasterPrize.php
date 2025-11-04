<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPrize extends Model
{
    use HasFactory;

    protected $table = 'table_prizecampains';
   
    protected $fillable = [
        'title',
        'description',
        'status',
        'img',
        'created_at',
        'updated_at',
    ];


    public function getImgAttribute($value)
    {
        return $value ? asset($value) : null;
    }
}

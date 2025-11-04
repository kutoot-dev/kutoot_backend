<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopularPost extends Model
{
    use HasFactory;

    public function blog(){
        return $this->belongsTo(Blog::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageItem extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'image_type_id', 'image_path'];

    public function type()
{
    return $this->belongsTo(ImageType::class, 'image_type_id');
}
}

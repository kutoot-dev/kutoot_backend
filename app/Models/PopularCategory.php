<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopularCategory extends Model
{
    use HasFactory;

    public function firstCategory()
    {
        return $this->belongsTo(Category::class, 'first_category_id');
    }

    public function secondCategory()
    {
        return $this->belongsTo(Category::class, 'second_category_id');
    }

    public function thirdCategory()
    {
        return $this->belongsTo(Category::class, 'third_category_id');
    }

    /**
     * @deprecated Use firstCategory(), secondCategory(), or thirdCategory() instead
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'first_category_id');
    }
}

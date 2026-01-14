<?php

namespace App\Models;

use App\Enums\BrandApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $casts = [
        'approval_status' => BrandApprovalStatus::class,
    ];

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function seller(){
        return $this->belongsTo(Vendor::class, 'seller_id');
    }

    public function scopeSellerBrands($query){
        return $query->whereNotNull('seller_id');
    }

    public function scopeAdminBrands($query){
        return $query->whereNull('seller_id');
    }

    public function scopeApproved($query){
        return $query->where('approval_status', BrandApprovalStatus::APPROVED->value);
    }

    public function scopePending($query){
        return $query->where('approval_status', BrandApprovalStatus::PENDING->value);
    }

    public function scopeRejected($query){
        return $query->where('approval_status', BrandApprovalStatus::REJECTED->value);
    }
}

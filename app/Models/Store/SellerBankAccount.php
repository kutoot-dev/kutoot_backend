<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class SellerBankAccount extends Model
{
    protected $table = 'seller_bank_accounts';

    protected $fillable = [
        'seller_id',
        'bank_name',
        'account_number',
        'ifsc',
        'upi_id',
        'beneficiary_name',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
}



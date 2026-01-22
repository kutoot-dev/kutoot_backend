<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinLedger extends Model
{
    use HasFactory;

    protected $table = 'coin_ledger';

    protected $fillable = [
        'user_id',
        'entry_type',
        'coins_in',
        'coins_out',
        'coin_category',
        'expiry_date',
        'reference_id',
        'metadata',
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
        'metadata' => 'array',
    ];

    // Entry Types
    const TYPE_PAID_CREDIT = 'PAID_COIN_CREDIT';
    const TYPE_REWARD_CREDIT = 'REWARD_COIN_CREDIT';
    const TYPE_REDEEM = 'COIN_REDEEM';
    const TYPE_EXPIRE = 'COIN_EXPIRE';
    const TYPE_REVERSAL = 'COIN_REVERSAL';

    // Categories
    const CAT_PAID = 'PAID';
    const CAT_REWARD = 'REWARD';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for available (unexpired) credits.
     */
    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expiry_date')
                ->orWhere('expiry_date', '>=', now());
        });
    }
}

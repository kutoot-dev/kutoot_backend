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

    // Zoho Account Mappings
    const ZOHO_MAPPINGS = [
        // Category + Entry Type => Zoho Account
        'PAID|PAID_COIN_CREDIT' => 'Coin Liability',
        'REWARD|REWARD_COIN_CREDIT' => 'Marketing Liability',
        'PAID|COIN_REDEEM' => 'Discount Expense',
        'REWARD|COIN_REDEEM' => 'Marketing Expense',
        'PAID|COIN_EXPIRE' => 'Liability Write-off',
        'REWARD|COIN_EXPIRE' => 'Liability Write-off',
        'PAID|COIN_REVERSAL' => 'Reversal Adjustment',
        'REWARD|COIN_REVERSAL' => 'Reversal Adjustment',
    ];

    /**
     * Get the Zoho account type for this entry.
     */
    public function getZohoAccountAttribute(): string
    {
        $key = $this->coin_category . '|' . $this->entry_type;
        return self::ZOHO_MAPPINGS[$key] ?? 'Unknown';
    }

    /**
     * Helper to get all valid entry types.
     */
    public static function getEntryTypes(): array
    {
        return [
            self::TYPE_PAID_CREDIT,
            self::TYPE_REWARD_CREDIT,
            self::TYPE_REDEEM,
            self::TYPE_EXPIRE,
            self::TYPE_REVERSAL,
        ];
    }

    /**
     * Helper to get all valid categories.
     */
    public static function getCategories(): array
    {
        return [
            self::CAT_PAID,
            self::CAT_REWARD,
        ];
    }

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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultiCurrency extends Model
{
    use HasFactory;

    protected $table = 'multi_currencies';

    protected $fillable = [
        'currency_name',
        'country_code',
        'currency_code',
        'currency_icon',
        'currency_rate',
        'is_default',
        'currency_position',
        'status',
    ];

    protected $casts = [
        'currency_rate' => 'decimal:4',
        'status' => 'integer',
    ];

    /**
     * Scope for active currencies
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for default currency
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', 'Yes');
    }

    /**
     * Get the default currency
     */
    public static function getDefault()
    {
        return static::where('is_default', 'Yes')->first();
    }
}

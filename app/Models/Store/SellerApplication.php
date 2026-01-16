<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class SellerApplication extends Model
{
    // Status constants
    const STATUS_PENDING = 'PENDING';
    const STATUS_VERIFIED = 'VERIFIED';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';

    protected $table = 'seller_applications';

    protected $fillable = [
        'application_id',
        'store_name',
        'owner_mobile',
        'store_type',
        'store_address',
        'lat',
        'lng',
        'min_bill_amount',
        'status',
        'verified_by',
        'verification_notes',
        'verified_at',
        'approved_by',
        'seller_email',
        'approved_at',
        'rejected_by',
        'rejection_reason',
        'rejected_at',
        'seller_id',
    ];

    protected $casts = [
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'min_bill_amount' => 'decimal:2',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Generate a unique application ID
     */
    public static function generateApplicationId(): string
    {
        do {
            $id = 'KT-' . str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('application_id', $id)->exists());

        return $id;
    }

    /**
     * Check if application is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if application is verified
     */
    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    /**
     * Check if application is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if application is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Relationship to verifier admin
     */
    public function verifier()
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }

    /**
     * Relationship to approver admin
     */
    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    /**
     * Relationship to rejecter admin
     */
    public function rejecter()
    {
        return $this->belongsTo(Admin::class, 'rejected_by');
    }

    /**
     * Relationship to created seller
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', strtoupper($status));
        }
        return $query;
    }
}


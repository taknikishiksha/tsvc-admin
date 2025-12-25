<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'transactionable_type', 'transactionable_id', 'service_id',
        'transaction_type', 'points', 'cash_value', 'currency', 'loyalty_tier',
        'multiplier', 'reference_type', 'reference_id', 'description',
        'validity_days', 'expires_at', 'is_expired', 'status', 'used_at',
        'redemption_id', 'redemption_type', 'redemption_details'
    ];

    protected $casts = [
        'points' => 'integer',
        'cash_value' => 'decimal:2',
        'multiplier' => 'decimal:2',
        'is_expired' => 'boolean',
        'expires_at' => 'date',
        'used_at' => 'datetime',
        'redemption_details' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function redemption()
    {
        return $this->belongsTo(LoyaltyTransaction::class, 'redemption_id');
    }

    public function redemptions()
    {
        return $this->hasMany(LoyaltyTransaction::class, 'redemption_id');
    }

    // Scopes
    public function scopeEarned($query)
    {
        return $query->where('transaction_type', 'points_earned');
    }

    public function scopeRedeemed($query)
    {
        return $query->where('transaction_type', 'points_redeemed');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    })
                    ->where('is_expired', false);
    }

    public function scopeExpired($query)
    {
        return $query->where('is_expired', true)
                    ->orWhere(function($q) {
                        $q->whereNotNull('expires_at')
                          ->where('expires_at', '<=', now());
                    });
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function isExpired()
    {
        return $this->is_expired || ($this->expires_at && $this->expires_at->isPast());
    }

    public function markAsUsed($redemptionType = null, $details = null)
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
            'redemption_type' => $redemptionType,
            'redemption_details' => $details,
        ]);
    }

    public function expirePoints()
    {
        if (!$this->isExpired()) {
            $this->update([
                'is_expired' => true,
                'status' => 'expired',
            ]);
        }
    }

    public function getCashEquivalent()
    {
        return $this->cash_value ?? ($this->points * 0.5); // 1 point = ₹0.5
    }

    public static function awardPoints($user, $points, $type, $description, $transactionable = null, $service = null)
    {
        return self::create([
            'user_id' => $user->id,
            'transactionable_type' => $transactionable ? get_class($transactionable) : null,
            'transactionable_id' => $transactionable ? $transactionable->id : null,
            'service_id' => $service?->id,
            'transaction_type' => 'points_earned',
            'points' => $points,
            'reference_type' => $type,
            'description' => $description,
            'expires_at' => now()->addDays(365),
            'loyalty_tier' => $user->client?->membership_tier ?? 'basic',
        ]);
    }
}
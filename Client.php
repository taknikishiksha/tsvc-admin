<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'health_issues', 'medical_history', 'yoga_goals', 
        'experience_level', 'preferences', 'service_type', 'address',
        'city', 'state', 'pincode', 'latitude', 'longitude',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_relation',
        'loyalty_points', 'wallet_balance', 'total_sessions_taken',
        'referral_count', 'membership_tier', 'membership_expiry'
    ];

    protected $casts = [
        'yoga_goals' => 'array',
        'preferences' => 'array',
        'wallet_balance' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'membership_expiry' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function loyaltyTransactions()
    {
        return $this->morphMany(LoyaltyTransaction::class, 'transactionable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereHas('services', function ($q) {
            $q->whereIn('status', ['confirmed', 'in_progress']);
        });
    }

    public function scopeWithMembership($query, $tier = null)
    {
        if ($tier) {
            return $query->where('membership_tier', $tier);
        }
        return $query->whereNotNull('membership_tier');
    }

    // Methods
    public function getActiveServices()
    {
        return $this->services()->whereIn('status', ['confirmed', 'in_progress'])->get();
    }

    public function getTotalSpent()
    {
        return $this->payments()->where('status', 'captured')->sum('amount');
    }

    public function addLoyaltyPoints($points, $description)
    {
        $this->increment('loyalty_points', $points);
        
        $this->loyaltyTransactions()->create([
            'user_id' => $this->user_id,
            'transaction_type' => 'points_earned',
            'points' => $points,
            'description' => $description,
            'expires_at' => now()->addDays(365),
        ]);
    }

    public function isMembershipActive()
    {
        return $this->membership_expiry && $this->membership_expiry->isFuture();
    }
}
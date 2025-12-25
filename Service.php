<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id', 'teacher_id', 'service_type', 'package_type', 'total_sessions',
        'sessions_completed', 'sessions_remaining', 'start_date', 'end_date',
        'scheduled_days', 'preferred_time', 'session_duration', 'service_address',
        'landmark', 'location_type', 'package_amount', 'per_session_rate',
        'platform_fee', 'tds_amount', 'final_amount', 'status', 'payment_status',
        'has_emergency_substitute', 'allows_rescheduling', 'reschedule_count',
        'special_instructions', 'client_rating', 'client_feedback', 'teacher_rating',
        'teacher_feedback', 'auto_renew', 'next_billing_date'
    ];

    protected $casts = [
        'scheduled_days' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'preferred_time' => 'datetime:H:i',
        'package_amount' => 'decimal:2',
        'per_session_rate' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'tds_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'has_emergency_substitute' => 'boolean',
        'allows_rescheduling' => 'boolean',
        'auto_renew' => 'boolean',
        'next_billing_date' => 'date',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function teacher()
    {
        return $this->belongsTo(YogaTeacher::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function loyaltyTransactions()
    {
        return $this->morphMany(LoyaltyTransaction::class, 'transactionable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['confirmed', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    // Methods
    public function markAttendance($sessionNumber, $data = [])
    {
        return $this->attendances()->create(array_merge([
            'session_number' => $sessionNumber,
            'session_date' => now(),
            'scheduled_time' => $this->preferred_time,
            'client_id' => $this->client_id,
            'teacher_id' => $this->teacher_id,
        ], $data));
    }

    public function calculateProgress()
    {
        if ($this->total_sessions == 0) return 0;
        return ($this->sessions_completed / $this->total_sessions) * 100;
    }

    public function canBeRescheduled()
    {
        return $this->allows_rescheduling && $this->reschedule_count < 3;
    }

    public function getUpcomingSession()
    {
        return $this->attendances()
            ->where('session_date', '>=', now())
            ->where('overall_status', '!=', 'completed')
            ->orderBy('session_date')
            ->first();
    }

    public function isActive()
    {
        return in_array($this->status, ['confirmed', 'in_progress']);
    }
}
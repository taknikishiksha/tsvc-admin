<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id', 'client_id', 'teacher_id', 'session_number', 'session_date',
        'scheduled_time', 'actual_start_time', 'actual_end_time', 'duration_minutes',
        'session_location', 'teacher_latitude', 'teacher_longitude', 'client_latitude',
        'client_longitude', 'distance_km', 'teacher_status', 'client_status',
        'overall_status', 'teacher_marked', 'teacher_marked_at', 'client_confirmed',
        'client_confirmed_at', 'auto_verified', 'teacher_notes', 'client_notes',
        'asanas_practiced', 'focus_area', 'client_energy_level', 'session_quality_rating',
        'was_rescheduled', 'original_attendance_id', 'cancellation_reason',
        'cancelled_by', 'payment_processed', 'payment_id'
    ];

    protected $casts = [
        'session_date' => 'date',
        'scheduled_time' => 'datetime:H:i',
        'actual_start_time' => 'datetime:H:i',
        'actual_end_time' => 'datetime:H:i',
        'teacher_latitude' => 'decimal:8',
        'teacher_longitude' => 'decimal:8',
        'client_latitude' => 'decimal:8',
        'client_longitude' => 'decimal:8',
        'distance_km' => 'decimal:2',
        'teacher_marked' => 'boolean',
        'teacher_marked_at' => 'datetime',
        'client_confirmed' => 'boolean',
        'client_confirmed_at' => 'datetime',
        'auto_verified' => 'boolean',
        'asanas_practiced' => 'array',
        'was_rescheduled' => 'boolean',
        'payment_processed' => 'boolean',
    ];

    // Relationships
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function teacher()
    {
        return $this->belongsTo(YogaTeacher::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function originalAttendance()
    {
        return $this->belongsTo(Attendance::class, 'original_attendance_id');
    }

    public function rescheduledAttendance()
    {
        return $this->hasOne(Attendance::class, 'original_attendance_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('overall_status', 'completed');
    }

    public function scopePendingConfirmation($query)
    {
        return $query->where('teacher_marked', true)
                    ->where('client_confirmed', false)
                    ->where('auto_verified', false);
    }

    public function scopeToday($query)
    {
        return $query->where('session_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('session_date', '>=', today())
                    ->where('overall_status', '!=', 'completed')
                    ->where('overall_status', '!=', 'cancelled');
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    // Methods
    public function markTeacherAttendance($notes = null, $locationData = null)
    {
        $this->update([
            'teacher_status' => 'present',
            'teacher_marked' => true,
            'teacher_marked_at' => now(),
            'actual_start_time' => $this->actual_start_time ?? now()->format('H:i:s'),
            'teacher_notes' => $notes,
            'teacher_latitude' => $locationData['latitude'] ?? null,
            'teacher_longitude' => $locationData['longitude'] ?? null,
        ]);

        $this->updateOverallStatus();
    }

    public function confirmByClient()
    {
        $this->update([
            'client_status' => 'present',
            'client_confirmed' => true,
            'client_confirmed_at' => now(),
            'client_latitude' => $this->client_latitude ?? null,
            'client_longitude' => $this->client_longitude ?? null,
        ]);

        $this->updateOverallStatus();
        
        // Auto-verify if both marked and 1 hour passed
        if ($this->teacher_marked && $this->client_confirmed) {
            $this->autoVerify();
        }
    }

    public function updateOverallStatus()
    {
        if ($this->teacher_status === 'present' && $this->client_status === 'present') {
            $this->update(['overall_status' => 'completed']);
        } elseif ($this->teacher_status === 'absent') {
            $this->update(['overall_status' => 'teacher_absent']);
        } elseif ($this->client_status === 'absent') {
            $this->update(['overall_status' => 'client_absent']);
        }
    }

    public function autoVerify()
    {
        if (!$this->auto_verified && $this->teacher_marked && !$this->client_confirmed) {
            // Auto confirm after 24 hours if teacher marked but client didn't respond
            if ($this->teacher_marked_at->diffInHours(now()) >= 24) {
                $this->update([
                    'client_confirmed' => true,
                    'client_confirmed_at' => now(),
                    'auto_verified' => true,
                    'overall_status' => 'completed'
                ]);
            }
        }
    }

    public function calculateDistance()
    {
        if ($this->teacher_latitude && $this->teacher_longitude && 
            $this->client_latitude && $this->client_longitude) {
            
            $earthRadius = 6371; // km

            $latDelta = deg2rad($this->client_latitude - $this->teacher_latitude);
            $lonDelta = deg2rad($this->client_longitude - $this->teacher_longitude);

            $a = sin($latDelta/2) * sin($latDelta/2) +
                 cos(deg2rad($this->teacher_latitude)) * cos(deg2rad($this->client_latitude)) *
                 sin($lonDelta/2) * sin($lonDelta/2);
            
            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            
            $this->distance_km = round($earthRadius * $c, 2);
            $this->save();
        }
    }

    public function isCompleted()
    {
        return $this->overall_status === 'completed';
    }

    public function needsClientConfirmation()
    {
        return $this->teacher_marked && !$this->client_confirmed && !$this->auto_verified;
    }
}
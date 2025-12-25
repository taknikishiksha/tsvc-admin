<?php
// app/Models/TeacherCalendar.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TeacherCalendar extends Model
{
    protected $fillable = [
        'teacher_id',
        'booking_id',
        'event_date',
        'event_time',
        'event_type',
        'title',
        'description',
        'status',
        'client_id',
        'session_type',
        'location',
        'duration',
        'color'
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i',
        'duration' => 'integer' // in minutes
    ];

    /**
     * Event type constants
     */
    const TYPE_CLASS = 'class';
    const TYPE_AVAILABILITY = 'availability';
    const TYPE_BREAK = 'break';
    const TYPE_HOLIDAY = 'holiday';

    /**
     * Status constants
     */
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_RESCHEDULED = 'rescheduled';

    /**
     * Get the teacher that owns the calendar event
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the associated booking
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Get the client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Scope for upcoming events
     */
    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('event_date', '>=', now()->toDateString())
                    ->where('event_date', '<=', now()->addDays($days)->toDateString())
                    ->orderBy('event_date')
                    ->orderBy('event_time');
    }

    /**
     * Scope for specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('event_date', $date)
                    ->orderBy('event_time');
    }

    /**
     * Scope for active classes
     */
    public function scopeActiveClasses($query)
    {
        return $query->where('event_type', self::TYPE_CLASS)
                    ->whereIn('status', [self::STATUS_SCHEDULED, self::STATUS_CONFIRMED]);
    }

    /**
     * Get event datetime
     */
    public function getEventDateTimeAttribute()
    {
        return $this->event_date->setTimeFrom($this->event_time);
    }

    /**
     * Get end datetime
     */
    public function getEndDateTimeAttribute()
    {
        return $this->event_date_time->addMinutes($this->duration);
    }

    /**
     * Check if event is upcoming
     */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->event_date_time->isFuture();
    }

    /**
     * Check if event can be rescheduled
     */
    public function getCanRescheduleAttribute(): bool
    {
        return $this->event_type === self::TYPE_CLASS && 
               $this->status === self::STATUS_CONFIRMED &&
               $this->event_date_time->diffInHours(now()) > 24;
    }

    /**
     * Get event color based on type and status
     */
    public function getEventColorAttribute(): string
    {
        if ($this->color) {
            return $this->color;
        }

        if ($this->event_type === self::TYPE_CLASS) {
            return match($this->status) {
                self::STATUS_CONFIRMED => '#10B981', // green
                self::STATUS_SCHEDULED => '#3B82F6', // blue
                self::STATUS_COMPLETED => '#6B7280', // gray
                self::STATUS_CANCELLED => '#EF4444', // red
                self::STATUS_RESCHEDULED => '#F59E0B', // yellow
                default => '#8B5CF6' // purple
            };
        }

        return match($this->event_type) {
            self::TYPE_AVAILABILITY => '#D1FAE5', // light green
            self::TYPE_BREAK => '#FEE2E2', // light red
            self::TYPE_HOLIDAY => '#FEF3C7', // light yellow
            default => '#E5E7EB' // light gray
        };
    }

    /**
     * Get FullCalendar event format
     */
    public function toFullCalendarEvent(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->event_date_time->toIso8601String(),
            'end' => $this->end_date_time->toIso8601String(),
            'color' => $this->event_color,
            'extendedProps' => [
                'type' => $this->event_type,
                'status' => $this->status,
                'client_name' => $this->client->name ?? null,
                'session_type' => $this->session_type,
                'location' => $this->location,
                'can_reschedule' => $this->can_reschedule,
                'description' => $this->description
            ]
        ];
    }

    /**
     * Check for scheduling conflicts
     */
    public static function hasConflict($teacherId, $start, $end, $excludeId = null): bool
    {
        $query = self::where('teacher_id', $teacherId)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('event_date_time', [$start, $end])
                  ->orWhereBetween('end_date_time', [$start, $end])
                  ->orWhere(function ($q) use ($start, $end) {
                      $q->where('event_date_time', '<', $start)
                        ->where('end_date_time', '>', $end);
                  });
            })
            ->whereIn('status', [self::STATUS_SCHEDULED, self::STATUS_CONFIRMED]);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
?>
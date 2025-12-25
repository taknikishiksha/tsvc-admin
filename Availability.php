<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id', 'day_of_week', 'start_time', 'end_time', 'slot_duration',
        'service_types', 'locations', 'is_recurring', 'specific_date',
        'is_available', 'max_bookings_per_slot', 'current_bookings',
        'buffer_minutes', 'priority_level', 'dynamic_pricing'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'service_types' => 'array',
        'locations' => 'array',
        'is_recurring' => 'boolean',
        'is_available' => 'boolean',
        'specific_date' => 'date',
        'dynamic_pricing' => 'decimal:2',
    ];

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(YogaTeacher::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeForDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeOneTime($query)
    {
        return $query->where('is_recurring', false);
    }

    public function scopeWithCapacity($query)
    {
        return $query->whereRaw('current_bookings < max_bookings_per_slot');
    }

    public function scopeForServiceType($query, $serviceType)
    {
        return $query->whereJsonContains('service_types', $serviceType);
    }

    // Methods
    public function isAvailable()
    {
        return $this->is_available && $this->current_bookings < $this->max_bookings_per_slot;
    }

    public function incrementBookings()
    {
        if ($this->current_bookings < $this->max_bookings_per_slot) {
            $this->increment('current_bookings');
            return true;
        }
        return false;
    }

    public function decrementBookings()
    {
        if ($this->current_bookings > 0) {
            $this->decrement('current_bookings');
            return true;
        }
        return false;
    }

    public function getTimeSlots()
    {
        $slots = [];
        $current = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);

        while ($current->addMinutes($this->slot_duration + $this->buffer_minutes) <= $end) {
            $slotEnd = $current->copy()->addMinutes($this->slot_duration);
            if ($slotEnd <= $end) {
                $slots[] = [
                    'start' => $current->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'available' => $this->isAvailable()
                ];
            }
            $current = $slotEnd;
        }

        return $slots;
    }

    public function coversLocation($location)
    {
        if (empty($this->locations)) {
            return true; // Available for all locations
        }
        return in_array($location, $this->locations);
    }

    public function getEffectivePrice($basePrice)
    {
        return $this->dynamic_pricing ?? $basePrice;
    }

    public function isForDate($date)
    {
        if ($this->is_recurring) {
            return strtolower($date->englishDayOfWeek) === $this->day_of_week;
        } else {
            return $this->specific_date && $this->specific_date->equalTo($date);
        }
    }
}
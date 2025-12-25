<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\TeacherAvailability;
use Carbon\Carbon;

class CalendarService
{
    public function getTeacherEvents($teacherId, $start, $end)
    {
        $bookings = Booking::with('client')
            ->where('teacher_id', $teacherId)
            ->whereBetween('session_date', [$start, $end])
            ->get();

        return $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => "Class with {$booking->client->name}",
                'start' => $booking->session_date->toIso8601String(),
                'end' => $booking->session_date->copy()->addHour()->toIso8601String(),
                'color' => $this->getEventColor($booking->status),
                'extendedProps' => [
                    'client_name' => $booking->client->name,
                    'status' => $booking->status,
                    'type' => $booking->session_type,
                    'location' => $booking->location
                ]
            ];
        });
    }

    private function getEventColor($status): string
    {
        return match($status) {
            'confirmed' => '#10B981', // green
            'pending' => '#F59E0B',   // yellow
            'completed' => '#6B7280', // gray
            'cancelled' => '#EF4444', // red
            default => '#3B82F6'      // blue
        };
    }

    public function rescheduleEvent($eventId, array $newTiming): bool
    {
        $booking = Booking::findOrFail($eventId);
        
        // Check if new timing conflicts with existing bookings
        $conflict = Booking::where('teacher_id', $booking->teacher_id)
            ->where('id', '!=', $eventId)
            ->where('session_date', $newTiming['start'])
            ->exists();

        if ($conflict) {
            return false;
        }

        $booking->update([
            'session_date' => $newTiming['start'],
            'rescheduled_at' => now()
        ]);

        return true;
    }

    public function getDailySchedule($teacherId, $date)
    {
        return Booking::with('client')
            ->where('teacher_id', $teacherId)
            ->whereDate('session_date', $date)
            ->orderBy('session_date')
            ->get()
            ->map(function ($booking) {
                return [
                    'time' => $booking->session_date->format('H:i'),
                    'client' => $booking->client->name,
                    'type' => $booking->session_type,
                    'status' => $booking->status,
                    'duration' => '60 mins'
                ];
            });
    }
}
?>
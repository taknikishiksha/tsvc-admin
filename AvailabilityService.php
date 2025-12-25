<?php

namespace App\Services;

use App\Models\TeacherAvailability;
use Carbon\Carbon;

class AvailabilityService
{
    public function getTeacherAvailability($teacherId)
    {
        return TeacherAvailability::where('teacher_id', $teacherId)->get();
    }

    public function updateAvailability($teacherId, array $slots): void
    {
        TeacherAvailability::where('teacher_id', $teacherId)->delete();

        foreach ($slots as $slot) {
            TeacherAvailability::create([
                'teacher_id' => $teacherId,
                'day_of_week' => $slot['day'],
                'start_time' => $slot['time_slots']['start'],
                'end_time' => $slot['time_slots']['end'],
                'is_available' => $slot['is_available'] ?? true
            ]);
        }
    }

    public function getWeeklyAvailability($teacherId): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $availability = [];

        foreach ($days as $day) {
            $slots = TeacherAvailability::where('teacher_id', $teacherId)
                ->where('day_of_week', $day)
                ->where('is_available', true)
                ->get();

            $availability[$day] = $slots->map(function ($slot) {
                return [
                    'start' => $slot->start_time,
                    'end' => $slot->end_time
                ];
            });
        }

        return $availability;
    }

    public function isTeacherAvailable($teacherId, $date, $time): bool
    {
        $dayOfWeek = strtolower(Carbon::parse($date)->englishDayOfWeek);
        
        return TeacherAvailability::where('teacher_id', $teacherId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->whereTime('start_time', '<=', $time)
            ->whereTime('end_time', '>=', $time)
            ->exists();
    }
}
?>
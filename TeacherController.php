<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Attendance;
use App\Models\Payment;
use App\Models\Availability;
use App\Models\YogaTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeacherController extends Controller
{
    /**
     * Teacher Dashboard - NEW METHOD
     */
    public function dashboard()
    {
        $user = Auth::user();
        $teacher = $user->yogaTeacher;

        if (!$teacher) {
    return redirect()->route('teacher.profile.complete');
}

        // Quick stats for dashboard
        $stats = [
            'active_clients' => $teacher->services()->whereIn('status', ['confirmed', 'in_progress'])->count(),
            'completed_services' => $teacher->services()->where('status', 'completed')->count(),
            'total_earnings' => $teacher->payments()->where('payout_status', 'paid')->sum('net_teacher_share'),
            'pending_payouts' => $teacher->payments()->where('payout_status', 'pending')->sum('net_teacher_share'),
            'avg_rating' => $teacher->rating,
            'total_ratings' => $teacher->total_ratings,
            'upcoming_sessions' => Attendance::where('teacher_id', $teacher->id)
                ->where('session_date', '>=', today())
                ->where('overall_status', 'scheduled')
                ->count(),
        ];

        // Recent activities
        $recentServices = $teacher->services()
            ->with('client.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Upcoming sessions for next 7 days
        $upcomingSessions = Attendance::with(['service.client.user'])
            ->where('teacher_id', $teacher->id)
            ->where('session_date', '>=', today())
            ->where('session_date', '<=', today()->addDays(7))
            ->where('overall_status', 'scheduled')
            ->orderBy('session_date')
            ->orderBy('scheduled_time')
            ->get();

        return view('teacher.dashboard.index', compact('teacher', 'stats', 'recentServices', 'upcomingSessions'));
    }

    /**
     * Dashboard Stats API - NEW METHOD
     */
    public function getDashboardStats()
    {
        $teacher = Auth::user()->yogaTeacher;

        $stats = [
            'active_clients' => $teacher->services()->whereIn('status', ['confirmed', 'in_progress'])->count(),
            'total_earnings' => $teacher->payments()->where('payout_status', 'paid')->sum('net_teacher_share'),
            'pending_payments' => $teacher->payments()->where('payout_status', 'pending')->sum('net_teacher_share'),
            'avg_rating' => $teacher->rating ?? 0,
            'upcoming_classes' => Attendance::where('teacher_id', $teacher->id)
                ->where('session_date', '>=', today())
                ->where('overall_status', 'scheduled')
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Calendar View - NEW METHOD
     */
    public function calendar()
    {
        $teacher = Auth::user()->yogaTeacher;

        // Get sessions for calendar
        $sessions = Attendance::with(['service.client.user'])
            ->where('teacher_id', $teacher->id)
            ->where('session_date', '>=', today()->subDays(30))
            ->where('session_date', '<=', today()->addDays(60))
            ->orderBy('session_date')
            ->orderBy('scheduled_time')
            ->get()
            ->map(function ($session) {
                $startTime = Carbon::parse($session->scheduled_time);
                $endTime = $startTime->copy()->addHour();

                return [
                    'id' => $session->id,
                    'title' => "Class with " . ($session->service->client->user->name ?? 'Client'),
                    'start' => $session->session_date->format('Y-m-d') . 'T' . $startTime->format('H:i:s'),
                    'end' => $session->session_date->format('Y-m-d') . 'T' . $endTime->format('H:i:s'),
                    'color' => $this->getSessionColor($session->overall_status),
                    'extendedProps' => [
                        'client_name' => $session->service->client->user->name ?? 'Client',
                        'status' => $session->overall_status,
                        'type' => $session->service->service_type,
                        'location' => $session->service->location,
                    ]
                ];
            });

        return view('teacher.dashboard.calendar', compact('teacher', 'sessions'));
    }

    /**
     * Get session color for calendar
     */
    private function getSessionColor($status)
    {
        return match ($status) {
            'scheduled' => '#3B82F6',    // blue
            'completed' => '#10B981',    // green
            'cancelled' => '#EF4444',    // red
            'rescheduled' => '#F59E0B',  // yellow
            'teacher_absent' => '#6B7280', // gray
            default => '#8B5CF6'         // purple
        };
    }

    /**
     * Get daily schedule - NEW METHOD
     */
    public function getDailySchedule(Request $request)
    {
        $teacher = Auth::user()->yogaTeacher;
        $date = $request->get('date', today()->toDateString());

        $schedule = Attendance::with(['service.client.user'])
            ->where('teacher_id', $teacher->id)
            ->where('session_date', $date)
            ->orderBy('scheduled_time')
            ->get()
            ->map(function ($session) {
                return [
                    'time' => Carbon::parse($session->scheduled_time)->format('h:i A'),
                    'client' => $session->service->client->user->name ?? 'Client',
                    'type' => $session->service->service_type,
                    'status' => $session->overall_status,
                    'duration' => '60 mins',
                    'session_id' => $session->id
                ];
            });

        return response()->json($schedule);
    }

    // ========================================
    // मौजूदा मेथड्स (नीचे हैं)
    // ========================================

    /**
     * Display teacher profile
     */
    public function profile()
    {
        $user = Auth::user();
        $teacher = $user->yogaTeacher;

        if (!$teacher) {
    return redirect()->route('teacher.profile.complete');
}

        $stats = [
            'active_clients' => $teacher->services()->whereIn('status', ['confirmed', 'in_progress'])->count(),
            'completed_services' => $teacher->services()->where('status', 'completed')->count(),
            'total_earnings' => $teacher->payments()->where('payout_status', 'paid')->sum('net_teacher_share'),
            'avg_rating' => $teacher->rating,
            'total_ratings' => $teacher->total_ratings,
        ];

        return view('teacher.profile.index', compact('teacher', 'stats'));
    }

    /**
     * Update teacher profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->yogaTeacher;

        $validated = $request->validate([
            'bio' => 'required|string|min:100|max:1000',
            'specializations' => 'required|array',
            'specializations.*' => 'string',
            'languages' => 'required|array',
            'languages.*' => 'string',
            'certifications' => 'required|array',
            'certifications.*' => 'string',
            'experience_years' => 'required|integer|min:0|max:50',
            'hourly_rate' => 'required|numeric|min:100|max:5000',
            'service_types' => 'required|array',
            'service_types.*' => 'in:home,online,corporate,group',
            'locations_covered' => 'required|array',
            'locations_covered.*' => 'string',
            'max_clients' => 'required|integer|min:1|max:20',
            'working_days' => 'required|array',
            'working_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'shift_start' => 'required|date_format:H:i',
            'shift_end' => 'required|date_format:H:i|after:shift_start',
        ]);

        $teacher->update($validated);

        // Update user basic info if provided
        if ($request->has('name')) {
            $user->update(['name' => $request->name]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Manage availability
     */
    public function availability()
    {
        $teacher = Auth::user()->yogaTeacher;
        $availabilities = $teacher->availabilities()->orderBy('day_of_week')->orderBy('start_time')->get();

        // Group by day for easier display
        $groupedAvailabilities = $availabilities->groupBy('day_of_week');

        return view('teacher.availability.index', compact('teacher', 'groupedAvailabilities'));
    }

    /**
     * Update availability
     */
    public function updateAvailability(Request $request)
    {
        $teacher = Auth::user()->yogaTeacher;

        $request->validate([
            'availabilities' => 'required|array',
            'availabilities.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'availabilities.*.start_time' => 'required|date_format:H:i',
            'availabilities.*.end_time' => 'required|date_format:H:i|after:availabilities.*.start_time',
            'availabilities.*.service_types' => 'required|array',
            'availabilities.*.service_types.*' => 'in:home,online,corporate,group',
            'availabilities.*.max_bookings_per_slot' => 'required|integer|min:1|max:5',
        ]);

        DB::transaction(function () use ($teacher, $request) {
            // Delete existing availabilities
            $teacher->availabilities()->delete();

            // Create new availabilities
            foreach ($request->availabilities as $availability) {
                Availability::create([
                    'teacher_id' => $teacher->id,
                    'day_of_week' => $availability['day_of_week'],
                    'start_time' => $availability['start_time'],
                    'end_time' => $availability['end_time'],
                    'slot_duration' => 60, // Default 60 minutes
                    'service_types' => $availability['service_types'],
                    'is_recurring' => true,
                    'is_available' => true,
                    'max_bookings_per_slot' => $availability['max_bookings_per_slot'],
                    'current_bookings' => 0,
                    'buffer_minutes' => 30,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Availability updated successfully.');
    }

    /**
     * Teacher's services list
     */
    public function services(Request $request)
    {
        $teacher = Auth::user()->yogaTeacher;

        $query = $teacher->services()->with(['client.user']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(10);

        $serviceStats = [
            'pending' => $teacher->services()->where('status', 'pending')->count(),
            'active' => $teacher->services()->whereIn('status', ['confirmed', 'in_progress'])->count(),
            'completed' => $teacher->services()->where('status', 'completed')->count(),
        ];

        return view('teacher.services.index', compact('services', 'serviceStats'));
    }

    /**
     * Show service details
     */
    public function showService($id)
    {
        $service = Service::with([
            'client.user', 
            'attendances', 
            'payments'
        ])->findOrFail($id);

        // Check if teacher owns this service
        if ($service->teacher_id !== Auth::user()->yogaTeacher->id) {
            abort(403);
        }

        return view('teacher.services.show', compact('service'));
    }

    /**
     * Update service status
     */
    public function updateServiceStatus(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $teacher = Auth::user()->yogaTeacher;

        // Check if teacher owns this service
        if ($service->teacher_id !== $teacher->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:confirmed,rejected,in_progress,completed,on_hold',
            'teacher_notes' => 'nullable|string|max:500',
        ]);

        $service->update([
            'status' => $request->status,
            'teacher_notes' => $request->teacher_notes,
        ]);

        // If service is confirmed, create attendance records
        if ($request->status === 'confirmed') {
            $this->createAttendanceRecords($service);
        }

        // TODO: Send notification to client

        return redirect()->back()->with('success', 'Service status updated successfully.');
    }

    /**
     * Mark attendance for session
     */
    public function markAttendance(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $teacher = Auth::user()->yogaTeacher;

        if ($service->teacher_id !== $teacher->id) {
            abort(403);
        }

        $request->validate([
            'session_number' => 'required|integer|min:1|max:' . $service->total_sessions,
            'teacher_notes' => 'nullable|string|max:500',
            'asanas_practiced' => 'nullable|array',
            'focus_area' => 'nullable|string|max:255',
            'client_energy_level' => 'nullable|integer|min:1|max:5',
            'session_quality_rating' => 'nullable|integer|min:1|max:5',
        ]);

        // Check if attendance already exists for this session
        $existingAttendance = Attendance::where('service_id', $serviceId)
            ->where('session_number', $request->session_number)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()->with('error', 'Attendance already marked for this session.');
        }

        // Create attendance record
        $attendance = Attendance::create([
            'service_id' => $serviceId,
            'client_id' => $service->client_id,
            'teacher_id' => $teacher->id,
            'session_number' => $request->session_number,
            'session_date' => today(),
            'scheduled_time' => $service->preferred_time,
            'actual_start_time' => now()->format('H:i:s'),
            'teacher_status' => 'present',
            'teacher_marked' => true,
            'teacher_marked_at' => now(),
            'teacher_notes' => $request->teacher_notes,
            'asanas_practiced' => $request->asanas_practiced,
            'focus_area' => $request->focus_area,
            'client_energy_level' => $request->client_energy_level,
            'session_quality_rating' => $request->session_quality_rating,
        ]);

        // Update service session counts
        $service->increment('sessions_completed');
        $service->decrement('sessions_remaining');

        // Check if service is completed
        if ($service->sessions_remaining <= 0) {
            $service->update(['status' => 'completed']);
        }

        return redirect()->back()->with('success', 'Attendance marked successfully. Waiting for client confirmation.');
    }

    /**
     * Teacher's earnings and payments
     */
    public function earnings(Request $request)
    {
        $teacher = Auth::user()->yogaTeacher;

        $query = $teacher->payments()->with(['service.client.user']);

        if ($request->has('payout_status') && $request->payout_status) {
            $query->where('payout_status', $request->payout_status);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

$earningsStats = [
    // 1. Total Earnings received by teacher
    'total_earnings' => $teacher->payments()
        ->where('payout_status', 'paid')
        ->sum('net_teacher_share') ?? 0,

    // 2. Pending payout (teacher ko milna baaki)
    'pending_payouts' => $teacher->payments()
        ->where('payout_status', 'pending')
        ->sum('net_teacher_share') ?? 0,

    // Duplicate key (Blade error avoid karne ke liye)
    'pending_payout' => $teacher->payments()
        ->where('payout_status', 'pending')
        ->sum('net_teacher_share') ?? 0,

    // 3. Platform earnings (TSVC earning)
    'platform_earnings' => $teacher->payments()
        ->where('payout_status', 'paid')
        ->sum('platform_fee') ?? 0,

    // 4. This month earning
    'this_month' => $teacher->payments()
        ->where('payout_status', 'paid')
        ->whereMonth('created_at', now()->month)
        ->sum('net_teacher_share') ?? 0,

    // 5. Completed sessions count
    'completed_sessions' => $teacher->services()
        ->where('status', 'completed')
        ->count() ?? 0,
];


        // Monthly earnings for chart
        $monthlyEarnings = $teacher->payments()
            ->where('payout_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(net_teacher_share) as earnings')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('teacher.earnings.index', compact('payments', 'earningsStats', 'monthlyEarnings'));
    }

    /**
     * Request payout
     */
    public function requestPayout(Request $request)
    {
        $teacher = Auth::user()->yogaTeacher;

        $pendingAmount = $teacher->payments()
            ->where('payout_status', 'pending')
            ->sum('net_teacher_share');

        if ($pendingAmount < 500) { // Minimum payout amount
            return redirect()->back()->with('error', 'Minimum payout amount is ₹500. Current pending: ₹' . $pendingAmount);
        }

        // TODO: Implement actual payout request logic
        // This would typically create a payout request and notify admin

        return redirect()->back()->with('success', 'Payout request submitted successfully. It will be processed within 3-5 business days.');
    }

    /**
     * Teacher's schedule calendar
     */
    public function schedule()
    {
        $teacher = Auth::user()->yogaTeacher;

        // Get upcoming sessions for the next 30 days
        $sessions = Attendance::with(['service.client.user'])
            ->where('teacher_id', $teacher->id)
            ->where('session_date', '>=', today())
            ->where('session_date', '<=', today()->addDays(30))
            ->where('overall_status', '!=', 'cancelled')
            ->orderBy('session_date')
            ->orderBy('scheduled_time')
            ->get();

        // Get availability for calendar
        $availabilities = $teacher->availabilities()
            ->where('is_available', true)
            ->get();

        return view('teacher.schedule.index', compact('sessions', 'availabilities'));
    }

    /**
     * Reschedule a session
     */
    public function rescheduleSession(Request $request, $attendanceId)
    {
        $attendance = Attendance::findOrFail($attendanceId);
        $teacher = Auth::user()->yogaTeacher;

        if ($attendance->teacher_id !== $teacher->id) {
            abort(403);
        }

        $request->validate([
            'new_date' => 'required|date|after:today',
            'new_time' => 'required|date_format:H:i',
            'reschedule_reason' => 'required|string|max:500',
        ]);

        // Check if teacher is available at new time
        $isAvailable = $this->checkTeacherAvailability($teacher->id, $request->new_date, $request->new_time);

        if (!$isAvailable) {
            return redirect()->back()->with('error', 'You are not available at the selected time. Please check your availability schedule.');
        }

        // Create new attendance record
        $newAttendance = $attendance->replicate();
        $newAttendance->session_date = $request->new_date;
        $newAttendance->scheduled_time = $request->new_time;
        $newAttendance->was_rescheduled = true;
        $newAttendance->original_attendance_id = $attendance->id;
        $newAttendance->teacher_notes = $request->reschedule_reason;
        $newAttendance->save();

        // Mark original attendance as rescheduled
        $attendance->update([
            'overall_status' => 'rescheduled',
            'cancellation_reason' => $request->reschedule_reason,
            'cancelled_by' => 'teacher',
        ]);

        // TODO: Send notification to client

        return redirect()->back()->with('success', 'Session rescheduled successfully. Client has been notified.');
    }

    /**
     * Create attendance records for confirmed service
     */
    private function createAttendanceRecords(Service $service)
    {
        $sessionDates = $this->generateSessionDates(
            $service->start_date,
            $service->scheduled_days,
            $service->total_sessions
        );

        foreach ($sessionDates as $sessionNumber => $sessionDate) {
            Attendance::create([
                'service_id' => $service->id,
                'client_id' => $service->client_id,
                'teacher_id' => $service->teacher_id,
                'session_number' => $sessionNumber + 1,
                'session_date' => $sessionDate,
                'scheduled_time' => $service->preferred_time,
                'overall_status' => 'scheduled',
            ]);
        }
    }

    /**
     * Generate session dates based on schedule
     */
    private function generateSessionDates($startDate, $scheduledDays, $totalSessions)
    {
        $dates = [];
        $currentDate = Carbon::parse($startDate);
        $sessionCount = 0;

        while ($sessionCount < $totalSessions) {
            $dayOfWeek = strtolower($currentDate->englishDayOfWeek);

            if (in_array($dayOfWeek, $scheduledDays)) {
                $dates[] = $currentDate->copy();
                $sessionCount++;
            }

            $currentDate->addDay();
        }

        return $dates;
    }

    /**
     * Check teacher availability
     */
    private function checkTeacherAvailability($teacherId, $date, $time)
    {
        $dayOfWeek = strtolower(Carbon::parse($date)->englishDayOfWeek);

        return Availability::where('teacher_id', $teacherId)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>=', $time)
            ->where('is_available', true)
            ->exists();
    }

    /**
     * Get teacher performance analytics
     */
    public function analytics()
    {
        $teacher = Auth::user()->yogaTeacher;

        // Service completion rate
        $totalServices = $teacher->services()->count();
        $completedServices = $teacher->services()->where('status', 'completed')->count();
        $completionRate = $totalServices > 0 ? ($completedServices / $totalServices) * 100 : 0;

        // Client retention
        $uniqueClients = $teacher->services()->distinct('client_id')->count('client_id');
        $repeatClients = DB::table('services')
            ->where('teacher_id', $teacher->id)
            ->groupBy('client_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        // Monthly performance
        $monthlyPerformance = $teacher->payments()
            ->where('payout_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, 
                        COUNT(*) as sessions, SUM(net_teacher_share) as earnings,
                        AVG(net_teacher_share) as avg_earning_per_session')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('teacher.analytics.index', compact(
            'teacher',
            'completionRate',
            'uniqueClients',
            'repeatClients',
            'monthlyPerformance'
        ));
    }
}
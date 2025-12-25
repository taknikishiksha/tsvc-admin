<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\YogaTeacher;
use App\Models\Payment;
use App\Models\Attendance;
use App\Models\Client;
use App\Models\Course; // added for courses
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Client dashboard (landing page after login/registration)
     */
    public function dashboard()
    {
        $user = Auth::user();

        // If the user does not yet have a client profile, send to profile completion
        if (!$user->client) {
            return redirect()->route('client.profile.create')
                ->with('warning', 'Please complete your client profile to continue.');
        }

        $client = $user->client;

        // Basic stats
        $totalServices = $client->services()->count();
        $activeServices = $client->services()->whereIn('status', ['confirmed', 'in_progress'])->count();
        $completedSessions = $client->attendances()->where('overall_status', 'completed')->count();
        $totalSpent = $client->payments()->where('status', 'captured')->sum('amount');

        // Upcoming sessions (next 7 days)
        $upcomingAttendances = $client->attendances()
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(8)
            ->get();

        // Recent services and payments
        $recentServices = $client->services()->with('teacher.user')->orderBy('created_at', 'desc')->take(6)->get();
        $recentPayments = $client->payments()->with('service')->orderBy('created_at', 'desc')->take(6)->get();

        // Suggested teachers (top-rated, available)
        $suggestedTeachers = YogaTeacher::where('verification_status', 'verified')
            ->where('is_available', true)
            ->orderByDesc('rating')
            ->take(6)
            ->get();

        $stats = [
            'total_services'   => $totalServices,
            'active_services'  => $activeServices,
            'completed_sessions' => $completedSessions,
            'total_spent'      => $totalSpent,
        ];

        return view('client.dashboard', compact(
            'client',
            'stats',
            'upcomingAttendances',
            'recentServices',
            'recentPayments',
            'suggestedTeachers'
        ));
    }

    /**
     * Show available courses / join course UI for client
     */
    public function joinCourse()
    {
        // load basic data — if Course model exists, else send empty collection
        $courses = class_exists(\App\Models\Course::class) ? \App\Models\Course::take(10)->get() : collect();

        return view('client.courses.join', compact('courses'));
    }

    /**
     * Show payments / invoices page for client
     */
    public function payments(Request $request)
    {
        $user = $request->user();

        // load payments/invoices if you have a Payment model; fallback empty collection
        $payments = class_exists(\App\Models\Payment::class)
            ? \App\Models\Payment::where('user_id', $user->id)->latest()->get()
            : collect();

        return view('client.payments.index', compact('user', 'payments'));
    }

    /**
     * Show teacher change request page (simple form)
     */
    public function requestTeacherChange(Request $request)
    {
        $user = $request->user();

        // If you want to list available teachers, load minimal set (optional)
        $availableTeachers = class_exists(\App\Models\User::class)
            ? \App\Models\User::where('role', 'teacher')->limit(10)->get()
            : collect();

        return view('client.teacher.change', compact('user', 'availableTeachers'));
    }

    /**
     * Display client profile
     */
    public function profile()
    {
        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            return redirect()->route('client.profile.create');
        }

        $stats = [
            'total_services' => $client->services()->count(),
            'active_services' => $client->services()->whereIn('status', ['confirmed', 'in_progress'])->count(),
            'completed_sessions' => $client->attendances()->where('overall_status', 'completed')->count(),
            'total_spent' => $client->payments()->where('status', 'captured')->sum('amount'),
        ];

        return view('client.profile.index', compact('client', 'stats'));
    }

    /**
     * Update client profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            return redirect()->route('client.profile.create');
        }

        $validated = $request->validate([
            'health_issues' => 'nullable|string|max:1000',
            'medical_history' => 'nullable|string|max:1000',
            'yoga_goals' => 'required|array',
            'yoga_goals.*' => 'in:weight_loss,stress_relief,flexibility,strength,meditation,rehabilitation',
            'experience_level' => 'required|in:beginner,intermediate,advanced',
            'preferences' => 'nullable|array',
            'service_type' => 'required|in:home,online,group,corporate',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:15',
            'emergency_relation' => 'required|string|max:100',
        ]);

        $client->update($validated);

        // Update user basic info if provided
        if ($request->filled('name')) {
            $user->update(['name' => $request->name]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Show teacher search and booking page
     */
    public function findTeachers(Request $request)
    {
        $query = YogaTeacher::with(['user', 'availabilities'])
            ->where('verification_status', 'verified')
            ->where('is_available', true);

        // Search filters
        if ($request->filled('specialization')) {
            $query->whereJsonContains('specializations', $request->specialization);
        }

        if ($request->filled('service_type')) {
            $query->whereJsonContains('service_types', $request->service_type);
        }

        if ($request->filled('city')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('city', 'like', '%' . $request->city . '%');
            });
        }

        if ($request->filled('language')) {
            $query->whereJsonContains('languages', $request->language);
        }

        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        $teachers = $query->orderBy('rating', 'desc')
                         ->orderBy('experience_years', 'desc')
                         ->paginate(12);

        return view('client.teachers.index', compact('teachers'));
    }

    /**
     * Show teacher profile for booking
     */
    public function showTeacher($id)
    {
        $teacher = YogaTeacher::with(['user', 'availabilities', 'services.client.user'])
            ->where('verification_status', 'verified')
            ->findOrFail($id);

        $reviews = $teacher->services()
            ->whereNotNull('client_rating')
            ->with('client.user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('client.teachers.show', compact('teacher', 'reviews'));
    }

    /**
     * Book a service with teacher
     */
    public function bookService(Request $request, $teacherId)
    {
        $teacher = YogaTeacher::findOrFail($teacherId);
        $client = Auth::user()->client;

        if (!$client) {
            return redirect()->route('client.profile.create')
                ->with('warning', 'Please complete your client profile to book a service.');
        }

        $validated = $request->validate([
            'service_type' => 'required|in:personal,group,corporate,online',
            'package_type' => 'required|in:single,weekly,monthly,custom',
            'total_sessions' => 'required|integer|min:1|max:100',
            'start_date' => 'required|date|after:today',
            'preferred_time' => 'required|string',
            'scheduled_days' => 'required|array',
            'session_duration' => 'required|integer|min:30|max:180',
            'service_address' => 'required_if:service_type,personal,corporate|nullable|string',
            'special_instructions' => 'nullable|string|max:500',
        ]);

        // Calculate package amount
        $packageAmount = $this->calculatePackageAmount(
            $teacher->hourly_rate,
            $validated['total_sessions'],
            $validated['session_duration'],
            $validated['package_type']
        );

        DB::transaction(function () use ($client, $teacher, $validated, $packageAmount) {
            // Create service
            $service = Service::create([
                'client_id' => $client->id,
                'teacher_id' => $teacher->id,
                'service_type' => $validated['service_type'],
                'package_type' => $validated['package_type'],
                'total_sessions' => $validated['total_sessions'],
                'sessions_completed' => 0,
                'sessions_remaining' => $validated['total_sessions'],
                'start_date' => $validated['start_date'],
                'end_date' => $this->calculateEndDate($validated['start_date'], $validated['package_type']),
                'scheduled_days' => $validated['scheduled_days'],
                'preferred_time' => $validated['preferred_time'],
                'session_duration' => $validated['session_duration'],
                'service_address' => $validated['service_address'] ?? null,
                'location_type' => $validated['service_type'] === 'online' ? 'online' : 'physical',
                'package_amount' => $packageAmount,
                'per_session_rate' => $packageAmount / $validated['total_sessions'],
                'final_amount' => $packageAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'special_instructions' => $validated['special_instructions'] ?? null,
            ]);

            // TODO: Send notification to teacher
            // TODO: Initialize first payment (create Payment record if needed)
        });

        return redirect()->route('client.services')
                       ->with('success', 'Service booking request sent successfully. Teacher will confirm shortly.');
    }

    /**
     * Client's services list
     */
    public function services(Request $request)
    {
        $client = Auth::user()->client;

        if (!$client) {
            return redirect()->route('client.profile.create');
        }

        $query = $client->services()->with(['teacher.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('client.services.index', compact('services'));
    }

    /**
     * Show service details
     */
    public function showService($id)
    {
        $service = Service::with([
            'teacher.user', 
            'attendances', 
            'payments'
        ])->findOrFail($id);

        // Check if client owns this service
        $client = Auth::user()->client;
        if (!$client || $service->client_id !== $client->id) {
            abort(403);
        }

        return view('client.services.show', compact('service'));
    }

    /**
     * Mark attendance confirmation
     */
    public function confirmAttendance(Request $request, $attendanceId)
    {
        $attendance = Attendance::findOrFail($attendanceId);
        $client = Auth::user()->client;

        if (!$client || $attendance->client_id !== $client->id) {
            abort(403);
        }

        $attendance->update([
            'client_status' => 'present',
            'client_confirmed' => true,
            'client_confirmed_at' => now(),
            'client_notes' => $request->input('notes'),
        ]);

        // Update overall status (assumes model method exists)
        if (method_exists($attendance, 'updateOverallStatus')) {
            $attendance->updateOverallStatus();
        }

        return redirect()->back()->with('success', 'Attendance confirmed successfully.');
    }

    /**
     * Submit feedback for service
     */
    public function submitFeedback(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $client = Auth::user()->client;

        if (!$client || $service->client_id !== $client->id) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'required|string|max:1000',
        ]);

        $service->update([
            'client_rating' => $request->rating,
            'client_feedback' => $request->feedback,
        ]);

        // Update teacher's rating
        $this->updateTeacherRating($service->teacher_id);

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }

    /**
     * Calculate package amount
     */
    private function calculatePackageAmount($hourlyRate, $totalSessions, $sessionDuration, $packageType)
    {
        $sessionRate = ($hourlyRate / 60) * $sessionDuration;
        $baseAmount = $sessionRate * $totalSessions;

        // Apply package discounts
        $multipliers = [
            'single' => 1.0,
            'weekly' => 0.9,  // 10% discount
            'monthly' => 0.8, // 20% discount
            'custom' => 1.0,
        ];

        return round($baseAmount * ($multipliers[$packageType] ?? 1.0), 2);
    }

    /**
     * Calculate end date based on package type
     */
    private function calculateEndDate($startDate, $packageType)
    {
        $start = \Carbon\Carbon::parse($startDate);

        return match($packageType) {
            'weekly' => $start->copy()->addWeek(),
            'monthly' => $start->copy()->addMonth(),
            default => $start->copy()->addMonths(3), // custom and single fallback
        };
    }

    /**
     * Update teacher rating
     */
    private function updateTeacherRating($teacherId)
    {
        $teacher = YogaTeacher::find($teacherId);
        if (!$teacher) return;

        $services = $teacher->services()->whereNotNull('client_rating')->get();

        if ($services->count() > 0) {
            $avgRating = $services->avg('client_rating');
            $teacher->update([
                'rating' => round($avgRating, 2),
                'total_ratings' => $services->count(),
            ]);
        }
    }
}
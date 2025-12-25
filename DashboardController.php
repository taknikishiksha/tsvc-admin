<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\Payment;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show appropriate dashboard based on user role
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        // Directly redirect based on role without any loops
        return $this->redirectToRoleDashboard($user);
    }

    /**
     * Redirect user to their specific dashboard based on role
     */
    protected function redirectToRoleDashboard($user)
    {
        // Get the dashboard route from User model
        $dashboardRoute = $user->getDashboardRoute();
        
        // Check if route exists and redirect
        if (\Illuminate\Support\Facades\Route::has($dashboardRoute)) {
            return redirect()->route($dashboardRoute);
        }

        // Fallback for roles without specific dashboard
        return $this->showGenericDashboard($user);
    }

    /**
     * Show generic dashboard for roles without specific dashboard
     */
    protected function showGenericDashboard($user)
    {
        // Basic stats for generic dashboard
        $stats = [
            'welcome_message' => "Welcome, {$user->name}!",
            'role' => $user->role,
            'last_login' => $user->last_login_at ? $user->last_login_at->format('M j, Y g:i A') : 'First login',
        ];

        return view('dashboard.generic', compact('user', 'stats'));
    }

    /**
     * Teacher Dashboard - This will be called from TeacherController
     */
    public function teacherDashboard()
    {
        $user = Auth::user();
        $teacher = $user->yogaTeacher;

        if (!$teacher) {
            return redirect()->route('teacher.profile.create')
                           ->with('warning', 'Please complete your teacher profile first.');
        }

        // Teacher Statistics
        $stats = [
            'active_services' => $teacher->services()->whereIn('status', ['confirmed', 'in_progress'])->count(),
            'completed_services' => $teacher->services()->where('status', 'completed')->count(),
            'total_earnings' => $teacher->payments()->where('payout_status', 'paid')->sum('net_teacher_share'),
            'pending_payouts' => $teacher->payments()->where('payout_status', 'pending')->sum('net_teacher_share'),
            'avg_rating' => $teacher->rating,
            'total_ratings' => $teacher->total_ratings,
        ];

        // Upcoming Sessions (next 7 days)
        $upcomingSessions = Attendance::with(['service.client.user'])
            ->where('teacher_id', $teacher->id)
            ->where('session_date', '>=', today())
            ->where('session_date', '<=', today()->addDays(7))
            ->where('overall_status', '!=', 'cancelled')
            ->orderBy('session_date')
            ->orderBy('scheduled_time')
            ->get();

        // Recent Payments
        $recentPayments = Payment::with(['service.client.user'])
            ->where('teacher_id', $teacher->id)
            ->where('status', 'captured')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('teacher.dashboard.index', compact(
            'teacher', 
            'stats', 
            'upcomingSessions',
            'recentPayments'
        ));
    }

    /**
     * Client Dashboard - This will be called from ClientController
     */
    public function clientDashboard()
    {
        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            return redirect()->route('client.profile.create')
                           ->with('warning', 'Please complete your client profile first.');
        }

        // Client Statistics
        $stats = [
            'active_services' => $client->services()->whereIn('status', ['confirmed', 'in_progress'])->count(),
            'completed_services' => $client->services()->where('status', 'completed')->count(),
            'total_spent' => $client->payments()->where('status', 'captured')->sum('amount'),
            'loyalty_points' => $client->loyalty_points,
            'upcoming_sessions' => $client->attendances()
                ->where('session_date', '>=', today())
                ->where('overall_status', '!=', 'cancelled')
                ->count(),
        ];

        // Service Timeline
        $services = $client->services()
            ->with(['teacher.user', 'attendances'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Upcoming Sessions
        $upcomingSessions = Attendance::with(['service.teacher.user'])
            ->where('client_id', $client->id)
            ->where('session_date', '>=', today())
            ->where('overall_status', '!=', 'cancelled')
            ->orderBy('session_date')
            ->orderBy('scheduled_time')
            ->get();

        return view('client.dashboard.index', compact(
            'client',
            'stats',
            'services',
            'upcomingSessions'
        ));
    }

    /**
     * Admin Dashboard - Simple redirect to admin dashboard
     */
    public function adminDashboard()
    {
        return redirect()->route('admin.dashboard');
    }

    /**
     * HR Dashboard
     */
    public function hrDashboard()
    {
        $user = Auth::user();
        $stats = [
            'total_employees' => \App\Models\User::where('role', 'teacher')->orWhere('role', 'admin')->count(),
            'pending_requests' => 0, // Add your HR logic
            'active_recruitments' => 0, // Add your HR logic
        ];

        return view('hr.dashboard', compact('user', 'stats'));
    }

    /**
     * Finance Dashboard
     */
    public function financeDashboard()
    {
        $user = Auth::user();
        $stats = [
            'total_revenue' => Payment::where('status', 'captured')->sum('amount'),
            'pending_payouts' => Payment::where('payout_status', 'pending')->sum('net_teacher_share'),
            'this_month_earnings' => Payment::where('status', 'captured')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ];

        return view('finance.dashboard', compact('user', 'stats'));
    }

    /**
     * Training Dashboard
     */
    public function trainingDashboard()
    {
        $user = Auth::user();
        $stats = [
            'active_courses' => 0, // Add your training logic
            'enrolled_students' => \App\Models\User::where('role', 'student')->count(),
            'completion_rate' => '85%', // Add your training logic
        ];

        return view('training.dashboard', compact('user', 'stats'));
    }

    /**
     * Exam Dashboard
     */
    public function examDashboard()
    {
        $user = Auth::user();
        $stats = [
            'upcoming_exams' => 0, // Add your exam logic
            'students_registered' => \App\Models\User::where('role', 'student')->count(),
            'results_pending' => 0, // Add your exam logic
        ];

        return view('exam.dashboard', compact('user', 'stats'));
    }

    /**
     * User Management Dashboard
     */
    public function userMgmtDashboard()
    {
        $user = Auth::user();
        $stats = [
            'total_users' => \App\Models\User::count(),
            'active_users' => \App\Models\User::where('is_active', true)->count(),
            'new_registrations' => \App\Models\User::whereDate('created_at', today())->count(),
        ];

        return view('usermgmt.dashboard', compact('user', 'stats'));
    }

    /**
     * Service Dashboard
     */
    public function serviceDashboard()
    {
        $user = Auth::user();
        $stats = [
            'active_services' => Service::whereIn('status', ['confirmed', 'in_progress'])->count(),
            'completed_services' => Service::where('status', 'completed')->count(),
            'service_queries' => 0, // Add your service logic
        ];

        return view('service.dashboard', compact('user', 'stats'));
    }

    /**
     * Student Dashboard
     */
    public function studentDashboard()
    {
        $user = Auth::user();
        $stats = [
            'enrolled_courses' => 0, // Add your student logic
            'completed_courses' => 0, // Add your student logic
            'upcoming_classes' => 0, // Add your student logic
        ];

        return view('student.dashboard', compact('user', 'stats'));
    }

    /**
     * Partner Dashboard
     */
    public function partnerDashboard()
    {
        $user = Auth::user();
        $stats = [
            'partnerships' => 0, // Add your partner logic
            'revenue_share' => 0, // Add your partner logic
            'active_collaborations' => 0, // Add your partner logic
        ];

        return view('partner.dashboard', compact('user', 'stats'));
    }

    /**
     * Consultant Dashboard
     */
    public function consultantDashboard()
    {
        $user = Auth::user();
        $stats = [
            'active_consultations' => 0, // Add your consultant logic
            'clients_served' => 0, // Add your consultant logic
            'satisfaction_rate' => '95%', // Add your consultant logic
        ];

        return view('consultant.dashboard', compact('user', 'stats'));
    }

    /**
     * Volunteer Dashboard
     */
    public function volunteerDashboard()
    {
        $user = Auth::user();
        $stats = [
            'volunteer_hours' => 0, // Add your volunteer logic
            'upcoming_events' => 0, // Add your volunteer logic
            'completed_tasks' => 0, // Add your volunteer logic
        ];

        return view('volunteer.dashboard', compact('user', 'stats'));
    }

    /**
     * Intern Dashboard
     */
    public function internDashboard()
    {
        $user = Auth::user();
        $stats = [
            'training_progress' => '60%', // Add your intern logic
            'assigned_tasks' => 0, // Add your intern logic
            'completed_projects' => 0, // Add your intern logic
        ];

        return view('intern.dashboard', compact('user', 'stats'));
    }

    /**
     * Get notifications for current user
     */
    public function notifications()
    {
        $user = Auth::user();
        
        $notifications = [
            [
                'type' => 'info',
                'message' => 'Welcome to your dashboard!',
                'time' => now()->subMinutes(5),
                'read' => true,
            ],
        ];

        return response()->json($notifications);
    }

    /**
     * Get quick stats for dashboard widgets
     */
    public function quickStats()
    {
        $user = Auth::user();
        $stats = [];

        if ($user->isTeacher()) {
            $teacher = $user->yogaTeacher;
            $stats = [
                'today_sessions' => $teacher->attendances()
                    ->where('session_date', today())
                    ->where('overall_status', '!=', 'cancelled')
                    ->count(),
                'weekly_earnings' => $teacher->payments()
                    ->where('status', 'captured')
                    ->where('created_at', '>=', Carbon::now()->startOfWeek())
                    ->sum('net_teacher_share'),
            ];
        } elseif ($user->isClient()) {
            $client = $user->client;
            $stats = [
                'today_sessions' => $client->attendances()
                    ->where('session_date', today())
                    ->where('overall_status', '!=', 'cancelled')
                    ->count(),
                'weekly_sessions' => $client->attendances()
                    ->where('session_date', '>=', Carbon::now()->startOfWeek())
                    ->where('session_date', '<=', Carbon::now()->endOfWeek())
                    ->where('overall_status', '!=', 'cancelled')
                    ->count(),
            ];
        }

        return response()->json($stats);
    }
}
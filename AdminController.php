<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\YogaTeacher;
use App\Models\Client;
use App\Models\Service;
use App\Models\Payment;
use App\Models\Application;
use App\Models\DocumentSubmission;
use App\Models\DemoBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        // Basic Statistics
        $stats = [
            'total_teachers' => YogaTeacher::count(),
            'total_clients' => Client::count(),
            'total_services' => Service::count(),
            'total_revenue' => Payment::where('status', 'captured')->sum('amount'),
            'pending_teachers' => YogaTeacher::where('verification_status', 'pending')->count(),
            'active_services' => Service::whereIn('status', ['confirmed', 'in_progress'])->count(),
            'pending_applications' => Application::where('status', 'pending')->count(),
            'pending_documents' => DocumentSubmission::where('status', 'pending')->count(),
            'submitted_documents' => DocumentSubmission::where('status', 'submitted')->count(),
        ];

        // Demo Analytics (NEW)
        $totalDemos = DemoBooking::count();
        $convertedDemos = DemoBooking::where('converted_to_service', 1)->count();
        $conversionPercentage = $totalDemos > 0 
            ? round(($convertedDemos / $totalDemos) * 100, 2) 
            : 0;

        $stats['total_demos'] = $totalDemos;
        $stats['converted_demos'] = $convertedDemos;
        $stats['demo_conversion_percentage'] = $conversionPercentage;

        // Recent Activities
        $recentTeachers = YogaTeacher::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentServices = Service::with(['client.user', 'teacher.user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent Document Submissions
        $recentSubmissions = DocumentSubmission::with(['teacher.user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent Demo Bookings (NEW) - FIXED: Check relationships first
        try {
            // Try to load with relationships, but handle if they don't exist
            $recentDemos = DemoBooking::with(['user', 'teacher.user'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // Fallback to basic query without relationships
            Log::warning('DemoBooking relationships not found: ' . $e->getMessage());
            $recentDemos = DemoBooking::orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        // Revenue Chart Data (Last 30 days)
        $revenueData = Payment::where('status', 'captured')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'recentTeachers', 
            'recentServices',
            'recentSubmissions',
            'recentDemos',
            'revenueData'
        ));
    }

    /**
     * Dashboard Data for AJAX
     * Defensive implementation: guards for missing tables/columns and logs warnings/errors.
     */
public function dashboardData(Request $request)
{
    try {
        $totalTeachers = YogaTeacher::count();
        $activeClients = Client::count();

        $monthlyRevenue = Schema::hasTable('payments')
            ? Payment::where('status', 'captured')
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->sum('amount')
            : 0;

        $pendingActions = Application::where('status', 'pending')->count();
        $pendingDocuments = DocumentSubmission::where('status', 'pending')->count();

        // 🔥 Demo Analytics (Single Source: demo_bookings)
        $totalDemos = DemoBooking::count();
        $convertedDemos = Schema::hasColumn('demo_bookings', 'converted_to_service')
            ? DemoBooking::where('converted_to_service', 1)->count()
            : 0;

        $conversionPercentage = $totalDemos > 0
            ? round(($convertedDemos / $totalDemos) * 100, 2)
            : 0;

        return response()->json([
            'totalTeachers'    => $totalTeachers,
            'activeClients'    => $activeClients,
            'monthlyRevenue'   => $monthlyRevenue,
            'pendingActions'   => $pendingActions,
            'pendingDocuments' => $pendingDocuments,
            'demoAnalytics' => [
                'totalDemos' => $totalDemos,
                'conversionPercentage' => $conversionPercentage,
            ],
        ]);

    } catch (\Throwable $e) {
        Log::error('AdminController::dashboardData failed', [
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'totalTeachers' => 0,
            'activeClients' => 0,
            'monthlyRevenue' => 0,
            'pendingActions' => 0,
            'pendingDocuments' => 0,
            'demoAnalytics' => [
                'totalDemos' => 0,
                'conversionPercentage' => 0,
            ],
        ], 500);
    }
}

    /**
     * Demo Bookings Management (NEW)
     * FIXED: Updated relationship handling
     */
    public function demoBookings(Request $request)
    {
        try {
            $query = DemoBooking::query();
            
            // Try to add relationships if they exist
            if (method_exists(DemoBooking::class, 'user')) {
                $query->with('user');
            }
            if (method_exists(DemoBooking::class, 'teacher')) {
                $query->with('teacher.user');
            }

            if ($request->has('search') && $request->search) {
                // Search by user name if user relationship exists
                if (method_exists(DemoBooking::class, 'user')) {
                    $query->whereHas('user', function($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    });
                } else {
                    // Fallback search on demo_bookings table
                    $query->where(function($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('email', 'like', '%' . $request->search . '%');
                    });
                }
            }

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('converted_to_service')) {
                $convertedValue = $request->converted_to_service == 'yes' ? 1 : 0;
                $query->where('converted_to_service', $convertedValue);
            }

            $demoBookings = $query->orderBy('created_at', 'desc')->paginate(20);

            // Demo Analytics Summary
            $totalDemos = DemoBooking::count();
            $convertedDemos = DemoBooking::where('converted_to_service', 1)->count();
            $conversionPercentage = $totalDemos > 0 
                ? round(($convertedDemos / $totalDemos) * 100, 2) 
                : 0;

            $demoStats = [
                'total_demos' => $totalDemos,
                'pending_demos' => DemoBooking::where('status', 'pending')->count(),
                'scheduled_demos' => DemoBooking::where('status', 'scheduled')->count(),
                'completed_demos' => DemoBooking::where('status', 'completed')->count(),
                'converted_demos' => $convertedDemos,
                'conversion_percentage' => $conversionPercentage,
            ];

            return view('admin.demo-bookings.index', compact('demoBookings', 'demoStats'));
        } catch (\Exception $e) {
            Log::error('Demo bookings error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading demo bookings: ' . $e->getMessage());
        }
    }

    public function showDemoBooking($id)
    {
        try {
            $demoBooking = DemoBooking::findOrFail($id);
            
            // Try to load relationships if they exist
            if (method_exists(DemoBooking::class, 'user')) {
                $demoBooking->load('user');
            }
            if (method_exists(DemoBooking::class, 'teacher')) {
                $demoBooking->load('teacher.user');
            }
            if (method_exists(DemoBooking::class, 'service')) {
                $demoBooking->load('service');
            }

            return view('admin.demo-bookings.show', compact('demoBooking'));
        } catch (\Exception $e) {
            Log::error('Show demo booking error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Demo booking not found.');
        }
    }

    public function updateDemoBookingStatus(Request $request, $id)
    {
        $demoBooking = DemoBooking::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,scheduled,completed,cancelled',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $demoBooking->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->back()->with('success', 'Demo booking status updated successfully.');
    }

    /**
     * Show Teacher Details
     */
    public function showTeacher($id)
    {
        $teacher = YogaTeacher::with(['user', 'services.client.user', 'payments', 'documentSubmissions'])
            ->findOrFail($id);

        $teacherStats = [
            'total_services' => $teacher->services()->count(),
            'completed_services' => $teacher->services()->where('status', 'completed')->count(),
            'total_earnings' => $teacher->payments()->where('status', 'captured')->sum('teacher_share'),
            'avg_rating' => $teacher->rating,
            'document_submissions' => $teacher->documentSubmissions()->count(),
            'verified_documents' => $teacher->documentSubmissions()->where('status', 'verified')->count(),
        ];

        return view('admin.teachers.show', compact('teacher', 'teacherStats'));
    }
    
    /**
 * Teacher Management – List
 */
public function teachers(Request $request)
{
    $query = YogaTeacher::with(['user', 'documentSubmissions']);

    if ($request->filled('search')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('email', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('verification_status')) {
        $query->where('verification_status', $request->verification_status);
    }

    $teachers = $query->orderBy('created_at', 'desc')->paginate(20);

    return view('admin.teachers.index', compact('teachers'));
}

/**
 * Scheduled Demo Classes
 */
public function scheduledDemos()
{
    $demos = DemoBooking::with(['user', 'teacher.user'])
        ->where('status', 'scheduled')
        ->orderBy('preferred_date', 'asc')
        ->orderBy('preferred_time', 'asc')
        ->paginate(20);

    return view('admin.demo.scheduled', compact('demos'));
}

/**
 * Reschedule / Cancel Demo Requests
 */
public function demoChangeRequests()
{
    $query = DemoBooking::with(['user', 'teacher.user']);

    // ✅ Defensive & future-ready column checks
    if (
        Schema::hasColumn('demo_bookings', 'reschedule_requested_at') &&
        Schema::hasColumn('demo_bookings', 'cancel_requested_at')
    ) {
        $query->where(function ($q) {
            $q->whereNotNull('reschedule_requested_at')
              ->orWhereNotNull('cancel_requested_at');
        });
    } else {
        // Fallback – no crash in production
        Log::warning('Demo change request columns missing in demo_bookings table.');
        $query->whereRaw('1 = 0'); // return empty result safely
    }

    $demos = $query
        ->orderBy('updated_at', 'desc')
        ->paginate(20);

    return view('admin.demo.change-requests', compact('demos'));
}

/**
 * ✅ Approve Demo Change Request (Admin)
 */
public function approveDemoChangeRequest($id)
{
    $demo = DemoBooking::findOrFail($id);

    // Only scheduled demos allowed
    if ($demo->status !== 'scheduled') {
        return back()->with('error', 'Only scheduled demos can be modified.');
    }

    // Approve cancellation
    if ($demo->cancel_requested_at) {
        $demo->update([
            'status' => 'cancelled',
            'cancel_requested_at' => null,
            'reschedule_requested_at' => null,
        ]);

        return back()->with('success', 'Demo cancelled successfully.');
    }

    // Approve reschedule (date/time handled later)
    if ($demo->reschedule_requested_at) {
        $demo->update([
            'reschedule_requested_at' => null,
        ]);

        return back()->with('success', 'Reschedule request approved.');
    }

    return back()->with('warning', 'No pending change request found.');
}

/**
 * ❌ Reject Demo Change Request (Admin)
 */
public function rejectDemoChangeRequest(Request $request, $id)
{
    $demo = DemoBooking::findOrFail($id);

    if ($demo->status !== 'scheduled') {
        return back()->with('error', 'Invalid demo status.');
    }

    // Clear both requests
    $demo->update([
        'reschedule_requested_at' => null,
        'cancel_requested_at' => null,
    ]);

    return back()->with('success', 'Change request rejected.');
}

    /**
     * Request Documents for Teacher
     */
    public function requestTeacherDocuments(Request $request, $id)
    {
        $teacher = YogaTeacher::with('user')->findOrFail($id);

        $request->validate([
            'documents' => 'required|array',
            'documents.*' => 'in:aadhar_front,aadhar_back,ycb_certificate,police_verification,profile_photo,educational_certificate',
            'instructions' => 'nullable|string|max:1000',
        ]);

        // Create document submission
        $submission = DocumentSubmission::createForTeacher($teacher, $request->documents);

        // Send document request email - FIXED MAIL METHOD
        try {
            \Illuminate\Support\Facades\Mail::to($teacher->user->email)->send(
                new \App\Mail\DocumentRequestMail($submission, $teacher, $teacher->user, $request->instructions)
            );
            
            $submission->update(['request_sent_at' => now()]);
            
            return redirect()->back()->with('success', 'Document request sent to teacher successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Failed to send document request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send document request. Please try again.');
        }
    }

    /**
     * Verify Teacher
     */
    public function verifyTeacher(Request $request, $id)
    {
        $teacher = YogaTeacher::findOrFail($id);

        $request->validate([
            'verification_status' => 'required|in:verified,rejected',
            'verification_notes' => 'nullable|string|max:500',
        ]);

        $teacher->update([
            'verification_status' => $request->verification_status,
            'verification_notes' => $request->verification_notes,
            'verified_at' => $request->verification_status === 'verified' ? now() : null,
        ]);

        if ($request->verification_status === 'verified') {
            \Log::info("Teacher {$teacher->id} verified successfully");
        }

        return redirect()->back()->with('success', 'Teacher verification status updated successfully.');
    }

    // ========================================
    // NEW METHOD: Create Teacher Form
    // ========================================
    public function createTeacher()
    {
        return view('admin.teachers.create');
    }

    // ========================================
    // NEW METHOD: Store New Teacher
    // ========================================
    public function storeTeacher(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:15',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt('temp-password-123'), // Temporary password
            'email_verified_at' => now(),
            'role' => 'teacher',
        ]);

        YogaTeacher::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
            'status' => 'active',
            'verification_status' => 'pending',
        ]);

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Teacher created successfully! Password: temp-password-123');
    }

    // ========================================
    // Client Management
    // ========================================
    public function clients(Request $request)
    {
        $query = Client::with('user');

        if ($request->has('search') && $request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('service_type') && $request->service_type) {
            $query->where('service_type', $request->service_type);
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.clients.index', compact('clients'));
    }

    public function showClient($id)
    {
        $client = Client::with(['user', 'services.teacher.user', 'payments'])
            ->findOrFail($id);

        $clientStats = [
            'total_services' => $client->services()->count(),
            'active_services' => $client->services()->whereIn('status', ['confirmed', 'in_progress'])->count(),
            'total_spent' => $client->payments()->where('status', 'captured')->sum('amount'),
            'loyalty_points' => $client->loyalty_points,
        ];

        return view('admin.clients.show', compact('client', 'clientStats'));
    }

    // ========================================
    // Services Management
    // ========================================
    public function services(Request $request)
    {
        $query = Service::with(['client.user', 'teacher.user']);

        if ($request->has('search') && $request->search) {
            $query->whereHas('client.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('teacher.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('service_type') && $request->service_type) {
            $query->where('service_type', $request->service_type);
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.services.index', compact('services'));
    }

    public function showService($id)
    {
        $service = Service::with([
            'client.user', 
            'teacher.user', 
            'payments',
            'attendances'
        ])->findOrFail($id);

        return view('admin.services.show', compact('service'));
    }

    public function updateServiceStatus(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled,on_hold',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $service->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->back()->with('success', 'Service status updated successfully.');
    }

    // ========================================
    // Payments, Applications, Settings, etc.
    // ========================================
    public function payments(Request $request)
    {
        $query = Payment::with(['client.user', 'teacher.user', 'service']);

        if ($request->has('search') && $request->search) {
            $query->whereHas('client.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('teacher.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('payout_status') && $request->payout_status) {
            $query->where('payout_status', $request->payout_status);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        $paymentStats = [
            'total_revenue' => Payment::where('status', 'captured')->sum('amount'),
            'pending_payouts' => Payment::where('payout_status', 'pending')->sum('teacher_share'),
            'platform_earnings' => Payment::where('status', 'captured')->sum('platform_fee'),
        ];

        return view('admin.payments.index', compact('payments', 'paymentStats'));
    }

    public function processPayout(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->payout_status !== 'pending') {
            return redirect()->back()->with('error', 'Payout already processed or not pending.');
        }

        $payment->update([
            'payout_status' => 'paid',
            'payout_processed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Payout processed successfully.');
    }

    public function bulkPayout(Request $request)
    {
        $request->validate([
            'payment_ids' => 'required|array',
            'payment_ids.*' => 'exists:payments,id',
        ]);

        $payments = Payment::whereIn('id', $request->payment_ids)
            ->where('payout_status', 'pending')
            ->get();

        $processedCount = 0;
        foreach ($payments as $payment) {
            $payment->update([
                'payout_status' => 'paid',
                'payout_processed_at' => now(),
            ]);
            $processedCount++;
        }

        return redirect()->back()->with('success', "Successfully processed {$processedCount} payouts.");
    }

    public function applications(Request $request)
    {
        $query = Application::query();

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.applications.index', compact('applications'));
    }

    public function documentSubmissions(Request $request)
    {
        $query = DocumentSubmission::with(['teacher.user', 'documents']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('teacher')) {
            $query->whereHas('teacher.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->teacher . '%');
            });
        }

        $submissions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.document-submissions.index', compact('submissions'));
    }

    public function showDocumentSubmission($id)
    {
        $submission = DocumentSubmission::with(['teacher.user', 'documents', 'reviewer'])
            ->findOrFail($id);

        return view('admin.document-submissions.show', compact('submission'));
    }

    public function analytics()
    {
        $topTeachers = YogaTeacher::with('user')
            ->orderBy('rating', 'desc')
            ->orderBy('completed_sessions', 'desc')
            ->take(10)
            ->get();

        $monthlyRevenue = Payment::where('status', 'captured')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as revenue'),
                DB::raw('SUM(platform_fee) as platform_earnings')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $serviceDistribution = Service::select(
                'service_type',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('service_type')
            ->get();

        $clientAcquisition = User::where('role', 'client')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as new_clients')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $documentStats = DocumentSubmission::select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status')
            ->get();

        // Demo Analytics (NEW)
        $demoAnalytics = DemoBooking::where('created_at', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_demos'),
                DB::raw('SUM(CASE WHEN converted_to_service = 1 THEN 1 ELSE 0 END) as converted_demos')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function($item) {
                $item->conversion_percentage = $item->total_demos > 0 
                    ? round(($item->converted_demos / $item->total_demos) * 100, 2) 
                    : 0;
                return $item;
            });

        $demoStatusStats = DemoBooking::select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status')
            ->get();

        return view('admin.analytics.index', compact(
            'topTeachers',
            'monthlyRevenue',
            'serviceDistribution',
            'clientAcquisition',
            'documentStats',
            'demoAnalytics',
            'demoStatusStats'
        ));
    }

    public function settings()
    {
        $settings = [
            'platform_commission' => 20,
            'coordinator_commission' => 10,
            'teacher_commission' => 70,
            'tds_percentage' => 5,
            'auto_approve_teachers' => false,
            'max_services_per_teacher' => 10,
            'document_expiry_days' => 7,
            'auto_send_reminders' => true,
            'demo_expiry_days' => 3,
            'auto_convert_demo_to_service' => false,
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'platform_commission' => 'required|numeric|min:0|max:50',
            'coordinator_commission' => 'required|numeric|min:0|max:20',
            'teacher_commission' => 'required|numeric|min:50|max:100',
            'tds_percentage' => 'required|numeric|min:0|max:10',
            'auto_approve_teachers' => 'boolean',
            'max_services_per_teacher' => 'required|integer|min:1|max:50',
            'document_expiry_days' => 'required|integer|min:1|max:30',
            'auto_send_reminders' => 'boolean',
            'demo_expiry_days' => 'required|integer|min:1|max:30',
            'auto_convert_demo_to_service' => 'boolean',
        ]);

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function users(Request $request)
    {
        $query = User::with(['yogaTeacher', 'client']);

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function updateUserStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $user->update([
            'is_active' => $request->is_active,
        ]);

        return redirect()->back()->with('success', 'User status updated successfully.');
    }

    public function bulkTeacherActions(Request $request)
    {
        $request->validate([
            'teacher_ids' => 'required|array',
            'teacher_ids.*' => 'exists:yoga_teachers,id',
            'action' => 'required|in:request_documents,verify,deactivate',
        ]);

        $processedCount = 0;

        foreach ($request->teacher_ids as $teacherId) {
            $teacher = YogaTeacher::find($teacherId);

            switch ($request->action) {
                case 'request_documents':
                    $documents = ['aadhar_front', 'aadhar_back', 'profile_photo'];
                    DocumentSubmission::createForTeacher($teacher, $documents);
                    $processedCount++;
                    break;

                case 'verify':
                    $teacher->update([
                        'verification_status' => 'verified',
                        'verified_at' => now(),
                    ]);
                    $processedCount++;
                    break;

                case 'deactivate':
                    $teacher->user->update(['is_active' => false]);
                    $processedCount++;
                    break;
            }
        }

        return redirect()->back()->with('success', "Successfully processed {$processedCount} teachers.");
    }
}
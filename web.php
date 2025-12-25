<?php

use App\Http\Controllers\AdminApplicationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\DemoDecisionController;
use App\Http\Controllers\Admin\TeacherVerificationController;
use App\Http\Controllers\Admin\DocumentSubmissionController;
use App\Http\Controllers\Admin\DemoAnalyticsController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\RegisterRoleController;
use App\Http\Controllers\Auth\VerifyEmailController;

/*
|--------------------------------------------------------------------------
| AUTH LOGIN CONTROLLER (ALIAS – IMPORTANT CLEANUP)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Auth\LoginController as GeneralLoginController;

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Corporate\ProfileController as CorporateProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\DocumentCollectionController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OTPVerificationController;
use App\Http\Controllers\ProfileCompletionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\DocumentUploadController as PublicDocumentUploadController;
use App\Http\Controllers\Public\WorkshopRegistrationController;
use App\Http\Controllers\Admin\WorkshopRegistrationAdminController;
use App\Http\Controllers\Admin\WorkshopAttendanceController;
use App\Http\Controllers\Admin\WorkshopCertificateController;
use App\Http\Controllers\RecruiterController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Student\EnrollmentController;
use App\Http\Controllers\Student\SupportController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\InternalUserController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\Teacher\AvailabilityController;
use App\Http\Controllers\Teacher\DocumentUploadController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherDirectoryController;

use App\Models\WorkshopCertificate;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes — Consolidated & Updated
|--------------------------------------------------------------------------
|
| Contains public pages, auth, profile completion, dashboards and
| role-based routes. Updated to include the requested public and
| superadmin-only roles.
|
*/

/** ============================
 *  SECTION 1: PUBLIC PAGES
 *  ============================ */

/** ----------------------
 *  HOME & APPLICATION
 *  ---------------------*/
Route::get('/', fn() => view('welcome'))->name('home');

// Application (public)
Route::get('/apply', [ApplicationController::class, 'create'])->name('apply');
Route::post('/apply', [ApplicationController::class, 'store'])->name('apply.store');
Route::get('/apply/email/{subject}', [ApplicationController::class, 'emailDocuments'])->name('apply.email');

// Static file serving (public storage)
Route::get('/storage/{path}', fn($path) => response()->file(storage_path('app/public/' . $path)))
    ->where('path', '.*')->name('storage.file');

/** ----------------------
 *  CONTACT & RECRUITMENT
 *  ---------------------*/
Route::get('/recruitment-cell', [ContactController::class, 'recruitment'])->name('recruitment-cell');
Route::post('/become-recruiter', [RecruiterController::class, 'store'])->name('become.recruiter');

// CONTACT PAGES
Route::view('/admission-process', 'pages.contact.admission-process')->name('admission-process');
Route::view('/complaints-feedback', 'pages.contact.complaints-feedback')->name('complaints-feedback');
Route::view('/course-information', 'pages.contact.course-information')->name('course-information');
Route::view('/faculty-support', 'pages.contact.faculty-support')->name('faculty-support');
Route::view('/fees-structure', 'pages.contact.fees-structure')->name('fees-structure');
Route::view('/GENERAL-enquiry', 'pages.contact.general-enquiry')->name('general-enquiry');
Route::view('/media-enquiry', 'pages.contact.media-enquiry')->name('media-enquiry');
Route::view('/partnership-collaboration', 'pages.contact.partnership-collaboration')->name('partnership-collaboration');
Route::view('/student-support', 'pages.contact.student-support')->name('student-support');
Route::view('/technical-support', 'pages.contact.technical-support')->name('technical-support');
Route::view('/vendor-registration', 'pages.contact.vendor-registration')->name('vendor-registration');
Route::view('/refund-policy', 'pages.contact.refund-policy')->name('refund-policy');
Route::view('/privacy-policy', 'pages.contact.privacy-policy')->name('privacy-policy');
Route::view('/terms-&-conditions', 'pages.contact.terms-&-conditions')->name('terms-&-conditions');

/** ----------------------
 *  ABOUT PAGES
 *  ---------------------*/
Route::view('/our-mission', 'pages.about.our-mission')->name('our-mission');
Route::view('/our-vision', 'pages.about.our-vision')->name('our-vision');
Route::view('/our-values', 'pages.about.our-values')->name('our-values');
Route::view('/history', 'pages.about.history')->name('history');
Route::view('/leadership', 'pages.about.leadership')->name('leadership');
Route::view('/instructor', 'pages.about.instructor')->name('instructor');
Route::view('/support-staff', 'pages.about.support-staff')->name('support-staff');
Route::view('/advisory-board', 'pages.about.advisory-board')->name('advisory-board');
Route::view('/certification', 'pages.about.certification')->name('certification');
Route::view('/accreditations', 'pages.about.accreditations')->name('accreditations');
Route::view('/awards', 'pages.about.awards')->name('awards');
Route::view('/partners', 'pages.about.partners')->name('partners');

/** ----------------------
 *  CAREERS PAGES
 *  ---------------------*/
Route::view('/yoga-instructor', 'pages.careers.yoga-instructor')->name('yoga-instructor');
Route::view('/therapy-specialist', 'pages.careers.therapy-specialist')->name('therapy-specialist');
Route::view('/wellness-coach', 'pages.careers.wellness-coach')->name('wellness-coach');
Route::view('/admin-staff', 'pages.careers.admin-staff')->name('admin-staff');
Route::view('/teacher-training', 'pages.careers.teacher-training')->name('teacher-training');
Route::view('/skill-enhancement', 'pages.careers.skill-enhancement')->name('skill-enhancement');
Route::view('/certification-program', 'pages.careers.certification-program')->name('certification-program');
Route::view('/workshops', 'pages.careers.workshops')->name('workshops');
Route::view('/internship', 'pages.careers.internship')->name('internship');
Route::view('/volunteer', 'pages.careers.volunteer')->name('volunteer');
Route::view('/part-time-position', 'pages.careers.part-time-position')->name('part-time-position');
Route::view('/freelance-opportunities', 'pages.careers.freelance-opportunities')->name('freelance-opportunities');

/** ----------------------
 *  SERVICES PAGES
 *  ---------------------*/
Route::view('/home-class', 'pages.services.home-class')->name('home-class');
Route::view('/online-class', 'pages.services.online-class')->name('online-class');
Route::view('/group-class', 'pages.services.group-class')->name('group-class');
Route::view('/corporate-wellness', 'pages.services.corporate-wellness')->name('corporate-wellness');
Route::view('/therapeutic-yoga', 'pages.services.therapeutic-yoga')->name('therapeutic-yoga');
Route::view('/prenatal-yoga', 'pages.services.prenatal-yoga')->name('prenatal-yoga');
Route::view('/senior-yoga', 'pages.services.senior-yoga')->name('senior-yoga');
Route::view('/sports-yoga', 'pages.services.sports-yoga')->name('sports-yoga');
Route::view('/stress-management', 'pages.services.stress-management')->name('stress-management');
Route::view('/weight-management', 'pages.services.weight-management')->name('weight-management');
Route::view('/detox-programs', 'pages.services.detox-programs')->name('detox-programs');
Route::view('/mindfulness-training', 'pages.services.mindfulness-training')->name('mindfulness-training');

/** ----------------------
 *  COURSES PAGES
 *  ---------------------*/
Route::view('/YCB-Level-1', 'pages.courses.YCB-Level-1')->name('YCB-Level-1');
Route::view('/YCB-Level-2', 'pages.courses.YCB-Level-2')->name('YCB-Level-2');
Route::view('/YCB-Level-3', 'pages.courses.YCB-Level-3')->name('YCB-Level-3');
Route::view('/RYT-200', 'pages.courses.RYT-200')->name('RYT-200');
Route::view('/RYT-500', 'pages.courses.RYT-500')->name('RYT-500');
Route::view('/specialist-courses', 'pages.courses.specialist-courses')->name('specialist-courses');
Route::view('/yoga-therapy', 'pages.courses.yoga-therapy')->name('yoga-therapy');
Route::view('/meditation', 'pages.courses.meditation')->name('meditation');
Route::view('/pranayama', 'pages.courses.pranayama')->name('pranayama');
Route::view('/live-sessions', 'pages.courses.live-sessions')->name('live-sessions');
Route::view('/recorded-classes', 'pages.courses.recorded-classes')->name('recorded-classes');
Route::view('/self-paced-courses', 'pages.courses.self-paced-courses')->name('self-paced-courses');
Route::view('/beginner-yoga', 'pages.courses.beginner-yoga')->name('beginner-yoga');
Route::view('/advanced-asanas', 'pages.courses.advanced-asanas')->name('advanced-asanas');
Route::view('/yoga-for-kids', 'pages.courses.yoga-for-kids')->name('yoga-for-kids');
Route::view('/study-materials', 'pages.courses.study-materials')->name('study-materials');
Route::view('/practice-videos', 'pages.courses.practice-videos')->name('practice-videos');
Route::view('/exam-preparation', 'pages.courses.exam-preparation')->name('exam-preparation');

/** ============================
 *  SECTION 2: AUTHENTICATION
 *  ============================ */

/** ----------------------
 *  LANGUAGE SWITCH
 *  ---------------------*/
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

/** ----------------------
 *  OTP VERIFICATION
 *  ---------------------*/
Route::get('/verify-otp', [OTPVerificationController::class, 'show'])->name('verification.otp');
Route::post('/verify-otp', [OTPVerificationController::class, 'verify'])->name('verification.otp.verify');
Route::post('/resend-otp', [OTPVerificationController::class, 'resend'])->name('verification.otp.resend');

/** ----------------------
 *  GUEST ONLY ROUTES
 *  ---------------------*/
Route::middleware('guest')->group(function () {

    // Login (Auth LoginController ONLY)
    Route::get('/login', [GeneralLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [GeneralLoginController::class, 'login'])->name('login.submit');

    Route::post('/login/otp/send', [GeneralLoginController::class, 'sendOTP'])->name('login.otp.send');
    Route::post('/login/otp/verify', [GeneralLoginController::class, 'verifyOTPLogin'])->name('login.otp.verify');


    // Register - Role Selection
    Route::get('/register', function () {
        return view('auth.register-role');
    })->name('register');

    // Public roles pattern
    $publicRolesPattern = 'student|teacher|client|intern|volunteer|donor|corporate';

    // Register - Role Form
    Route::get('/register/{role}', function ($role) {
        return view("auth.register-$role", compact('role'));
    })->where('role', $publicRolesPattern)->name('register.role');

    // Register - Role Submit
    Route::post('/register/{role}', [RegisterRoleController::class, 'store'])
        ->where('role', $publicRolesPattern)
        ->name('register.role.store');
});

/** ----------------------
 *  AUTHENTICATED ROUTES
 *  ---------------------*/
Route::middleware('auth')->group(function () {
    /** ----------------------
     *  LOGOUT & ACCOUNT STATES
     *  ---------------------*/
Route::post('/logout', [GeneralLoginController::class, 'logout'])->name('logout');
Route::get('/account/suspended', [GeneralLoginController::class, 'suspended'])->name('login.suspended');
Route::get('/verification-required', [GeneralLoginController::class, 'verificationRequired'])->name('verification.required');

    /** ----------------------
     *  TEACHER PROFILE COMPLETION
     *  FIX: Separated from complete.profile middleware to avoid infinite loop
     *  ---------------------*/
    Route::get('/teacher/profile-complete', [ProfileCompletionController::class, 'showTeacherProfileForm'])
        ->name('teacher.profile.create');
    Route::post('/teacher/profile-complete', [ProfileCompletionController::class, 'completeTeacherProfile'])
        ->name('teacher.profile.store');
});

/** ----------------------
 *  VERIFIED EMAIL ROUTES
 *  ---------------------*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Email Verification
    Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])->name('email.verify');
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed'])->name('email.verify.link');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')->name('verification.resend');

    // Password Confirmation
    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])->name('password.confirm.store');

    // Profile Management (OTP PROTECTED)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->middleware('otp.required')
        ->name('profile.update');

    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])
        ->middleware('otp.required')
        ->name('profile.change-password');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/** ============================
 *  SECTION 3: DASHBOARDS & ROLE-BASED ROUTES
 *  ============================ */

/** ----------------------
 *  MAIN DASHBOARD
 *  ---------------------*/
Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

/** ----------------------
 *  ROLE DEFINITIONS
 *  ---------------------*/
$publicRoles = ['student', 'teacher', 'client', 'intern', 'volunteer', 'donor', 'corporate'];
$superadminOnlyRoles = [
    'superadmin', 'admin', 'hr', 'finance', 'training', 'exam', 'usermgmt', 'service',
    'consultant', 'partner', 'franchise', 'affiliate'
];
$allRoles = array_merge($publicRoles, $superadminOnlyRoles);

/** ----------------------
 *  SIMPLE ROLE DASHBOARDS
 *  ---------------------*/
foreach ($allRoles as $role) {
    // Skip roles that have full route groups
    if (in_array($role, ['superadmin', 'admin', 'teacher', 'client', 'student', 'corporate'])) {
        continue;
    }

    $prefix = $role === 'usermgmt' ? 'usermgmt' : $role;

    Route::middleware(['auth', "role:$role"])
        ->prefix($prefix)
        ->name("$role.")
        ->group(function () use ($role) {
            Route::get('/dashboard', function () use ($role) {
                $roleView = "{$role}.dashboard";

                if (View::exists($roleView)) {
                    return view($roleView);
                }

                if (View::exists('dashboard')) {
                    return view('dashboard');
                }

                abort(500, "Dashboard view for role '{$role}' not found. Expected view: '{$roleView}' or 'dashboard'.");
            })->name('dashboard');
        });
}

/** ----------------------
 *  SUPERADMIN ROUTES
 *  ---------------------*/
Route::middleware(['auth', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

        // Internal Users Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [InternalUserController::class, 'index'])->name('index');
            Route::get('/create', [InternalUserController::class, 'create'])->name('create');
            Route::post('/', [InternalUserController::class, 'store'])->name('store');

            // Pending Approvals
            Route::get('/pending', [SuperAdminUserController::class, 'pending'])->name('pending');

            // User Actions
            Route::post('/{user}/approve', [SuperAdminUserController::class, 'approve'])->name('approve');
            Route::post('/{user}/reject', [SuperAdminUserController::class, 'reject'])->name('reject');
            Route::post('/{user}/suspend', [SuperAdminUserController::class, 'suspend'])->name('suspend');
            Route::post('/{user}/unsuspend', [SuperAdminUserController::class, 'unsuspend'])->name('unsuspend');

            // User Management
            Route::post('/{user}/toggle-visibility', [SuperAdminUserController::class, 'toggleVisibility'])->name('toggleVisibility');
            Route::post('/{user}/set-sequence', [SuperAdminUserController::class, 'setSequence'])->name('setSequence');
            Route::post('/{user}/move-sequence', [SuperAdminUserController::class, 'moveSequence'])->name('moveSequence');
            Route::post('/{user}/toggle-verified-batch', [SuperAdminUserController::class, 'toggleVerifiedBatch'])->name('toggleVerifiedBatch');
        });

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

        /** ----------------------
         *  SUPERADMIN DEMO ROUTES (Alias)
         *  ---------------------*/
        Route::prefix('admin')->group(function () {
            // Demo Management
            Route::get('/demo-requests', [DemoController::class, 'index'])->name('demo.index');
            Route::get('/demo-requests/{id}', [DemoController::class, 'show'])->name('demo.show');
            Route::post('/demo-requests/{id}/status', [DemoController::class, 'updateStatus'])->name('demo.updateStatus');
            Route::post('/demo-requests/{id}/assign-teacher', [DemoController::class, 'assignTeacher'])->name('demo.assignTeacher');

            // Demo Conversion
            Route::post('/demo-requests/{id}/convert', [DemoController::class, 'manualConvert'])->name('demo.convert');

            // Demo Flag Management
            Route::post('/demo-requests/{id}/flag', [DemoController::class, 'flag'])->name('demo.flag');
            Route::post('/demo-requests/{id}/unflag', [DemoController::class, 'unflag'])->name('demo.unflag');
            Route::delete('/demo-requests/{id}', [DemoController::class, 'destroy'])->name('demo.destroy');

            // Flagged Demos List
            Route::get('/demo-requests/flagged', [DemoController::class, 'flaggedList'])->name('demo.flaggedList');
        });
    });

/** ----------------------
 *  ADMIN ROUTES
 *  ---------------------*/
Route::middleware(['auth', 'role:admin|superadmin'])->prefix('admin')->name('admin.')->group(function () {
    
Route::prefix('document-submissions')
    ->name('document-submissions.')
    ->group(function () {

        Route::get('/', [DocumentSubmissionController::class, 'index'])->name('index');
        Route::get('/{submission}', [DocumentSubmissionController::class, 'show'])->name('show');
        Route::post( '/{submission}/resend-link', [DocumentSubmissionController::class, 'resendLink'] )->name('resend-link');
        Route::post('/request', [DocumentSubmissionController::class, 'store'])->name('store');
        Route::post('/{submission}/expire', [DocumentSubmissionController::class, 'expire'])->name('expire');
    });

Route::prefix('teacher-verifications')
    ->name('teacher-verifications.')
    ->group(function () {

        Route::get('/', [TeacherVerificationController::class, 'index'])->name('index');
        Route::get('/stats', [TeacherVerificationController::class, 'getStats'])->name('stats');

        Route::get('/{id}', [TeacherVerificationController::class, 'show'])->name('show');

        Route::post('/{id}/approve', [TeacherVerificationController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [TeacherVerificationController::class, 'reject'])->name('reject');

        Route::post('/bulk-action', [TeacherVerificationController::class, 'bulkAction'])->name('bulk-action');

        Route::get('/{id}/view', [TeacherVerificationController::class, 'viewDocument'])->name('view');
        Route::get('/{id}/download', [TeacherVerificationController::class, 'downloadDocument'])->name('download');
    });

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard-data', [AdminController::class, 'dashboardData'])->name('dashboard.data');

    // Analytics
    Route::get('/analytics/demos', [\App\Http\Controllers\Admin\DemoAnalyticsController::class, 'index'])->name('analytics.demos');
    Route::get('/analytics/demos/export',[\App\Http\Controllers\Admin\DemoAnalyticsController::class, 'export'])->name('analytics.demos.export');
    Route::get('/analytics/follow-ups',[\App\Http\Controllers\Admin\DemoAnalyticsController::class, 'followUpAnalytics'])->name('analytics.followups');
    Route::get('/analytics/follow-up-trends',[DemoAnalyticsController::class, 'followUpTrends'])->name('analytics.followup.trends');
   Route::get('/analytics/teacher-followups',[DemoAnalyticsController::class, 'teacherFollowUpAccountability'])->name('analytics.teacher.followups');

    // Demo Follow Ups
    Route::get('/demo-follow-ups',[\App\Http\Controllers\Admin\DemoFollowUpController::class, 'index'])->name('demo.followups');
    Route::post('/demo-follow-ups/{id}',[\App\Http\Controllers\Admin\DemoFollowUpController::class, 'update'])->name('demo.followups.update');


    // Demo Operations
    Route::get('/scheduled-demos', [AdminController::class, 'scheduledDemos'])->name('demos.scheduled');
    Route::get('/demo-change-requests', [AdminController::class, 'demoChangeRequests'])->name('demos.changeRequests');
    Route::post('/demo-change-requests/{id}/approve', [AdminController::class, 'approveDemoChangeRequest'])->name('demos.changeRequests.approve');
    Route::post('/demo-change-requests/{id}/reject', [AdminController::class, 'rejectDemoChangeRequest'])->name('demos.changeRequests.reject');
    Route::post('/demos/{id}/outcome',[DemoDecisionController::class, 'setOutcome'])->name('demos.outcome');

    // Teachers Management
    Route::prefix('teachers')->name('teachers.')->group(function () {
        Route::get('/', [AdminController::class, 'teachers'])->name('index');
        Route::get('/filters', [AdminController::class, 'teachersWithFilters'])->name('filters');
        Route::get('/{id}', [AdminController::class, 'showTeacher'])->name('show');
        Route::post('/{id}/request-documents', [AdminController::class, 'requestTeacherDocuments'])->name('request-documents');
        Route::post('/{id}/verify', [AdminController::class, 'verifyTeacher'])->name('verify');
        Route::post('/bulk-verify', [AdminController::class, 'bulkTeacherActions'])->name('bulk-verify');
    });

    // Teacher Creation
    Route::get('/teachers/create', [AdminController::class, 'createTeacher'])->name('teachers.create');
    Route::post('/teachers', [AdminController::class, 'storeTeacher'])->name('teachers.store');

    // Clients Management
    Route::get('/clients', [AdminController::class, 'clients'])->name('clients');
    Route::get('/clients/{id}', [AdminController::class, 'showClient'])->name('clients.show');

    // Services Management
    Route::get('/services', [AdminController::class, 'services'])->name('services');
    Route::get('/services/{id}', [AdminController::class, 'showService'])->name('services.show');
    Route::post('/services/{id}/status', [AdminController::class, 'updateServiceStatus'])->name('services.update-status');

    // Payments Management
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    Route::post('/payments/{id}/payout', [AdminController::class, 'processPayout'])->name('payments.payout');
    Route::post('/payments/bulk-payout', [AdminController::class, 'bulkPayout'])->name('payments.bulk-payout');

    // Client Course Join
    Route::get('/courses/join', [ClientController::class, 'joinCourse'])->name('client.join.course');

    // Users Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{id}/status', [AdminController::class, 'updateUserStatus'])->name('users.update-status');

    // Analytics & Settings
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');

    // Applications Management
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [AdminApplicationController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminApplicationController::class, 'show'])->name('show');
        Route::post('/{id}/verify', [AdminApplicationController::class, 'verifyPayment'])->name('applications.verify');
        Route::post('/{id}/reject', [AdminApplicationController::class, 'rejectPayment'])->name('applications.reject');
        Route::patch('/{id}', [AdminApplicationController::class, 'updateStatus'])->name('applications.update-status');
    });

    /** ----------------------
     *  ADMIN DEMO ROUTES
     *  ---------------------*/
    Route::prefix('demo-requests')->name('demos.')->group(function () {
        Route::get('/', [DemoController::class, 'index'])->name('index');
        Route::get('/{id}', [DemoController::class, 'show'])->name('show');
        Route::post('/{id}/status', [DemoController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{id}/assign-teacher', [DemoController::class, 'assignTeacher'])->name('assignTeacher');
        Route::post('/{id}/flag', [DemoController::class, 'flag'])->name('flag');
        Route::post('/{id}/unflag', [DemoController::class, 'unflag'])->name('unflag');
        Route::delete('/{id}', [DemoController::class, 'destroy'])->name('destroy');
        Route::get('/flagged', [DemoController::class, 'flaggedList'])->name('flaggedList');
    });
});

/** ----------------------
 *  TEACHER ROUTES (FINAL – LOOP SAFE)
 *  ---------------------*/

/*
|--------------------------------------------------------------------------
| TEACHER → PROFILE & PROFILE COMPLETION
| (NO complete.profile middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        // Profile edit (must be accessible even if incomplete)
        Route::get('/profile', [TeacherController::class, 'profile'])
            ->name('profile');

        Route::post('/profile', [TeacherController::class, 'updateProfile'])
            ->name('profile.update');

        // Profile completion
        Route::get(
            '/profile-complete',
            [ProfileCompletionController::class, 'showTeacherProfileForm']
        )->name('profile.complete');

        Route::post(
            '/profile-complete',
            [ProfileCompletionController::class, 'completeTeacherProfile']
        )->name('profile.complete.store');
    });

/*
|--------------------------------------------------------------------------
| TEACHER → PROTECTED BUSINESS ROUTES
| (WITH complete.profile middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:teacher', 'complete.profile:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/stats', [TeacherDashboardController::class, 'getStats'])
            ->name('stats');

        // Calendar & Schedule
        Route::get('/calendar', [TeacherController::class, 'calendar'])
            ->name('calendar');

        Route::get('/daily-schedule', [TeacherController::class, 'getDailySchedule'])
            ->name('daily-schedule');

        // Availability
        Route::get('/availability', [TeacherController::class, 'availability'])
            ->name('availability');

        Route::post('/availability', [AvailabilityController::class, 'store'])
            ->name('availability.store');

        Route::post('/availability/update', [TeacherController::class, 'updateAvailability'])
            ->name('availability.update');

        // Services
        Route::get('/services', [TeacherController::class, 'services'])
            ->name('services');

        Route::get('/services/{id}', [TeacherController::class, 'showService'])
            ->name('services.show');

        Route::post('/services/{id}/status', [TeacherController::class, 'updateServiceStatus'])
            ->name('services.update-status');

        Route::post('/services/{id}/attendance', [TeacherController::class, 'markAttendance'])
            ->name('services.mark-attendance');

        // Earnings & Payouts
        Route::get('/earnings', [TeacherController::class, 'earnings'])
            ->name('earnings');

        Route::post('/payout/request', [TeacherController::class, 'requestPayout'])
            ->name('payout.request');

        // Schedule Management
        Route::get('/schedule', [TeacherController::class, 'schedule'])
            ->name('schedule');

        Route::post('/sessions/{id}/reschedule', [TeacherController::class, 'rescheduleSession'])
            ->name('sessions.reschedule');

        // Analytics
        Route::get('/analytics', [TeacherController::class, 'analytics'])
            ->name('analytics');

        /*
        |--------------------------------------------------------------------------
        | TEACHER → DEMO ROUTES
        |--------------------------------------------------------------------------
        */
        Route::prefix('demo-requests')
            ->name('demo.')
            ->group(function () {

                Route::get('/', [DemoController::class, 'teacherIndex'])
                    ->name('requests');   // teacher.demo.requests

                Route::get('/{id}', [DemoController::class, 'teacherShow'])
                    ->name('show');       // teacher.demo.show

                Route::post('/{id}/convert', [DemoController::class, 'manualConvert'])
                    ->name('convert');

                Route::post('/{id}/not-interested', [DemoController::class, 'markNotInterested'])
                    ->name('markNotInterested');
            });
    });

/** ----------------------
 *  STUDENT ROUTES
 *  ---------------------*/
Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', fn() => view('student.dashboard'))->name('dashboard');

        // Profile
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Courses
        Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

        // Enrollments
        Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');

        // Timetable
        Route::get('/timetable', fn() => redirect()->route('student.dashboard'))->name('timetable.index');

        // Payments
        Route::get('/payments', fn() => redirect()->route('student.dashboard'))->name('payments.index');

        // Certificates
        Route::get('/certificates', fn() => redirect()->route('student.dashboard'))->name('certificates.index');

        // Assignments
        Route::get('/assignments', fn() => redirect()->route('student.dashboard'))->name('assignments.index');

        // Opportunities
        Route::get('/opportunities', fn() => redirect()->route('student.dashboard'))->name('opportunities.index');

        // Support
        Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    });

/** ----------------------
 *  CLIENT ROUTES
 *  ---------------------*/
Route::middleware(['auth', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            return view('client.dashboard');
        })->name('dashboard');

        // Course Join
        Route::get('/courses/join', [ClientController::class, 'joinCourse'])->name('join.course');

        // Payments
        Route::get('/payments', [ClientController::class, 'payments'])->name('payments');

        // Teacher Change Request
        Route::get('/teacher/change', [ClientController::class, 'requestTeacherChange'])->name('change.teacher');

        // Demo History
        Route::get('/demo-history', [\App\Http\Controllers\Client\ClientProfileController::class, 'demoHistory'])
            ->name('demoHistory');
    });

// Client Profile Completion (without EnsureProfileComplete middleware)
Route::middleware(['auth', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/profile-complete', [\App\Http\Controllers\Client\ClientProfileController::class, 'edit'])
            ->name('profile.complete');
        Route::post('/profile-complete', [\App\Http\Controllers\Client\ClientProfileController::class, 'update'])
            ->name('profile.store');
    });

/** ----------------------
 *  CORPORATE ROUTES
 *  ---------------------*/
Route::middleware(['auth', 'role:corporate'])
    ->prefix('corporate')
    ->name('corporate.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', fn() => view('corporate.dashboard'))->name('dashboard');

        // Profile
        Route::get('/profile/edit', [CorporateProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/update', [CorporateProfileController::class, 'update'])->name('profile.update');
    });

/** ============================
 *  SECTION 4: PUBLIC FEATURES
 *  ============================ */
Route::prefix('documents')
    ->name('documents.')
    ->group(function () {

        Route::get('/submit/{token}', [PublicDocumentUploadController::class, 'showForm'])
        ->name('submit.form');

        Route::post('/submit/{token}', [PublicDocumentUploadController::class, 'submit'])
        ->name('submit.store');
    });

/** ----------------------
 *  PUBLIC TEACHER DIRECTORY
 *  ---------------------*/
Route::prefix('teachers')->name('teachers.public.')->group(function () {
    Route::get('/', [TeacherDirectoryController::class, 'index'])->name('index');
    Route::get('/{id}', [TeacherDirectoryController::class, 'show'])->name('show');
    Route::post('/{id}/book', [TeacherDirectoryController::class, 'book'])
        ->middleware('auth')
        ->name('book');
});

/** ----------------------
 *  DEMO BOOKING (PUBLIC)
 *  ---------------------*/
Route::get('/services/book-demo', [DemoController::class, 'create'])->name('demo.create');
Route::post('/services/book-demo', [DemoController::class, 'store'])->name('demo.store');

/** ----------------------
 *  DEMO CHANGE REQUESTS
 *  ---------------------*/
Route::middleware(['auth', 'role:teacher|client'])->group(function () {
    Route::post('/demo-requests/{id}/reschedule', [DemoController::class, 'requestReschedule'])
        ->name('demo.requestReschedule');
    Route::post('/demo-requests/{id}/cancel', [DemoController::class, 'requestCancel'])
        ->name('demo.requestCancel');
});

/** ============================
 *  SECTION 5: SERVICE MANAGEMENT ROUTES
 *  ============================ */

/** ----------------------
 *  SERVICE COORDINATOR DEMO ROUTES
 *  ---------------------*/
Route::middleware(['auth', 'role:superadmin|admin|service'])->group(function () {
    Route::get('/admin/demo-requests', [DemoController::class, 'index'])->name('demo.index');
    Route::get('/admin/demo-requests/{id}', [DemoController::class, 'show'])->name('demo.show');
    Route::post('/admin/demo-requests/{id}/status', [DemoController::class, 'updateStatus'])->name('demo.updateStatus');
    Route::post('/admin/demo-requests/{id}/assign-teacher', [DemoController::class, 'assignTeacher'])->name('demo.assignTeacher');
});

/** ----------------------
 *  USER MANAGEMENT DEMO ROUTES
 *  ---------------------*/
Route::middleware(['auth', 'role:usermgmt|admin|superadmin'])->group(function () {
    Route::post('/admin/demo-requests/{id}/flag', [DemoController::class, 'flag'])->name('demo.flag');
    Route::post('/admin/demo-requests/{id}/unflag', [DemoController::class, 'unflag'])->name('demo.unflag');
    Route::delete('/admin/demo-requests/{id}', [DemoController::class, 'destroy'])->name('demo.destroy');
    Route::get('/admin/demo-requests/flagged', [DemoController::class, 'flaggedList'])->name('demo.flaggedList');
});

/** ============================
 *  SECTION 6: WEBHOOKS & INTEGRATION
 *  ============================ */

/** ----------------------
 *  WEBHOOKS
 *  ---------------------*/
Route::post('/webhooks/document-received', [DocumentCollectionController::class, 'processIncomingDocuments'])
    ->name('webhooks.document-received');

/** ============================
 *  Workshops
 *  ============================ */
Route::post('/workshops/register', [WorkshopRegistrationController::class, 'store'])->name('workshops.register');

Route::middleware(['auth', 'role:SuperAdmin|Admin|Finance|Training Team'])
    ->prefix('admin/workshops')
    ->name('admin.workshops.')
    ->group(function () {

        Route::get('/registrations', [WorkshopRegistrationAdminController::class, 'index'])->name('registrations');
        Route::post('/registrations/{id}/approve-payment', [WorkshopRegistrationAdminController::class, 'approvePayment'])->name('approvePayment');
        Route::post('/registrations/{id}/reject-payment', [WorkshopRegistrationAdminController::class, 'rejectPayment'])->name('rejectPayment');
    });

Route::middleware(['auth', 'role:SuperAdmin|Admin|Training Team'])
    ->prefix('admin/workshops')
    ->name('admin.workshops.')
    ->group(function () {

        Route::post('/registrations/{id}/mark-present', [WorkshopAttendanceController::class, 'markPresent'])->name('markPresent');
        Route::post('/registrations/{id}/mark-absent', [WorkshopAttendanceController::class, 'markAbsent'])->name('markAbsent');
    });
    
    Route::middleware(['auth', 'role:SuperAdmin|Admin|Exam Team'])
    ->post('/admin/workshops/registrations/{id}/generate-certificate',[WorkshopCertificateController::class, 'generate'])->name('admin.workshops.generateCertificate');
    
    Route::get('/verify-certificate', function () { return view('certificates.verify');});
    Route::post('/verify-certificate', function (\Illuminate\Http\Request $request) { $certificate = \App\Models\WorkshopCertificate::where('certificate_no', $request->certificate_no)->firstOrFail();
    return view('certificates.verify-result', compact('certificate'));
});
    
    Route::get('/verify-certificate/{certificate_no}', function($certificate_no) { $certificate = WorkshopCertificate::where('certificate_no', $certificate_no)->firstOrFail();
    return view('certificates.verify-result', compact('certificate')); })->name('verify.certificate.auto');
    
/** ============================
 *  SECTION 7: AUTHENTICATION INCLUDES
 *  ============================ */

/*
|--------------------------------------------------------------------------
| TELEGRAM INLINE APPROVAL / REJECTION (SIGNED + SAFE)
|--------------------------------------------------------------------------
*/
Route::middleware('signed')->group(function () {

    Route::get(
        '/telegram/demo/{id}/approve',
        [DemoDecisionController::class, 'approve']
    )->name('telegram.demo.approve');

    Route::get(
        '/telegram/demo/{id}/reject',
        [DemoDecisionController::class, 'reject']
    )->name('telegram.demo.reject');

});

require __DIR__ . '/auth.php';
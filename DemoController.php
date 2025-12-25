<?php

namespace App\Http\Controllers;

use App\Models\DemoBooking;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DemoController extends Controller
{
    /* =====================================================
        PUBLIC DEMO BOOKING
    ===================================================== */

    public function create()
    {
        return view('demo.book-demo');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'phone'              => 'required|string|max:20',
            'email'              => 'nullable|email|max:255',
            'reason'             => 'required|string',
            'address'            => 'required|string',
            'mode'               => ['required', Rule::in(['online','offline_home','offline_center'])],
            'preferred_date'     => 'required|date',
            'preferred_time'     => 'required',
            'teacher_preference' => ['required', Rule::in(['male','female','no_preference'])],
            'experience_level'   => ['nullable', Rule::in(['beginner','occasional','regular'])],
            'requirements'       => 'nullable|string',
        ]);

        $booking = DemoBooking::create($data);

        try {
            $month = str_pad((int)date('m', strtotime($booking->created_at)), 2, '0', STR_PAD_LEFT);
            $year  = date('Y', strtotime($booking->created_at));
            $seq   = str_pad(10000 + $booking->id, 5, '0', STR_PAD_LEFT);
            $booking->booking_number = "TSVC/DEMO/{$month}/{$year}/{$seq}";
            $booking->save();
        } catch (\Throwable $e) {
            Log::warning('Booking number generation failed: '.$e->getMessage());
        }

        try { $this->sendTelegramToAdmin($booking); } catch (\Throwable $e) {}
        if ($booking->email) {
            try { $this->sendConfirmationEmail($booking); } catch (\Throwable $e) {}
        }

        return back()->with(
            'success',
            'Your demo class request has been received. Our support team will contact you shortly.'
        );
    }

    /* =====================================================
        ADMIN / ROLE BASED LISTING
    ===================================================== */

    public function index()
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['superadmin','admin','service','usermgmt'])) {
            $bookings = DemoBooking::latest()->paginate(25);
        } elseif ($user->hasRole('teacher')) {
            return redirect()->route('demo.teacherIndex');
        } else {
            $bookings = DemoBooking::where('user_id', $user->id)->latest()->paginate(25);
        }

        return view('admin.demo.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking  = DemoBooking::findOrFail($id);
        $teachers = User::where('role','teacher')->where('is_active',1)->get();
        return view('admin.demo.show', compact('booking','teachers'));
    }

    /* =====================================================
        STATUS UPDATE + AUTO CONVERSION
    ===================================================== */

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in([
                'pending','contacted','scheduled','completed',
                'cancel','reschedule',
                'cancel_requested','reschedule_requested'
            ])]
        ]);

        $booking = DemoBooking::findOrFail($id);
        $booking->status = $request->status;
        $booking->save();

        if ($request->status === 'completed') {
            $this->autoConvertToService($booking);
        }

        return back()->with('success','Status updated.');
    }

    /* =====================================================
        ASSIGN TEACHER
    ===================================================== */

    public function assignTeacher(Request $request, $id)
    {
        $request->validate(['teacher_id' => 'required|exists:users,id']);

        $booking = DemoBooking::findOrFail($id);
        $booking->assigned_teacher_id = $request->teacher_id;
        $booking->status = 'scheduled';
        $booking->save();

        try { $this->notifyTeacher($booking); } catch (\Throwable $e) {}
        if ($booking->email) {
            try { $this->sendAssignmentEmailToClient($booking); } catch (\Throwable $e) {}
        }

        return back()->with('success','Teacher assigned successfully.');
    }

    /* =====================================================
        TEACHER VIEWS
    ===================================================== */

    public function teacherIndex()
    {
        abort_unless(Auth::user()->hasRole('teacher'), 403);

        $bookings = DemoBooking::where('assigned_teacher_id', Auth::id())
            ->orderBy('preferred_date')
            ->paginate(25);

        return view('teacher.demo.index', compact('bookings'));
    }

    public function teacherShow($id)
    {
        $booking = DemoBooking::findOrFail($id);
        abort_unless($booking->assigned_teacher_id === Auth::id(), 403);
        return view('teacher.demo.show', compact('booking'));
    }

    /* =====================================================
        🔒 MANUAL CONVERSION
    ===================================================== */

    public function manualConvert($id)
    {
        $booking = DemoBooking::findOrFail($id);
        $user    = auth()->user();

        if (
            $user->hasRole('teacher') &&
            $booking->assigned_teacher_id !== $user->id
        ) {
            abort(403, 'Unauthorized conversion attempt.');
        }

        if (!$user->hasAnyRole(['teacher','admin','superadmin','service'])) {
            abort(403, 'Unauthorized role.');
        }

        if ($booking->converted_to_service) {
            return back()->with('warning', 'Already converted.');
        }

        DB::transaction(function () use ($booking) {

            $service = Service::create([
                'client_id'    => $booking->user_id,
                'teacher_id'   => $booking->assigned_teacher_id,
                'service_type' => 'demo_converted',
                'status'       => 'confirmed',
                'start_date'   => now()->toDateString(),
            ]);

            $booking->update([
                'converted_to_service' => 1,
                'converted_service_id' => $service->id,
                'conversion_mode'      => 'manual',
                'converted_at'         => now(),
            ]);
        });

        return back()->with('success', 'Demo converted to paid service.');
    }

    /* =====================================================
        🚫 MARK DEMO AS NOT INTERESTED
    ===================================================== */

    public function markNotInterested($id)
    {
        $booking = DemoBooking::findOrFail($id);

        if ($booking->converted_to_service) {
            return back()->with('warning', 'Demo already converted.');
        }

        if (
            auth()->user()->hasRole('teacher') &&
            $booking->assigned_teacher_id !== auth()->id()
        ) {
            abort(403, 'Unauthorized action.');
        }

        $booking->update([
            'demo_outcome'          => 'not_interested',
            'converted_to_service' => 0,
            'conversion_mode'      => null,
            'converted_at'         => now(),
        ]);

        return back()->with('success', 'Demo marked as not interested.');
    }

    /* =====================================================
        🔁 AUTO CONVERSION
    ===================================================== */

    private function autoConvertToService(DemoBooking $booking)
    {
        if ($booking->converted_to_service) return;
        if (!$booking->user_id || !$booking->assigned_teacher_id) return;

        $service = Service::create([
            'client_id'    => $booking->user_id,
            'teacher_id'   => $booking->assigned_teacher_id,
            'service_type' => 'demo_converted',
            'mode'         => $booking->mode,
            'status'       => 'confirmed',
            'scheduled_at' => $booking->preferred_date.' '.$booking->preferred_time,
            'created_by'   => 'system',
        ]);

        $booking->update([
            'converted_to_service'  => 1,
            'converted_service_id' => $service->id,
            'conversion_mode'      => 'auto',
            'converted_at'         => now(),
        ]);
    }

    /* =====================================================
        🔒 REQUEST RESCHEDULE / CANCEL
    ===================================================== */

    public function requestReschedule(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $demo = DemoBooking::findOrFail($id);

        if ($demo->status !== 'scheduled') {
            abort(403, 'Invalid demo state.');
        }

        if (
            auth()->user()->hasRole('teacher') &&
            $demo->assigned_teacher_id !== auth()->id()
        ) {
            abort(403, 'Unauthorized action.');
        }

        $demo->update([
            'status'                  => 'reschedule_requested',
            'reschedule_requested_at' => now(),
            'change_request_reason'   => $request->reason,
        ]);
        
        $this->sendTelegramChangeRequest($demo);
        $this->mailAdminChangeRequest($demo, 'Reschedule');

        return back()->with('success', 'Reschedule request sent to admin.');
    }

    public function requestCancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $demo = DemoBooking::findOrFail($id);

        if ($demo->status !== 'scheduled') {
            abort(403, 'Invalid demo state.');
        }

        if (
            auth()->user()->hasRole('teacher') &&
            $demo->assigned_teacher_id !== auth()->id()
        ) {
            abort(403, 'Unauthorized action.');
        }

        $demo->update([
            'status'                => 'cancel_requested',
            'cancel_requested_at'   => now(),
            'change_request_reason' => $request->reason,
        ]);
        
        $this->sendTelegramChangeRequest($demo);
        $this->mailAdminChangeRequest($demo, 'Cancel');

        return back()->with('success', 'Cancellation request sent to admin.');
    }

    /* =====================================================
        NOTIFICATIONS
    ===================================================== */

    private function notifyAdminChangeRequest(DemoBooking $demo, string $type)
    {
        $token  = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        if (!$token || !$chatId) return;

        $msg  = "*Demo {$type} Request*\n\n";
        $msg .= "*Booking:* {$demo->booking_number}\n";
        $msg .= "*Status:* {$demo->status}\n";
        $msg .= "*By:* " . auth()->user()->name . "\n";
        $msg .= "*Reason:* {$demo->change_request_reason}\n";

        Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id'    => $chatId,
            'text'       => $msg,
            'parse_mode' => 'Markdown',
        ]);
    }

    private function mailAdminChangeRequest(DemoBooking $demo, string $type)
    {
        Mail::raw(
            "Demo {$type} Request\n\nBooking: {$demo->booking_number}\nReason: {$demo->change_request_reason}",
            fn ($m) => $m->to(config('mail.from.address'))
                         ->subject("Demo {$type} Request")
        );
    }

    private function sendTelegramToAdmin(DemoBooking $booking)
    {
        $token  = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
        $chatId = config('services.telegram.chat_id') ?: env('TELEGRAM_CHAT_ID');
        if (!$token || !$chatId) return;

        $msg  = "*New Demo Class Request*\n\n";
        $msg .= "*Booking:* {$booking->booking_number}\n";
        $msg .= "*Name:* {$booking->name}\n";
        $msg .= "*Phone:* {$booking->phone}\n";
        $msg .= "*Mode:* {$booking->mode}\n";
        $msg .= "*Date:* {$booking->preferred_date}\n";
        $msg .= "*Time:* {$booking->preferred_time}\n";

        Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id'    => $chatId,
            'text'       => $msg,
            'parse_mode' => 'Markdown'
        ]);
    }
    
    private function sendTelegramChangeRequest(DemoBooking $demo)
{
    $token  = env('TELEGRAM_BOT_TOKEN');
    $chatId = env('TELEGRAM_CHAT_ID');
    if (!$token || !$chatId) return;

    $approveUrl = \URL::signedRoute('telegram.demo.approve', ['id' => $demo->id]);
    $rejectUrl  = \URL::signedRoute('telegram.demo.reject', ['id' => $demo->id]);

    $msg  = "*Demo Change Request*\n\n";
    $msg .= "*Booking:* {$demo->booking_number}\n";
    $msg .= "*Type:* {$demo->status}\n";
    $msg .= "*By:* " . auth()->user()->name . "\n";
    $msg .= "*Reason:* {$demo->change_request_reason}\n";

    Http::asForm()->post(
        "https://api.telegram.org/bot{$token}/sendMessage",
        [
            'chat_id' => $chatId,
            'text' => $msg,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => '✅ Approve', 'url' => $approveUrl],
                        ['text' => '❌ Reject',  'url' => $rejectUrl],
                    ],
                ],
            ]),
        ]
    );
}

    private function sendConfirmationEmail(DemoBooking $booking)
    {
        Mail::send('emails.demo-confirmation', compact('booking'), function ($m) use ($booking) {
            $m->to($booking->email)
              ->subject('Demo Confirmation – '.$booking->booking_number);
        });
    }

    private function sendAssignmentEmailToClient(DemoBooking $booking)
    {
        Mail::send('emails.demo-assigned', compact('booking'), function ($m) use ($booking) {
            $m->to($booking->email)
              ->subject('Teacher Assigned – '.$booking->booking_number);
        });
    }

    private function notifyTeacher(DemoBooking $booking)
    {
        if (!$booking->teacher?->email) return;

        Mail::send('emails.demo-assigned-to-teacher', compact('booking'), function ($m) use ($booking) {
            $m->to($booking->teacher->email)
              ->subject('New Demo Assigned – '.$booking->booking_number);
        });
    }
}

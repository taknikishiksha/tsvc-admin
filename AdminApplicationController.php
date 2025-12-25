<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminApplicationController extends Controller
{
    /**
     * Display a listing of applications
     */
    public function index(Request $request)
    {
        $query = Application::query();

        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Course filter
        if ($request->has('course') && $request->course) {
            $query->where('course', $request->course);
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => Application::count(),
            'pending' => Application::where('status', 'pending')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];

        return view('admin.applications.index', compact('applications', 'stats'));
    }

    /**
     * Show application details
     */
    public function show($id)
    {
        $application = Application::with(['payments'])->findOrFail($id);

        return view('admin.applications.show', compact('application'));
    }

    /**
     * Verify payment for application
     */
    public function verifyPayment(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        $request->validate([
            'payment_verified_by' => 'required|string|max:255',
            'payment_notes' => 'nullable|string|max:500',
        ]);

        $application->update([
            'payment_status' => 'verified',
            'payment_verified_by' => $request->payment_verified_by,
            'payment_verified_at' => now(),
            'payment_notes' => $request->payment_notes,
        ]);

        // Send email notification
        Mail::send('emails.payment-verified', ['application' => $application], function ($message) use ($application) {
            $message->to($application->email)
                   ->subject('Payment Verified - Takniki Shiksha Careers');
        });

        return redirect()->back()->with('success', 'Payment verified successfully and notification sent.');
    }

    /**
     * Reject payment for application
     */
    public function rejectPayment(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $application->update([
            'payment_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'status' => 'rejected',
        ]);

        // Send rejection email
        Mail::send('emails.payment-rejected', ['application' => $application], function ($message) use ($application) {
            $message->to($application->email)
                   ->subject('Payment Rejected - Takniki Shiksha Careers');
        });

        return redirect()->back()->with('success', 'Payment rejected and applicant notified.');
    }

    /**
     * Update application status
     */
    public function updateStatus(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,approved,rejected,on_hold',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $application->status;
        $application->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        // Send status update email if status changed
        if ($oldStatus !== $request->status) {
            Mail::send('emails.application-status-update', ['application' => $application], function ($message) use ($application) {
                $message->to($application->email)
                       ->subject('Application Status Update - Takniki Shiksha Careers');
            });
        }

        return redirect()->back()->with('success', 'Application status updated successfully.');
    }

    /**
     * Export applications to CSV
     */
    public function export(Request $request)
    {
        $applications = Application::when($request->status, function($query, $status) {
            return $query->where('status', $status);
        })->when($request->course, function($query, $course) {
            return $query->where('course', $course);
        })->get();

        $fileName = 'applications_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($applications) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Phone', 'Course', 'Status', 
                'Payment Status', 'Applied Date', 'Payment Verified At'
            ]);

            // Add data rows
            foreach ($applications as $application) {
                fputcsv($file, [
                    $application->id,
                    $application->name,
                    $application->email,
                    $application->phone,
                    $application->course,
                    $application->status,
                    $application->payment_status,
                    $application->created_at->format('Y-m-d H:i:s'),
                    $application->payment_verified_at?->format('Y-m-d H:i:s') ?? 'Not Verified'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk update application status
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id',
            'status' => 'required|in:approved,rejected,on_hold',
            'bulk_notes' => 'nullable|string|max:500',
        ]);

        $applications = Application::whereIn('id', $request->application_ids)->get();

        foreach ($applications as $application) {
            $application->update([
                'status' => $request->status,
                'admin_notes' => $request->bulk_notes,
            ]);
        }

        return redirect()->back()->with('success', "Successfully updated {$applications->count()} applications.");
    }
}
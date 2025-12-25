<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherVerificationController extends Controller
{
    // List all teacher verifications
    public function index()
    {
        // Fetch teacher verifications from DB
        // Yahan appropriate model call karna
        return view('teacher-verifications.index'); // Corresponding view create karen
    }

    // Show stats (optional)
    public function getStats()
    {
        // Statistics logic
        return response()->json([
            'total' => 100,
            'verified' => 80,
            'pending' => 20,
        ]);
    }

    // Show analytics (optional)
    public function analytics()
    {
        return view('teacher-verifications.analytics');
    }

    // Show a specific teacher verification details
    public function show($id)
    {
        // Fetch verification record by $id
        return view('teacher-verifications.show', compact('id'));
    }

    // View a specific document attached for verification
    public function viewDocument($id)
    {
        // Logic to show document (e.g. file streaming)
        return response()->file(storage_path("app/public/verification_docs/{$id}.pdf"));
    }

    // Download a specific document attached for verification
    public function downloadDocument($id)
    {
        return response()->download(storage_path("app/public/verification_docs/{$id}.pdf"));
    }

    // Approve a verification request
    public function approve(Request $request, $id)
    {
        // Logic to approve
        // For example: update DB status to 'approved'
        return back()->with('success', 'Verification approved.');
    }

    // Reject a verification request
    public function reject(Request $request, $id)
    {
        // Logic to reject
        // Update status and maybe save reason from request
        return back()->with('success', 'Verification rejected.');
    }

    // Bulk action on multiple verification requests
    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids'); // expects array of IDs
        $action = $request->input('action'); // 'approve' or 'reject'

        // Loop over IDs and apply actions
        foreach ($ids as $id) {
            if ($action === 'approve') {
                // update DB for approval
            } elseif ($action === 'reject') {
                // update DB for rejection
            }
        }

        return back()->with('success', "Bulk action '$action' performed on selected verifications.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\DocumentSubmission;
use App\Models\YogaTeacher;
use App\Models\Document;
use App\Services\FileValidationService;
use App\Mail\DocumentRequestMail;
use App\Mail\DocumentReceivedMail;
use App\Mail\DocumentVerifiedMail;
use App\Mail\DocumentReminderMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentCollectionController extends Controller
{
    protected $fileValidationService;

    public function __construct(FileValidationService $fileValidationService)
    {
        $this->fileValidationService = $fileValidationService;
    }

    /**
     * Request documents from teacher via email
     */
    public function requestDocuments(Request $request, $teacherId)
    {
        $teacher = YogaTeacher::with('user')->findOrFail($teacherId);

        $request->validate([
            'documents' => 'required|array',
            'documents.*' => 'in:aadhar_front,aadhar_back,ycb_certificate,police_verification,profile_photo,educational_certificate',
            'instructions' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($teacher, $request) {
            // Create document submission record
            $submission = DocumentSubmission::createForTeacher(
                $teacher, 
                $request->documents
            );

            // Send document request email
            $this->sendDocumentRequestEmail($submission, $request->instructions);

            // Update submission with email sent timestamp
            $submission->update(['request_sent_at' => now()]);
        });

        return redirect()->back()->with('success', 'Document request sent to teacher via email successfully.');
    }

    /**
     * Send document request email to teacher
     */
    private function sendDocumentRequestEmail(DocumentSubmission $submission, $instructions = null)
    {
        try {
            Mail::send(new DocumentRequestMail($submission, $submission->teacher, $submission->teacher->user, $instructions));
        } catch (\Exception $e) {
            \Log::error('Failed to send document request email: ' . $e->getMessage());
        }
    }

    /**
     * Process incoming documents via email (Webhook for email processing)
     */
    public function processIncomingDocuments(Request $request)
    {
        \Log::info('Document webhook received', $request->all());

        $request->validate([
            'from_email' => 'required|email',
            'subject' => 'required|string',
            'attachments' => 'required|array',
            'attachments.*.filename' => 'required|string',
            'attachments.*.content' => 'required|string',
            'attachments.*.mime_type' => 'required|string',
            'message_id' => 'required|string',
        ]);

        // Find teacher by email
        $teacher = YogaTeacher::whereHas('user', function($query) use ($request) {
            $query->where('email', $request->from_email);
        })->first();

        if (!$teacher) {
            \Log::warning('Teacher not found for email: ' . $request->from_email);
            return response()->json(['error' => 'Teacher not found'], 404);
        }

        // Find active document submission
        $submission = DocumentSubmission::where('user_id', $teacher->user_id)
            ->where('status', 'pending')
            ->where('is_expired', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$submission) {
            \Log::warning('No active document submission found for teacher: ' . $teacher->id);
            return response()->json(['error' => 'No active document submission found'], 404);
        }

        $processedCount = 0;

        foreach ($request->attachments as $attachment) {
            try {
                $this->processSingleAttachment(
                    $submission, 
                    $attachment, 
                    $request->message_id
                );
                $processedCount++;
            } catch (\Exception $e) {
                \Log::error('Failed to process attachment: ' . $e->getMessage(), [
                    'filename' => $attachment['filename'],
                    'submission_id' => $submission->id
                ]);
            }
        }

        // Send confirmation email to teacher
        if ($processedCount > 0) {
            $this->sendDocumentReceivedEmail($submission, $processedCount);
        }

        \Log::info('Document processing completed', [
            'submission_id' => $submission->id,
            'processed_count' => $processedCount,
            'total_attachments' => count($request->attachments)
        ]);

        return response()->json([
            'message' => "Processed {$processedCount} documents successfully",
            'submission_id' => $submission->id,
        ]);
    }

    /**
     * Process a single document attachment
     */
    private function processSingleAttachment($submission, $attachment, $messageId)
    {
        // Validate file security
        $validationResult = $this->fileValidationService->validateFile(
            $attachment['content'],
            $attachment['filename'],
            $attachment['mime_type']
        );

        if (!$validationResult['is_safe']) {
            throw new \Exception('File failed security validation: ' . $validationResult['message']);
        }

        // Determine document type from filename or other heuristics
        $documentType = $this->determineDocumentType(
            $attachment['filename'],
            $submission->requested_documents
        );

        // Create document record
        $document = Document::create([
            'document_submission_id' => $submission->id,
            'teacher_id' => $submission->teacher_id,
            'document_type' => $documentType,
            'original_filename' => $attachment['filename'],
            'file_size' => $validationResult['file_size'],
            'mime_type' => $attachment['mime_type'],
            'email_message_id' => $messageId,
            'is_safe' => true,
            'virus_scan_result' => $validationResult['scan_result'],
            'file_hash' => $validationResult['file_hash'],
            'storage_reference' => $this->generateStorageReference($submission, $documentType),
        ]);

        // Update submission progress
        $this->updateSubmissionProgress($submission);

        return $document;
    }

    /**
     * Determine document type from filename and requested documents
     */
    private function determineDocumentType($filename, $requestedDocuments)
    {
        $filename = strtolower($filename);
        
        // Simple heuristics
        if (str_contains($filename, 'aadhar') || str_contains($filename, 'uidai')) {
            if (str_contains($filename, 'front')) return 'aadhar_front';
            if (str_contains($filename, 'back')) return 'aadhar_back';
            return 'aadhar_front';
        }
        
        if (str_contains($filename, 'ycb') || str_contains($filename, 'certificate')) {
            return 'ycb_certificate';
        }
        
        if (str_contains($filename, 'police') || str_contains($filename, 'verification')) {
            return 'police_verification';
        }
        
        if (str_contains($filename, 'profile') || str_contains($filename, 'photo')) {
            return 'profile_photo';
        }
        
        if (str_contains($filename, 'educational') || str_contains($filename, 'degree')) {
            return 'educational_certificate';
        }
        
        // Return first requested document type as fallback
        return $requestedDocuments[0] ?? 'unknown';
    }

    /**
     * Generate storage reference
     */
    private function generateStorageReference($submission, $documentType)
    {
        return "email_attachment://{$submission->id}/{$documentType}/" . time();
    }

    /**
     * Update submission progress
     */
    private function updateSubmissionProgress($submission)
    {
        $receivedCount = $submission->documents()->count();
        $submission->update([
            'documents_received' => $receivedCount,
            'status' => $receivedCount >= $submission->documents_required ? 'submitted' : 'pending',
            'submission_received_at' => $receivedCount >= $submission->documents_required ? now() : null,
        ]);
    }

    /**
     * Send document received confirmation email
     */
    private function sendDocumentReceivedEmail(DocumentSubmission $submission, $documentCount)
    {
        try {
            Mail::send(new DocumentReceivedMail($submission, $submission->teacher, $submission->teacher->user, $documentCount));
        } catch (\Exception $e) {
            \Log::error('Failed to send document received email: ' . $e->getMessage());
        }
    }

    /**
     * Admin: View document submissions
     */
    public function index(Request $request)
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

    /**
     * Admin: Show submission details
     */
    public function show($id)
    {
        $submission = DocumentSubmission::with(['teacher.user', 'documents', 'reviewer'])
            ->findOrFail($id);

        return view('admin.document-submissions.show', compact('submission'));
    }

    /**
     * Admin: Verify documents
     */
    public function verifyDocuments(Request $request, $submissionId)
    {
        $submission = DocumentSubmission::with('documents')->findOrFail($submissionId);

        $request->validate([
            'status' => 'required|in:verified,rejected',
            'review_notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($submission, $request) {
            if ($request->status === 'verified') {
                // Mark all documents as verified
                $submission->documents()->update(['status' => 'verified']);
                
                // Update teacher verification status
                $submission->teacher->update([
                    'verification_status' => 'verified',
                ]);
                
                $submission->update([
                    'status' => 'verified',
                    'verified_at' => now(),
                    'reviewed_by' => auth()->id(),
                    'review_notes' => $request->review_notes,
                    'reviewed_at' => now(),
                ]);

                // Send verification email
                $this->sendVerificationEmail($submission);
            } else {
                $submission->update([
                    'status' => 'rejected',
                    'reviewed_by' => auth()->id(),
                    'review_notes' => $request->review_notes,
                    'reviewed_at' => now(),
                ]);
            }
        });

        return redirect()->back()->with('success', 'Documents verification completed.');
    }

    /**
     * Send verification completion email
     */
    private function sendVerificationEmail(DocumentSubmission $submission)
    {
        try {
            Mail::send(new DocumentVerifiedMail($submission, $submission->teacher, $submission->teacher->user));
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email: ' . $e->getMessage());
        }
    }

    /**
     * Send reminder email (for DocumentStatusTracker)
     */
    public function sendReminderEmail(DocumentSubmission $submission)
    {
        try {
            Mail::send(new DocumentReminderMail($submission, $submission->teacher, $submission->teacher->user, $submission->getDaysUntilExpiry()));
        } catch (\Exception $e) {
            \Log::error('Failed to send reminder email: ' . $e->getMessage());
        }
    }

    /**
     * Resend document request email
     */
    public function resendRequestEmail($submissionId)
    {
        $submission = DocumentSubmission::with(['teacher.user'])->findOrFail($submissionId);

        $this->sendDocumentRequestEmail($submission);

        return redirect()->back()->with('success', 'Document request email resent successfully.');
    }
}
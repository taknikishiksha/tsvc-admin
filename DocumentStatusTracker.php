<?php

namespace App\Services;

use App\Models\DocumentSubmission;
use App\Models\YogaTeacher;
use App\Mail\DocumentRequestMail;
use Illuminate\Support\Facades\Mail;

class DocumentStatusTracker
{
    /**
     * Get overall document submission statistics
     */
    public function getSubmissionStats()
    {
        return [
            'total_submissions' => DocumentSubmission::count(),
            'pending' => DocumentSubmission::where('status', 'pending')->count(),
            'submitted' => DocumentSubmission::where('status', 'submitted')->count(),
            'under_review' => DocumentSubmission::where('status', 'under_review')->count(),
            'verified' => DocumentSubmission::where('status', 'verified')->count(),
            'rejected' => DocumentSubmission::where('status', 'rejected')->count(),
            'expired' => DocumentSubmission::where('is_expired', true)->count(),
        ];
    }

    /**
     * Get teacher document status
     */
    public function getTeacherDocumentStatus($teacherId)
    {
        $teacher = YogaTeacher::with(['documentSubmissions.documents'])->find($teacherId);

        if (!$teacher) {
            return null;
        }

        $currentSubmission = $teacher->documentSubmissions()
            ->where('is_expired', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        $requiredDocuments = [
            'aadhar_front' => 'Aadhar Card (Front)',
            'aadhar_back' => 'Aadhar Card (Back)',
            'ycb_certificate' => 'YCB Certificate',
            'police_verification' => 'Police Verification',
            'profile_photo' => 'Profile Photo',
            'educational_certificate' => 'Educational Certificate',
        ];

        $documentStatus = [];
        $submittedDocuments = [];

        if ($currentSubmission) {
            $submittedDocuments = $currentSubmission->documents->pluck('document_type')->toArray();
        }

        foreach ($requiredDocuments as $docType => $docName) {
            $documentStatus[$docType] = [
                'name' => $docName,
                'submitted' => in_array($docType, $submittedDocuments),
                'verified' => $this->isDocumentVerified($currentSubmission, $docType),
                'required' => $currentSubmission ? in_array($docType, $currentSubmission->requested_documents) : false,
            ];
        }

        return [
            'teacher' => $teacher,
            'current_submission' => $currentSubmission,
            'document_status' => $documentStatus,
            'progress_percentage' => $currentSubmission ? $currentSubmission->getProgressPercentage() : 0,
            'days_until_expiry' => $currentSubmission ? $currentSubmission->getDaysUntilExpiry() : null,
            'overall_status' => $this->getOverallStatus($teacher, $currentSubmission),
        ];
    }

    /**
     * Check if specific document is verified
     */
    private function isDocumentVerified($submission, $documentType)
    {
        if (!$submission) {
            return false;
        }

        $document = $submission->documents->where('document_type', $documentType)->first();
        return $document && $document->status === 'verified';
    }

    /**
     * Get overall document status for teacher
     */
    private function getOverallStatus($teacher, $currentSubmission)
    {
        if ($teacher->verification_status === 'verified') {
            return 'verified';
        }

        if (!$currentSubmission) {
            return 'not_started';
        }

        if ($currentSubmission->isExpired()) {
            return 'expired';
        }

        return $currentSubmission->status;
    }

    /**
     * Send reminder emails for pending submissions - UPDATED MAIL METHOD
     */
    public function sendReminderEmails()
    {
        $submissionsNeedingReminder = DocumentSubmission::needsReminder()->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($submissionsNeedingReminder as $submission) {
            try {
                $this->sendReminderEmail($submission);
                $sentCount++;
                
                \Log::info('Document reminder sent', [
                    'submission_id' => $submission->id,
                    'teacher_id' => $submission->teacher_id,
                ]);

            } catch (\Exception $e) {
                $failedCount++;
                \Log::error('Failed to send reminder email', [
                    'submission_id' => $submission->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'sent' => $sentCount,
            'failed' => $failedCount,
            'total' => $submissionsNeedingReminder->count(),
        ];
    }

    /**
     * Send individual reminder email - UPDATED MAIL METHOD
     */
    private function sendReminderEmail(DocumentSubmission $submission)
    {
        $teacher = $submission->teacher;
        $user = $teacher->user;

        // OLD: Mail::send('emails.document-reminder', [...], function ($message) {...});
        // NEW: Use Mailable class with Mail::to()->send()
        Mail::to($user->email)->send(
            new DocumentRequestMail(
                $submission,
                $teacher,
                $user,
                'Reminder: Your document submission is due in ' . $submission->getDaysUntilExpiry() . ' days.'
            )
        );
    }

    /**
     * Get submission timeline
     */
    public function getSubmissionTimeline($submissionId)
    {
        $submission = DocumentSubmission::with(['documents', 'reviewer'])->find($submissionId);

        if (!$submission) {
            return [];
        }

        $timeline = [];

        // Submission created
        $timeline[] = [
            'event' => 'submission_created',
            'title' => 'Document Request Sent',
            'description' => 'Document submission request was created',
            'timestamp' => $submission->created_at,
            'user' => 'System',
        ];

        // Email sent
        if ($submission->request_sent_at) {
            $timeline[] = [
                'event' => 'email_sent',
                'title' => 'Request Email Sent',
                'description' => 'Document request email sent to teacher',
                'timestamp' => $submission->request_sent_at,
                'user' => 'System',
            ];
        }

        // Documents received
        if ($submission->documents->isNotEmpty()) {
            foreach ($submission->documents as $document) {
                $timeline[] = [
                    'event' => 'document_received',
                    'title' => 'Document Received: ' . $this->getDocumentTypeName($document->document_type),
                    'description' => "File: {$document->original_filename}",
                    'timestamp' => $document->created_at,
                    'user' => 'Teacher',
                ];
            }
        }

        // Submission marked as submitted
        if ($submission->submission_received_at) {
            $timeline[] = [
                'event' => 'submission_complete',
                'title' => 'All Documents Submitted',
                'description' => 'Teacher has submitted all required documents',
                'timestamp' => $submission->submission_received_at,
                'user' => 'System',
            ];
        }

        // Review actions
        if ($submission->reviewed_at) {
            $timeline[] = [
                'event' => 'review_completed',
                'title' => 'Documents ' . ucfirst($submission->status),
                'description' => $submission->review_notes ?: 'Documents reviewed by admin',
                'timestamp' => $submission->reviewed_at,
                'user' => $submission->reviewer->name ?? 'Admin',
            ];
        }

        // Verification completed
        if ($submission->verified_at) {
            $timeline[] = [
                'event' => 'verification_complete',
                'title' => 'Teacher Verified',
                'description' => 'Teacher verification process completed successfully',
                'timestamp' => $submission->verified_at,
                'user' => 'System',
            ];
        }

        // Sort timeline by timestamp
        usort($timeline, function ($a, $b) {
            return $a['timestamp'] <=> $b['timestamp'];
        });

        return $timeline;
    }

    /**
     * Get document type display name
     */
    private function getDocumentTypeName($documentType)
    {
        $names = [
            'aadhar_front' => 'Aadhar Card (Front)',
            'aadhar_back' => 'Aadhar Card (Back)',
            'ycb_certificate' => 'YCB Certificate',
            'police_verification' => 'Police Verification',
            'profile_photo' => 'Profile Photo',
            'educational_certificate' => 'Educational Certificate',
        ];

        return $names[$documentType] ?? ucfirst(str_replace('_', ' ', $documentType));
    }

    /**
     * Generate document submission report
     */
    public function generateReport($startDate = null, $endDate = null)
    {
        $query = DocumentSubmission::with(['teacher.user', 'documents', 'reviewer']);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $submissions = $query->get();

        $report = [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'summary' => $this->getSubmissionStats(),
            'submissions' => $submissions->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'teacher_name' => $submission->teacher->user->name,
                    'teacher_email' => $submission->teacher->user->email,
                    'status' => $submission->status,
                    'documents_requested' => $submission->documents_required,
                    'documents_received' => $submission->documents_received,
                    'progress_percentage' => $submission->getProgressPercentage(),
                    'submission_date' => $submission->created_at->format('Y-m-d H:i'),
                    'expiry_date' => $submission->expires_at->format('Y-m-d H:i'),
                    'reviewer' => $submission->reviewer->name ?? null,
                    'review_date' => $submission->reviewed_at?->format('Y-m-d H:i'),
                ];
            }),
        ];

        return $report;
    }

    /**
     * Check and update expired submissions
     */
    public function updateExpiredSubmissions()
    {
        $expiredSubmissions = DocumentSubmission::where('expires_at', '<', now())
            ->where('is_expired', false)
            ->get();

        $updatedCount = 0;

        foreach ($expiredSubmissions as $submission) {
            $submission->update([
                'is_expired' => true,
                'status' => 'expired'
            ]);
            $updatedCount++;
        }

        return $updatedCount;
    }

    /**
     * Get submissions needing immediate attention
     */
    public function getUrgentSubmissions()
    {
        return DocumentSubmission::with(['teacher.user'])
            ->where('status', 'pending')
            ->where('expires_at', '<=', now()->addDays(2))
            ->where('expires_at', '>', now())
            ->orderBy('expires_at', 'asc')
            ->get()
            ->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'teacher_name' => $submission->teacher->user->name,
                    'teacher_email' => $submission->teacher->user->email,
                    'days_until_expiry' => $submission->getDaysUntilExpiry(),
                    'progress_percentage' => $submission->getProgressPercentage(),
                    'expires_at' => $submission->expires_at,
                ];
            });
    }
}
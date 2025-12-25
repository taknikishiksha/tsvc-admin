<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\DocumentSubmission;

class DocumentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $teacher;
    public $user;
    public $daysRemaining;

    /**
     * Create a new message instance.
     */
    public function __construct(DocumentSubmission $submission, $teacher, $user, $daysRemaining)
    {
        $this->submission = $submission;
        $this->teacher = $teacher;
        $this->user = $user;
        $this->daysRemaining = $daysRemaining;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Reminder: Document Submission Required - Takniki Shiksha Careers')
                   ->view('emails.document-reminder')
                   ->replyTo('info@taknikishiksha.org.in');
    }
}
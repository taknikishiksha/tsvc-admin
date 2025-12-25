<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\DocumentSubmission;

class DocumentReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $teacher;
    public $user;
    public $documentCount;

    /**
     * Create a new message instance.
     */
    public function __construct(DocumentSubmission $submission, $teacher, $user, $documentCount)
    {
        $this->submission = $submission;
        $this->teacher = $teacher;
        $this->user = $user;
        $this->documentCount = $documentCount;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Documents Received - Takniki Shiksha Careers')
                   ->view('emails.document-received');
    }
}
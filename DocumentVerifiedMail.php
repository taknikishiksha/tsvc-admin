<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\DocumentSubmission;

class DocumentVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $teacher;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(DocumentSubmission $submission, $teacher, $user)
    {
        $this->submission = $submission;
        $this->teacher = $teacher;
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Verification Complete - Takniki Shiksha Careers')
                   ->view('emails.document-verified');
    }
}
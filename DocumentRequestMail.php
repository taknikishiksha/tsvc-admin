<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $teacher;
    public $user;
    public $instructions;

    /**
     * Create a new message instance.
     */
    public function __construct($submission, $teacher, $user, $instructions = null)
    {
        $this->submission = $submission;
        $this->teacher = $teacher;
        $this->user = $user;
        $this->instructions = $instructions;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Document Submission Required - Takniki Shiksha Careers')
                   ->view('emails.document-request')
                   ->replyTo('info@taknikishiksha.org.in');
    }
}
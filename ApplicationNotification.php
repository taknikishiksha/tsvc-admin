<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;

class ApplicationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $type;

    public function __construct(Application $application, $type = 'hr')
    {
        $this->application = $application;
        $this->type = $type;
    }

    public function build()
    {
        if ($this->type === 'hr') {
            // HR ke liye email
            return $this->subject('🎯 New Application Received - ' . $this->application->registration_number)
                        ->view('emails.application-hr')
                        ->with(['application' => $this->application]);
        } else {
            // Candidate ke liye email
            return $this->subject('✅ Application Submitted Successfully - ' . $this->application->registration_number)
                        ->view('emails.application-candidate')
                        ->with(['application' => $this->application]);
        }
    }
}
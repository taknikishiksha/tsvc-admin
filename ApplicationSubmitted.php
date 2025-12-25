<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $isApplicant = false;

    public function __construct(Application $application, $isApplicant = false)
    {
        $this->application = $application;
        $this->isApplicant = $isApplicant;
    }

    public function build()
    {
        $subject = $this->isApplicant 
            ? 'आवेदन प्राप्ति की पुष्टि - Takniki Shiksha Careers'
            : 'नया आवेदन प्राप्त हुआ - ' . $this->application->name;

        return $this->subject($subject)
            ->view('emails.application-submitted')
            ->with(['application' => $this->application, 'isApplicant' => $this->isApplicant]);
    }
}
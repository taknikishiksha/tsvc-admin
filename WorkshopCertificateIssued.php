<?php

namespace App\Mail;

use App\Models\WorkshopCertificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkshopCertificateIssued extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $certificate;

    public function __construct(WorkshopCertificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public function build()
    {
        $downloadUrl = asset('storage/' . $this->certificate->certificate_path);
        $verifyUrl   = route('verify.certificate.auto', $this->certificate->certificate_no);

        return $this->from('certificates@taknikishikshacareers.org.in', 'TSVC Certificates')
            ->subject('Your Workshop Certificate is Ready')
            ->view('emails.workshop-certificate-issued')
            ->with([
                'name'           => $this->certificate->registration->name,
                'workshop'       => $this->certificate->registration->workshop->title,
                'certificate_no' => $this->certificate->certificate_no,
                'downloadUrl'    => $downloadUrl,
                'verifyUrl'      => $verifyUrl,
            ]);
    }
}

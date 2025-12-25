<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationNotification;
use App\Models\Application;

class EmailController extends Controller
{
    public function sendApplicationEmail($applicationId)
    {
        try {
            $application = Application::findOrFail($applicationId);
            
            // HR ko email bhejein
            Mail::to('hr@taknikishiksha.org.in')->send(new ApplicationNotification($application, 'hr'));
            
            // Candidate ko confirmation email bhejein  
            Mail::to($application->email)->send(new ApplicationNotification($application, 'candidate'));
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }
}
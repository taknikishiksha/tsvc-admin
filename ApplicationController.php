<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationNotification;

class ApplicationController extends Controller
{
    public function create()
    {
        return view('apply.index');
    }

    public function store(Request $request)
    {
        Log::info('=== APPLICATION FORM SUBMISSION STARTED ===');

        try {
            // Validate all fields
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|digits:10',
                'address' => 'required|string|max:500',
                'qualification' => 'required|string|max:100',
                'application_type' => 'required|in:job,internship,course',
                'transaction_id' => 'required|string|max:100',
                'documents_sent' => 'required|accepted',
                'transaction_screenshot' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
            ]);

            Log::info('Form validation passed');

            // Handle file upload
            $filePath = null;
            if ($request->hasFile('transaction_screenshot')) {
                $file = $request->file('transaction_screenshot');
                
                // Create uploads directory if not exists
                $uploadPath = public_path('uploads');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Generate unique filename
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                
                // Move file to uploads directory
                $file->move($uploadPath, $fileName);
                $filePath = 'uploads/' . $fileName;
                
                Log::info('File uploaded successfully: ' . $filePath);
            }

            // Generate unique registration number
            do {
                $registrationNumber = 'TSVC-' . strtoupper(Str::random(6));
            } while (Application::where('registration_number', $registrationNumber)->exists());

            Log::info('Generated registration number: ' . $registrationNumber);

            // Create application record
            $application = Application::create([
                'registration_number' => $registrationNumber,
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'address' => $validatedData['address'],
                'qualification' => $validatedData['qualification'],
                'application_type' => $validatedData['application_type'],
                'documents_sent' => true,
                'transaction_id' => $validatedData['transaction_id'],
                'transaction_screenshot' => $filePath,
                'registration_fee' => 500,
                'payment_status' => 'pending',
                'status' => 'pending',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent() ?? 'Unknown'
            ]);

            Log::info('Application created successfully. ID: ' . $application->id);

            // ✅ SEND EMAIL NOTIFICATIONS
            try {
                // HR ko email
                Mail::to('hr@taknikishiksha.org.in')->send(new ApplicationNotification($application, 'hr'));
                Log::info('HR email sent successfully');
                
                // Candidate ko email
                Mail::to($application->email)->send(new ApplicationNotification($application, 'candidate'));
                Log::info('Candidate email sent successfully');
                
            } catch (\Exception $emailError) {
                Log::error('Email sending failed: ' . $emailError->getMessage());
                // Email fail hone par bhi success response dein
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully!',
                'registration_number' => $registrationNumber,
                'redirect_url' => url('/')  // Home page redirect
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Please check all required fields: ' . implode(', ', array_keys($e->errors()))
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Application submission failed: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'message' => 'Application submission failed. Please try again.'
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecruiterApplication;
use Illuminate\Support\Facades\Mail;

class RecruiterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'state' => 'required|string|max:100',
            'experience' => 'nullable|string|max:100',
            'message' => 'nullable|string',
        ]);

        // डेटाबेस में सेव करें
        RecruiterApplication::create($data);

        // मेल भेजें
        Mail::raw("New Recruiter Application:\n\n" . print_r($data, true), function ($message) use ($data) {
            $message->to('hr@taknikishiksha.org.in')
                    ->subject('New Recruiter Application from ' . $data['name']);
        });

        return redirect()->back()->with('success', 'Application submitted successfully.');
    }
}

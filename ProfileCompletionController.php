<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TeacherProfile;
use App\Models\Client;
use App\Models\Document;
use Illuminate\Support\Str;

class ProfileCompletionController extends Controller
{
    /**
     * ============================
     * TEACHER PROFILE COMPLETION
     * ============================
     */

    public function showTeacherProfileForm()
    {
        $user = Auth::user();

        if ($user->teacherProfile) {
            return redirect()->route('teacher.dashboard')
                ->with('info', 'Your profile is already completed.');
        }

        return view('teacher.profile.profile-completion');
    }

    public function completeTeacherProfile(Request $request)
    {
        $user = Auth::user();

        if ($user->teacherProfile) {
            return redirect()->route('teacher.dashboard');
        }

        /* ============================
         | VALIDATION (UPDATED)
         ============================ */
        $validated = $request->validate([
            'teacher_type'       => 'required|in:volunteer,organization',

            'qualification'      => 'nullable|string|max:1000',
            'experience_years'   => 'nullable|integer|min:0|max:80',
            'bio'                => 'required|string|min:20|max:3000',

            'specializations'    => 'nullable|string|max:1000',
            'languages'          => 'nullable|string|max:500',
            'certifications'     => 'nullable|string|max:1000',
            'locations_covered'  => 'nullable|string|max:1000',

            'hourly_rate'        => 'nullable|numeric|min:0|max:10000',
            'max_clients'        => 'nullable|integer|min:1|max:500',

            'working_days'       => 'nullable|array',
            'working_days.*'     => 'string',

            'service_types'      => 'nullable|array',
            'service_types.*'    => 'string',

            'shift_start'        => 'nullable|date_format:H:i',
            'shift_end'          => 'nullable|date_format:H:i',

            'ycb_certified'      => 'nullable|boolean',

            'documents.*'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        /* ============================
         | CREATE / UPDATE PROFILE
         ============================ */
        $profile = TeacherProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'qualifications'      => $validated['qualification'] ?? null,
                'experience_years'    => $validated['experience_years'] ?? null,
                'bio'                 => $validated['bio'],

                'specializations'     => $this->toJsonArray($validated['specializations'] ?? null),
                'languages'           => $this->toJsonArray($validated['languages'] ?? null),
                'certifications'      => $this->toJsonArray($validated['certifications'] ?? null),
                'locations_covered'   => $this->toJsonArray($validated['locations_covered'] ?? null),

                'hourly_rate'         => $validated['hourly_rate'] ?? null,
                'max_clients'         => $validated['max_clients'] ?? 5,

                'working_days'        => $validated['working_days'] ?? [],
                'service_types'       => $validated['service_types'] ?? [],

                'shift_start'         => $validated['shift_start'] ?? null,
                'shift_end'           => $validated['shift_end'] ?? null,

                'teacher_type'        => $validated['teacher_type'],   // 🔑 MASTER SWITCH
                'employment_status'   => 'active',

                'ycb_certified'       => $validated['ycb_certified'] ?? 0,
                'verification_status' => 'pending',
            ]
        );

        /* ============================
         | DOCUMENT UPLOAD
         ============================ */
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {

                $original = $file->getClientOriginalName();
                $filename = time() . '_' . Str::slug(
                    pathinfo($original, PATHINFO_FILENAME)
                ) . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs(
                    'public/documents/' . $user->id,
                    $filename
                );

                Document::create([
                    'user_id'       => $user->id,
                    'type'          => 'teacher_profile',
                    'original_name' => $original,
                    'path'          => $path,
                ]);
            }
        }

        return redirect()->route('teacher.dashboard')
            ->with('success', 'Teacher profile completed successfully!');
    }

    /**
     * ============================
     * CLIENT PROFILE COMPLETION
     * ============================
     */

    public function showClientProfileForm()
    {
        $user = Auth::user();

        if ($user->clientProfile) {
            return redirect()->route('client.dashboard')
                ->with('info', 'Your profile is already completed.');
        }

        return view('client.profile-completion');
    }

    public function completeClientProfile(Request $request)
    {
        $user = Auth::user();

        if ($user->clientProfile) {
            return redirect()->route('client.dashboard');
        }

        $validated = $request->validate([
            'health_issues'       => 'nullable|string',
            'medical_history'     => 'nullable|string',
            'yoga_goals'          => 'required|string|max:500',
            'experience_level'    => 'required|string|max:20',
            'preferences'         => 'nullable|string|max:500',
            'service_type'        => 'required|string|max:50',

            'emergency_contact_name'   => 'required|string|max:191',
            'emergency_contact_phone'  => 'required|string|max:15',
            'emergency_relation'       => 'required|string|max:191',
        ]);

        Client::create([
            'user_id'                => $user->id,
            'health_issues'          => $validated['health_issues'] ?? null,
            'medical_history'        => $validated['medical_history'] ?? null,
            'yoga_goals'             => $validated['yoga_goals'],
            'experience_level'       => $validated['experience_level'],
            'preferences'            => $validated['preferences'] ?? null,
            'service_type'           => $validated['service_type'],
            'emergency_contact_name' => $validated['emergency_contact_name'],
            'emergency_contact_phone'=> $validated['emergency_contact_phone'],
            'emergency_relation'     => $validated['emergency_relation'],
        ]);

        return redirect()->route('client.dashboard')
            ->with('success', 'Client profile completed successfully!');
    }

    /**
     * Helper: CSV → Array
     */
    private function toJsonArray($csv)
    {
        if (!$csv) return null;

        $arr = array_filter(array_map('trim', explode(',', $csv)));
        return empty($arr) ? null : $arr;
    }
}

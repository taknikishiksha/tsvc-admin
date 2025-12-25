<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class PublicRegistrationController extends Controller
{
    /**
     * If you prefer to drive public roles from config, leave this empty and we will
     * load from config('registration.public_roles') in constructor.
     */
    protected $publicRoles = [];

    public function __construct()
    {
        // load public roles from config -> allows central control
        $this->publicRoles = array_map(fn($r) => strtolower(trim($r)), config('registration.public_roles', [
            'client','teacher','student','partner','consultant','volunteer','intern','donor','corporate','affiliate'
        ]));
    }

    /**
     * Show registration form for a public role.
     */
    public function show($role)
    {
        $role = is_string($role) ? strtolower(trim($role)) : $role;

        if (! in_array($role, $this->publicRoles)) {
            abort(404);
        }

        // SEO defaults for the registration page (blade can use $seo)
        $seo = [
            'title'       => ucfirst($role) . ' Registration | Takniki Shiksha Careers',
            'description' => 'Register as '.ucfirst($role).' on Takniki Shiksha Careers to access tailored features, dashboards and services.',
            'keywords'    => 'yoga, registration, '. $role .', careers, takniki shiksha',
        ];

        return view('auth.register-role', compact('role','seo'));
    }

    /**
     * Store registration for a public role.
     */
    public function store(Request $request, $role)
    {
        $role = is_string($role) ? strtolower(trim($role)) : $role;

        // Allow role passed in request (hidden input) but normalize it.
        if ($reqRole = $request->input('role')) {
            $reqRole = is_string($reqRole) ? strtolower(trim($reqRole)) : $reqRole;
            // prefer route role but ensure both match or request role is acceptable
            if ($reqRole !== $role) {
                // if request role differs, prefer the route role but still validate against allowed list
                $request->merge(['role' => $role]);
            } else {
                $request->merge(['role' => $reqRole]);
            }
        } else {
            $request->merge(['role' => $role]);
        }

        if (! in_array($role, $this->publicRoles)) {
            return back()->withInput()->withErrors(['role' => 'Invalid role selected.']);
        }

        $rules = $this->validationRulesFor($role);

        // Use Validator so we can log failures, and return back with input (consistent UX)
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info('Public registration validation failed', [
                'role' => $role,
                'errors' => $validator->errors()->all(),
                'input_sample' => array_intersect_key($request->all(), array_flip(['organization_name','organization_address','organization','email','phone']))
            ]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        DB::beginTransaction();
        $tempFiles = []; // store temporary uploaded paths to delete later

        try {
            $password = $data['password'] ?? Str::random(10);

            // create user
            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'] ?? null,
                'phone'     => $data['phone'] ?? null,
                'password'  => Hash::make($password),
                'role'      => $role,
                'is_active' => 1,
            ]);

            // If teacher and yoga_teachers table exists, insert lightweight record
            if ($role === 'teacher' && Schema::hasTable('yoga_teachers')) {
                DB::table('yoga_teachers')->insert([
                    'user_id'               => $user->id,
                    'bio'                   => $data['bio'] ?? null,
                    'specializations'       => !empty($data['specializations']) ? json_encode($this->normalizeToArray($data['specializations'])) : null,
                    'languages'             => !empty($data['languages']) ? json_encode($this->normalizeToArray($data['languages'])) : null,
                    'certifications'        => !empty($data['certifications']) ? json_encode($this->normalizeToArray($data['certifications'])) : null,
                    'experience_years'      => (int)($data['experience_years'] ?? 0),
                    'hourly_rate'           => isset($data['hourly_rate']) ? (float)$data['hourly_rate'] : 0,
                    'service_types'         => !empty($data['service_types']) ? json_encode($this->normalizeToArray($data['service_types'])) : null,
                    'locations_covered'     => !empty($data['locations_covered']) ? json_encode($this->normalizeToArray($data['locations_covered'])) : null,
                    'max_clients'           => (int)($data['max_clients'] ?? 5),
                    'verification_status'   => 'pending',
                    'police_verified'       => false,
                    'ycb_certified'         => (bool)($data['ycb_certified'] ?? false),
                    'verification_documents'=> null,
                    'rating'                => 0,
                    'total_ratings'         => 0,
                    'completed_sessions'    => 0,
                    'earnings_total'        => 0,
                    'is_available'          => true,
                    'working_days'          => !empty($data['working_days']) ? json_encode($this->normalizeToArray($data['working_days'])) : null,
                    'shift_start'           => $data['shift_start'] ?? null,
                    'shift_end'             => $data['shift_end'] ?? null,
                    'loyalty_points'        => 0,
                    'level'                 => 'bronze',
                    'commission_rate'       => 70.00,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);
            }

            // Minimal JSON profile for partner/corporate/consultant if users table has 'profile' column
            if (in_array($role, ['partner','corporate','consultant']) && Schema::hasColumn('users','profile')) {
                $user->profile = [
                    // allow multiple possible field names from forms
                    'organization_name'    => $data['organization_name'] ?? $data['organization'] ?? null,
                    'organization_address' => $data['organization_address'] ?? $data['organization_address'] ?? null,
                    'gst_number'           => $data['gst_number'] ?? $data['gst'] ?? null,
                ];
                $user->save();
            }

            // Handle uploaded documents (if any) - temporarily store, attach to admin email, then delete
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    if ($file && $file->isValid()) {
                        $tmpPath = $file->store('tmp'); // storage/app/tmp
                        $tempFiles[] = storage_path('app/' . $tmpPath);
                    }
                }
            }

            // === OTP GENERATION & STORAGE (DB + SESSION + CACHE fallback) ===
            $otp = null;
            if (!empty($user->email)) {
                $otp = random_int(100000, 999999);

                // Prefer DB columns if present
                if (Schema::hasColumn('users', 'otp_code') && Schema::hasColumn('users', 'otp_expires_at')) {
                    $user->otp_code = (string)$otp;
                    $user->otp_expires_at = now()->addMinutes(15);
                    $user->save();
                } else {
                    // fallback to cache
                    Cache::put('registration_otp:'.$user->id, $otp, now()->addMinutes(15));
                }

                // MUST: also store into session so verification page can use it
                session([
                    'otp_user_id'    => $user->id,
                    'otp_code'       => (string)$otp,
                    'otp_expires_at' => now()->addMinutes(15),
                ]);
            }

            // Send role routed email (with attachments if any) to internal mailbox
            $to      = $this->roleMailbox($role);
            $subject = 'New '.ucfirst($role).' Registration - Takniki Shiksha Careers';

            try {
                Mail::send([], [], function ($message) use ($to, $subject, $user, $role, $data, $tempFiles) {
                    $message->to($to)
                        ->subject($subject)
                        ->setBody($this->mailBody($user, $role, $data), 'text/plain');

                    foreach ($tempFiles as $filePath) {
                        if (file_exists($filePath)) {
                            $message->attach($filePath);
                        }
                    }
                });
            } catch (\Throwable $m) {
                Log::warning('Registration email failed (admin): '.$m->getMessage());
            }

            // Send OTP email to user
            if ($otp && !empty($user->email)) {
                try {
                    Mail::send([], [], function ($message) use ($user, $otp) {
                        $message->to($user->email)
                                ->subject('Verify your email - Takniki Shiksha Careers')
                                ->setBody("Hello {$user->name},\n\nYour OTP is: {$otp}\nIt will expire in 15 minutes.\n\nIf you didn't request this, ignore this message.", 'text/plain');
                    });
                } catch (\Throwable $m) {
                    Log::warning('Registration OTP email failed (user): '.$m->getMessage());
                }
            }

            DB::commit();

            // clean temporary files (best-effort)
            foreach ($tempFiles as $p) {
                try { @unlink($p); } catch (\Throwable $e) { /* ignore */ }
            }

            // Redirect to OTP verify page if OTP generated, else show success and ask to login
            if ($otp) {
                session()->flash('success', 'Registration successful. Please verify the OTP sent to your email.');
                return redirect()->route('verification.otp');
            } else {
                session()->flash('success', 'Registration successful. Please login to continue.');
                return redirect()->route('login');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration error: '.$e->getMessage());

            foreach ($tempFiles as $p) {
                try { @unlink($p); } catch (\Throwable $ex) { /* ignore */ }
            }

            return back()->withInput()->withErrors(['error' => 'Registration failed, please try again.']);
        }
    }

    /**
     * Validation rules for each role.
     * Accepts both CSV strings and arrays for multi-value fields.
     */
    protected function validationRulesFor($role)
    {
        $base = [
            'name'     => ['required','string','max:255'],
            'phone'    => ['required','string','max:20'],
            'email'    => ['nullable','email','max:255', function($attr, $value, $fail) {
                if ($value && User::where('email', $value)->exists()) {
                    $fail('The email has already been taken.');
                }
            }],
            'password' => ['nullable','string','min:6','confirmed'],
            'documents.*' => ['file','max:10240'],
        ];

        switch ($role) {
            case 'teacher':
                return array_merge($base, [
                    'bio'               => ['nullable','string'],
                    'specializations'   => ['nullable'],
                    'languages'         => ['nullable'],
                    'certifications'    => ['nullable'],
                    'experience_years'  => ['nullable','integer','min:0','max:80'],
                    'hourly_rate'       => ['nullable','numeric','min:0'],
                    'service_types'     => ['nullable'],
                    'locations_covered' => ['nullable'],
                    'max_clients'       => ['nullable','integer','min:1','max:50'],
                    'ycb_certified'     => ['nullable','boolean'],
                    'working_days'      => ['nullable'],
                    'shift_start'       => ['nullable'],
                    'shift_end'         => ['nullable'],
                ]);

            case 'student':
                return array_merge($base, [
                    'course_selected' => ['required','string','max:255'],
                    'dob'             => ['nullable','date'],
                ]);

            case 'partner':
            case 'corporate':
            case 'consultant':
                return array_merge($base, [
                    // accept both organization_name (preferred) and organization (legacy)
                    'organization_name'    => ['required','string','max:255'],
                    'organization_address' => ['nullable','string'],
                    'gst_number'           => ['nullable','string','max:50'],
                ]);

            case 'intern':
                return array_merge($base, [
                    'college'     => ['required','string','max:255'],
                    'intern_type' => ['required','in:college,job'],
                ]);

            case 'volunteer':
                return array_merge($base, [
                    'address'        => ['required','string'],
                    'available_days' => ['nullable'],
                ]);

            case 'donor':
                return array_merge($base, [
                    'donation_interest' => ['nullable','string','max:255'],
                ]);

            case 'client':
            case 'affiliate':
            default:
                return $base;
        }
    }

    /**
     * Normalize input that can be CSV string or array.
     */
    protected function normalizeToArray($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map(fn($v) => trim($v), $value)));
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') return [];
            return array_values(array_filter(array_map('trim', explode(',', $value))));
        }

        return [];
    }

    /**
     * Role -> mailbox mapping
     */
    protected function roleMailbox(string $role): string
    {
        $map = [
            'teacher'    => 'info@taknikishiksha.org.in',
            'volunteer'  => 'info@taknikishiksha.org.in',
            'student'    => 'hr@taknikishiksha.org.in',
            'intern'     => 'hr@taknikishiksha.org.in',
            'consultant' => 'hr@taknikishiksha.org.in',
            'client'     => 'enquiry@taknikishiksha.org.in',
            'corporate'  => 'enquiry@taknikishiksha.org.in',
            'partner'    => 'enquiry@taknikishiksha.org.in',
            'donor'      => 'sales@taknikishiksha.org.in',
            'affiliate'  => 'admin@taknikishiksha.org.in',
        ];

        return $map[$role] ?? (config('mail.from.address') ?? 'noreply.tsvccareers@gmail.com');
    }

    /**
     * Text body used for admin notification email
     */
    protected function mailBody(User $user, string $role, array $data): string
    {
        $lines = [
            "New ".ucfirst($role)." registration on Takniki Shiksha Careers",
            "----------------------------------------",
            "Name:  ".$user->name,
            "Email: ".($user->email ?? '-'),
            "Phone: ".($user->phone ?? '-'),
            "Role:  ".$role,
            "Registered at: ".now()->toDateTimeString(),
        ];

        if ($role === 'teacher') {
            $lines[] = "";
            $lines[] = "Teacher quick profile:";
            $lines[] = "Experience (years): ".($data['experience_years'] ?? '0');
            $lines[] = "Specializations: ".(!empty($data['specializations']) ? (is_array($data['specializations']) ? implode(', ', $data['specializations']) : $data['specializations']) : '-');
            $lines[] = "Languages: ".(!empty($data['languages']) ? (is_array($data['languages']) ? implode(', ', $data['languages']) : $data['languages']) : '-');
            $lines[] = "Service types: ".(!empty($data['service_types']) ? (is_array($data['service_types']) ? implode(', ', $data['service_types']) : $data['service_types']) : '-');
            $lines[] = "Locations covered: ".($data['locations_covered'] ?? '-');
            $lines[] = "Hourly rate: ".($data['hourly_rate'] ?? '-');
            $lines[] = "YCB certified: ".(!empty($data['ycb_certified']) ? 'Yes' : 'No');
        }

        return implode("\n", $lines);
    }
}

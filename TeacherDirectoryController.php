<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

// IMPORTANT: Adjust these model imports to match your app.
// Assuming TeacherProfile = model for "yoga_teachers" table
use App\Models\TeacherProfile;

class TeacherDirectoryController extends Controller
{
    /**
     * PUBLIC LIST with advanced filters.
     *
     * Query params supported:
     * q              : generic search (name, bio, certifications, specializations)
     * name           : teacher name exact/like
     * pincode        : 110036, etc.
     * city           : any city string found in locations_covered/bio/etc.
     * district       : district name
     * state          : state/region
     * country        : country
     * village        : village/area
     * specialization : any specialization keyword
     * language       : any language keyword
     * mode           : home|online|corporate|group
     * day            : mon|tue|wed|thu|fri|sat|sun
     * min_rate       : numeric
     * max_rate       : numeric
     * min_exp        : integer years
     * max_exp        : integer years
     * sort           : rating|price_asc|price_desc|exp_desc|exp_asc
     */
    public function index(Request $request)
    {
        $filters = [
            'q'             => trim($request->input('q', '')),
            'name'          => trim($request->input('name', '')),
            'pincode'       => trim($request->input('pincode', '')),
            'city'          => trim($request->input('city', '')),
            'district'      => trim($request->input('district', '')),
            'state'         => trim($request->input('state', '')),
            'country'       => trim($request->input('country', '')),
            'village'       => trim($request->input('village', '')),
            'specialization'=> trim($request->input('specialization', '')),
            'language'      => trim($request->input('language', '')),
            'mode'          => trim($request->input('mode', '')),
            'day'           => trim($request->input('day', '')),
            'min_rate'      => $request->input('min_rate'),
            'max_rate'      => $request->input('max_rate'),
            'min_exp'       => $request->input('min_exp'),
            'max_exp'       => $request->input('max_exp'),
            'sort'          => $request->input('sort', 'rating'),
        ];

        $query = TeacherProfile::query()
            ->with(['user:id,name,email,phone'])
            ->where('verification_status', 'verified')
            ->where('is_available', true);

        // generic search across common text/json columns
        if ($filters['q'] !== '') {
            $q = $filters['q'];
            $query->where(function($sub) use ($q) {
                $sub->where('bio', 'like', "%{$q}%")
                    ->orWhere('certifications', 'like', "%{$q}%")
                    ->orWhere('specializations', 'like', "%{$q}%")
                    ->orWhere('languages', 'like', "%{$q}%")
                    ->orWhere('service_types', 'like', "%{$q}%")
                    ->orWhere('locations_covered', 'like', "%{$q}%");
            });
        }

        // teacher name
        if ($filters['name'] !== '') {
            $name = $filters['name'];
            $query->whereHas('user', function($u) use ($name) {
                $u->where('name', 'like', "%{$name}%");
            });
        }

        // location-like filters (all LIKE against locations_covered JSON/text)
        foreach (['pincode','city','district','state','country','village'] as $locKey) {
            if ($filters[$locKey] !== '') {
                $val = $filters[$locKey];
                $query->where('locations_covered', 'like', "%{$val}%");
            }
        }

        if ($filters['specialization'] !== '') {
            $query->where('specializations', 'like', "%{$filters['specialization']}%");
        }

        if ($filters['language'] !== '') {
            $query->where('languages', 'like', "%{$filters['language']}%");
        }

        if ($filters['mode'] !== '' && in_array($filters['mode'], ['home','online','corporate','group'])) {
            $query->where('service_types', 'like', "%{$filters['mode']}%");
        }

        if ($filters['day'] !== '' && in_array($filters['day'], ['mon','tue','wed','thu','fri','sat','sun'])) {
            $query->where('working_days', 'like', "%{$filters['day']}%");
        }

        if (!is_null($filters['min_rate'])) {
            $query->where('hourly_rate', '>=', (float)$filters['min_rate']);
        }
        if (!is_null($filters['max_rate'])) {
            $query->where('hourly_rate', '<=', (float)$filters['max_rate']);
        }

        if (!is_null($filters['min_exp'])) {
            $query->where('experience_years', '>=', (int)$filters['min_exp']);
        }
        if (!is_null($filters['max_exp'])) {
            $query->where('experience_years', '<=', (int)$filters['max_exp']);
        }

        // sorting
        switch ($filters['sort']) {
            case 'price_asc':  $query->orderBy('hourly_rate', 'asc'); break;
            case 'price_desc': $query->orderBy('hourly_rate', 'desc'); break;
            case 'exp_desc':   $query->orderBy('experience_years', 'desc'); break;
            case 'exp_asc':    $query->orderBy('experience_years', 'asc'); break;
            default:           $query->orderBy('rating', 'desc'); // rating (default)
        }

        $teachers = $query->paginate(12)->withQueryString();

        return view('teachers.public.index', [
            'teachers' => $teachers,
            'filters'  => $filters,
        ]);
    }

    public function show($id)
    {
        $teacher = TeacherProfile::with(['user:id,name,email,phone'])
            ->where('verification_status', 'verified')
            ->findOrFail($id);

        $maskedEmail = $this->maskEmail(optional($teacher->user)->email);
        $maskedPhone = $this->maskPhone(optional($teacher->user)->phone);

        return view('teachers.public.show', compact('teacher','maskedEmail','maskedPhone'));
    }

    public function book(Request $request, $id)
    {
        $teacher = TeacherProfile::with('user:id,name,email,phone')
            ->where('verification_status', 'verified')
            ->findOrFail($id);

        $request->validate([
            'date' => ['required','date','after_or_equal:today'],
            'session_type' => ['required','in:online,offline'],
            'agree' => ['accepted']
        ],[
            'agree.accepted' => 'Please accept terms & conditions to proceed.'
        ]);

        $user = Auth::user();

        $adminEmail = env('ADMIN_EMAIL', config('mail.from.address'));
        $subject = "New Booking Request — {$teacher->user->name} ({$request->session_type})";

        $lines = [
            "Teacher: {$teacher->user->name} (Teacher ID: {$teacher->id})",
            "Date: {$request->date}",
            "Session Type: {$request->session_type}",
            "Requested by: {$user->name} (User ID: {$user->id})",
            "Email: " . ($user->email ?? 'N/A'),
            "Phone: " . ($user->phone ?? 'N/A'),
            "Notes: " . ($request->input('notes','')),
        ];

        try {
            Mail::raw(implode("\n", $lines), function($m) use ($adminEmail, $subject) {
                $m->to($adminEmail)->subject($subject);
            });
        } catch (\Throwable $e) {
            // ignore silently in UI
        }

        return back()->with('status', 'Request submitted. Our team will contact you to confirm the session.');
    }

    private function maskEmail($email)
    {
        if (!$email) return 'xxxxxxxx@xxxxx.xxx';
        [$name,$domain] = array_pad(explode('@', $email), 2, '');
        $nameMasked = strlen($name) <= 2
            ? str_repeat('x', strlen($name))
            : substr($name,0,2) . str_repeat('x', max(0, strlen($name)-2));
        $domainMasked = preg_replace('/[^\.]/', 'x', $domain);
        return "{$nameMasked}@{$domainMasked}";
    }

    private function maskPhone($phone)
    {
        if (!$phone) return 'xxxxxx' . rand(1000,9999);
        $len = strlen($phone);
        if ($len <= 4) return str_repeat('x', $len);
        return str_repeat('x', max(0, $len-4)) . substr($phone, -4);
    }
}

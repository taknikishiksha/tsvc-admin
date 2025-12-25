<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    use HasFactory;

    protected $table = 'teacher_profiles';

    protected $fillable = [
        'user_id',

        /* =========================
         | BASIC / REGISTRATION
         ========================= */
        'qualifications',
        'qualification', // legacy compatibility
        'experience_years',
        'bio',
        'specializations',
        'specialization', // legacy
        'languages',
        'certifications',

        /* =========================
         | LOCATION & SERVICE
         ========================= */
        'state',
        'city',
        'locations_covered',
        'service_types',
        'working_days',
        'max_clients',

        /* =========================
         | AVAILABILITY
         ========================= */
        'shift_start',
        'shift_end',
        'availability_status',

        /* =========================
         | PRICING (VOLUNTEER / FREELANCE)
         ========================= */
        'hourly_rate',

        /* =========================
         | ORGANIZATION TEACHER (NEW – HR / FINANCE)
         ========================= */
        'teacher_type',          // volunteer | organization
        'employment_status',     // active | inactive | on_hold
        'salary_type',           // fixed | commission | hybrid
        'base_salary',
        'commission_percent',
        'esi_applicable',
        'pf_applicable',

        /* =========================
         | VERIFICATION & VISIBILITY
         ========================= */
        'ycb_certified',
        'verified',
        'visibility_score',

        /* =========================
         | FILES & MEDIA
         ========================= */
        'profile_photo',
        'resume_path',

        /* =========================
         | LEGACY (DO NOT REMOVE)
         ========================= */
        'teaching_style',
    ];

    protected $casts = [
        /* Arrays */
        'specializations'   => 'array',
        'languages'         => 'array',
        'certifications'    => 'array',
        'locations_covered' => 'array',
        'service_types'     => 'array',
        'working_days'      => 'array',

        /* Booleans */
        'ycb_certified'     => 'boolean',
        'verified'          => 'boolean',
        'esi_applicable'    => 'boolean',
        'pf_applicable'     => 'boolean',

        /* Numbers */
        'base_salary'        => 'float',
        'commission_percent' => 'float',
        'hourly_rate'        => 'float',
        'visibility_score'   => 'integer',
    ];

    /* =========================
     | RELATIONSHIPS
     ========================= */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function availabilities()
    {
        return $this->hasMany(TeacherAvailability::class);
    }

    public function verifications()
    {
        return $this->hasMany(TeacherVerification::class);
    }

    /* =========================
     | QUERY SCOPES
     ========================= */
    public function scopeAvailable($query)
    {
        return $query->where('availability_status', 'available');
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->whereJsonContains('languages', $language);
    }

    public function scopeFeatured($query)
    {
        return $query->available()
            ->where('visibility_score', '>=', 80)
            ->orderByDesc('visibility_score');
    }

    public function scopeOrganizationTeachers($query)
    {
        return $query->where('teacher_type', 'organization');
    }

    public function scopeVolunteerTeachers($query)
    {
        return $query->where('teacher_type', 'volunteer');
    }
}

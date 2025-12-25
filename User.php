<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',   // → synced with Spatie role
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'pincode',
        'profile_photo',
        'is_admin',
        'is_active',
        'email_verified_at',
        'phone_verified_at',
        'last_login_at',

        'status',
        'profile_visible',
        'profile_sequence',
        'verified_batch',
        'approved_by',
        'approved_at',
        'profile_completion_percent',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'date_of_birth'     => 'date',
        'password'          => 'hashed',
        'is_admin'          => 'boolean',
        'is_active'         => 'boolean',
        'profile_visible'   => 'boolean',
        'verified_batch'    => 'boolean',
        'approved_at'       => 'datetime',
        'profile_sequence'  => 'integer',
        'profile_completion_percent' => 'integer',
    ];

    /* -----------------------------
     * Auto-normalize role BEFORE save
     * ----------------------------- */
    public function setRoleAttribute($value)
    {
        if (is_string($value)) {
            $value = strtolower(trim($value));
        }

        // store normalized value
        $this->attributes['role'] = $value;

        // also sync Spatie role (if exists)
        if ($this->exists) {
            try {
                $this->syncRoles([$value]);
            } catch (\Throwable $e) {
                \Log::warning("Role sync failed in User model: " . $e->getMessage());
            }
        }
    }

    /* -----------------------------
     * Sync Spatie role AFTER user created
     * ----------------------------- */
    protected static function booted()
    {
        static::created(function ($user) {
            if (!empty($user->role)) {
                try {
                    $user->assignRole($user->role);
                } catch (\Throwable $e) {
                    \Log::warning("Role assign failed on create: " . $e->getMessage());
                }
            }
        });
    }

    /* -----------------------------
     * Relationships
     * ----------------------------- */
    public function teacherProfile() { return $this->hasOne(\App\Models\TeacherProfile::class, 'user_id'); }
    public function studentProfile() { return $this->hasOne(\App\Models\StudentProfile::class, 'user_id'); }
    public function clientProfile() { return $this->hasOne(\App\Models\ClientProfile::class, 'user_id'); }
    public function internProfile() { return $this->hasOne(\App\Models\InternProfile::class, 'user_id'); }
    public function volunteerProfile() { return $this->hasOne(\App\Models\VolunteerProfile::class, 'user_id'); }
    public function donorProfile() { return $this->hasOne(\App\Models\DonorProfile::class, 'user_id'); }
    public function corporateProfile() { return $this->hasOne(\App\Models\CorporateProfile::class, 'user_id'); }

    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }

    /* -----------------------------
     * Role helpers (clean)
     * ----------------------------- */
    public function hasRoleName($role)
    {
        return $this->role === strtolower($role);
    }

    public function isSuperAdmin() { return $this->hasRoleName('superadmin'); }
    public function isAdmin()      { return $this->hasRoleName('admin') || $this->is_admin; }

    /* -----------------------------
     * Dashboard Routes
     * ----------------------------- */
    public function getDashboardRoute(): string
    {
        return match ($this->role) {
            'superadmin' => 'superadmin.dashboard',
            'admin'      => 'admin.dashboard',
            'hr'         => 'hr.dashboard',
            'finance'    => 'finance.dashboard',
            'training'   => 'training.dashboard',
            'exam'       => 'exam.dashboard',
            'usermgmt'   => 'usermgmt.dashboard',
            'service'    => 'service.dashboard',
            'client'     => 'client.dashboard',
            'teacher'    => 'teacher.dashboard',
            'student'    => 'student.dashboard',
            'partner'    => 'partner.dashboard',
            'franchise'    => 'franchise.dashboard',
            'consultant' => 'consultant.dashboard',
            'volunteer'  => 'volunteer.dashboard',
            'intern'     => 'intern.dashboard',
            'donor'      => 'donor.dashboard',
            'corporate'  => 'corporate.dashboard',
            'affiliate'    => 'affiliate.dashboard',
            default      => 'dashboard',
        };
    }

    /* -----------------------------
     * Profile Fetcher
     * ----------------------------- */
    public function getProfile()
    {
        return match ($this->role) {
            'teacher'   => $this->teacherProfile,
            'student'   => $this->studentProfile,
            'client'    => $this->clientProfile,
            'intern'    => $this->internProfile,
            'volunteer' => $this->volunteerProfile,
            'donor'     => $this->donorProfile,
            'corporate' => $this->corporateProfile,
            default     => null,
        };
    }

    /* -----------------------------
     * Scopes
     * ----------------------------- */
    public function scopeActive($query) { return $query->where('is_active', 1); }
    public function scopeByRole($query, $role) { return $query->where('role', strtolower($role)); }
}

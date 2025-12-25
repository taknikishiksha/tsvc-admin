<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientProfile extends Model
{
    use HasFactory;

    protected $table = 'client_profiles';

    /**
     * Fillable fields (expand if you add more columns later)
     */
    protected $fillable = [
        'user_id',

        // profile / booking preferences
        'service_type',             // home/online/group/corporate
        'class_type',               // online/offline/home/group
        'preferred_teacher_level',  // beginner/intermediate/advanced
        'experience_level',         // beginner/intermediate/advanced

        // scheduling
        'start_date',
        'preferred_days',           // text like "Mon/Wed/Fri, 7-8 AM"

        // health
        'health_issues',
        'medical_history',
        'medical_conditions',

        // goals
        'yoga_goals',               // JSON / array
        'yoga_goals_other',         // free text if "other"

        // emergency
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_relation',

        // admin / tracking
        'assigned_teacher_id',      // user_id of assigned teacher
        'referral_code',
        'special_discounts',
        'improvement_summary',
        'billing_info',             // JSON (optional)
    ];

    /**
     * Casts
     */
    protected $casts = [
        'yoga_goals' => 'array',
        'start_date' => 'date',
        'billing_info' => 'array',
        // leave special_discounts and improvement_summary as text (no cast)
    ];

    /**
     * Relationships
     */

    /**
     * ClientProfile belongs to the owning User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Assigned teacher relation.
     * Note: assumes client_profiles.assigned_teacher_id holds the teacher's user id.
     * If your schema uses a different column name, update the foreign key here.
     */
    public function assignedTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_teacher_id');
    }

    /**
     * Bookings for this client (one-to-many).
     * Adjust Booking::class and foreign key if your booking model/table uses different names.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(\App\Models\Booking::class, 'client_id');
    }

    /**
     * Payments made by this client (one-to-many).
     * Adjust Payment::class and foreign key if your payments table differs.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(\App\Models\Payment::class, 'user_id');
    }

    /**
     * Helpers
     */

    /**
     * Return true when profile has required minimum fields to be considered complete.
     * Adjust the required fields to suit your business rules.
     */
    public function isComplete(): bool
    {
        $required = [
            'service_type',
            'experience_level',
            'emergency_contact_name',
            'emergency_contact_phone',
        ];

        foreach ($required as $f) {
            if (empty($this->{$f})) {
                return false;
            }
        }

        return true;
    }

    /**
     * Human friendly list of goals (array of strings).
     * Ensures we always return array even if DB stored JSON string or CSV.
     */
    public function getGoalsListAttribute(): array
    {
        if (is_array($this->yoga_goals)) {
            return $this->yoga_goals;
        }

        if (is_null($this->yoga_goals)) {
            return [];
        }

        // if stored as JSON string
        if (is_string($this->yoga_goals)) {
            $decoded = json_decode($this->yoga_goals, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            // fallback: try CSV
            $arr = array_filter(array_map('trim', explode(',', $this->yoga_goals)));
            return array_values($arr);
        }

        return [];
    }

    /**
     * Short helper returning assigned teacher or null (alias)
     */
    public function getAssignedTeacherAttribute()
    {
        return $this->assignedTeacher()->first();
    }
}

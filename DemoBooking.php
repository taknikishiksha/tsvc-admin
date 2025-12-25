<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class DemoBooking
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $assigned_teacher_id
 * @property string|null $booking_number
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property string $reason
 * @property string $address
 * @property string $mode
 * @property Carbon|null $preferred_date
 * @property string|null $preferred_time
 * @property string $teacher_preference
 * @property string|null $experience_level
 * @property string|null $requirements
 * @property string|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class DemoBooking extends Model
{
    /**
     * =========================
     * MASS ASSIGNMENT
     * =========================
     */
    protected $fillable = [
        'user_id',
        'assigned_teacher_id',
        'booking_number',
        'name',
        'phone',
        'email',
        'reason',
        'address',
        'mode',
        'preferred_date',
        'preferred_time',
        'teacher_preference',
        'experience_level',
        'requirements',
        'status',
    ];

    /**
     * =========================
     * CASTS
     * =========================
     */
    protected $casts = [
        'preferred_date' => 'date',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    /**
     * User who booked the demo (Client / Student)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Assigned Teacher (TeacherProfile)
     *
     * NOTE:
     * assigned_teacher_id = users.id
     * teacher_profiles.user_id = users.id
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(
            TeacherProfile::class,
            'assigned_teacher_id',
            'user_id'
        );
    }

    /**
     * =========================
     * SCOPES (OPTIONAL BUT USEFUL)
     * =========================
     */

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}

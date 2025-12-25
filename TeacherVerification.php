<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'document_type',
        'document_path',
        'status',
        'submitted_at',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'resubmission_instructions',
        'admin_notes',
        'document_number',
        'expiry_date'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'expiry_date' => 'date'
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';

    /**
     * Document type constants
     */
    const DOCUMENT_YCB_CERTIFICATE = 'ycb_certificate';
    const DOCUMENT_POLICE_VERIFICATION = 'police_verification';
    const DOCUMENT_ID_PROOF = 'id_proof';
    const DOCUMENT_EDUCATION_CERTIFICATE = 'education_certificate';
    const DOCUMENT_EXPERIENCE_CERTIFICATE = 'experience_certificate';

    /**
     * Get the teacher who submitted verification
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the admin who verified
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope for pending verifications
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for verified documents
     */
    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_VERIFIED);
    }

    /**
     * Get document type label
     */
    public function getDocumentTypeLabelAttribute(): string
    {
        return match($this->document_type) {
            self::DOCUMENT_YCB_CERTIFICATE => 'YCB Certificate',
            self::DOCUMENT_POLICE_VERIFICATION => 'Police Verification',
            self::DOCUMENT_ID_PROOF => 'ID Proof',
            self::DOCUMENT_EDUCATION_CERTIFICATE => 'Education Certificate',
            self::DOCUMENT_EXPERIENCE_CERTIFICATE => 'Experience Certificate',
            default => 'Unknown Document'
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_VERIFIED => 'success',
            self::STATUS_PENDING => 'warning',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_EXPIRED => 'secondary',
            default => 'secondary'
        };
    }
}
?>
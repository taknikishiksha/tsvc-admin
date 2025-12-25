<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentSubmission extends Model
{
    use HasFactory;

    protected $table = 'document_submissions';

    protected $fillable = [
        'teacher_id',
        'user_id',
        'submission_token',
        'requested_documents',
        'instructions',
        'status',
        'documents_required',
        'documents_received',
        'request_email_sent_to',
        'request_sent_at',
        'submission_received_at',
        'reviewed_by',
        'review_notes',
        'reviewed_at',
        'verified_at',
        'expires_at',
        'is_expired',
    ];

    protected $attributes = [
        'status' => 'pending',
        'documents_required' => 0,
        'documents_received' => 0,
        'is_expired' => false,
    ];

    protected $casts = [
        'requested_documents'      => 'array',
        'request_sent_at'          => 'datetime',
        'submission_received_at'   => 'datetime',
        'reviewed_at'              => 'datetime',
        'verified_at'              => 'datetime',
        'expires_at'               => 'datetime',
        'is_expired'               => 'boolean',
    ];

    /* =========================
     |  RELATIONSHIPS
     ========================= */

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'submission_id');
    }

    /* =========================
     |  SCOPES
     ========================= */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('is_expired', true)
              ->orWhere('expires_at', '<', now());
        });
    }

    public function scopeNeedsReminder($query)
    {
        return $query->pending()
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', now()->addDays(2))
            ->whereNull('submission_received_at');
    }

    /* =========================
     |  BUSINESS METHODS
     ========================= */

    public function generateToken(): string
    {
        if (!$this->submission_token) {
            $this->submission_token = Str::uuid()->toString();
            $this->save();
        }

        return $this->submission_token;
    }

    public function markAsSubmitted(): void
    {
        $this->update([
            'status' => 'submitted',
            'submission_received_at' => now(),
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update([
            'is_expired' => true,
            'status' => 'expired',
        ]);
    }

    public function incrementDocumentsReceived(): void
    {
        $this->increment('documents_received');
    }

    public function getProgressPercentage(): float
    {
        if ($this->documents_required === 0) {
            return 0;
        }

        return round(
            ($this->documents_received / $this->documents_required) * 100,
            2
        );
    }

    public function getDaysUntilExpiry(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    public function isExpired(): bool
    {
        return $this->is_expired || (
            $this->expires_at && $this->expires_at->isPast()
        );
    }

    /* =========================
     |  FACTORY METHOD
     ========================= */

    public static function createForTeacher(
        User $teacher,
        array $requestedDocuments,
        ?string $instructions = null,
        int $expiryDays = 7
    ): self {
        return self::create([
            'teacher_id'            => $teacher->id,
            'user_id'               => $teacher->id,
            'submission_token'      => Str::uuid()->toString(),
            'requested_documents'   => $requestedDocuments,
            'documents_required'    => count($requestedDocuments),
            'documents_received'    => 0,
            'instructions'          => $instructions,
            'status'                => 'pending',
            'request_email_sent_to' => $teacher->email,
            'request_sent_at'       => now(),
            'expires_at'            => now()->addDays($expiryDays),
            'is_expired'            => false,
        ]);
    }
}

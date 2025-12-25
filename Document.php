<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_submission_id', 'teacher_id', 'document_type', 'original_filename',
        'file_size', 'mime_type', 'email_message_id', 'is_safe', 'virus_scan_result',
        'file_hash', 'storage_reference', 'status', 'rejection_reason', 'verified_at', 'rejected_at'
    ];

    protected $casts = [
        'is_safe' => 'boolean',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Relationships
    public function submission()
    {
        return $this->belongsTo(DocumentSubmission::class, 'document_submission_id');
    }

    public function teacher()
    {
        return $this->belongsTo(YogaTeacher::class);
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSafe($query)
    {
        return $query->where('is_safe', true);
    }

    // Methods
    public function markAsVerified()
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);
    }

    public function markAsRejected($reason)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_at' => now(),
        ]);
    }

    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf()
    {
        return $this->mime_type === 'application/pdf';
    }

    public function getFormattedFileSize()
    {
        $size = (int) $this->file_size;
        if ($size < 1024) {
            return $size . ' KB';
        }
        return round($size / 1024, 2) . ' MB';
    }
}
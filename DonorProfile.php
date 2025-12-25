<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DonorProfile extends Model
{
    use HasFactory;

    protected $table = 'donor_profiles';

    protected $fillable = [
        'user_id',
        'donor_type',
        'pan_or_gstin',
        'preferred_receipt_method',
        'billing_address',
    ];

    protected $casts = [
        'billing_address' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

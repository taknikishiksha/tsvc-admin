<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CorporateProfile extends Model
{
    use HasFactory;

    protected $table = 'corporate_profiles';

    protected $fillable = [
        'user_id',
        'company_name',
        'company_size',
        'hr_contact_name',
        'hr_contact_email',
        'gstin',
        'billing_address',
    ];

    protected $casts = [
        'billing_address' => 'array',
        'company_size' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

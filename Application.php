<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'registration_number',
        'name', 
        'email',
        'phone',
        'address',
        'qualification',
        'application_type',
        'documents_sent',
        'transaction_id',
        'transaction_screenshot',
        'registration_fee',
        'payment_status',
        'status',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'documents_sent' => 'boolean',
        'registration_fee' => 'integer'
    ];
}
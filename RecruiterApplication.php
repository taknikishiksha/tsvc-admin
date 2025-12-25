<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecruiterApplication extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'state',
        'experience',
        'message',
    ];
}
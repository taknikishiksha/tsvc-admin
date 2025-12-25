<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SuperAdmin extends Authenticatable
{
    use Notifiable;

    protected $table = 'users'; // Use the users table

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Ensure only superadmin users are queried
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('superadmin', function ($query) {
            $query->where('role', 'super_admin');
        });
    }
}
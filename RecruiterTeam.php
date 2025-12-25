<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecruiterTeam extends Model
{
    protected $fillable = ['state', 'member_name', 'role', 'contact_number'];
}
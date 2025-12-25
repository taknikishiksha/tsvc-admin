<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InternProfile extends Model
{
    use HasFactory;

    protected $table = 'intern_profiles';

    protected $fillable = [
        'user_id',
        'college_name',
        'course',
        'year_of_study',
        'portfolio_link',
        'resume_path',
    ];

    protected $casts = [
        // none (all scalar)
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

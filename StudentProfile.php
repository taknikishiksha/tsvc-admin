<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentProfile extends Model {
    use HasFactory;
    protected $fillable = [
        'user_id','dob','highest_qualification','guardian_name','emergency_phone','course_interest'
    ];
    protected $casts = [
        'course_interest' => 'array'
    ];
    public function user() { return $this->belongsTo(User::class); }
}

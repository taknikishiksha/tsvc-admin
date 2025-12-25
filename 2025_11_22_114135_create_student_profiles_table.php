<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('dob')->nullable();
            $table->string('highest_qualification')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->json('course_interest')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('student_profiles');
    }
};

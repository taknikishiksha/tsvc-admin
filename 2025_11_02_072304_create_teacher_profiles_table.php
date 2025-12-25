<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->json('specialization')->nullable();
            $table->integer('experience_years')->default(0);
            $table->decimal('hourly_rate', 8, 2)->default(0);
            $table->json('languages')->nullable();
            $table->json('certifications')->nullable();
            $table->text('teaching_style')->nullable();
            $table->json('availability_slots')->nullable();
            $table->integer('completion_percentage')->default(0);
            $table->integer('visibility_score')->default(0);
            $table->enum('verification_status', ['not_verified', 'pending', 'verified'])->default('not_verified');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('teacher_profiles');
    }
};
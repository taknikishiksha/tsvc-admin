<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('yoga_teachers')->onDelete('cascade');
            
            // Session Details
            $table->integer('session_number'); // 1, 2, 3... from total_sessions
            $table->date('session_date');
            $table->time('scheduled_time');
            $table->time('actual_start_time')->nullable();
            $table->time('actual_end_time')->nullable();
            $table->integer('duration_minutes')->default(60);
            
            // Location Tracking
            $table->string('session_location')->nullable();
            $table->decimal('teacher_latitude', 10, 8)->nullable();
            $table->decimal('teacher_longitude', 11, 8)->nullable();
            $table->decimal('client_latitude', 10, 8)->nullable();
            $table->decimal('client_longitude', 11, 8)->nullable();
            $table->decimal('distance_km', 5, 2)->nullable(); // Distance between teacher and client
            
            // Attendance Status
            $table->enum('teacher_status', ['present', 'absent', 'late', 'rescheduled'])->default('present');
            $table->enum('client_status', ['present', 'absent', 'late', 'rescheduled'])->default('present');
            $table->enum('overall_status', [
                'completed',
                'teacher_absent', 
                'client_absent',
                'cancelled',
                'rescheduled'
            ])->default('completed');
            
            // Verification System
            $table->boolean('teacher_marked')->default(false);
            $table->timestamp('teacher_marked_at')->nullable();
            $table->boolean('client_confirmed')->default(false);
            $table->timestamp('client_confirmed_at')->nullable();
            $table->boolean('auto_verified')->default(false); // Auto-verify after 24h if no dispute
            
            // Session Notes & Progress
            $table->text('teacher_notes')->nullable(); // What was taught
            $table->text('client_notes')->nullable(); // Client feedback
            $table->json('asanas_practiced')->nullable(); // List of asanas
            $table->string('focus_area')->nullable(); // flexibility, strength, relaxation
            $table->integer('client_energy_level')->nullable(); // 1-5 scale
            $table->integer('session_quality_rating')->nullable(); // 1-5 scale
            
            // Rescheduling & Cancellation
            $table->boolean('was_rescheduled')->default(false);
            $table->foreignId('original_attendance_id')->nullable()->constrained('attendances')->onDelete('set null');
            $table->text('cancellation_reason')->nullable();
            $table->string('cancelled_by')->nullable(); // teacher, client, system
            
            // Payment Link
            $table->boolean('payment_processed')->default(false);
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index('service_id');
            $table->index('client_id');
            $table->index('teacher_id');
            $table->index('session_date');
            $table->index('overall_status');
            $table->index(['teacher_marked', 'client_confirmed']);
            $table->unique(['service_id', 'session_number']); // Prevent duplicate sessions
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
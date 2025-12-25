<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('yoga_teachers')->onDelete('cascade');
            
            // Service Details
            $table->string('service_type'); // personal, group, corporate, online
            $table->string('package_type'); // single, weekly, monthly, custom
            $table->integer('total_sessions');
            $table->integer('sessions_completed')->default(0);
            $table->integer('sessions_remaining')->default(0);
            
            // Timing & Schedule
            $table->date('start_date');
            $table->date('end_date');
            $table->json('scheduled_days'); // ['monday', 'wednesday', 'friday']
            $table->time('preferred_time');
            $table->integer('session_duration')->default(60); // in minutes
            
            // Location
            $table->string('service_address')->nullable();
            $table->string('landmark')->nullable();
            $table->string('location_type'); // home, studio, online, office
            
            // Pricing
            $table->decimal('package_amount', 10, 2);
            $table->decimal('per_session_rate', 8, 2);
            $table->decimal('platform_fee', 8, 2)->default(0);
            $table->decimal('tds_amount', 8, 2)->default(0);
            $table->decimal('final_amount', 10, 2);
            
            // Status Tracking
            $table->enum('status', [
                'pending', 
                'confirmed', 
                'in_progress', 
                'on_hold', 
                'completed', 
                'cancelled',
                'refunded'
            ])->default('pending');
            
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            
            // Additional Features
            $table->boolean('has_emergency_substitute')->default(false);
            $table->boolean('allows_rescheduling')->default(true);
            $table->integer('reschedule_count')->default(0);
            $table->text('special_instructions')->nullable();
            
            // Progress Tracking
            $table->integer('client_rating')->nullable();
            $table->text('client_feedback')->nullable();
            $table->integer('teacher_rating')->nullable();
            $table->text('teacher_feedback')->nullable();
            
            // Auto Completion
            $table->boolean('auto_renew')->default(false);
            $table->date('next_billing_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('client_id');
            $table->index('teacher_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index(['start_date', 'end_date']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
};
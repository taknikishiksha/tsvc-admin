<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('teacher_id')->constrained('yoga_teachers')->onDelete('cascade');
            
            // Time Slot Details
            $table->enum('day_of_week', [
                'monday', 
                'tuesday', 
                'wednesday', 
                'thursday', 
                'friday', 
                'saturday', 
                'sunday'
            ]);
            
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration')->default(60); // minutes
            
            // Service Type for this slot
            $table->json('service_types')->nullable(); // ['home', 'online', 'corporate']
            $table->json('locations')->nullable(); // Specific areas for this slot
            
            // Slot Management
            $table->boolean('is_recurring')->default(true);
            $table->date('specific_date')->nullable(); // For one-time availability
            $table->boolean('is_available')->default(true);
            
            // Booking Limits
            $table->integer('max_bookings_per_slot')->default(1);
            $table->integer('current_bookings')->default(0);
            $table->integer('buffer_minutes')->default(30); // Gap between sessions
            
            // Priority & Pricing
            $table->integer('priority_level')->default(1); // 1-5, higher = more preferred
            $table->decimal('dynamic_pricing', 8, 2)->nullable(); // Different rate for this slot
            
            $table->timestamps();
            
            // Indexes
            $table->index('teacher_id');
            $table->index('day_of_week');
            $table->index('start_time');
            $table->index('is_available');
            $table->index(['teacher_id', 'day_of_week', 'is_available']);
            $table->index('specific_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('availabilities');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_submissions', function (Blueprint $table) {
            $table->id();
            
            // Relationship with teacher
            $table->foreignId('teacher_id')->constrained('yoga_teachers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Document Request Details
            $table->string('submission_token')->unique(); // Unique token for email tracking
            $table->json('requested_documents'); // ['aadhar', 'ycb_certificate', 'photo', 'police_verification']
            $table->text('instructions')->nullable();
            
            // Document Status Tracking
            $table->enum('status', ['pending', 'submitted', 'under_review', 'verified', 'rejected'])->default('pending');
            $table->integer('documents_received')->default(0);
            $table->integer('documents_required')->default(0);
            
            // Email Tracking
            $table->string('request_email_sent_to'); // Teacher's email
            $table->timestamp('request_sent_at')->nullable();
            $table->timestamp('submission_received_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            
            // Review Details
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            // Expiry & Cleanup
            $table->timestamp('expires_at'); // 7 days from request
            $table->boolean('is_expired')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index('teacher_id');
            $table->index('submission_token');
            $table->index('status');
            $table->index('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_submissions');
    }
};
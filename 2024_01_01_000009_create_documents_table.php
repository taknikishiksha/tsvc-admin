<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('document_submission_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('yoga_teachers')->onDelete('cascade');
            
            // Document Details
            $table->string('document_type'); // aadhar_front, aadhar_back, ycb_certificate, etc.
            $table->string('original_filename');
            $table->string('file_size'); // in KB/MB
            $table->string('mime_type');
            $table->string('email_message_id')->nullable(); // For tracking which email it came from
            
            // Security & Validation
            $table->boolean('is_safe')->default(false);
            $table->string('virus_scan_result')->nullable();
            $table->string('file_hash'); // For duplicate detection
            
            // Storage Reference (We're NOT storing files on server, just metadata)
            $table->text('storage_reference')->nullable(); // Reference to email/cloud storage
            
            // Status
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            
            // Timestamps
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('document_submission_id');
            $table->index('teacher_id');
            $table->index('document_type');
            $table->index('file_hash');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
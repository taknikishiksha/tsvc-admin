<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('teacher_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->enum('document_type', ['ycb_certificate', 'police_verification', 'id_proof', 'education_certificate']);
            $table->string('document_path');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamp('submitted_at')->useCurrent();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('resubmission_instructions')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('document_number')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('teacher_verifications');
    }
};
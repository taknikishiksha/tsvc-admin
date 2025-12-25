<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['email_verification', 'phone_verification', 'password_reset']);
            $table->string('otp', 6);
            $table->string('token')->unique();
            $table->timestamp('expires_at');
            $table->boolean('is_used')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index('token');
            $table->index('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('otp_verifications');
    }
};
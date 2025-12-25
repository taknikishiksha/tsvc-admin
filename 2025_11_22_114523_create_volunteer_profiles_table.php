<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('volunteer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->json('areas_of_interest')->nullable();
            $table->text('prior_experience')->nullable();
            $table->string('availability')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('volunteer_profiles'); }
};

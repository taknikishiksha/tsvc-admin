<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('intern_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('college_name')->nullable();
            $table->string('course')->nullable();
            $table->integer('year_of_study')->nullable();
            $table->string('portfolio_link')->nullable();
            $table->string('resume_path')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('intern_profiles'); }
};

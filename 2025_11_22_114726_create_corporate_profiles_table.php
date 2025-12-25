<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('corporate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('company_name')->nullable();
            $table->integer('company_sizee')->nullable();
            $table->string('hr_contact_name')->nullable();
            $table->string('hr_contact_email')->nullable();
            $table->string('gstin')->nullable();
            $table->json('billing_address')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('corporate_profiles'); }
};

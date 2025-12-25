<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('donor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('donor_type',['individual','organisation'])->default('individual');
            $table->string('pan_or_gstin')->nullable();
            $table->string('preferred_receipt_method')->nullable();
            $table->json('billing_address')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('donor_profiles'); }
};

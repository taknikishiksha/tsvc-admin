<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('purpose')->nullable();
            $table->string('preferred_mode')->nullable(); // online/offline
            $table->string('preferred_location')->nullable();
            $table->string('frequency')->nullable();
            $table->json('billing_info')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('client_profiles'); }
};

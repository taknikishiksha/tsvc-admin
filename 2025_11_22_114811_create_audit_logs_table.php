<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->text('details')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('audit_logs'); }
};

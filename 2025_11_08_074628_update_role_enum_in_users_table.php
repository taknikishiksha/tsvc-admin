<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', [
            'superadmin',
            'admin',
            'hr', 'finance', 'training', 'exam', 'usermgmt', 'service',
            'client', 'teacher', 'student', 'partner', 'consultant', 'volunteer', 'intern'
        ])->default('client')->change();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};

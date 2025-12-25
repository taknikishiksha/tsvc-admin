<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            // Check if column exists before adding
            if (!Schema::hasColumn('applications', 'documents_sent')) {
                $table->boolean('documents_sent')->default(false)->after('application_type');
            }
            
            if (!Schema::hasColumn('applications', 'transaction_screenshot')) {
                $table->string('transaction_screenshot')->nullable()->after('transaction_id');
            }
            
            if (!Schema::hasColumn('applications', 'registration_fee')) {
                $table->integer('registration_fee')->default(500)->after('transaction_screenshot');
            }
            
            if (!Schema::hasColumn('applications', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'verified', 'rejected'])->default('pending')->after('registration_fee');
            }
            
            // Skip registration_number if it exists (AVOID DUPLICATE)
            if (!Schema::hasColumn('applications', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('payment_status');
            }
            
            if (!Schema::hasColumn('applications', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
        });
    }

    public function down()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'documents_sent',
                'transaction_screenshot', 
                'registration_fee',
                'payment_status',
                'ip_address',
                'user_agent'
            ]);
        });
    }
};
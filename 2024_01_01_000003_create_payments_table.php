<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('yoga_teachers')->onDelete('cascade');
            
            // Payment Details
            $table->string('payment_id')->unique(); // Razorpay/Stripe ID
            $table->string('order_id')->nullable();
            $table->string('payment_method')->default('online'); // online, wallet, cash
            $table->string('gateway')->nullable(); // razorpay, stripe
            
            // Amount Breakdown
            $table->decimal('amount', 10, 2); // Total amount paid
            $table->decimal('platform_fee', 8, 2)->default(0); // 20%
            $table->decimal('teacher_share', 8, 2)->default(0); // 70%
            $table->decimal('coordinator_share', 8, 2)->default(0); // 10%
            $table->decimal('tds_deducted', 8, 2)->default(0); // TDS amount
            $table->decimal('net_teacher_share', 8, 2)->default(0); // After TDS
            
            // Payment Status & Timing
            $table->enum('status', [
                'pending', 
                'captured', 
                'failed', 
                'refunded', 
                'partially_refunded'
            ])->default('pending');
            
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('payout_processed_at')->nullable();
            
            // Payout Details
            $table->enum('payout_status', ['pending', 'processing', 'paid', 'failed'])->default('pending');
            $table->string('payout_id')->nullable();
            $table->text('payout_response')->nullable();
            
            // Invoice Details (NGO Format)
            $table->string('invoice_number')->unique()->nullable();
            $table->date('invoice_date')->nullable();
            $table->text('invoice_details')->nullable(); // JSON of invoice items
            
            // Refund Details
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->string('refund_id')->nullable();
            $table->text('refund_reason')->nullable();
            $table->timestamp('refunded_at')->nullable();
            
            // Gateway Response
            $table->text('gateway_request')->nullable();
            $table->text('gateway_response')->nullable();
            $table->text('gateway_error')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('service_id');
            $table->index('client_id');
            $table->index('teacher_id');
            $table->index('payment_id');
            $table->index('status');
            $table->index('payout_status');
            $table->index('invoice_number');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
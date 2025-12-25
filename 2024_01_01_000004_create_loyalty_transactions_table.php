<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('transactionable', 'loyalty_trans_idx'); // ✅ Short index name diya
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null');
            
            // Transaction Details
            $table->string('transaction_type');
            $table->integer('points');
            $table->decimal('cash_value', 8, 2)->nullable();
            $table->string('currency')->default('INR');
            
            // Loyalty Program Details
            $table->string('loyalty_tier')->default('basic');
            $table->decimal('multiplier', 3, 2)->default(1.00);
            
            // Reference Details
            $table->string('reference_type')->nullable();
            $table->string('reference_id')->nullable();
            $table->text('description');
            
            // Expiry & Validity
            $table->integer('validity_days')->default(365);
            $table->date('expires_at')->nullable();
            $table->boolean('is_expired')->default(false);
            
            // Status
            $table->enum('status', ['active', 'used', 'expired', 'cancelled'])->default('active');
            $table->timestamp('used_at')->nullable();
            
            // Redemption Details
            $table->foreignId('redemption_id')->nullable()->constrained('loyalty_transactions')->onDelete('set null');
            $table->string('redemption_type')->nullable();
            $table->text('redemption_details')->nullable();
            
            $table->timestamps();
            
            // Indexes (morphs wala index already ban gaya, wo hataya)
            $table->index('user_id');
            // ❌ $table->index(['transactionable_type', 'transactionable_id']); // Ye line REMOVE karo
            $table->index('transaction_type');
            $table->index('status');
            $table->index('expires_at');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('loyalty_transactions');
    }
};

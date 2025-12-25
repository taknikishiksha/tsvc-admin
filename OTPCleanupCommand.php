<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OTPVerification;
use Carbon\Carbon;

class OTPCleanupCommand extends Command
{
    protected $signature = 'otp:cleanup';
    protected $description = 'Clean up expired OTP verification records';

    public function handle()
    {
        $this->info('Cleaning up expired OTP records...');

        $expiredCount = OTPVerification::where('expires_at', '<', Carbon::now())
                                      ->orWhere('is_used', true)
                                      ->delete();

        $this->info("Successfully cleaned up {$expiredCount} expired OTP records.");

        // Log the cleanup
        \Log::info('OTP cleanup completed', [
            'cleaned_count' => $expiredCount,
            'cleaned_at' => now()->toDateTimeString()
        ]);

        return Command::SUCCESS;
    }
}
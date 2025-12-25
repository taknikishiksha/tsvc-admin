<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Explicitly register commands (Owner Reports)
     */
    protected $commands = [
        \App\Console\Commands\WeeklyOwnerSummary::class,
        \App\Console\Commands\MonthlyOwnerSummary::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        /*
        |--------------------------------------------------------------------------
        | DAILY / SYSTEM MAINTENANCE TASKS
        |--------------------------------------------------------------------------
        */

        // Daily cleanup of expired document submissions (12:00 AM)
        $schedule->command('cleanup:documents')
            ->daily()
            ->description('Clean up expired document submissions')
            ->appendOutputTo(storage_path('logs/cleanup.log'));

        // Daily reminder emails for expiring submissions (10:00 AM)
        $schedule->command('documents:send-reminders')
            ->dailyAt('10:00')
            ->description('Send reminder emails for expiring document submissions')
            ->appendOutputTo(storage_path('logs/reminders.log'));

        // Demo follow-up Telegram reminder (10:00 AM)
        $schedule->command('demo:followup-reminder')
            ->dailyAt('10:00')
            ->description('Send Telegram reminders for pending demo follow-ups')
            ->appendOutputTo(storage_path('logs/demo-followup-reminder.log'));

        // OTP cleanup (2:00 AM)
        $schedule->command('otp:cleanup')
            ->dailyAt('02:00')
            ->description('Clean up expired OTP verification records');

        // Daily database backup (11:30 PM)
        $schedule->command('backup:run --only-db')
            ->dailyAt('23:30')
            ->description('Daily database backup');


        /*
        |--------------------------------------------------------------------------
        | ANALYTICS & REPORTING
        |--------------------------------------------------------------------------
        */

        // Weekly analytics report (Monday 9:00 AM)
        $schedule->command('analytics:generate-weekly')
            ->weeklyOn(1, '09:00')
            ->description('Generate weekly analytics report');


        /*
        |--------------------------------------------------------------------------
        | OWNER TELEGRAM / DASHBOARD SUMMARIES
        |--------------------------------------------------------------------------
        */

        // Weekly Owner Summary (Monday 9:00 AM)
        $schedule->command('report:weekly-owner-summary')
            ->weeklyOn(1, '09:00')
            ->withoutOverlapping()
            ->description('Send weekly owner performance summary')
            ->appendOutputTo(storage_path('logs/owner-weekly-summary.log'));

        // 🚨 Revenue Dip Alert (Monday 10:00 AM)
        $schedule->command('alert:revenue-dip')
            ->weeklyOn(1, '10:00')
            ->withoutOverlapping()
            ->description('Send alert if weekly revenue conversion dips')
            ->appendOutputTo(storage_path('logs/revenue-dip-alert.log'));

        // 🚨 Teacher-specific Conversion Dip Alert (Monday 10:15 AM)
        // Offset from owner summary → avoids message collision
        $schedule->command('teacher:conversion-dip-alert')
            ->weeklyOn(1, '10:15')
            ->withoutOverlapping()
            ->description('Send teacher-wise conversion dip alerts')
            ->appendOutputTo(storage_path('logs/teacher-conversion-dip-alert.log'));

        // Monthly Owner Summary (1st day, 9:15 AM)
        $schedule->command('report:monthly-owner-summary')
            ->monthlyOn(1, '09:15')
            ->withoutOverlapping()
            ->description('Send monthly owner performance summary')
            ->appendOutputTo(storage_path('logs/owner-monthly-summary.log'));


        /*
        |--------------------------------------------------------------------------
        | PAYMENTS
        |--------------------------------------------------------------------------
        */

        // Monthly payout processing (1st of month 10:00 AM)
        $schedule->command('payments:process-payouts')
            ->monthlyOn(1, '10:00')
            ->description('Process monthly teacher payouts');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }

    /**
     * Application timezone for scheduler
     */
    protected function scheduleTimezone(): string
    {
        return 'Asia/Kolkata';
    }
}

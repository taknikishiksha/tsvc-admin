<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DemoBooking;
use App\Services\TelegramService;
use Carbon\Carbon;

class DemoFollowUpReminder extends Command
{
    protected $signature = 'demo:followup-reminder';
    protected $description = 'Send Telegram reminder for stale demo follow-ups';

    public function handle()
    {
        $thresholdDays = 5;
        $cutoffDate = Carbon::now()->subDays($thresholdDays);

        $query = DemoBooking::where('status', 'completed')
            ->where('converted_to_service', 0)
            ->whereIn('follow_up_status', ['pending', 'contacted'])
            ->where(function ($q) use ($cutoffDate) {
                $q->whereNull('last_follow_up_at')
                  ->orWhere('last_follow_up_at', '<=', $cutoffDate);
            });

        $staleCount = $query->count();

        // ✅ Silent exit if clean
        if ($staleCount === 0) {
            return Command::SUCCESS;
        }

        // 🔹 Fetch only limited list for Telegram (anti-spam)
        $staleDemos = $query
            ->orderBy('last_follow_up_at', 'asc')
            ->limit(5)
            ->get(['id', 'name', 'phone']);

        // 🧩 Build message
        $message = "⚠️ *Stale Demo Follow-ups Alert*\n\n"
                 . "• *{$staleCount}* yoga demo client(s) pending for *{$thresholdDays}+ days*\n\n"
                 . "*Sample Pending List:*\n";

        foreach ($staleDemos as $demo) {
            $message .= "• {$demo->name} ({$demo->phone})\n";
        }

        if ($staleCount > $staleDemos->count()) {
            $remaining = $staleCount - $staleDemos->count();
            $message .= "• + {$remaining} more…\n";
        }

        $message .= "\n➡️ Admin Panel → Demo Follow-ups → Stale";

        app(TelegramService::class)->sendMessage($message);

        $this->info("Telegram hybrid alert sent for {$staleCount} stale follow-ups.");

        return Command::SUCCESS;
    }
}

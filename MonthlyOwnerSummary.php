<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OwnerReportService;
use App\Services\TelegramService;
use Carbon\Carbon;

class MonthlyOwnerSummary extends Command
{
    protected $signature = 'report:monthly-owner-summary';
    protected $description = 'Monthly owner performance summary (Telegram)';

    public function handle(
        OwnerReportService $report,
        TelegramService $telegram
    ) {
        $from = Carbon::now()->subMonth()->startOfMonth();
        $to   = Carbon::now()->subMonth()->endOfMonth();

        $data = $report->generate($from, $to);

        $month = $from->format('F');

        $message =
            "📈 *Monthly Performance Summary ({$month})*\n\n" .
            "📌 *Funnel*\n" .
            "• Demos: {$data['total_demos']}\n" .
            "• Completed: {$data['completed_demos']}\n" .
            "• Converted: {$data['converted_demos']}\n" .
            "• Conversion Rate: {$data['conversion_rate']}%\n\n" .
            "📞 *Follow-up ROI*\n" .
            "• Pending Follow-ups: {$data['pending_followups']}\n" .
            "• Converted after Follow-up: {$data['converted_followups']}\n\n" .
            "🏆 *Best Teacher*\n" .
            "• {$data['top_teacher']}\n\n" .
            "— Takniki Shiksha Careers";

        $telegram->sendToOwner($message);

        $this->info('Monthly owner summary sent.');
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DocumentStatusTracker;

class DocumentReminderCommand extends Command
{
    protected $signature = 'documents:send-reminders';
    protected $description = 'Send reminder emails for pending document submissions';

    protected $documentTracker;

    public function __construct(DocumentStatusTracker $documentTracker)
    {
        parent::__construct();
        $this->documentTracker = $documentTracker;
    }

    public function handle()
    {
        $this->info('Sending reminder emails for pending document submissions...');

        $result = $this->documentTracker->sendReminderEmails();

        $this->info("Reminder emails sent successfully!");
        $this->info("Sent: {$result['sent']}, Failed: {$result['failed']}, Total: {$result['total']}");

        if ($result['failed'] > 0) {
            $this->error("Failed to send {$result['failed']} reminder emails. Check logs for details.");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
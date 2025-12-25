<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class TelegramService
{
    protected string $token;
    protected $defaultChatId;

    public function __construct()
    {
        // Prefer config/services.php, fallback to .env
        $this->token = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
        $this->defaultChatId = config('services.telegram.chat_id') ?: env('TELEGRAM_CHAT_ID');
    }

    /**
     * Core method: Send message and return Telegram API response
     *
     * @param string $text
     * @param mixed|null $chatId
     * @param array $options
     * @return array
     */
    public function sendMessage(string $text, $chatId = null, array $options = []): array
    {
        $chatId = $chatId ?: $this->defaultChatId;

        if (! $this->token) {
            throw new InvalidArgumentException(
                'Telegram bot token not configured. Set TELEGRAM_BOT_TOKEN.'
            );
        }

        if (! $chatId) {
            throw new InvalidArgumentException(
                'Telegram chat ID not configured. Set TELEGRAM_CHAT_ID.'
            );
        }

        $payload = array_merge([
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'HTML',
        ], $options);

        try {
            $response = Http::post(
                "https://api.telegram.org/bot{$this->token}/sendMessage",
                $payload
            );

            return $response->json();

        } catch (\Throwable $e) {
            Log::error('Telegram sendMessage failed', [
                'error'   => $e->getMessage(),
                'chat_id'=> $chatId,
            ]);

            return [
                'ok'    => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Simple helper method: Send message and return true/false
     */
    public function send(string $message, $chatId = null): bool
    {
        $result = $this->sendMessage($message, $chatId);
        return isset($result['ok']) && $result['ok'] === true;
    }

    /**
     * Send message directly to Owner (Admin / Founder)
     * Uses services.telegram.owner_chat_id
     */
    public function sendToOwner(string $message): bool
    {
        $chatId = config('services.telegram.owner_chat_id');

        if (! $chatId) {
            Log::warning('Telegram owner_chat_id not configured.');
            return false;
        }

        $result = $this->sendMessage($message, $chatId);

        return isset($result['ok']) && $result['ok'] === true;
    }
}
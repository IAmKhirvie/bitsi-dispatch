<?php

namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SemaphoreService
{
    private string $apiKey;
    private string $senderName;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('semaphore.api_key') ?? '';
        $this->senderName = config('semaphore.sender_name', 'SEMAPHORE');
        $this->baseUrl = config('semaphore.base_url', 'https://api.semaphore.co/api/v4');
    }

    public function send(string $phone, string $message, ?int $dispatchEntryId = null): ?SmsLog
    {
        $log = SmsLog::create([
            'dispatch_entry_id' => $dispatchEntryId,
            'recipient_phone' => $phone,
            'message' => $message,
            'status' => 'pending',
        ]);

        try {
            $response = Http::post("{$this->baseUrl}/messages", [
                'apikey' => $this->apiKey,
                'number' => $phone,
                'message' => $message,
                'sendername' => $this->senderName,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $log->update([
                    'status' => 'sent',
                    'provider_message_id' => $data[0]['message_id'] ?? null,
                    'sent_at' => now(),
                ]);
            } else {
                $log->update(['status' => 'failed']);
                Log::error('Semaphore SMS failed', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            $log->update(['status' => 'failed']);
            Log::error('Semaphore SMS exception', ['error' => $e->getMessage()]);
        }

        return $log;
    }

    public function sendPriority(string $phone, string $message, ?int $dispatchEntryId = null): ?SmsLog
    {
        $log = SmsLog::create([
            'dispatch_entry_id' => $dispatchEntryId,
            'recipient_phone' => $phone,
            'message' => $message,
            'status' => 'pending',
        ]);

        try {
            $response = Http::post("{$this->baseUrl}/priority", [
                'apikey' => $this->apiKey,
                'number' => $phone,
                'message' => $message,
                'sendername' => $this->senderName,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $log->update([
                    'status' => 'sent',
                    'provider_message_id' => $data[0]['message_id'] ?? null,
                    'sent_at' => now(),
                ]);
            } else {
                $log->update(['status' => 'failed']);
                Log::error('Semaphore priority SMS failed', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            $log->update(['status' => 'failed']);
            Log::error('Semaphore priority SMS exception', ['error' => $e->getMessage()]);
        }

        return $log;
    }
}

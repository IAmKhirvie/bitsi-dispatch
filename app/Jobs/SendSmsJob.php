<?php

namespace App\Jobs;

use App\Services\SemaphoreService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        private string $phone,
        private string $message,
        private ?int $dispatchEntryId = null,
        private bool $priority = false,
    ) {}

    public function handle(SemaphoreService $service): void
    {
        if ($this->priority) {
            $service->sendPriority($this->phone, $this->message, $this->dispatchEntryId);
        } else {
            $service->send($this->phone, $this->message, $this->dispatchEntryId);
        }
    }
}

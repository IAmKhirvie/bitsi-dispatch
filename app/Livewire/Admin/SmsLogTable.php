<?php

namespace App\Livewire\Admin;

use App\Jobs\SendSmsJob;
use App\Models\SmsLog;
use Livewire\Component;
use Livewire\WithPagination;

class SmsLogTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => '', 'as' => 'status'],
        'dateFrom' => ['except' => '', 'as' => 'from'],
        'dateTo' => ['except' => '', 'as' => 'to'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function retrySms(int $smsLogId): void
    {
        $log = SmsLog::findOrFail($smsLogId);

        if ($log->status->value !== 'failed') {
            session()->flash('error', 'Only failed SMS can be retried.');
            return;
        }

        SendSmsJob::dispatch(
            $log->recipient_phone,
            $log->message,
            $log->dispatch_entry_id
        );

        session()->flash('status', 'SMS retry queued for ' . $log->recipient_phone);
    }

    public function render()
    {
        $logs = SmsLog::with('dispatchEntry')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('recipient_phone', 'like', "%{$this->search}%")
                  ->orWhere('message', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate(20);

        $todayStats = [
            'sent' => SmsLog::where('status', 'sent')->whereDate('created_at', today())->count(),
            'failed' => SmsLog::where('status', 'failed')->whereDate('created_at', today())->count(),
            'pending' => SmsLog::where('status', 'pending')->whereDate('created_at', today())->count(),
        ];

        return view('livewire.admin.sms-log-table', compact('logs', 'todayStats'));
    }
}

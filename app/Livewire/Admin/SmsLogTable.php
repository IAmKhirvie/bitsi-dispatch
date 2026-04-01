<?php

namespace App\Livewire\Admin;

use App\Enums\SmsStatus;
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

    // Modal state
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;

    // Form fields
    public ?int $editingSmsLogId = null;
    public ?int $viewingSmsLogId = null;
    public ?int $deletingSmsLogId = null;

    public string $recipientPhone = '';
    public string $message = '';
    public string $status = '';
    public ?int $dispatchEntryId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => '', 'as' => 'status'],
        'dateFrom' => ['except' => '', 'as' => 'from'],
        'dateTo' => ['except' => '', 'as' => 'to'],
    ];

    protected function rules(): array
    {
        return [
            'recipientPhone' => 'required|string|max:20',
            'message' => 'required|string|max:160',
            'status' => 'required|in:pending,sent,failed',
            'dispatchEntryId' => 'nullable|exists:dispatch_entries,id',
        ];
    }

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

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->status = 'pending';
        $this->showCreateModal = true;
    }

    public function openEditModal(int $smsLogId): void
    {
        $log = SmsLog::findOrFail($smsLogId);
        $this->editingSmsLogId = $smsLogId;
        $this->recipientPhone = $log->recipient_phone;
        $this->message = $log->message;
        $this->status = $log->status->value;
        $this->dispatchEntryId = $log->dispatch_entry_id;
        $this->showEditModal = true;
    }

    public function openViewModal(int $smsLogId): void
    {
        $this->viewingSmsLogId = $smsLogId;
        $this->showViewModal = true;
    }

    public function openDeleteModal(int $smsLogId): void
    {
        $this->deletingSmsLogId = $smsLogId;
        $this->showDeleteModal = true;
    }

    public function sendSms(int $smsLogId): void
    {
        $log = SmsLog::findOrFail($smsLogId);

        SendSmsJob::dispatch(
            $log->recipient_phone,
            $log->message,
            $log->dispatch_entry_id
        );

        session()->flash('status', 'SMS queued for sending to ' . $log->recipient_phone);
    }

    public function closeModals(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showViewModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingSmsLogId = null;
        $this->viewingSmsLogId = null;
        $this->deletingSmsLogId = null;
        $this->recipientPhone = '';
        $this->message = '';
        $this->status = '';
        $this->dispatchEntryId = null;
    }

    public function createSmsLog(): void
    {
        $this->validate();

        SmsLog::create([
            'recipient_phone' => $this->recipientPhone,
            'message' => $this->message,
            'status' => $this->status,
            'dispatch_entry_id' => $this->dispatchEntryId,
        ]);

        session()->flash('status', 'SMS log created successfully.');
        $this->closeModals();
    }

    public function updateSmsLog(): void
    {
        $this->validate();

        $log = SmsLog::findOrFail($this->editingSmsLogId);
        $log->update([
            'recipient_phone' => $this->recipientPhone,
            'message' => $this->message,
            'status' => $this->status,
            'dispatch_entry_id' => $this->dispatchEntryId,
        ]);

        session()->flash('status', 'SMS log updated successfully.');
        $this->closeModals();
    }

    public function deleteSmsLog(): void
    {
        $log = SmsLog::findOrFail($this->deletingSmsLogId);
        $log->delete();

        session()->flash('status', 'SMS log deleted successfully.');
        $this->closeModals();
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

        $todayCounts = SmsLog::whereDate('created_at', today())
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $todayStats = [
            'sent' => $todayCounts['sent'] ?? 0,
            'failed' => $todayCounts['failed'] ?? 0,
            'pending' => $todayCounts['pending'] ?? 0,
        ];

        return view('livewire.admin.sms-log-table', compact('logs', 'todayStats'));
    }
}

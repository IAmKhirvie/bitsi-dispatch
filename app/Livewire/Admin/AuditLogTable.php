<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $actionFilter = '';
    public string $modelFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'actionFilter' => ['except' => '', 'as' => 'action'],
        'modelFilter' => ['except' => '', 'as' => 'model'],
        'dateFrom' => ['except' => '', 'as' => 'from'],
        'dateTo' => ['except' => '', 'as' => 'to'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActionFilter(): void
    {
        $this->resetPage();
    }

    public function updatingModelFilter(): void
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

    public function render()
    {
        $logs = AuditLog::with('user')
            ->when($this->search, fn ($q) => $q->whereHas('user', fn ($q) =>
                $q->where('name', 'like', "%{$this->search}%")
            ))
            ->when($this->actionFilter, fn ($q) => $q->where('action', $this->actionFilter))
            ->when($this->modelFilter, fn ($q) => $q->where('auditable_type', $this->modelFilter))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate(20);

        $modelTypes = AuditLog::distinct()->pluck('auditable_type');

        return view('livewire.admin.audit-log-table', compact('logs', 'modelTypes'));
    }

    public function getShortModelName(string $fqcn): string
    {
        return class_basename($fqcn);
    }
}

<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\HasTableControls;
use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogTable extends Component
{
    use HasTableControls;
    use WithPagination;

    public string $search = '';
    public string $actionFilter = '';
    public string $modelFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public int $perPage = 20;
    public array $perPageOptions = [5, 10, 15, 20, 30, 40, 50, 100];
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected array $sortableFields = [
        'created_at',
        'user',
        'action',
        'auditable_type',
        'auditable_id',
        'ip_address',
    ];

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
            ->leftJoin('users as sort_users', 'audit_logs.user_id', '=', 'sort_users.id')
            ->select('audit_logs.*')
            ->when($this->search, fn ($q) => $q->whereHas('user', fn ($q) =>
                $q->where('name', 'like', "%{$this->search}%")
            ))
            ->when($this->actionFilter, fn ($q) => $q->where('action', $this->actionFilter))
            ->when($this->modelFilter, fn ($q) => $q->where('auditable_type', $this->modelFilter))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('audit_logs.created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('audit_logs.created_at', '<=', $this->dateTo))
            ->tap(fn ($query) => $this->applyTableSort($query, [
                'user' => 'sort_users.name',
                'created_at' => 'audit_logs.created_at',
                'action' => 'audit_logs.action',
                'auditable_type' => 'audit_logs.auditable_type',
                'auditable_id' => 'audit_logs.auditable_id',
                'ip_address' => 'audit_logs.ip_address',
            ]))
            ->paginate($this->perPage);

        $modelTypes = AuditLog::distinct()->pluck('auditable_type');

        return view('livewire.admin.audit-log-table', compact('logs', 'modelTypes'));
    }

    public function getShortModelName(string $fqcn): string
    {
        return class_basename($fqcn);
    }
}

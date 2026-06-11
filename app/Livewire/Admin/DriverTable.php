<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\HasTableControls;
use App\Models\Driver;
use Livewire\Component;
use Livewire\WithPagination;

class DriverTable extends Component
{
    use HasTableControls;
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public bool $showTrashed = false;
    public int $perPage = 15;
    public array $perPageOptions = [5, 10, 15, 20, 30, 40, 50, 100];
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    protected array $sortableFields = ['name', 'phone', 'license_number', 'status', 'is_active', 'created_at'];

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => '', 'as' => 'status'],
        'showTrashed' => ['except' => false, 'as' => 'trashed'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingShowTrashed(): void
    {
        $this->resetPage();
    }

    public function deleteDriver(int $driverId): void
    {
        Driver::findOrFail($driverId)->delete();
        session()->flash('status', 'Driver deleted successfully.');
    }

    public function restoreDriver(int $driverId): void
    {
        Driver::onlyTrashed()->findOrFail($driverId)->restore();
        session()->flash('status', 'Driver restored successfully.');
    }

    public function toggleActive(int $driverId): void
    {
        $driver = Driver::findOrFail($driverId);
        $driver->update(['is_active' => !$driver->is_active]);
    }

    public function updateStatus(int $driverId, string $status): void
    {
        Driver::findOrFail($driverId)->update(['status' => $status]);
    }

    public function render()
    {
        $drivers = Driver::query()
            ->when($this->showTrashed, fn ($q) => $q->onlyTrashed())
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%")
                  ->orWhere('license_number', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->tap(fn ($query) => $this->applyTableSort($query))
            ->paginate($this->perPage);

        return view('livewire.admin.driver-table', compact('drivers'));
    }
}

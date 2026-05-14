<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\HasTableControls;
use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleTable extends Component
{
    use HasTableControls;
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public bool $showTrashed = false;
    public int $perPage = 15;
    public array $perPageOptions = [5, 10, 15, 20, 30, 40, 50, 100];
    public string $sortField = 'bus_number';
    public string $sortDirection = 'asc';

    protected array $sortableFields = [
        'bus_number',
        'brand',
        'bus_type',
        'plate_number',
        'status',
        'current_location',
        'current_kmr',
        'last_pms_kmr',
        'next_pms_date',
        'last_dispatched_at',
        'created_at',
    ];

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

    public function deleteVehicle(int $vehicleId): void
    {
        Vehicle::findOrFail($vehicleId)->delete();
        session()->flash('status', 'Vehicle deleted successfully.');
    }

    public function restoreVehicle(int $vehicleId): void
    {
        Vehicle::onlyTrashed()->findOrFail($vehicleId)->restore();
        session()->flash('status', 'Vehicle restored successfully.');
    }

    public function render()
    {
        $vehicles = Vehicle::query()
            ->when($this->showTrashed, fn ($q) => $q->onlyTrashed())
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('bus_number', 'like', "%{$this->search}%")
                  ->orWhere('brand', 'like', "%{$this->search}%")
                  ->orWhere('plate_number', 'like', "%{$this->search}%")
                  ->orWhere('current_location', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->tap(fn ($query) => $this->applyTableSort($query))
            ->paginate($this->perPage);

        return view('livewire.admin.vehicle-table', compact('vehicles'));
    }
}

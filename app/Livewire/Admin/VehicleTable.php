<?php

namespace App\Livewire\Admin;

use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public bool $showTrashed = false;

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
                  ->orWhere('plate_number', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.vehicle-table', compact('vehicles'));
    }
}

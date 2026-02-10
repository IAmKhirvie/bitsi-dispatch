<?php

namespace App\Livewire;

use App\Models\DispatchDay;
use App\Models\DispatchEntry;
use App\Models\Driver;
use App\Models\TripCode;
use App\Models\Vehicle;
use Livewire\Component;

class DispatchBoard extends Component
{
    public string $date;

    // Add entry form
    public ?int $addTripCodeId = null;
    public ?int $addVehicleId = null;
    public ?int $addDriverId = null;
    public ?int $addDriver2Id = null;
    public string $addBrand = '';
    public string $addBusNumber = '';
    public string $addRoute = '';
    public string $addBusType = '';
    public string $addDepartureTerminal = '';
    public string $addArrivalTerminal = '';
    public string $addScheduledDeparture = '';
    public string $addActualDeparture = '';
    public string $addDirection = '';
    public string $addStatus = 'scheduled';
    public string $addRemarks = '';

    // Edit entry form
    public ?int $editingEntryId = null;
    public ?int $editTripCodeId = null;
    public ?int $editVehicleId = null;
    public ?int $editDriverId = null;
    public ?int $editDriver2Id = null;
    public string $editBrand = '';
    public string $editBusNumber = '';
    public string $editRoute = '';
    public string $editBusType = '';
    public string $editDepartureTerminal = '';
    public string $editArrivalTerminal = '';
    public string $editScheduledDeparture = '';
    public string $editActualDeparture = '';
    public string $editDirection = '';
    public string $editStatus = 'scheduled';
    public string $editRemarks = '';

    public bool $showAddDialog = false;
    public bool $showEditDialog = false;

    public function mount(string $date = ''): void
    {
        $this->date = $date ?: now()->toDateString();
    }

    public function updatedDate(): void
    {
        // Date changed — component re-renders automatically
    }

    // Auto-fill from trip code (Add form)
    public function updatedAddTripCodeId($value): void
    {
        if ($value) {
            $tripCode = TripCode::find($value);
            if ($tripCode) {
                $this->addRoute = $tripCode->origin_terminal . ' - ' . $tripCode->destination_terminal;
                $this->addBusType = $tripCode->bus_type?->value ?? $tripCode->bus_type ?? '';
                $this->addDepartureTerminal = $tripCode->origin_terminal;
                $this->addArrivalTerminal = $tripCode->destination_terminal;
                $this->addScheduledDeparture = $tripCode->scheduled_departure_time ?? '';
                $this->addDirection = $tripCode->direction?->value ?? $tripCode->direction ?? '';
            }
        }
    }

    // Auto-fill from vehicle (Add form)
    public function updatedAddVehicleId($value): void
    {
        if ($value) {
            $vehicle = Vehicle::find($value);
            if ($vehicle) {
                $this->addBrand = $vehicle->brand ?? '';
                $this->addBusNumber = $vehicle->bus_number ?? '';
                if (!$this->addBusType) {
                    $this->addBusType = $vehicle->bus_type?->value ?? $vehicle->bus_type ?? '';
                }
            }
        }
    }

    // Auto-fill from trip code (Edit form)
    public function updatedEditTripCodeId($value): void
    {
        if ($value) {
            $tripCode = TripCode::find($value);
            if ($tripCode) {
                $this->editRoute = $tripCode->origin_terminal . ' - ' . $tripCode->destination_terminal;
                $this->editBusType = $tripCode->bus_type?->value ?? $tripCode->bus_type ?? '';
                $this->editDepartureTerminal = $tripCode->origin_terminal;
                $this->editArrivalTerminal = $tripCode->destination_terminal;
                $this->editScheduledDeparture = $tripCode->scheduled_departure_time ?? '';
                $this->editDirection = $tripCode->direction?->value ?? $tripCode->direction ?? '';
            }
        }
    }

    // Auto-fill from vehicle (Edit form)
    public function updatedEditVehicleId($value): void
    {
        if ($value) {
            $vehicle = Vehicle::find($value);
            if ($vehicle) {
                $this->editBrand = $vehicle->brand ?? '';
                $this->editBusNumber = $vehicle->bus_number ?? '';
                if (!$this->editBusType) {
                    $this->editBusType = $vehicle->bus_type?->value ?? $vehicle->bus_type ?? '';
                }
            }
        }
    }

    public function createDispatchDay(): void
    {
        DispatchDay::firstOrCreate(
            ['service_date' => $this->date],
            ['created_by' => auth()->id(), 'notes' => '']
        );
    }

    public function submitAddEntry(): void
    {
        $dispatchDay = DispatchDay::where('service_date', $this->date)->first();
        if (!$dispatchDay) return;

        $this->validate([
            'addTripCodeId' => 'nullable|exists:trip_codes,id',
            'addVehicleId' => 'nullable|exists:vehicles,id',
            'addDriverId' => 'nullable|exists:drivers,id',
            'addDriver2Id' => 'nullable|exists:drivers,id',
        ]);

        $entry = $dispatchDay->entries()->create([
            'trip_code_id' => $this->addTripCodeId,
            'vehicle_id' => $this->addVehicleId,
            'driver_id' => $this->addDriverId,
            'driver2_id' => $this->addDriver2Id,
            'brand' => $this->addBrand,
            'bus_number' => $this->addBusNumber,
            'route' => $this->addRoute,
            'bus_type' => $this->addBusType,
            'departure_terminal' => $this->addDepartureTerminal,
            'arrival_terminal' => $this->addArrivalTerminal,
            'scheduled_departure' => $this->addScheduledDeparture ?: null,
            'actual_departure' => $this->addActualDeparture ?: null,
            'direction' => $this->addDirection,
            'status' => $this->addStatus,
            'remarks' => $this->addRemarks,
            'sort_order' => $dispatchDay->entries()->count(),
        ]);

        $this->resetAddForm();
        $this->showAddDialog = false;
    }

    public function openEditDialog(int $entryId): void
    {
        $entry = DispatchEntry::findOrFail($entryId);
        $this->editingEntryId = $entry->id;
        $this->editTripCodeId = $entry->trip_code_id;
        $this->editVehicleId = $entry->vehicle_id;
        $this->editDriverId = $entry->driver_id;
        $this->editDriver2Id = $entry->driver2_id;
        $this->editBrand = $entry->brand ?? '';
        $this->editBusNumber = $entry->bus_number ?? '';
        $this->editRoute = $entry->route ?? '';
        $this->editBusType = $entry->bus_type ?? '';
        $this->editDepartureTerminal = $entry->departure_terminal ?? '';
        $this->editArrivalTerminal = $entry->arrival_terminal ?? '';
        $this->editScheduledDeparture = $entry->scheduled_departure ?? '';
        $this->editActualDeparture = $entry->actual_departure ?? '';
        $this->editDirection = $entry->direction ?? '';
        $this->editStatus = $entry->status?->value ?? $entry->status ?? 'scheduled';
        $this->editRemarks = $entry->remarks ?? '';
        $this->showEditDialog = true;
    }

    public function submitEditEntry(): void
    {
        if (!$this->editingEntryId) return;

        $entry = DispatchEntry::findOrFail($this->editingEntryId);
        $entry->update([
            'trip_code_id' => $this->editTripCodeId,
            'vehicle_id' => $this->editVehicleId,
            'driver_id' => $this->editDriverId,
            'driver2_id' => $this->editDriver2Id,
            'brand' => $this->editBrand,
            'bus_number' => $this->editBusNumber,
            'route' => $this->editRoute,
            'bus_type' => $this->editBusType,
            'departure_terminal' => $this->editDepartureTerminal,
            'arrival_terminal' => $this->editArrivalTerminal,
            'scheduled_departure' => $this->editScheduledDeparture ?: null,
            'actual_departure' => $this->editActualDeparture ?: null,
            'direction' => $this->editDirection,
            'status' => $this->editStatus,
            'remarks' => $this->editRemarks,
        ]);

        $this->showEditDialog = false;
        $this->editingEntryId = null;
    }

    public function deleteEntry(int $entryId): void
    {
        DispatchEntry::findOrFail($entryId)->delete();
    }

    private function resetAddForm(): void
    {
        $this->addTripCodeId = null;
        $this->addVehicleId = null;
        $this->addDriverId = null;
        $this->addDriver2Id = null;
        $this->addBrand = '';
        $this->addBusNumber = '';
        $this->addRoute = '';
        $this->addBusType = '';
        $this->addDepartureTerminal = '';
        $this->addArrivalTerminal = '';
        $this->addScheduledDeparture = '';
        $this->addActualDeparture = '';
        $this->addDirection = '';
        $this->addStatus = 'scheduled';
        $this->addRemarks = '';
    }

    public function render()
    {
        $dispatchDay = DispatchDay::with(['entries' => fn ($q) => $q->orderBy('sort_order')->with(['tripCode', 'vehicle', 'driver', 'driver2']), 'summary'])
            ->where('service_date', $this->date)
            ->first();

        $tripCodes = TripCode::where('is_active', true)->orderBy('code')->get();
        $vehicles = Vehicle::orderBy('bus_number')->get();
        $drivers = Driver::where('is_active', true)->orderBy('name')->get();

        return view('livewire.dispatch-board', compact('dispatchDay', 'tripCodes', 'vehicles', 'drivers'));
    }
}

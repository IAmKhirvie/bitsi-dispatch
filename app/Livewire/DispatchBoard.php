<?php

namespace App\Livewire;

use App\Actions\Dispatch\TransitionStatus;
use App\Enums\DispatchStatus;
use App\Enums\VehicleStatus;
use App\Models\DispatchDay;
use App\Models\DispatchEntry;
use App\Models\Driver;
use App\Models\TripCode;
use App\Models\Vehicle;
use App\Services\DispatchService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DispatchBoard extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $date;
    public string $search = '';
    public int $perPage = 20;
    public array $perPageOptions = [5, 10, 15, 20, 30, 40, 50, 100];
    public string $sortField = 'sort_order';
    public string $sortDirection = 'asc';

    protected array $sortableFields = [
        'sort_order',
        'trip',
        'bus_number',
        'direction',
        'route',
        'scheduled_departure',
        'driver',
        'status',
        'remarks',
    ];

    // Add entry form
    public ?int $addTripCodeId = null;
    public ?int $addVehicleId = null;
    public ?int $addDriverId = null;
    public ?int $addDriver2Id = null;
    public string $addBrand = '';
    public string $addBusNumber = '';
    public ?int $addSeatingCapacity = null;
    public string $addRoute = '';
    public string $addBusType = '';
    public string $addDepartureTerminal = '';
    public string $addArrivalTerminal = '';
    public string $addScheduledDeparture = '';
    public string $addActualDeparture = '';
    public string $addDirection = '';
    public string $addStatus = 'scheduled';
    public string $addRemarks = '';
    public string $addManualTripCode = '';
    public $addEvidencePhoto = null;

    // Edit entry form
    public ?int $editingEntryId = null;
    public ?int $editTripCodeId = null;
    public ?int $editVehicleId = null;
    public ?int $editDriverId = null;
    public ?int $editDriver2Id = null;
    public string $editBrand = '';
    public string $editBusNumber = '';
    public ?int $editSeatingCapacity = null;
    public string $editRoute = '';
    public string $editBusType = '';
    public string $editDepartureTerminal = '';
    public string $editArrivalTerminal = '';
    public string $editScheduledDeparture = '';
    public string $editActualDeparture = '';
    public string $editDirection = '';
    public string $editStatus = 'scheduled';
    public string $editRemarks = '';
    public string $editManualTripCode = '';

    public bool $showAddDialog = false;
    public bool $showEditDialog = false;

    // Status transition (KMR prompt) modal
    public bool $showStatusDialog = false;
    public ?int $statusEntryId = null;
    public string $statusTo = '';
    public ?int $statusKmr = null;
    public ?int $statusKmrSuggested = null;
    public string $statusEntryLabel = '';
    public string $statusOccurredAt = '';
    public string $statusReason = '';
    public string $statusNotes = '';

    // Driver event modal
    public bool $showDriverEventDialog = false;
    public ?int $driverEventEntryId = null;
    public string $driverEventSlot = 'driver1';
    public string $driverEventType = 'arrived';
    public string $driverEventOccurredAt = '';
    public ?int $driverEventReplacementDriverId = null;
    public string $driverEventReason = '';
    public string $driverEventNotes = '';
    public string $driverEventEntryLabel = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount(string $date = ''): void
    {
        $this->date = $date ?: now()->toDateString();
        $this->search = request('search', '');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDate(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, $this->sortableFields, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    // Auto-set actual departure time when evidence photo is uploaded
    public function updatedAddEvidencePhoto(): void
    {
        if ($this->addEvidencePhoto && !$this->addActualDeparture) {
            $this->addActualDeparture = now()->format('H:i');
        }
    }

    // Auto-fill from trip code (Add form)
    public function updatedAddTripCodeId($value): void
    {
        if ($value) {
            $tripCode = TripCode::with('defaultVehicle')->find($value);
            if ($tripCode) {
                $this->addRoute = $tripCode->origin_terminal . ' - ' . $tripCode->destination_terminal;
                $this->addBusType = $tripCode->bus_type?->value ?? $tripCode->bus_type ?? '';
                $this->addDepartureTerminal = $tripCode->origin_terminal;
                $this->addArrivalTerminal = $tripCode->destination_terminal;
                $this->addScheduledDeparture = $tripCode->scheduled_departure_time ?? '';
                $this->addDirection = $tripCode->direction?->value ?? $tripCode->direction ?? '';

                if ($v = $tripCode->defaultVehicle) {
                    $this->addVehicleId = $v->id;
                    $this->addBrand = $v->brand ?? '';
                    $this->addBusNumber = $v->bus_number ?? '';
                    $this->addSeatingCapacity = $v->seating_capacity;
                } else {
                    $this->addBrand = $tripCode->default_brand ?? $this->addBrand;
                    $this->addSeatingCapacity = $tripCode->default_seating_capacity ?? $this->addSeatingCapacity;
                }
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
                $this->addSeatingCapacity = $vehicle->seating_capacity;
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
            $tripCode = TripCode::with('defaultVehicle')->find($value);
            if ($tripCode) {
                $this->editRoute = $tripCode->origin_terminal . ' - ' . $tripCode->destination_terminal;
                $this->editBusType = $tripCode->bus_type?->value ?? $tripCode->bus_type ?? '';
                $this->editDepartureTerminal = $tripCode->origin_terminal;
                $this->editArrivalTerminal = $tripCode->destination_terminal;
                $this->editScheduledDeparture = $tripCode->scheduled_departure_time ?? '';
                $this->editDirection = $tripCode->direction?->value ?? $tripCode->direction ?? '';

                if ($v = $tripCode->defaultVehicle) {
                    $this->editVehicleId = $v->id;
                    $this->editBrand = $v->brand ?? '';
                    $this->editBusNumber = $v->bus_number ?? '';
                    $this->editSeatingCapacity = $v->seating_capacity;
                } else {
                    $this->editBrand = $tripCode->default_brand ?? $this->editBrand;
                    $this->editSeatingCapacity = $tripCode->default_seating_capacity ?? $this->editSeatingCapacity;
                }
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
                $this->editSeatingCapacity = $vehicle->seating_capacity;
                if (!$this->editBusType) {
                    $this->editBusType = $vehicle->bus_type?->value ?? $vehicle->bus_type ?? '';
                }
            }
        }
    }

    public function transitionStatus(
        int $entryId,
        string $to,
        ?int $kmr = null,
        ?string $occurredAt = null,
        ?string $reason = null,
        ?string $notes = null,
    ): void
    {
        try {
            $entry = DispatchEntry::with('vehicle')->findOrFail($entryId);
            app(TransitionStatus::class)->execute(
                $entry,
                DispatchStatus::from($to),
                auth()->user(),
                $kmr,
                $occurredAt ? Carbon::parse($occurredAt) : null,
                $reason,
                $notes,
            );
        } catch (\InvalidArgumentException $e) {
            session()->flash('dispatch_error', $e->getMessage());
        }
    }

    public function openStatusDialog(int $entryId, string $to): void
    {
        $entry = DispatchEntry::with('vehicle')->findOrFail($entryId);
        $this->statusEntryId = $entry->id;
        $this->statusTo = $to;
        $this->statusKmrSuggested = $to === 'departed'
            ? ($entry->vehicle?->current_kmr ?? null)
            : ($entry->kmr_at_dispatch ?? $entry->vehicle?->current_kmr ?? null);
        $this->statusKmr = $this->statusKmrSuggested;
        $this->statusEntryLabel = trim(($entry->tripCode->code ?? '') . ' · ' . ($entry->bus_number ?? ''), ' ·');
        $this->statusOccurredAt = now()->format('Y-m-d\TH:i');
        $this->statusReason = '';
        $this->statusNotes = '';
        $this->showStatusDialog = true;
    }

    public function confirmStatusDialog(): void
    {
        if (!$this->statusEntryId || !$this->statusTo) return;
        $this->validate([
            'statusOccurredAt' => 'required|date',
            'statusReason' => 'nullable|string|max:255',
            'statusNotes' => 'nullable|string|max:1000',
        ]);

        $this->transitionStatus(
            $this->statusEntryId,
            $this->statusTo,
            in_array($this->statusTo, ['departed', 'arrived'], true) ? ($this->statusKmr ?: null) : null,
            $this->statusOccurredAt,
            $this->statusReason ?: null,
            $this->statusNotes ?: null,
        );
        $this->showStatusDialog = false;
        $this->statusEntryId = null;
        $this->statusTo = '';
        $this->statusKmr = null;
        $this->statusKmrSuggested = null;
        $this->statusEntryLabel = '';
        $this->statusOccurredAt = '';
        $this->statusReason = '';
        $this->statusNotes = '';
    }

    public function openDriverEventDialog(int $entryId, string $slot, string $type): void
    {
        $entry = DispatchEntry::with(['tripCode', 'driver', 'driver2'])->findOrFail($entryId);
        $this->driverEventEntryId = $entry->id;
        $this->driverEventSlot = in_array($slot, ['driver1', 'driver2'], true) ? $slot : 'driver1';
        $this->driverEventType = in_array($type, ['arrived', 'cutoff'], true) ? $type : 'arrived';
        $this->driverEventOccurredAt = now()->format('Y-m-d\TH:i');
        $this->driverEventReplacementDriverId = null;
        $this->driverEventReason = '';
        $this->driverEventNotes = '';
        $this->driverEventEntryLabel = trim(($entry->tripCode->code ?? '') . ' · ' . ($entry->bus_number ?? ''), ' ·');
        $this->showDriverEventDialog = true;
    }

    public function confirmDriverEventDialog(): void
    {
        if (!$this->driverEventEntryId) return;

        $this->validate([
            'driverEventOccurredAt' => 'required|date',
            'driverEventReplacementDriverId' => 'nullable|exists:drivers,id',
            'driverEventReason' => 'nullable|string|max:255',
            'driverEventNotes' => 'nullable|string|max:1000',
        ]);

        $entry = DispatchEntry::findOrFail($this->driverEventEntryId);
        $occurredAt = Carbon::parse($this->driverEventOccurredAt);
        $isDriver1 = $this->driverEventSlot === 'driver1';
        $driverId = $isDriver1 ? $entry->driver_id : $entry->driver2_id;

        if ($this->driverEventType === 'arrived') {
            $entry->{$isDriver1 ? 'driver1_arrived_at' : 'driver2_arrived_at'} = $occurredAt;
        } else {
            $entry->{$isDriver1 ? 'driver1_cutoff_at' : 'driver2_cutoff_at'} = $occurredAt;
            $entry->{$isDriver1 ? 'replacement_driver1_id' : 'replacement_driver2_id'} = $this->driverEventReplacementDriverId;
        }

        if ($this->driverEventNotes) {
            $entry->operations_notes = trim(($entry->operations_notes ? $entry->operations_notes . "\n" : '') . $this->driverEventNotes);
        }
        $entry->save();

        $entry->events()->create([
            'event_type' => $this->driverEventType === 'arrived' ? $this->driverEventSlot . '_arrived' : $this->driverEventSlot . '_cutoff',
            'occurred_at' => $occurredAt,
            'actor_user_id' => auth()->id(),
            'driver_id' => $driverId,
            'vehicle_id' => $entry->vehicle_id,
            'old_value' => $driverId ? (string) $driverId : null,
            'new_value' => $this->driverEventReplacementDriverId ? (string) $this->driverEventReplacementDriverId : null,
            'reason' => $this->driverEventReason ?: null,
            'notes' => $this->driverEventNotes ?: null,
        ]);

        $this->showDriverEventDialog = false;
        $this->driverEventEntryId = null;
    }

    public function createDispatchDay(): void
    {
        $day = DispatchDay::withTrashed()
            ->whereDate('service_date', $this->date)
            ->first();

        if (! $day) {
            $day = DispatchDay::create([
                'service_date' => $this->date,
                'created_by' => auth()->id(),
                'notes' => '',
            ]);
        } elseif ($day->trashed()) {
            $day->restore();
        }

        // Auto-populate from active trip codes
        if ($day->wasRecentlyCreated || $day->entries()->count() === 0) {
            app(DispatchService::class)->populateFromTripCodes($day);
        }
    }

    public function submitAddEntry(): void
    {
        $dispatchDay = DispatchDay::whereDate('service_date', $this->date)->first();
        if (!$dispatchDay) return;

        $this->validate([
            'addTripCodeId' => 'nullable|exists:trip_codes,id',
            'addVehicleId' => 'nullable|exists:vehicles,id',
            'addDriverId' => 'nullable|exists:drivers,id',
            'addDriver2Id' => 'nullable|exists:drivers,id',
            'addEvidencePhoto' => 'nullable|image|max:5120',
        ]);

        $entry = $dispatchDay->entries()->create([
            'trip_code_id' => $this->addTripCodeId,
            'manual_trip_code' => $this->addManualTripCode ?: null,
            'vehicle_id' => $this->addVehicleId,
            'driver_id' => $this->addDriverId,
            'driver2_id' => $this->addDriver2Id,
            'dispatcher_user_id' => auth()->id(),
            'brand' => $this->addBrand,
            'bus_number' => $this->addBusNumber,
            'seating_capacity' => $this->addSeatingCapacity,
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

        if ($this->addEvidencePhoto) {
            $path = $this->addEvidencePhoto->store('dispatch-evidence', 'local');
            $entry->attachments()->create([
                'file_name' => $this->addEvidencePhoto->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $this->addEvidencePhoto->getMimeType(),
                'file_size' => $this->addEvidencePhoto->getSize(),
                'label' => 'Dispatch evidence',
                'uploaded_by' => auth()->id(),
            ]);
        }

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
        $this->editSeatingCapacity = $entry->seating_capacity;
        $this->editRoute = $entry->route ?? '';
        $this->editBusType = $entry->bus_type ?? '';
        $this->editDepartureTerminal = $entry->departure_terminal ?? '';
        $this->editArrivalTerminal = $entry->arrival_terminal ?? '';
        $this->editScheduledDeparture = $entry->scheduled_departure ? \Carbon\Carbon::parse($entry->scheduled_departure)->format('H:i') : '';
        $this->editActualDeparture = $entry->actual_departure ? \Carbon\Carbon::parse($entry->actual_departure)->format('H:i') : '';
        $this->editDirection = $entry->direction ?? '';
        $this->editStatus = $entry->status?->value ?? $entry->status ?? 'scheduled';
        $this->editRemarks = $entry->remarks ?? '';
        $this->editManualTripCode = $entry->manual_trip_code ?? '';
        $this->showEditDialog = true;
    }

    public function submitEditEntry(): void
    {
        if (!$this->editingEntryId) return;

        $entry = DispatchEntry::findOrFail($this->editingEntryId);
        $entry->update([
            'trip_code_id' => $this->editTripCodeId,
            'manual_trip_code' => $this->editManualTripCode ?: null,
            'vehicle_id' => $this->editVehicleId,
            'driver_id' => $this->editDriverId,
            'driver2_id' => $this->editDriver2Id,
            'brand' => $this->editBrand,
            'bus_number' => $this->editBusNumber,
            'seating_capacity' => $this->editSeatingCapacity,
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
        $this->addSeatingCapacity = null;
        $this->addRoute = '';
        $this->addBusType = '';
        $this->addDepartureTerminal = '';
        $this->addArrivalTerminal = '';
        $this->addScheduledDeparture = '';
        $this->addActualDeparture = '';
        $this->addDirection = '';
        $this->addStatus = 'scheduled';
        $this->addRemarks = '';
        $this->addManualTripCode = '';
        $this->addEvidencePhoto = null;
    }

    public function render()
    {
        $dispatchDay = DispatchDay::with('summary.items')
            ->whereDate('service_date', $this->date)
            ->first();

        $entries = $dispatchDay
            ? DispatchEntry::where('dispatch_day_id', $dispatchDay->id)
                ->with(['tripCode', 'vehicle', 'driver', 'driver2', 'replacementDriver1', 'replacementDriver2'])
                ->leftJoin('trip_codes as sort_trip_codes', 'dispatch_entries.trip_code_id', '=', 'sort_trip_codes.id')
                ->leftJoin('drivers as sort_drivers', 'dispatch_entries.driver_id', '=', 'sort_drivers.id')
                ->select('dispatch_entries.*')
                ->when($this->search, fn ($query) => $query->where(function ($query) {
                    $query->where('dispatch_entries.bus_number', 'like', "%{$this->search}%")
                        ->orWhere('dispatch_entries.brand', 'like', "%{$this->search}%")
                        ->orWhere('dispatch_entries.route', 'like', "%{$this->search}%")
                        ->orWhere('dispatch_entries.departure_terminal', 'like', "%{$this->search}%")
                        ->orWhere('dispatch_entries.arrival_terminal', 'like', "%{$this->search}%")
                        ->orWhere('dispatch_entries.remarks', 'like', "%{$this->search}%")
                        ->orWhere('sort_trip_codes.code', 'like', "%{$this->search}%")
                        ->orWhere('sort_drivers.name', 'like', "%{$this->search}%");
                }))
                ->tap(fn ($query) => match ($this->sortField) {
                    'trip' => $query->orderBy('sort_trip_codes.code', $this->sortDirection),
                    'driver' => $query->orderBy('sort_drivers.name', $this->sortDirection),
                    'bus_number',
                    'direction',
                    'route',
                    'scheduled_departure',
                    'status',
                    'remarks',
                    'sort_order' => $query->orderBy('dispatch_entries.' . $this->sortField, $this->sortDirection),
                    default => $query->orderBy('dispatch_entries.sort_order', 'asc'),
                })
                ->orderBy('dispatch_entries.sort_order')
                ->paginate($this->perPage)
            : null;

        $tripCodes = TripCode::where('is_active', true)->orderBy('code')->get();
        $vehicles = Vehicle::where('status', VehicleStatus::OK)->orderBy('bus_number')->get();
        $drivers = Driver::where('is_active', true)->orderBy('name')->get();

        return view('livewire.dispatch-board', compact('dispatchDay', 'entries', 'tripCodes', 'vehicles', 'drivers'));
    }
}

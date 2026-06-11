<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasTableControls;
use App\Models\DispatchEntry;
use Livewire\Component;
use Livewire\WithPagination;

class HistoryTable extends Component
{
    use HasTableControls;
    use WithPagination;

    public string $search = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $direction = '';
    public string $status = '';
    public int $perPage = 20;
    public array $perPageOptions = [5, 10, 15, 20, 30, 40, 50, 100];
    public string $sortField = 'service_date';
    public string $sortDirection = 'desc';

    protected array $sortableFields = ['service_date', 'brand', 'trip', 'bus_number', 'route', 'direction', 'scheduled_departure', 'actual_departure', 'driver', 'status', 'remarks'];

    protected $queryString = [
        'search' => ['except' => ''],
        'dateFrom' => ['except' => '', 'as' => 'date_from'],
        'dateTo' => ['except' => '', 'as' => 'date_to'],
        'direction' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'dateFrom', 'dateTo', 'direction', 'status']);
        $this->resetPage();
    }

    public function render()
    {
        $entries = DispatchEntry::with(['dispatchDay', 'vehicle', 'tripCode', 'driver'])
            ->leftJoin('dispatch_days as sort_days', 'dispatch_entries.dispatch_day_id', '=', 'sort_days.id')
            ->leftJoin('trip_codes as sort_trip_codes', 'dispatch_entries.trip_code_id', '=', 'sort_trip_codes.id')
            ->leftJoin('drivers as sort_drivers', 'dispatch_entries.driver_id', '=', 'sort_drivers.id')
            ->select('dispatch_entries.*')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('bus_number', 'like', "%{$this->search}%")
                  ->orWhere('route', 'like', "%{$this->search}%")
                  ->orWhereHas('tripCode', fn ($q) => $q->where('code', 'like', "%{$this->search}%"));
            }))
            ->when($this->dateFrom, fn ($q) => $q->whereHas('dispatchDay', fn ($q) => $q->where('service_date', '>=', $this->dateFrom)))
            ->when($this->dateTo, fn ($q) => $q->whereHas('dispatchDay', fn ($q) => $q->where('service_date', '<=', $this->dateTo)))
            ->when($this->direction, fn ($q) => $q->where('direction', $this->direction))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->tap(fn ($query) => $this->applyTableSort($query, [
                'service_date' => 'sort_days.service_date',
                'trip' => 'sort_trip_codes.code',
                'driver' => 'sort_drivers.name',
                'brand' => 'dispatch_entries.brand',
                'bus_number' => 'dispatch_entries.bus_number',
                'route' => 'dispatch_entries.route',
                'direction' => 'dispatch_entries.direction',
                'scheduled_departure' => 'dispatch_entries.scheduled_departure',
                'actual_departure' => 'dispatch_entries.actual_departure',
                'status' => 'dispatch_entries.status',
                'remarks' => 'dispatch_entries.remarks',
            ]))
            ->paginate($this->perPage);

        return view('livewire.history-table', compact('entries'));
    }
}

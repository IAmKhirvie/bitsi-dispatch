<?php

namespace App\Livewire;

use App\Models\DispatchEntry;
use Livewire\Component;
use Livewire\WithPagination;

class HistoryTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $direction = '';
    public string $status = '';

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
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('bus_number', 'like', "%{$this->search}%")
                  ->orWhere('route', 'like', "%{$this->search}%")
                  ->orWhereHas('tripCode', fn ($q) => $q->where('code', 'like', "%{$this->search}%"));
            }))
            ->when($this->dateFrom, fn ($q) => $q->whereHas('dispatchDay', fn ($q) => $q->where('service_date', '>=', $this->dateFrom)))
            ->when($this->dateTo, fn ($q) => $q->whereHas('dispatchDay', fn ($q) => $q->where('service_date', '<=', $this->dateTo)))
            ->when($this->direction, fn ($q) => $q->where('direction', $this->direction))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->latest('id')
            ->paginate(20);

        return view('livewire.history-table', compact('entries'));
    }
}

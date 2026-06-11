<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\HasTableControls;
use App\Models\TripCode;
use Livewire\Component;
use Livewire\WithPagination;

class TripCodeTable extends Component
{
    use HasTableControls;
    use WithPagination;

    public string $search = '';
    public string $directionFilter = '';
    public bool $showTrashed = false;
    public int $perPage = 15;
    public array $perPageOptions = [5, 10, 15, 20, 30, 40, 50, 100];
    public string $sortField = 'code';
    public string $sortDirection = 'asc';

    protected array $sortableFields = [
        'code',
        'operator',
        'direction',
        'origin_terminal',
        'destination_terminal',
        'scheduled_departure_time',
        'bus_type',
        'is_active',
        'created_at',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'directionFilter' => ['except' => '', 'as' => 'direction'],
        'showTrashed' => ['except' => false, 'as' => 'trashed'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDirectionFilter(): void
    {
        $this->resetPage();
    }

    public function updatingShowTrashed(): void
    {
        $this->resetPage();
    }

    public function deleteTripCode(int $tripCodeId): void
    {
        TripCode::findOrFail($tripCodeId)->delete();
        session()->flash('status', 'Trip code deleted successfully.');
    }

    public function restoreTripCode(int $tripCodeId): void
    {
        TripCode::onlyTrashed()->findOrFail($tripCodeId)->restore();
        session()->flash('status', 'Trip code restored successfully.');
    }

    public function toggleActive(int $tripCodeId): void
    {
        $tc = TripCode::findOrFail($tripCodeId);
        $tc->update(['is_active' => !$tc->is_active]);
    }

    public function render()
    {
        $tripCodes = TripCode::query()
            ->when($this->showTrashed, fn ($q) => $q->onlyTrashed())
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('code', 'like', "%{$this->search}%")
                  ->orWhere('operator', 'like', "%{$this->search}%")
                  ->orWhere('origin_terminal', 'like', "%{$this->search}%")
                  ->orWhere('destination_terminal', 'like', "%{$this->search}%");
            }))
            ->when($this->directionFilter, fn ($q) => $q->where('direction', $this->directionFilter))
            ->tap(fn ($query) => $this->applyTableSort($query))
            ->paginate($this->perPage);

        return view('livewire.admin.trip-code-table', compact('tripCodes'));
    }
}

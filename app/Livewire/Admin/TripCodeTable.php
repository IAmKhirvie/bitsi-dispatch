<?php

namespace App\Livewire\Admin;

use App\Models\TripCode;
use Livewire\Component;
use Livewire\WithPagination;

class TripCodeTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $directionFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'directionFilter' => ['except' => '', 'as' => 'direction'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDirectionFilter(): void
    {
        $this->resetPage();
    }

    public function deleteTripCode(int $tripCodeId): void
    {
        TripCode::findOrFail($tripCodeId)->delete();
        session()->flash('status', 'Trip code deleted successfully.');
    }

    public function toggleActive(int $tripCodeId): void
    {
        $tc = TripCode::findOrFail($tripCodeId);
        $tc->update(['is_active' => !$tc->is_active]);
    }

    public function render()
    {
        $tripCodes = TripCode::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('code', 'like', "%{$this->search}%")
                  ->orWhere('operator', 'like', "%{$this->search}%")
                  ->orWhere('origin_terminal', 'like', "%{$this->search}%")
                  ->orWhere('destination_terminal', 'like', "%{$this->search}%");
            }))
            ->when($this->directionFilter, fn ($q) => $q->where('direction', $this->directionFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.trip-code-table', compact('tripCodes'));
    }
}

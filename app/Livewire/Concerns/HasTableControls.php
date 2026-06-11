<?php

namespace App\Livewire\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasTableControls
{
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

    protected function applyTableSort(Builder $query, array $sortMap = []): Builder
    {
        if (! in_array($this->sortField, $this->sortableFields, true)) {
            $this->sortField = $this->sortableFields[0] ?? 'created_at';
        }

        $sort = $sortMap[$this->sortField] ?? $this->sortField;

        if (is_callable($sort)) {
            $sort($query, $this->sortDirection);

            return $query;
        }

        return $query->orderBy($sort, $this->sortDirection);
    }
}

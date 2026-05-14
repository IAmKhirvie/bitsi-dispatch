<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\HasTableControls;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserTable extends Component
{
    use HasTableControls;
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public int $perPage = 15;
    public array $perPageOptions = [5, 10, 15, 20, 30, 40, 50, 100];
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    protected array $sortableFields = ['name', 'email', 'role', 'phone', 'is_active', 'created_at'];

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => '', 'as' => 'role'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function deleteUser(int $userId): void
    {
        User::findOrFail($userId)->delete();
        session()->flash('status', 'User deleted successfully.');
    }

    public function toggleActive(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['is_active' => !$user->is_active]);
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->roleFilter, fn ($q) => $q->where('role', $this->roleFilter))
            ->tap(fn ($query) => $this->applyTableSort($query))
            ->paginate($this->perPage);

        return view('livewire.admin.user-table', compact('users'));
    }
}

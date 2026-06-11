<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private ?string $dateFrom = null,
        private ?string $dateTo = null,
    ) {}

    public function collection(): Collection
    {
        return User::query()->orderBy('name')->get();
    }

    public function headings(): array
    {
        return ['Name', 'Email', 'Role', 'Phone', 'Active', 'Created At', 'Updated At'];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->getRoleNames()->first() ?? 'N/A',
            $user->phone ?? '--',
            $user->is_active ? 'Yes' : 'No',
            $user->created_at?->format('Y-m-d H:i') ?? '',
            $user->updated_at?->format('Y-m-d H:i') ?? '',
        ];
    }
}
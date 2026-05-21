<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DispatchDay;
use App\Models\DispatchEntry;
use App\Models\Driver;
use App\Models\TripCode;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TrashController extends Controller
{
    /**
     * Resource registry: short key => [model, label, searchable columns, display columns].
     */
    protected array $resources = [
        'users'            => [User::class,         'Users',           ['name', 'email'],                   ['name', 'email', 'role']],
        'drivers'          => [Driver::class,       'Drivers',         ['name', 'license_number', 'phone'], ['name', 'license_number', 'phone']],
        'vehicles'         => [Vehicle::class,      'Vehicles',        ['plate_number', 'brand', 'model'],  ['plate_number', 'brand', 'model']],
        'trip-codes'       => [TripCode::class,     'Trip Codes',      ['code', 'route_name'],              ['code', 'route_name']],
        'dispatch-entries' => [DispatchEntry::class,'Dispatch Entries',['remarks'],                         ['scheduled_departure', 'status', 'remarks']],
        'dispatch-days'    => [DispatchDay::class,  'Dispatch Days',   [],                                  ['date']],
    ];

    public function overview(): Response
    {
        $counts = [];
        foreach ($this->resources as $key => [$model, $label]) {
            $counts[] = [
                'key'   => $key,
                'label' => $label,
                'count' => $model::onlyTrashed()->count(),
            ];
        }

        return Inertia::render('admin/Trash/Overview', [
            'resources' => $counts,
        ]);
    }

    public function index(Request $request, string $resource): Response
    {
        $this->guardResource($resource);
        [$model, $label, $searchable, $columns] = $this->resources[$resource];

        $query = $model::onlyTrashed();

        $search = trim((string) $request->input('search', ''));
        if ($search !== '' && ! empty($searchable)) {
            $query->where(function ($q) use ($searchable, $search) {
                foreach ($searchable as $col) {
                    $q->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        $items = $query->latest('deleted_at')->paginate(15)->withQueryString();

        return Inertia::render('admin/Trash/Index', [
            'resource' => $resource,
            'label'    => $label,
            'columns'  => $columns,
            'items'    => $items,
            'filters'  => ['search' => $search],
        ]);
    }

    public function restore(string $resource, int $id): RedirectResponse
    {
        $this->guardResource($resource);
        [$model] = $this->resources[$resource];

        $record = $model::onlyTrashed()->findOrFail($id);
        $record->restore();

        return back()->with('success', 'Item restored.');
    }

    public function forceDelete(string $resource, int $id): RedirectResponse
    {
        $this->guardResource($resource);
        [$model] = $this->resources[$resource];

        $record = $model::onlyTrashed()->findOrFail($id);
        $record->forceDelete();

        return back()->with('success', 'Item permanently deleted.');
    }

    public function emptyTrash(string $resource): RedirectResponse
    {
        $this->guardResource($resource);
        [$model] = $this->resources[$resource];

        $model::onlyTrashed()->forceDelete();

        return back()->with('success', 'Trash emptied.');
    }

    protected function guardResource(string $resource): void
    {
        abort_unless(array_key_exists($resource, $this->resources), 404);
    }
}

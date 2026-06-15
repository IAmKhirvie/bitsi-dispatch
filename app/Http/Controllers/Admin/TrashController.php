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
use Illuminate\View\View;

class TrashController extends Controller
{
    /**
     * Resource registry: short key => [model, label, searchable columns, display columns].
     */
    protected array $resources = [
        'users'            => [User::class,          'Users',            ['name', 'email'],                    ['name', 'email', 'role']],
        'drivers'          => [Driver::class,        'Drivers',          ['name', 'license_number', 'phone'],  ['name', 'license_number', 'phone']],
        'vehicles'         => [Vehicle::class,       'Buses',            ['bus_number', 'plate_number', 'brand'], ['bus_number', 'plate_number', 'brand']],
        'trip-codes'       => [TripCode::class,     'Trip Codes',        ['code', 'origin_terminal'],          ['code', 'origin_terminal', 'destination_terminal']],
        'dispatch-entries' => [DispatchEntry::class, 'Dispatch Entries', ['remarks', 'bus_number'],            ['bus_number', 'route', 'scheduled_departure', 'status']],
        'dispatch-days'    => [DispatchDay::class,   'Dispatch Days',    [],                                   ['service_date']],
    ];

    public function overview(): View
    {
        $resources = [];
        foreach ($this->resources as $key => [$model, $label]) {
            $resources[] = [
                'key'   => $key,
                'label' => $label,
                'count' => $model::onlyTrashed()->count(),
            ];
        }

        return view('admin.trash.overview', compact('resources'));
    }

    public function index(Request $request, string $resource): View
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

        return view('admin.trash.index', compact('resource', 'label', 'columns', 'items', 'search'));
    }

    public function restore(string $resource, int $id): RedirectResponse
    {
        $this->guardResource($resource);
        [$model] = $this->resources[$resource];

        $model::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Item restored.');
    }

    public function forceDelete(string $resource, int $id): RedirectResponse
    {
        $this->guardResource($resource);
        [$model] = $this->resources[$resource];

        $model::onlyTrashed()->findOrFail($id)->forceDelete();

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

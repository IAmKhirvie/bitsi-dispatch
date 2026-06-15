<?php

namespace App\Http\Controllers;

use App\Models\DispatchEntry;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $category = (string) $request->query('category', 'all');
        $categories = $this->categories($request);
        $category = array_key_exists($category, $categories) ? $category : 'all';

        $results = $this->search($request, $query, $category, 10);

        return view('search.index', [
            'query' => $query,
            'category' => $category,
            'categories' => $categories,
            'results' => $results,
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        $category = (string) $request->query('category', 'all');
        $categories = $this->categories($request);
        $category = array_key_exists($category, $categories) ? $category : 'all';
        $results = $this->search($request, $query, $category, 5);
        $items = [];

        foreach ($results['buses'] as $result) {
            $items[] = [
                'category' => 'Buses',
                'title' => trim(($result->bus_number ?? '') . ' · ' . ($result->brand ?? ''), ' ·'),
                'subtitle' => trim(($result->current_location ?? 'No location') . ' · ' . ($result->plate_number ?? 'No plate'), ' ·'),
                'url' => route('admin.vehicles.index', ['search' => $query], false),
            ];
        }

        foreach ($results['dispatch'] as $result) {
            $items[] = [
                'category' => 'Dispatch',
                'title' => trim(($result->tripCode->code ?? 'No trip') . ' · ' . ($result->bus_number ?? 'No bus'), ' ·'),
                'subtitle' => trim(($result->dispatchDay?->service_date ?? 'No date') . ' · ' . ($result->route ?? 'No route'), ' ·'),
                'url' => route('dispatch.index', ['date' => $result->dispatchDay?->service_date, 'search' => $query], false),
            ];
        }

        foreach ($results['drivers'] as $result) {
            $items[] = [
                'category' => 'Drivers',
                'title' => $result->name,
                'subtitle' => trim(($result->phone ?? 'No phone') . ' · ' . ($result->license_number ?? 'No ID number'), ' ·'),
                'url' => route('admin.drivers.index', ['search' => $query], false),
            ];
        }

        foreach ($results['users'] as $result) {
            $items[] = [
                'category' => 'Users',
                'title' => $result->name,
                'subtitle' => trim($result->email . ' · ' . (is_object($result->role) ? $result->role->label() : $result->role), ' ·'),
                'url' => route('admin.users.index', ['search' => $query], false),
            ];
        }

        return response()->json([
            'items' => array_slice($items, 0, 8),
            'query' => $query,
            'category' => $category,
            'searchUrl' => route('search.index', ['q' => $query, 'category' => $category], false),
        ]);
    }

    private function search(Request $request, string $query, string $category, int $limit): array
    {
        $results = [
            'buses' => collect(),
            'dispatch' => collect(),
            'drivers' => collect(),
            'users' => collect(),
        ];

        if ($query === '') {
            return $results;
        }

        if ($request->user()?->is_admin && in_array($category, ['all', 'buses'], true)) {
            $results['buses'] = Vehicle::query()
                ->where(function ($builder) use ($query) {
                    $builder->where('bus_number', 'like', "%{$query}%")
                        ->orWhere('brand', 'like', "%{$query}%")
                        ->orWhere('bus_type', 'like', "%{$query}%")
                        ->orWhere('plate_number', 'like', "%{$query}%")
                        ->orWhere('current_location', 'like', "%{$query}%");
                })
                ->orderBy('bus_number')
                ->limit($limit)
                ->get();
        }

        if (in_array($category, ['all', 'dispatch'], true)) {
            $results['dispatch'] = DispatchEntry::query()
                ->with(['dispatchDay', 'tripCode', 'driver'])
                ->where(function ($builder) use ($query) {
                    $builder->where('bus_number', 'like', "%{$query}%")
                        ->orWhere('brand', 'like', "%{$query}%")
                        ->orWhere('route', 'like', "%{$query}%")
                        ->orWhere('departure_terminal', 'like', "%{$query}%")
                        ->orWhere('arrival_terminal', 'like', "%{$query}%")
                        ->orWhere('remarks', 'like', "%{$query}%")
                        ->orWhereHas('tripCode', fn ($trip) => $trip->where('code', 'like', "%{$query}%"))
                        ->orWhereHas('driver', fn ($driver) => $driver->where('name', 'like', "%{$query}%"));
                })
                ->latest('id')
                ->limit($limit)
                ->get();
        }

        if ($request->user()?->is_admin && in_array($category, ['all', 'drivers'], true)) {
            $results['drivers'] = Driver::query()
                ->where(function ($builder) use ($query) {
                    $builder->where('name', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%")
                        ->orWhere('license_number', 'like', "%{$query}%")
                        ->orWhere('status', 'like', "%{$query}%");
                })
                ->orderBy('name')
                ->limit($limit)
                ->get();
        }

        if ($request->user()?->is_admin && in_array($category, ['all', 'users'], true)) {
            $results['users'] = User::query()
                ->where(function ($builder) use ($query) {
                    $builder->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%")
                        ->orWhere('role', 'like', "%{$query}%");
                })
                ->orderBy('name')
                ->limit($limit)
                ->get();
        }

        return $results;
    }

    private function categories(Request $request): array
    {
        $categories = [
            'all' => 'All',
            'dispatch' => 'Dispatch',
        ];

        if ($request->user()?->is_admin) {
            $categories['buses'] = 'Buses';
            $categories['drivers'] = 'Drivers';
            $categories['users'] = 'Users';
        }

        return $categories;
    }
}

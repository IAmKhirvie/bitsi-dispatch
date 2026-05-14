<?php

namespace App\Http\Controllers;

use App\Models\DispatchEntry;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
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

        $results = [
            'buses' => collect(),
            'dispatch' => collect(),
            'drivers' => collect(),
            'users' => collect(),
        ];

        if ($query !== '') {
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
                    ->limit(10)
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
                    ->limit(10)
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
                    ->limit(10)
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
                    ->limit(10)
                    ->get();
            }
        }

        return view('search.index', [
            'query' => $query,
            'category' => $category,
            'categories' => $categories,
            'results' => $results,
        ]);
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

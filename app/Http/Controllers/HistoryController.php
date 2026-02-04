<?php

namespace App\Http\Controllers;

use App\Models\DispatchEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HistoryController extends Controller
{
    public function index(Request $request): Response
    {
        $entries = DispatchEntry::query()
            ->with(['dispatchDay', 'vehicle', 'tripCode', 'driver'])
            ->when($request->date_from, fn ($q, $d) =>
                $q->whereHas('dispatchDay', fn ($q2) => $q2->where('service_date', '>=', $d))
            )
            ->when($request->date_to, fn ($q, $d) =>
                $q->whereHas('dispatchDay', fn ($q2) => $q2->where('service_date', '<=', $d))
            )
            ->when($request->bus_number, fn ($q, $b) => $q->where('bus_number', 'like', "%{$b}%"))
            ->when($request->trip_code, fn ($q, $t) =>
                $q->whereHas('tripCode', fn ($q2) => $q2->where('code', 'like', "%{$t}%"))
            )
            ->when($request->direction, fn ($q, $d) => $q->where('direction', $d))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->route, fn ($q, $r) => $q->where('route', 'like', "%{$r}%"))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('history/Index', [
            'entries' => $entries,
            'filters' => $request->only([
                'date_from', 'date_to', 'bus_number', 'trip_code',
                'direction', 'status', 'route',
            ]),
        ]);
    }
}

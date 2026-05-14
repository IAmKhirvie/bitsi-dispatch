@extends('layouts.app')

@section('title', 'Search - BITSI Dispatch')

@section('content')
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div>
            <h1 class="text-2xl font-bold">Search Results</h1>
            <p class="text-sm text-muted-foreground">
                {{ $query !== '' ? 'Showing matches for "' . $query . '"' : 'Enter a search term to find records across BITSI.' }}
            </p>
        </div>

        @if ($query === '')
            <div class="rounded-lg border bg-card p-8 text-center text-sm text-muted-foreground">
                Use the search field in the top bar to search buses, dispatch entries, drivers, and users.
            </div>
        @else
            @php
                $sections = [
                    'buses' => ['title' => 'Buses', 'route' => fn () => route('admin.vehicles.index', ['search' => $query], false)],
                    'dispatch' => ['title' => 'Dispatch', 'route' => fn () => route('history.index', ['search' => $query], false)],
                    'drivers' => ['title' => 'Drivers', 'route' => fn () => route('admin.drivers.index', ['search' => $query], false)],
                    'users' => ['title' => 'Users', 'route' => fn () => route('admin.users.index', ['search' => $query], false)],
                ];
            @endphp

            @foreach ($sections as $key => $section)
                @continue(! array_key_exists($key, $categories))
                @continue($category !== 'all' && $category !== $key)
                @continue(! isset($results[$key]))

                <section class="rounded-lg border bg-card">
                    <div class="flex items-center justify-between border-b px-4 py-3">
                        <h2 class="text-sm font-semibold">{{ $section['title'] }}</h2>
                        <a href="{{ $section['route']() }}" class="text-xs font-medium text-primary hover:underline">Open table</a>
                    </div>
                    <div class="divide-y">
                        @forelse ($results[$key] as $result)
                            @if ($key === 'buses')
                                <a href="{{ route('admin.vehicles.index', ['search' => $query], false) }}" class="block px-4 py-3 hover:bg-muted/40">
                                    <div class="font-medium">{{ $result->bus_number }} · {{ $result->brand }}</div>
                                    <div class="text-xs text-muted-foreground">{{ $result->current_location ?? 'No location' }} · {{ $result->plate_number ?? 'No plate' }}</div>
                                </a>
                            @elseif ($key === 'dispatch')
                                <a href="{{ route('dispatch.index', ['date' => $result->dispatchDay?->service_date, 'search' => $query], false) }}" class="block px-4 py-3 hover:bg-muted/40">
                                    <div class="font-medium">{{ $result->tripCode->code ?? 'No trip' }} · {{ $result->bus_number ?? 'No bus' }}</div>
                                    <div class="text-xs text-muted-foreground">{{ $result->dispatchDay?->service_date ?? 'No date' }} · {{ $result->route ?? 'No route' }} · {{ $result->driver->name ?? 'No driver' }}</div>
                                </a>
                            @elseif ($key === 'drivers')
                                <a href="{{ route('admin.drivers.index', ['search' => $query], false) }}" class="block px-4 py-3 hover:bg-muted/40">
                                    <div class="font-medium">{{ $result->name }}</div>
                                    <div class="text-xs text-muted-foreground">{{ $result->phone ?? 'No phone' }} · {{ $result->license_number ?? 'No license' }}</div>
                                </a>
                            @elseif ($key === 'users')
                                <a href="{{ route('admin.users.index', ['search' => $query], false) }}" class="block px-4 py-3 hover:bg-muted/40">
                                    <div class="font-medium">{{ $result->name }}</div>
                                    <div class="text-xs text-muted-foreground">{{ $result->email }} · {{ is_object($result->role) ? $result->role->label() : $result->role }}</div>
                                </a>
                            @endif
                        @empty
                            <div class="px-4 py-6 text-sm text-muted-foreground">No matches.</div>
                        @endforelse
                    </div>
                </section>
            @endforeach
        @endif
    </div>
@endsection

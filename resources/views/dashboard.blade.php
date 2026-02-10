@extends('layouts.app')

@section('title', 'Dashboard - BITSI Dispatch')

@section('content')
    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        {{-- Stat Cards --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Today's Trips --}}
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">Today's Trips</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-muted-foreground"><path d="M8 6v6"/><path d="M15 6v6"/><path d="M2 12h19.6"/><path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3"/><circle cx="7" cy="18" r="2"/><path d="M9 18h5"/><circle cx="16" cy="18" r="2"/></svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $stats['today_trips'] }}</div>
                    <p class="text-xs text-muted-foreground">Total dispatched trips today</p>
                </div>
            </div>

            {{-- Departed --}}
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">Departed</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-blue-500"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $stats['departed'] }}</div>
                    <p class="text-xs text-muted-foreground">Buses that have departed</p>
                </div>
            </div>

            {{-- On Route --}}
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">On Route</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-indigo-500"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $stats['on_route'] }}</div>
                    <p class="text-xs text-muted-foreground">Currently in transit</p>
                </div>
            </div>

            {{-- Cancelled --}}
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">Cancelled</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-red-500"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $stats['cancelled'] }}</div>
                    <p class="text-xs text-muted-foreground">Cancelled trips today</p>
                </div>
            </div>

            {{-- Active Vehicles --}}
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">Active Vehicles</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-green-500"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $stats['active_vehicles'] }}</div>
                    <p class="text-xs text-muted-foreground">Vehicles in service</p>
                </div>
            </div>

            {{-- Under Repair --}}
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">Under Repair</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-red-500"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $stats['under_repair'] }}</div>
                    <p class="text-xs text-muted-foreground">Vehicles under repair</p>
                </div>
            </div>

            {{-- PMS Warning --}}
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">PMS Warning</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-orange-500"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $stats['pms_warning'] }}</div>
                    <p class="text-xs text-muted-foreground">Vehicles needing maintenance</p>
                </div>
            </div>

            {{-- Active Drivers --}}
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">Active Drivers</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-green-500"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $stats['active_drivers'] }}</div>
                    <p class="text-xs text-muted-foreground">Drivers on roster</p>
                </div>
            </div>
        </div>

        {{-- Today's Summary + Quick Actions --}}
        @if($todaySummary)
            <div class="grid gap-4 lg:grid-cols-2">
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6">
                        <h3 class="font-semibold leading-none tracking-tight">Today's Summary</h3>
                        <p class="text-sm text-muted-foreground">Trip breakdown by direction and destination</p>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">SB Trips</span>
                                    <span class="font-semibold">{{ $todaySummary->sb_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">NB Trips</span>
                                    <span class="font-semibold">{{ $todaySummary->nb_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Total Trips</span>
                                    <span class="text-lg font-bold">{{ $todaySummary->total_trips }}</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Naga</span>
                                    <span class="font-semibold">{{ $todaySummary->naga_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Legazpi</span>
                                    <span class="font-semibold">{{ $todaySummary->legazpi_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Sorsogon</span>
                                    <span class="font-semibold">{{ $todaySummary->sorsogon_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Virac</span>
                                    <span class="font-semibold">{{ $todaySummary->virac_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Masbate</span>
                                    <span class="font-semibold">{{ $todaySummary->masbate_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Tabaco</span>
                                    <span class="font-semibold">{{ $todaySummary->tabaco_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Visayas</span>
                                    <span class="font-semibold">{{ $todaySummary->visayas_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Cargo</span>
                                    <span class="font-semibold">{{ $todaySummary->cargo_trips }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6">
                        <h3 class="font-semibold leading-none tracking-tight">Quick Actions</h3>
                        <p class="text-sm text-muted-foreground">Common dispatch operations</p>
                    </div>
                    <div class="flex flex-col gap-3 p-6 pt-0">
                        <a href="{{ route('dispatch.index') }}" class="inline-flex w-full items-center justify-start rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="M8 6v6"/><path d="M15 6v6"/><path d="M2 12h19.6"/><path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3"/><circle cx="7" cy="18" r="2"/><path d="M9 18h5"/><circle cx="16" cy="18" r="2"/></svg>
                            Go to Dispatch Board
                        </a>
                        <a href="{{ route('reports.index') }}" class="inline-flex w-full items-center justify-start rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            View Reports
                        </a>
                        <a href="{{ route('history.index') }}" class="inline-flex w-full items-center justify-start rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            View History
                        </a>
                    </div>
                </div>
            </div>
        @else
            {{-- Quick Actions when no summary --}}
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="p-6">
                    <h3 class="font-semibold leading-none tracking-tight">Quick Actions</h3>
                    <p class="text-sm text-muted-foreground">No dispatch day created for today yet</p>
                </div>
                <div class="flex gap-3 p-6 pt-0">
                    <a href="{{ route('dispatch.index') }}" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="M8 6v6"/><path d="M15 6v6"/><path d="M2 12h19.6"/><path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3"/><circle cx="7" cy="18" r="2"/><path d="M9 18h5"/><circle cx="16" cy="18" r="2"/></svg>
                        Go to Dispatch Board
                    </a>
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground">
                        View Reports
                    </a>
                </div>
            </div>
        @endif

        {{-- Recent Dispatch Entries --}}
        <div class="rounded-xl border bg-card text-card-foreground shadow">
            <div class="p-6">
                <h3 class="font-semibold leading-none tracking-tight">Recent Dispatch Entries</h3>
                <p class="text-sm text-muted-foreground">Last 10 entries from today's dispatch</p>
            </div>
            <div class="p-6 pt-0">
                @if(count($recentEntries) === 0)
                    <div class="py-8 text-center text-muted-foreground">
                        No dispatch entries for today yet.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="pb-2 pr-4 font-medium text-muted-foreground">Bus No.</th>
                                    <th class="pb-2 pr-4 font-medium text-muted-foreground">Route</th>
                                    <th class="pb-2 pr-4 font-medium text-muted-foreground">Direction</th>
                                    <th class="pb-2 pr-4 font-medium text-muted-foreground">Sched. Dep.</th>
                                    <th class="pb-2 pr-4 font-medium text-muted-foreground">Actual Dep.</th>
                                    <th class="pb-2 pr-4 font-medium text-muted-foreground">Driver</th>
                                    <th class="pb-2 font-medium text-muted-foreground">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentEntries as $entry)
                                    <tr class="border-b last:border-0">
                                        <td class="py-2 pr-4 font-medium">{{ $entry->bus_number ?? '--' }}</td>
                                        <td class="py-2 pr-4">{{ $entry->route ?? '--' }}</td>
                                        <td class="py-2 pr-4">
                                            @php $dir = $entry->direction?->value ?? $entry->direction; @endphp
                                            @if($dir)
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $dir === 'SB' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300' }}">
                                                    {{ $dir }}
                                                </span>
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td class="py-2 pr-4">{{ $entry->scheduled_departure ? Str::substr($entry->scheduled_departure, 0, 5) : '--' }}</td>
                                        <td class="py-2 pr-4">{{ $entry->actual_departure ? Str::substr($entry->actual_departure, 0, 5) : '--' }}</td>
                                        <td class="py-2 pr-4">{{ $entry->driver->name ?? '--' }}</td>
                                        <td class="py-2">
                                            @php
                                                $statusClasses = [
                                                    'scheduled' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                                    'departed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                                    'on_route' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
                                                    'delayed' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
                                                    'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                                    'arrived' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                                ];
                                            @endphp
                                            @php $entryStatus = $entry->status?->value ?? $entry->status ?? 'scheduled'; @endphp
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClasses[$entryStatus] ?? $statusClasses['scheduled'] }}">
                                                {{ ucwords(str_replace('_', ' ', $entryStatus)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

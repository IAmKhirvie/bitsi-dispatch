<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'BITSI Dispatch')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Dark mode init (before CSS to prevent flash) -->
        <script>
            (function() {
                const theme = localStorage.getItem('theme') || 'system';
                function applyTheme(theme) {
                    if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
                applyTheme(theme);
                
                // Listen for system theme changes
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                    if (localStorage.getItem('theme') === 'system') {
                        applyTheme('system');
                    }
                });
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @stack('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        @php
            $searchCategory = request('category', 'all');
            if (! request()->routeIs('search.index')) {
                $searchCategory = match (true) {
                    request()->routeIs('admin.vehicles.*') => 'buses',
                    request()->routeIs('dispatch.*'), request()->routeIs('history.*') => 'dispatch',
                    request()->routeIs('admin.drivers.*') => 'drivers',
                    request()->routeIs('admin.users.*') => 'users',
                    default => 'all',
                };
            }

            $searchCategories = [
                'all' => 'All',
                'dispatch' => 'Dispatch',
            ];

            if (Auth::user()?->is_admin) {
                $searchCategories['buses'] = 'Buses';
                $searchCategories['drivers'] = 'Drivers';
                $searchCategories['users'] = 'Users';
            }
        @endphp

        {{-- SidebarProvider wrapper --}}
        <div
            x-data="{
                sidebarOpen: Alpine.$persist(true).as('sidebar'),
                mobileOpen: false,
                toggleSidebar() {
                    if (window.innerWidth < 768) {
                        this.mobileOpen = !this.mobileOpen;
                    } else {
                        this.sidebarOpen = !this.sidebarOpen;
                    }
                }
            }"
            x-on:keydown.window.prevent.ctrl.b="toggleSidebar()"
            x-on:keydown.window.prevent.meta.b="toggleSidebar()"
            class="group/sidebar-wrapper flex min-h-svh w-full text-sidebar-foreground has-[[data-variant=inset]]:bg-sidebar"
            style="--sidebar-width: 16rem; --sidebar-width-icon: 3rem;"
        >
            {{-- Sidebar --}}
            @include('partials.sidebar')

            {{-- Main Content (SidebarInset) --}}
            <main
                class="relative flex min-h-svh flex-1 flex-col bg-background peer-data-[variant=inset]:min-h-[calc(100svh-theme(spacing.4))] md:peer-data-[variant=inset]:m-2 md:peer-data-[state=collapsed]:peer-data-[variant=inset]:ml-2 md:peer-data-[variant=inset]:ml-0 md:peer-data-[variant=inset]:rounded-xl md:peer-data-[variant=inset]:shadow"
            >
                {{-- Header with sidebar trigger and breadcrumbs --}}
                <header class="flex h-16 shrink-0 items-center gap-4 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12 md:px-4">
                    <div class="flex min-w-0 items-center">
                        {{-- Sidebar Trigger --}}
                        <button
                            x-on:click="toggleSidebar()"
                            class="-ml-1 inline-flex h-7 w-7 items-center justify-center rounded-md text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/></svg>
                            <span class="sr-only">Toggle Sidebar</span>
                        </button>
                    </div>

                    <form
                        action="{{ route('search.index') }}"
                        method="GET"
                        class="relative flex min-w-0 flex-1 items-center"
                        x-data="{
                            query: @js(request('q', request('search', ''))),
                            category: @js($searchCategory),
                            items: [],
                            open: false,
                            loading: false,
                            timer: null,
                            fetchSuggestions() {
                                clearTimeout(this.timer);
                                if (this.query.trim().length < 2) {
                                    this.items = [];
                                    this.open = false;
                                    return;
                                }
                                this.timer = setTimeout(async () => {
                                    this.loading = true;
                                    const params = new URLSearchParams({ q: this.query, category: this.category });
                                    const response = await fetch('{{ route('search.suggestions', [], false) }}?' + params.toString(), {
                                        headers: { 'Accept': 'application/json' },
                                    });
                                    const data = await response.json();
                                    this.items = data.items || [];
                                    this.open = true;
                                    this.loading = false;
                                }, 220);
                            },
                            go(url) {
                                window.location.href = url;
                            },
                        }"
                        x-on:click.away="open = false"
                    >
                        <div class="flex h-9 w-full overflow-hidden rounded-md border border-input bg-background shadow-sm focus-within:ring-1 focus-within:ring-ring">
                            <div class="relative min-w-0 flex-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                <input
                                    type="search"
                                    name="q"
                                    x-model="query"
                                    x-on:input="fetchSuggestions()"
                                    x-on:focus="items.length && (open = true)"
                                    placeholder="Search"
                                    class="h-full w-full border-0 bg-transparent pl-9 pr-3 text-sm outline-none placeholder:text-muted-foreground"
                                />
                            </div>
                            <select
                                name="category"
                                aria-label="Search category"
                                x-model="category"
                                x-on:change="fetchSuggestions()"
                                class="w-32 border-0 border-l border-input bg-muted/40 px-2 text-xs font-medium outline-none"
                            >
                                @foreach ($searchCategories as $value => $label)
                                    <option value="{{ $value }}" @selected($searchCategory === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div
                            x-cloak
                            x-show="open"
                            x-transition
                            class="absolute left-0 right-0 top-full z-50 mt-2 overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-lg"
                        >
                            <template x-if="loading">
                                <div class="px-3 py-3 text-sm text-muted-foreground">Searching...</div>
                            </template>
                            <template x-if="!loading && items.length === 0">
                                <div class="px-3 py-3 text-sm text-muted-foreground">No suggestions found.</div>
                            </template>
                            <template x-for="item in items" :key="item.category + item.title + item.url">
                                <button
                                    type="button"
                                    x-on:click="go(item.url)"
                                    class="block w-full px-3 py-2 text-left hover:bg-accent hover:text-accent-foreground"
                                >
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="truncate text-sm font-medium" x-text="item.title"></span>
                                        <span class="shrink-0 rounded bg-muted px-1.5 py-0.5 text-[10px] font-medium text-muted-foreground" x-text="item.category"></span>
                                    </div>
                                    <div class="truncate text-xs text-muted-foreground" x-text="item.subtitle"></div>
                                </button>
                            </template>
                            <button
                                type="submit"
                                class="block w-full border-t px-3 py-2 text-left text-xs font-medium text-primary hover:bg-accent"
                                x-show="query.trim().length >= 2"
                            >
                                View all results
                            </button>
                        </div>
                    </form>

                    <div class="flex shrink-0 items-center">
                        {{-- Breadcrumbs --}}
                        @isset($breadcrumbs)
                            <nav aria-label="breadcrumb">
                                <ol class="flex flex-wrap items-center gap-1.5 break-words text-sm text-muted-foreground sm:gap-2.5">
                                    @foreach($breadcrumbs as $index => $crumb)
                                        <li class="inline-flex items-center gap-1.5">
                                            @if($loop->last)
                                                <span role="link" aria-disabled="true" aria-current="page" class="font-normal text-foreground">
                                                    {{ $crumb['title'] }}
                                                </span>
                                            @else
                                                <a href="{{ $crumb['href'] }}" class="transition-colors hover:text-foreground">
                                                    {{ $crumb['title'] }}
                                                </a>
                                            @endif
                                        </li>
                                        @if(!$loop->last)
                                            <li role="presentation" aria-hidden="true" class="[&>svg]:h-3.5 [&>svg]:w-3.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                            </li>
                                        @endif
                                    @endforeach
                                </ol>
                            </nav>
                        @endisset
                    </div>
                </header>

                {{-- Flash Messages --}}
                @if(session('status'))
                    <div class="mx-4 mt-4 rounded-md border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-950 dark:text-green-300">
                        {{ session('status') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mx-4 mt-4 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-300">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Page Content --}}
                @yield('content')
            </main>
        </div>

        @stack('modals')

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            function fleetMap(initial) {
                return {
                    map: null,
                    markers: {},
                    init() {
                        const center = initial.length
                            ? [initial[0].lat, initial[0].lng]
                            : [13.6218, 123.1948];

                        this.map = L.map('fleet-map-canvas').setView(center, initial.length ? 11 : 9);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap',
                        }).addTo(this.map);

                        this.update(initial);
                    },
                    update(positions) {
                        if (!this.map) return;

                        const seen = new Set();

                        positions.forEach((position) => {
                            seen.add(position.id);

                            const color = position.stale ? '#9ca3af' : '#16a34a';
                            const label = position.bus_number || `#${position.id}`;
                            const html = [
                                `<div style="background:${color};color:#fff;font-size:10px;font-weight:600;padding:2px 6px;border-radius:9999px;border:2px solid #fff;box-shadow:0 1px 2px rgba(0,0,0,.3);white-space:nowrap;">`,
                                label,
                                '</div>',
                            ].join('');
                            const icon = L.divIcon({ html, className: '', iconSize: null });
                            const popup = [
                                '<div style="font-size:12px;line-height:1.4;">',
                                `<div style="font-weight:600;">${position.bus_number || ''} <span style="color:#666;font-weight:400;">${position.brand || ''}</span></div>`,
                                `<div>Status: ${position.status || '&mdash;'}</div>`,
                                `<div>KMR: ${position.kmr ?? '&mdash;'}</div>`,
                                `<div>Updated: ${position.recorded_at ? new Date(position.recorded_at).toLocaleString() : '&mdash;'}</div>`,
                                position.stale ? '<div style="color:#b91c1c;font-weight:600;">STALE</div>' : '',
                                '</div>',
                            ].join('');

                            if (this.markers[position.id]) {
                                this.markers[position.id]
                                    .setLatLng([position.lat, position.lng])
                                    .setIcon(icon)
                                    .bindPopup(popup);
                            } else {
                                this.markers[position.id] = L.marker([position.lat, position.lng], { icon })
                                    .addTo(this.map)
                                    .bindPopup(popup);
                            }
                        });

                        Object.keys(this.markers).forEach((id) => {
                            if (!seen.has(parseInt(id))) {
                                this.map.removeLayer(this.markers[id]);
                                delete this.markers[id];
                            }
                        });
                    },
                };
            }
        </script>
        @livewireScripts
        @stack('scripts')
    </body>
</html>

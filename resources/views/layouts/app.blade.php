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
                if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        {{-- SidebarProvider wrapper --}}
        <div
            x-data="{
                sidebarOpen: JSON.parse(localStorage.getItem('sidebar') ?? 'true'),
                mobileOpen: false,
                toggleSidebar() {
                    if (window.innerWidth < 768) {
                        this.mobileOpen = !this.mobileOpen;
                    } else {
                        this.sidebarOpen = !this.sidebarOpen;
                        localStorage.setItem('sidebar', JSON.stringify(this.sidebarOpen));
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
                <header class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12 md:px-4">
                    <div class="flex items-center gap-2">
                        {{-- Sidebar Trigger --}}
                        <button
                            x-on:click="toggleSidebar()"
                            class="-ml-1 inline-flex h-7 w-7 items-center justify-center rounded-md text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/></svg>
                            <span class="sr-only">Toggle Sidebar</span>
                        </button>

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

        @livewireScripts
    </body>
</html>

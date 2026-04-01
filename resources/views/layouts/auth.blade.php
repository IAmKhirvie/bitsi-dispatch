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
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10">
            <div class="w-full max-w-sm">
                <div class="flex flex-col gap-8">
                    <div class="flex flex-col items-center gap-4">
                        {{-- Logo --}}
                        <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium">
                            <div class="mb-1 flex h-9 w-9 items-center justify-center rounded-md">
                                <img src="/bicol_isarog_logo.png" alt="Bicol Isarog Logo" class="size-9 fill-current text-[var(--foreground)] dark:text-white" style="background: transparent;" />
                            </div>
                            <span class="sr-only">@yield('title')</span>
                        </a>

                        {{-- Title and Description --}}
                        <div class="space-y-2 text-center">
                            <h1 class="text-xl font-medium">@yield('title')</h1>
                            <p class="text-center text-sm text-muted-foreground">@yield('description')</p>
                        </div>
                    </div>

                    {{-- Flash Messages --}}
                    @if(session('status'))
                        <div class="rounded-md border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-950 dark:text-green-300">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Content --}}
                    @yield('content')
                </div>
            </div>
        </div>

        @livewireScripts
    </body>
</html>

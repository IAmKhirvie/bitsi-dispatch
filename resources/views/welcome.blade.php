<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Streamlining bus dispatch operations across the Bicol region with real-time tracking, smart scheduling, and reliable service.">

    <title>BITSI - Bicol Isarog Transport System</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-white dark:bg-zinc-950">
        {{-- Navigation --}}
        <nav class="sticky top-0 z-50 border-b border-zinc-200 bg-white/80 backdrop-blur-lg dark:border-zinc-800 dark:bg-zinc-950/80">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6v6"/><path d="M15 6v6"/><path d="M2 12h19.6"/><path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3"/><circle cx="7" cy="18" r="2"/><path d="M9 18h5"/><circle cx="16" cy="18" r="2"/></svg>
                    </div>
                    <div>
                        <span class="text-lg font-bold text-zinc-900 dark:text-white">BITSI</span>
                        <span class="ml-1 hidden text-sm text-zinc-500 dark:text-zinc-400 sm:inline">Dispatch</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                        >
                            Dashboard
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="rounded-lg px-4 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                        >
                            Log in
                        </a>
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                        >
                            Get Started
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        {{-- Hero Section --}}
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSA2MCAwIEwgMCAwIDAgNjAiIGZpbGw9Im5vbmUiIHN0cm9rZT0icmdiYSgyNTUsMjU1LDI1NSwwLjA1KSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyaWQpIi8+PC9zdmc+')] opacity-40"></div>
            <div class="relative mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:py-40">
                <div class="mx-auto max-w-3xl text-center">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-sm text-blue-100 backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6v6"/><path d="M15 6v6"/><path d="M2 12h19.6"/><path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3"/><circle cx="7" cy="18" r="2"/><path d="M9 18h5"/><circle cx="16" cy="18" r="2"/></svg>
                        Trusted Transport Since 1990s
                    </div>
                    <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                        Bicol Isarog Transport System, Inc.
                    </h1>
                    <p class="mt-3 text-xl font-medium text-blue-200 sm:text-2xl">
                        BITSI Dispatch Management
                    </p>
                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-blue-100">
                        Streamlining bus dispatch operations across the Bicol region with real-time tracking, smart scheduling, and reliable service.
                    </p>
                    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-white px-8 py-3.5 text-sm font-semibold text-blue-700 shadow-lg transition-all hover:bg-blue-50 hover:shadow-xl"
                        >
                            Access Dispatch System
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                        <a
                            href="#services"
                            class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-8 py-3.5 text-sm font-semibold text-white transition-all hover:bg-white/10"
                        >
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
            {{-- Wave divider --}}
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                    <path d="M0 80L60 73.3C120 66.7 240 53.3 360 46.7C480 40 600 40 720 46.7C840 53.3 960 66.7 1080 70C1200 73.3 1320 66.7 1380 63.3L1440 60V80H1380C1320 80 1200 80 1080 80C960 80 840 80 720 80C600 80 480 80 360 80C240 80 120 80 60 80H0Z" class="fill-white dark:fill-zinc-950"/>
                </svg>
            </div>
        </section>

        {{-- Stats --}}
        <section class="relative z-10 -mt-4">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    @php
                        $stats = [
                            ['value' => '30+', 'label' => 'Years of Service', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>'],
                            ['value' => '50+', 'label' => 'Buses in Fleet', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6v6"/><path d="M15 6v6"/><path d="M2 12h19.6"/><path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3"/><circle cx="7" cy="18" r="2"/><path d="M9 18h5"/><circle cx="16" cy="18" r="2"/></svg>'],
                            ['value' => '20+', 'label' => 'Routes Covered', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="19" r="3"/><path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15"/><circle cx="18" cy="5" r="3"/></svg>'],
                            ['value' => '1M+', 'label' => 'Passengers Yearly', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'],
                        ];
                    @endphp
                    @foreach($stats as $stat)
                        <div class="rounded-2xl border border-zinc-200 bg-white p-6 text-center shadow-sm transition-shadow hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                            <div class="mx-auto mb-3 h-8 w-8 text-blue-600">{!! $stat['icon'] !!}</div>
                            <div class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $stat['value'] }}</div>
                            <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Services --}}
        <section id="services" class="py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold text-zinc-900 dark:text-white sm:text-4xl">
                        Dispatch System Features
                    </h2>
                    <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">
                        A complete digital solution for managing daily bus operations
                    </p>
                </div>
                <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @php
                        $services = [
                            ['title' => 'Daily Dispatch Management', 'description' => 'Comprehensive digital dispatch board replacing manual Excel sheets. Track every bus, driver, and trip in real-time.', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6v6"/><path d="M15 6v6"/><path d="M2 12h19.6"/><path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3"/><circle cx="7" cy="18" r="2"/><path d="M9 18h5"/><circle cx="16" cy="18" r="2"/></svg>'],
                            ['title' => 'Real-Time GPS Tracking', 'description' => 'Monitor fleet positions live on an interactive map. Know exactly where every bus is at any moment.', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>'],
                            ['title' => 'Smart Scheduling', 'description' => 'Automated trip code assignment with departure scheduling. Auto-fill routes, terminals, and bus types instantly.', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>'],
                            ['title' => 'Preventive Maintenance', 'description' => 'PMS tracking by kilometers or trips with configurable thresholds. Never miss a maintenance schedule again.', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/></svg>'],
                            ['title' => 'SMS Driver Notifications', 'description' => 'Automatic SMS alerts to drivers on trip assignments and status changes via Semaphore integration.', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>'],
                            ['title' => 'Multiple Bus Types', 'description' => 'Support for Regular, Deluxe, Super Deluxe, Elite, Sleeper, Single Seater, and SkyBus classifications.', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 9V6a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3"/><path d="M3 16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5a2 2 0 0 0-4 0v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V11a2 2 0 0 0-4 0z"/><path d="M5 18v2"/><path d="M19 18v2"/></svg>'],
                        ];
                    @endphp
                    @foreach($services as $service)
                        <div class="group rounded-2xl border border-zinc-200 bg-white p-8 transition-all hover:border-blue-200 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-blue-800">
                            <div class="mb-5 inline-flex rounded-xl bg-blue-50 p-3 text-blue-600 transition-colors group-hover:bg-blue-100 dark:bg-blue-950 dark:group-hover:bg-blue-900">
                                {!! $service['icon'] !!}
                            </div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $service['title'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ $service['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Routes --}}
        <section class="border-y border-zinc-200 bg-zinc-50 py-24 dark:border-zinc-800 dark:bg-zinc-900/50">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold text-zinc-900 dark:text-white sm:text-4xl">
                        Routes We Serve
                    </h2>
                    <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">
                        Connecting Manila to the Bicol region and beyond
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-4xl">
                    <div class="grid gap-4 sm:grid-cols-2">
                        @php
                            $routes = [
                                ['origin' => 'Cubao', 'destination' => 'Naga', 'types' => ['Regular', 'Deluxe', 'Sleeper']],
                                ['origin' => 'Cubao', 'destination' => 'Legazpi', 'types' => ['Regular', 'Super Deluxe']],
                                ['origin' => 'Cubao', 'destination' => 'Sorsogon', 'types' => ['Regular']],
                                ['origin' => 'Cubao', 'destination' => 'Tabaco', 'types' => ['Regular']],
                                ['origin' => 'Cubao', 'destination' => 'Virac', 'types' => ['Deluxe']],
                                ['origin' => 'Cubao', 'destination' => 'Tacloban', 'types' => ['Elite']],
                                ['origin' => 'Naga', 'destination' => 'Masbate', 'types' => ['Regular']],
                            ];
                        @endphp
                        @foreach($routes as $r)
                            <div class="flex items-center gap-4 rounded-xl border border-zinc-200 bg-white p-5 transition-all hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 dark:text-blue-400"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-semibold text-zinc-900 dark:text-white">
                                        {{ $r['origin'] }} <span class="mx-1 text-zinc-400">&rarr;</span> {{ $r['destination'] }}
                                    </div>
                                    <div class="mt-1 flex flex-wrap gap-1.5">
                                        @foreach($r['types'] as $type)
                                            <span class="inline-block rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300">
                                                {{ $type }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- FAQ --}}
        <section class="py-24" x-data="{ openFaq: null }">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold text-zinc-900 dark:text-white sm:text-4xl">
                        Frequently Asked Questions
                    </h2>
                    <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">
                        Common questions about the dispatch system
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-3xl space-y-4">
                    @php
                        $faqs = [
                            ['question' => 'What is the BITSI Dispatch System?', 'answer' => 'It is a web-based fleet management platform that digitizes the daily bus status report, enabling real-time dispatch operations, GPS tracking, reporting, and driver notifications.'],
                            ['question' => 'Who can access the system?', 'answer' => 'The system supports three roles: Administrators (full access), Operations Managers (fleet and route management), and Dispatchers (daily dispatch operations).'],
                            ['question' => 'How does the PMS tracking work?', 'answer' => 'Each vehicle has a configurable maintenance threshold based on kilometers traveled or number of trips. The system alerts when a vehicle approaches or exceeds its PMS threshold.'],
                            ['question' => 'What bus statuses are tracked?', 'answer' => 'Vehicles are tracked as OK (available), UR (Under Repair), PMS (due for maintenance), In Transit (on a long trip), or Lutaw (idle/unused with day counting).'],
                        ];
                    @endphp
                    @foreach($faqs as $index => $faq)
                        <div class="rounded-xl border border-zinc-200 bg-white transition-all dark:border-zinc-800 dark:bg-zinc-900">
                            <button
                                @click="openFaq = openFaq === {{ $index }} ? null : {{ $index }}"
                                class="flex w-full items-center justify-between px-6 py-5 text-left"
                            >
                                <span class="pr-4 font-semibold text-zinc-900 dark:text-white">{{ $faq['question'] }}</span>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="h-5 w-5 shrink-0 text-zinc-400 transition-transform duration-200"
                                    :class="{ 'rotate-90': openFaq === {{ $index }} }"
                                ><path d="m9 18 6-6-6-6"/></svg>
                            </button>
                            <div
                                x-show="openFaq === {{ $index }}"
                                x-transition
                                class="border-t border-zinc-100 px-6 pb-5 pt-4 dark:border-zinc-800"
                            >
                                <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ $faq['answer'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="border-t border-zinc-200 dark:border-zinc-800">
            <div class="mx-auto max-w-7xl px-6 py-24">
                <div class="rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-700 px-8 py-16 text-center sm:px-16">
                    <h2 class="text-3xl font-bold text-white sm:text-4xl">Ready to modernize your dispatch?</h2>
                    <p class="mx-auto mt-4 max-w-xl text-lg text-blue-100">
                        Access the BITSI Dispatch System to manage daily operations, track your fleet, and generate reports.
                    </p>
                    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-white px-8 py-3.5 text-sm font-semibold text-blue-700 shadow-lg transition-all hover:bg-blue-50"
                        >
                            Sign In to Dashboard
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                        @guest
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-8 py-3.5 text-sm font-semibold text-white transition-all hover:bg-white/10"
                            >
                                Create Account
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mx-auto max-w-7xl px-6 py-12">
                <div class="flex flex-col items-center justify-between gap-6 sm:flex-row">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6v6"/><path d="M15 6v6"/><path d="M2 12h19.6"/><path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3"/><circle cx="7" cy="18" r="2"/><path d="M9 18h5"/><circle cx="16" cy="18" r="2"/></svg>
                        </div>
                        <span class="font-semibold text-zinc-900 dark:text-white">BITSI Dispatch</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-6">
                        <a href="{{ route('dispatch.index') }}" class="text-sm text-zinc-500 transition-colors hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Dispatch Board</a>
                        <a href="{{ route('reports.index') }}" class="text-sm text-zinc-500 transition-colors hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Reports</a>
                        <a href="{{ route('history.index') }}" class="text-sm text-zinc-500 transition-colors hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">History</a>
                    </div>
                </div>
                <div class="mt-8 border-t border-zinc-200 pt-8 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-500">
                    &copy; {{ date('Y') }} Bicol Isarog Transport System, Inc. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</body>
</html>

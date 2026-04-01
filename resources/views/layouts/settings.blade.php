@extends('layouts.app')

@section('content')
    <div class="px-4 py-6">
        {{-- Heading --}}
        <div class="mb-8 space-y-0.5">
            <h2 class="text-xl font-semibold tracking-tight">Settings</h2>
            <p class="text-sm text-muted-foreground">Manage your profile and account settings</p>
        </div>

        {{-- Separator --}}
        <div class="my-6 shrink-0 bg-border h-[1px] w-full"></div>

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0">
            {{-- Settings Sidebar Nav --}}
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-x-0 space-y-1">
                    <a
                        href="{{ route('profile.edit') }}"
                        class="inline-flex items-center justify-start gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 w-full justify-start {{ request()->routeIs('profile.edit') ? 'bg-muted' : '' }}"
                    >
                        Profile
                    </a>
                    <a
                        href="{{ route('password.edit') }}"
                        class="inline-flex items-center justify-start gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 w-full justify-start {{ request()->routeIs('password.edit') ? 'bg-muted' : '' }}"
                    >
                        Password
                    </a>
                    <a
                        href="{{ route('appearance.edit') }}"
                        class="inline-flex items-center justify-start gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 w-full justify-start {{ request()->routeIs('appearance.edit') ? 'bg-muted' : '' }}"
                    >
                        Appearance
                    </a>
                </nav>
            </aside>

            {{-- Separator (mobile only) --}}
            <div class="my-6 shrink-0 bg-border h-[1px] w-full md:hidden"></div>

            {{-- Settings Content --}}
            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    @yield('settings-content')
                </section>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Appearance Settings - BITSI Dispatch')

@section('content')
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        {{-- Settings Navigation --}}
        <div class="mx-auto w-full max-w-2xl">
            <nav class="flex gap-1 rounded-lg bg-muted p-1">
                <a href="{{ route('profile.edit') }}" class="flex-1 rounded-md px-3 py-1.5 text-center text-sm font-medium {{ request()->routeIs('profile.edit') ? 'bg-background text-foreground shadow' : 'text-muted-foreground hover:text-foreground' }}">
                    Profile
                </a>
                <a href="{{ route('password.edit') }}" class="flex-1 rounded-md px-3 py-1.5 text-center text-sm font-medium {{ request()->routeIs('password.edit') ? 'bg-background text-foreground shadow' : 'text-muted-foreground hover:text-foreground' }}">
                    Password
                </a>
                <a href="{{ route('appearance') }}" class="flex-1 rounded-md px-3 py-1.5 text-center text-sm font-medium {{ request()->routeIs('appearance') ? 'bg-background text-foreground shadow' : 'text-muted-foreground hover:text-foreground' }}">
                    Appearance
                </a>
            </nav>
        </div>

        <div class="mx-auto w-full max-w-2xl">
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium">Appearance settings</h3>
                    <p class="text-sm text-muted-foreground">Update your account's appearance settings</p>
                </div>

                {{-- Theme Toggle --}}
                <div x-data="{
                    theme: localStorage.getItem('theme') || 'system',
                    setTheme(value) {
                        this.theme = value;
                        localStorage.setItem('theme', value);
                        if (value === 'dark' || (value === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    }
                }">
                    <div class="grid grid-cols-3 gap-4">
                        {{-- Light --}}
                        <button
                            @click="setTheme('light')"
                            class="flex flex-col items-center gap-3 rounded-lg border-2 p-4 transition-colors"
                            :class="theme === 'light' ? 'border-primary bg-primary/5' : 'border-transparent hover:border-border'"
                        >
                            <div class="flex h-16 w-full items-center justify-center rounded-md border bg-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-yellow-500"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                            </div>
                            <span class="text-sm font-medium">Light</span>
                        </button>

                        {{-- Dark --}}
                        <button
                            @click="setTheme('dark')"
                            class="flex flex-col items-center gap-3 rounded-lg border-2 p-4 transition-colors"
                            :class="theme === 'dark' ? 'border-primary bg-primary/5' : 'border-transparent hover:border-border'"
                        >
                            <div class="flex h-16 w-full items-center justify-center rounded-md border bg-zinc-900">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-blue-300"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                            </div>
                            <span class="text-sm font-medium">Dark</span>
                        </button>

                        {{-- System --}}
                        <button
                            @click="setTheme('system')"
                            class="flex flex-col items-center gap-3 rounded-lg border-2 p-4 transition-colors"
                            :class="theme === 'system' ? 'border-primary bg-primary/5' : 'border-transparent hover:border-border'"
                        >
                            <div class="flex h-16 w-full items-center justify-center rounded-md border bg-gradient-to-r from-white to-zinc-900">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-zinc-500"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
                            </div>
                            <span class="text-sm font-medium">System</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

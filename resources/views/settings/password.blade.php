@extends('layouts.app')

@section('title', 'Password Settings - BITSI Dispatch')

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
                    <h3 class="text-lg font-medium">Update password</h3>
                    <p class="text-sm text-muted-foreground">Ensure your account is using a long, random password to stay secure</p>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-2">
                        <label for="current_password" class="text-sm font-medium leading-none">Current Password</label>
                        <input id="current_password" name="current_password" type="password" autocomplete="current-password" placeholder="Current password" class="mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                        @error('current_password')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-2">
                        <label for="password" class="text-sm font-medium leading-none">New password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" placeholder="New password" class="mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                        @error('password')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-2">
                        <label for="password_confirmation" class="text-sm font-medium leading-none">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Confirm password" class="mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                        @error('password_confirmation')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90">Save password</button>

                        @if(session('status') === 'password-updated')
                            <p class="text-sm text-neutral-600">Saved.</p>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

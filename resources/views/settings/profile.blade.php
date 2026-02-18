@extends('layouts.app')

@section('title', 'Profile Settings - BITSI Dispatch')

@section('content')
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        {{-- Settings Navigation --}}
        <div class="mx-auto w-full max-w-2xl">
            <nav class="flex gap-1 rounded-lg bg-muted p-1">
                <a href="{{ route('profile.edit') }}" class="flex-1 rounded-md px-3 py-1.5 text-center text-sm font-medium {{ request()->routeIs('profile.edit') ? 'bg-background text-foreground shadow' : 'text-muted-foreground hover:text-foreground' }}">
                    Profile
                </a>
                <a href="{{ route('appearance') }}" class="flex-1 rounded-md px-3 py-1.5 text-center text-sm font-medium {{ request()->routeIs('appearance') ? 'bg-background text-foreground shadow' : 'text-muted-foreground hover:text-foreground' }}">
                    Appearance
                </a>
            </nav>
        </div>

        <div class="mx-auto w-full max-w-2xl space-y-10">
            {{-- Profile Information (with photo upload if enabled) --}}
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('profile.update-profile-information-form')
            @endif

            {{-- Update Password --}}
            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="border-t pt-6">
                    @livewire('profile.update-password-form')
                </div>
            @endif

            {{-- Two Factor Authentication --}}
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="border-t pt-6">
                    @livewire('profile.two-factor-authentication-form')
                </div>
            @endif

            {{-- Browser Sessions --}}
            <div class="border-t pt-6">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            {{-- Delete Account --}}
            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <div class="border-t pt-6">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
@endsection

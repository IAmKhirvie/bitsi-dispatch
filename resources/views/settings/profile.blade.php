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
                <a href="{{ route('password.edit') }}" class="flex-1 rounded-md px-3 py-1.5 text-center text-sm font-medium {{ request()->routeIs('password.edit') ? 'bg-background text-foreground shadow' : 'text-muted-foreground hover:text-foreground' }}">
                    Password
                </a>
                <a href="{{ route('appearance') }}" class="flex-1 rounded-md px-3 py-1.5 text-center text-sm font-medium {{ request()->routeIs('appearance') ? 'bg-background text-foreground shadow' : 'text-muted-foreground hover:text-foreground' }}">
                    Appearance
                </a>
            </nav>
        </div>

        <div class="mx-auto w-full max-w-2xl space-y-6">
            {{-- Profile Information --}}
            <div class="flex flex-col space-y-6">
                <div>
                    <h3 class="text-lg font-medium">Profile information</h3>
                    <p class="text-sm text-muted-foreground">Update your name and email address</p>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-2">
                        <label for="name" class="text-sm font-medium leading-none">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" required autocomplete="name" placeholder="Full name" class="mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                        @error('name')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-2">
                        <label for="email" class="text-sm font-medium leading-none">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required autocomplete="username" placeholder="Email address" class="mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                        @error('email')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    @if(isset($mustVerifyEmail) && $mustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                        <p class="mt-2 text-sm text-neutral-800">
                            Your email address is unverified.
                            <button form="send-verification" type="submit" class="rounded-md text-sm text-neutral-600 underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:text-neutral-900 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:decoration-neutral-500">
                                Click here to re-send the verification email.
                            </button>
                        </p>

                        @if(session('status') === 'verification-link-sent')
                            <div class="mt-2 text-sm font-medium text-green-600">
                                A new verification link has been sent to your email address.
                            </div>
                        @endif
                    @endif

                    <div class="flex items-center gap-4">
                        <button type="submit" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90">Save</button>

                        @if(session('status') === 'profile-updated')
                            <p class="text-sm text-neutral-600">Saved.</p>
                        @endif
                    </div>
                </form>
            </div>

            <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="hidden">
                @csrf
            </form>

            {{-- Delete Account --}}
            <div class="border-t pt-6">
                <div>
                    <h3 class="text-lg font-medium">Delete account</h3>
                    <p class="text-sm text-muted-foreground">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                </div>

                <form method="POST" action="{{ route('profile.destroy') }}" class="mt-6" x-data="{ confirmDelete: false }">
                    @csrf
                    @method('DELETE')

                    <div x-show="!confirmDelete">
                        <button type="button" @click="confirmDelete = true" class="inline-flex items-center rounded-md bg-destructive px-4 py-2 text-sm font-medium text-destructive-foreground shadow-sm hover:bg-destructive/90">
                            Delete Account
                        </button>
                    </div>

                    <div x-show="confirmDelete" x-cloak class="space-y-4">
                        <p class="text-sm text-muted-foreground">Please enter your password to confirm you would like to permanently delete your account.</p>

                        <div class="grid gap-2">
                            <label for="delete_password" class="text-sm font-medium leading-none">Password</label>
                            <input id="delete_password" name="password" type="password" placeholder="Password" class="flex h-9 w-full max-w-xs rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                            @error('password', 'userDeletion')
                                <p class="text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="button" @click="confirmDelete = false" class="inline-flex items-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground">
                                Cancel
                            </button>
                            <button type="submit" class="inline-flex items-center rounded-md bg-destructive px-4 py-2 text-sm font-medium text-destructive-foreground shadow-sm hover:bg-destructive/90">
                                Delete Account
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

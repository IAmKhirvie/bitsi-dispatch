@extends('layouts.auth')

@section('title', 'Register')
@section('heading', 'Create an account')
@section('description', 'Enter your details below to create your account')

@section('content')
    <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-6">
        @csrf

        <div class="grid gap-6">
            {{-- Name --}}
            <div class="grid gap-2">
                <label for="name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
                    Name
                </label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    tabindex="1"
                    autocomplete="name"
                    placeholder="Full name"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                />
                @error('name')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="grid gap-2">
                <label for="email" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
                    Email address
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    tabindex="2"
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                />
                @error('email')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="grid gap-2">
                <label for="password" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
                    Password
                </label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    tabindex="3"
                    autocomplete="new-password"
                    placeholder="Password"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                />
                @error('password')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="grid gap-2">
                <label for="password_confirmation" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
                    Confirm password
                </label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    tabindex="4"
                    autocomplete="new-password"
                    placeholder="Confirm password"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                />
                @error('password_confirmation')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                tabindex="5"
                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2 mt-2 w-full"
            >
                Create account
            </button>
        </div>

        <div class="text-center text-sm text-muted-foreground">
            Already have an account?
            <a href="{{ route('login') }}" tabindex="6" class="hover:decoration-current! text-foreground underline underline-offset-4 decoration-neutral-300 transition-colors duration-300 ease-out dark:decoration-neutral-500">
                Log in
            </a>
        </div>
    </form>
@endsection

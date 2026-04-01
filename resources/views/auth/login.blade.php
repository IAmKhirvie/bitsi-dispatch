@extends('layouts.auth')

@section('title', 'Log in')
@section('heading', 'Log in to your account')
@section('description', 'Enter your email and password below to log in')

@section('content')
    @if (session('status'))
        <div class="mb-4 text-center text-sm font-medium text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-6">
        @csrf

        <div class="grid gap-6">
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
                    autofocus
                    tabindex="1"
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
                <div class="flex items-center justify-between">
                    <label for="password" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
                        Password
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm hover:decoration-current! text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out dark:decoration-neutral-500" tabindex="5">
                            Forgot password?
                        </a>
                    @endif
                </div>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    tabindex="2"
                    autocomplete="current-password"
                    placeholder="Password"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                />
                @error('password')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between" tabindex="3">
                <label for="remember" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50 flex items-center space-x-3">
                    <input
                        id="remember"
                        type="checkbox"
                        name="remember"
                        tabindex="4"
                        class="h-4 w-4 rounded border border-primary shadow focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        {{ old('remember') ? 'checked' : '' }}
                    />
                    <span>Remember me</span>
                </label>
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                tabindex="4"
                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2 mt-4 w-full"
            >
                Log in
            </button>
        </div>

        <div class="text-center text-sm text-muted-foreground">
            Don't have an account?
            <a href="{{ route('register') }}" tabindex="5" class="hover:decoration-current! text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out dark:decoration-neutral-500">
                Sign up
            </a>
        </div>
    </form>
@endsection

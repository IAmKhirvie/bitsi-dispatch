@extends('layouts.auth')

@section('title', 'Email verification')
@section('heading', 'Verify email')
@section('description', 'Please verify your email address by clicking on the link we just emailed to you.')

@section('content')
    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-center text-sm font-medium text-green-600">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="space-y-6 text-center">
        @csrf

        <button
            type="submit"
            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
        >
            Resend verification email
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-6 text-center">
        @csrf

        <button
            type="submit"
            class="mx-auto block text-sm hover:decoration-current! text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out dark:decoration-neutral-500"
        >
            Log out
        </button>
    </form>
@endsection

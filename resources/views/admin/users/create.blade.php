@extends('layouts.app')

@section('title', 'Create User - BITSI Dispatch')

@section('content')
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="mx-auto w-full max-w-2xl">
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="p-6">
                    <h3 class="font-semibold leading-none tracking-tight">Create User</h3>
                    <p class="text-sm text-muted-foreground">Add a new user to the system.</p>
                </div>
                <div class="p-6 pt-0">
                    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                        @csrf

                        <div class="space-y-2">
                            <label for="name" class="text-sm font-medium leading-none">Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Full name" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                            @error('name')
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="email" class="text-sm font-medium leading-none">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="email@example.com" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                            @error('email')
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="password" class="text-sm font-medium leading-none">Password</label>
                                <input id="password" name="password" type="password" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                @error('password')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label for="password_confirmation" class="text-sm font-medium leading-none">Confirm Password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="role" class="text-sm font-medium leading-none">Role</label>
                                <select id="role" name="role" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                    <option value="admin" {{ old('role', 'dispatcher') === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="operations_manager" {{ old('role') === 'operations_manager' ? 'selected' : '' }}>Operations Manager</option>
                                    <option value="dispatcher" {{ old('role', 'dispatcher') === 'dispatcher' ? 'selected' : '' }}>Dispatcher</option>
                                </select>
                                @error('role')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label for="phone" class="text-sm font-medium leading-none">Phone</label>
                                <input id="phone" name="phone" type="text" value="{{ old('phone') }}" placeholder="09XX XXX XXXX" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                @error('phone')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 rounded border border-primary shadow focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                            <label for="is_active" class="text-sm font-medium leading-none">Active</label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground">Cancel</a>
                            <button type="submit" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90">
                                Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@php
    use App\Enums\UserRole;
    $isEdit = isset($user);
@endphp

<div class="space-y-2">
    <label for="name" class="text-sm font-medium leading-none">Name</label>
    <input id="name" name="name" type="text" value="{{ old('name', $isEdit ? $user->name : '') }}" placeholder="Full name" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
    @error('name')
        <p class="text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

<div class="space-y-2">
    <label for="email" class="text-sm font-medium leading-none">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email', $isEdit ? $user->email : '') }}" placeholder="email@example.com" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
    @error('email')
        <p class="text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div class="space-y-2">
        <label for="password" class="text-sm font-medium leading-none">Password</label>
        <input id="password" name="password" type="password" {{ $isEdit ? '' : 'required' }} placeholder="{{ $isEdit ? 'Leave blank to keep current' : '' }}" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('password')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="password_confirmation" class="text-sm font-medium leading-none">Confirm Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" {{ $isEdit ? '' : 'required' }} placeholder="{{ $isEdit ? 'Leave blank to keep current' : '' }}" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div class="space-y-2">
        <label for="role" class="text-sm font-medium leading-none">Role</label>
        <select id="role" name="role" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
            @foreach (UserRole::cases() as $role)
                <option value="{{ $role->value }}" {{ old('role', $isEdit ? $user->role : 'dispatcher') === $role->value ? 'selected' : '' }}>
                    {{ $role->label() }}
                </option>
            @endforeach
        </select>
        @error('role')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="phone" class="text-sm font-medium leading-none">Phone</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', $isEdit ? $user->phone : '') }}" placeholder="09XX XXX XXXX" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('phone')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="flex items-center gap-2">
    <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $isEdit ? $user->is_active : true) ? 'checked' : '' }} class="h-4 w-4 rounded border border-primary shadow focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
    <label for="is_active" class="text-sm font-medium leading-none">Active</label>
</div>

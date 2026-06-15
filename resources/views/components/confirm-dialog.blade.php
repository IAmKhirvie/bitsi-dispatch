{{--
  Usage:
    <x-confirm-dialog
        trigger-id="delete-user-{{ $user->id }}"
        title="Delete {{ $user->name }}?"
        message="The user will be moved to Trash. You can restore from Admin → Trash."
        confirm-label="Delete"
        variant="destructive"
        wire:click="deleteUser({{ $user->id }})"
    >
        <svg ...trash icon... />
    </x-confirm-dialog>

  Notes:
    - Renders a small trigger button (slot = icon/content) + Alpine modal.
    - Forwards wire:click / @click directives to the confirm action button.
--}}
@props([
    'triggerId' => 'confirm-' . uniqid(),
    'triggerClass' => 'inline-flex h-8 w-8 items-center justify-center rounded-md text-destructive hover:bg-destructive/10',
    'triggerTitle' => 'Confirm',
    'title' => 'Are you sure?',
    'message' => 'This action cannot be undone.',
    'confirmLabel' => 'Confirm',
    'cancelLabel' => 'Cancel',
    'variant' => 'destructive',
])

@php
    $confirmClass = $variant === 'destructive'
        ? 'bg-destructive text-destructive-foreground hover:bg-destructive/90'
        : 'bg-primary text-primary-foreground hover:bg-primary/90';
@endphp

<div x-data="{ open: false }" class="inline-block">
    <button type="button"
            class="{{ $triggerClass }}"
            title="{{ $triggerTitle }}"
            @click="open = true">
        {{ $slot }}
    </button>

    <div x-show="open" x-cloak
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
         @click.self="open = false"
         @keydown.escape.window="open = false">
        <div class="w-full max-w-md rounded-lg border bg-background p-6 shadow-lg"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <h3 class="text-lg font-semibold">{{ $title }}</h3>
            <p class="mt-2 text-sm text-muted-foreground">{{ $message }}</p>
            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button type="button"
                        @click="open = false"
                        class="inline-flex h-9 items-center justify-center rounded-md border border-input bg-background px-4 text-sm font-medium hover:bg-accent">
                    {{ $cancelLabel }}
                </button>
                <button type="button"
                        @click="open = false"
                        {{ $attributes->whereStartsWith(['wire:', '@', 'x-on:']) }}
                        class="inline-flex h-9 items-center justify-center rounded-md px-4 text-sm font-medium shadow {{ $confirmClass }}">
                    {{ $confirmLabel }}
                </button>
            </div>
        </div>
    </div>
</div>

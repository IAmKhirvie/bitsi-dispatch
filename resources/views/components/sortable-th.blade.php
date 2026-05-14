@props([
    'field',
    'label' => null,
    'active' => null,
    'direction' => 'asc',
])

<th {{ $attributes->merge(['class' => 'px-4 py-3 text-left']) }}>
    <button
        type="button"
        wire:click="sortBy('{{ $field }}')"
        class="inline-flex w-full items-center gap-1 text-left font-medium text-muted-foreground transition-colors hover:text-foreground"
    >
        {{ $label ?? $slot }}
        @if ($active === $field)
            @if ($direction === 'asc')
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
            @endif
        @endif
    </button>
</th>
